<?php

class CrashReport
{
    public $id;
    public $reported;
    public $client_version;
    public $client_channel;
    public $os;
    public $os_type;
    public $os_version;
    public $cpu;
    public $gpu;
    public $opengl_version;
    public $ram;
    public $grid;
    public $region;
    public $crash_reason;
    public $crash_address;
    public $crash_thread;
    public $modules = array();
    public $threads = array();
    public $raw_stacktrace;
    public $signature_id;
    public $signature;
    public $signature_text;
    
    public static $all_signatures;
    
    function htmlFrame($f, $chan, $version)
    {
        $urlBase = "https://github.com/singularity-viewer/SingularityViewer/blob/" . self::getHash($chan, $version);
        
        $ret = "";
        $link = "";
        if ($f->function)
        {
            $ret .= htmlentities($f->function);
            if ($f->source_file)
            {
                if (false !== $pos = strpos($f->source_file, "indra"))
                {
                    $source = substr($f->source_file, $pos + 6);
                    $source = str_replace("\\", "/", $source);
                    $ret .= " at " . $source;
                    $ret .= " (line {$f->source_line} + {$f->function_offset})";
                    $link = "$urlBase/indra/$source/#L{$f->source_line}";
                }
                else if (false !== $pos = strpos($f->source_file, "libraries"))                
                {
                    $source = substr($f->source_file, $pos);
                    $source = str_replace("\\", "/", $source);
                    $ret .= " at " . $source;
                    $ret .= " (line {$f->source_line} + {$f->function_offset})";
                }
                else
                {
                    $ret .= " " . $f->source_file;
                }
            }
        }
        
        if ($link)
        {
            $ret = '<a href="' . $link . '">' . $ret . '</a>';
        }
        return $ret;
    }
    function getTotal($filter)
    {
        $where = $filter->getWhere();
        $q = "select count(id) as total from reports $where";
        if (false !== $cached = Memc::getq($q)) return $cached;
        
        if (!$res = DBH::$db->query($q) OR !$row = DBH::$db->fetchRow($res))
        {
            return 0;
        }
        else
        {
            Memc::setq($q, $row["total"]);
            return $row["total"];
        }
    }
    
    function getReports($filter, $fields = "id, reported, client_version, client_channel, os, gpu, grid, region, signature_id")
    {
        $ret = array();
        $q = "select $fields from reports " . $filter->getWhere() . kl_str_sql(" order by id desc limit !i offset !i", $filter->limit, $filter->offset);
        if (false !== $cached = Memc::getq($q)) return $cached;
        
        if (!$res = DBH::$db->query($q))
        {
            return $ret;
        }
        
        while ($row = DBH::$db->fetchRow($res))
        {
            $r = new CrashReport;
            DBH::$db->loadFromDbRow($r, $res, $row);
            $ret[] = $r;
        }
        
        Memc::setq($q, $ret);
        return $ret;
    }
    
    function getReport($id)
    {
        $ret = array();
        if (!$res = DBH::$db->query(kl_str_sql("select * from reports where id=!i", $id)))
        {
            return null;
        }
        
        if ($row = DBH::$db->fetchRow($res))
        {
            $r = new CrashReport;
            DBH::$db->loadFromDbRow($r, $res, $row);
            $r->parseStackTrace($r->raw_stacktrace);
            return $r;
        }
        
        return null;
    }

    function delete()
    {
        DBH::$db->query(kl_str_sql("delete from reports where id=!i", $this->id));
    }
    
    function save()
    {
        $this->delete();
        $q = kl_str_sql("insert into reports (
                        id,
                        reported,
                        client_version,
                        client_channel,
                        os,
                        os_type,
                        os_version,
                        cpu,
                        gpu,
                        opengl_version,
                        ram,
                        grid,
                        region,
                        crash_reason,
                        crash_address,
                        crash_thread,
                        raw_stacktrace
                        ) values (!i, !t, !s, !s, !s, !s, !s, !s, !s, !s, !i, !s, !s, !s, !s, !i, !s)",
                        $this->id,
                        $this->reported,
                        $this->client_version,
                        $this->client_channel,
                        $this->os,
                        $this->os_type,
                        $this->os_version,
                        $this->cpu,
                        $this->gpu,
                        $this->opengl_version,
                        $this->ram,
                        $this->grid,
                        $this->region,
                        $this->crash_reason,
                        $this->crash_address,
                        $this->crash_thread,
                        $this->raw_stacktrace);
        if ($res = DBH::$db->query($q))
        {
            return true;
        }
        else
        {
            return false;
        }
    }
    
    function parseStackTrace($stacktrace = null)
    {
        if (null === $stacktrace)
        {
            $stacktrace = $this->raw_stacktrace;
        }
        $lines = explode("\n", $stacktrace);
        foreach($lines as $line)
        {
            $elems = explode("|", $line);
            $type = $elems[0];
            
            switch ($type)
            {
                case "OS":
                    $this->os_type = $elems[1];
                    $this->os_version = $elems[2];
                    break;
                
                case "Crash":
                    $this->crash_reason = $elems[1];
                    $this->crash_address = $elems[2];
                    $this->crash_thread = (int)$elems[3];
                    break;
                
                case "Module":
                    $m = new stdClass;
                    $m->name = $elems[1];
                    $m->version = $elems[2];
                    $this->modules[] = $m;
                    break;
                
                default:
                    if (false !== filter_var($type, FILTER_VALIDATE_INT))
                    {
                        $threadID = (int)$type;
                        if (!isset($this->threads[$threadID]))
                        {
                            $t = new stdClass;
                            $t->frames = array();
                        }
                        else
                        {
                            $t = $this->threads[$threadID];
                        }
                        
                        $frameID = (int)$elems[1];
                        
                        $frame = new stdClass;
                        $frame->module = $elems{2};
                        $frame->function = $elems[3];
                        $frame->source_file = $elems[4];
                        $frame->source_line = $elems[5];
                        $frame->function_offset = $elems[6];
                        $t->frames[$frameID] = $frame;
                        
                        $this->threads[$threadID] = $t;
                    }
                
            }
        }
    }
    
    function updateSignature()
    {
        $t = $this->threads[$this->crash_thread];
        if (!$t) return;
        //var_dump($t);
        
        $module = null;
        $function = null;
        $singu_function = null;
        
        for ($i = 0; $i < count($t->frames); $i++)
        {
            $f = $t->frames[$i]; // current frame
            
            $f->source_file = str_replace("\\", "/", $f->source_file);
            
            if (stristr($f->module, "singularity") !== false)
            {
                $f->module = "singularity";
            }
            else if (stristr($f->module, "llcommon") !== false)
            {
                $f->module = "llcommon";
            }
            else if (preg_match("/(kernel|ntdll).*\\.dll/i", $f->module))
            {
                $f->module = "windows-runtime";
            } else if (preg_match("/libc.*\\.so/i", $f->module))
            {
                $f->module = "linux-runtime";
            } else if (preg_match("/libsys.*\\.dylib/i", $f->module))
            {
                $f->module = "mac-runtime";
            } else if (preg_match("/(nvogl|libnvidia)/i", $f->module))
            {
                $f->module = "nvidia-driver";
            } else if (preg_match("/(atiogl|fglrx)/i", $f->module))
            {
                $f->module = "ati-driver";
            } else if (preg_match("/ig.*icd/i", $f->module))
            {
                $f->module = "intel-driver";
            }
            
            if (!$module && $f->module)
            {
                $module = $f->module;
            }
            
            $is_singu = stristr($f->source_file, "/indra/") !== false;
            if ($is_singu)
            {
                if (!$singu_function)
                {
                    $singu_function = $f->function;
                    if (!$function) $function = $f->function;
                }
            }
            else if (!$function && $f->function)
            {
                $function = $f->function;
            }
            
            if (preg_match("/^LLError::/", $f->function))
            {
                $function = null;
                $singu_function = null;
            }
           
        }
        
        if ($function == $singu_function)
        {
            $function = "";
        }
        if (!$singu_function && strpos($function, "LL") !== false)
        {
            $singu_function = $function;
            $function = "";
        }
        $this->signature_text = "$module|$function|$singu_function";
        $this->signature = md5($this->signature_text);
    }
    
    function getAllSignatureHashes()
    {
        $ret = array();
        $q = "select id, hash from signature";
        
        if (!$res = DBH::$db->query($q)) return $ret;

        while ($row = DBH::$db->fetchRow($res))
        {
            $ret[$row["hash"]] = (int)$row["id"];
        }
        
        return $ret;
    }
    
    function saveSignature()
    {
        if (!self::$all_signatures)
        {
            self::$all_signatures = self::getAllSignatureHashes();
        }
        
        if (!self::$all_signatures[$this->signature])
        {
            $q = kl_str_sql("insert into signature(hash, signature) values (!s, !s)", $this->signature, $this->signature_text);
            if (!$res = DBH::$db->query($q)) return;
            $this->signature_id = DBH::$db->insertID();
            self::$all_signatures[$this->signature] = $this->signature_id;
        }
        else
        {
            $this->signature_id = self::$all_signatures[$this->signature];
        }
        
        $q = kl_str_sql("update reports set signature_id=!i where id=!i", $this->signature_id, $this->id);
        $res = DBH::$db->query($q);
    }
    
    function init($id, $data, $stacktrace)
    {
        $this->id = $id;
        $this->reported = $data["reported"];
        $this->client_version = $data["clientVersion"];
        $this->client_channel = $data["clientChannel"];
        $this->os = $data["DebugLog"]["OSInfo"];
        $this->gpu = $data["DebugLog"]["GraphicsCard"];
        $this->cpu = $data["DebugLog"]["CPUInfo"]["CPUString"];
        $this->opengl_version = $data["DebugLog"]["GLInfo"]["GLVersion"];
        $this->ram = $data["DebugLog"]["RAMInfo"]["Allocated"];
        $this->grid = $data["DebugLog"]["GridName"];
        $this->region = $data["DebugLog"]["CurrentRegion"];
        unset($data["Minidump"]);
        $this->raw_stacktrace = $stacktrace;
        $this->parseStackTrace($this->raw_stacktrace);
    }
    
    static function getBuildsMap()
    {
        $ret = array();
        $q = "select * from builds order by chan asc, build_nr desc";
        if (false !== $cached = Memc::getq($q)) return $cached;
       
        if (!$res = DBH::$db->query($q)) return;
        
        while ($row = DBH::$db->fetchRow($res))
        {
            $build = new stdClass;
            DBH::$db->loadFromDbRow($build, $res, $row);
            if (!$ret[$build->chan])
            {
                $ret[$build->chan] = array();
            }
            $ret[$build->chan][$build->version] = $build->hash;
        }
        
        Memc::setq($q, $ret);
        return $ret;
    }
    
    static $builds_map = null;
    
    static function getHash($chan, $version)
    {
        if (null == self::$builds_map)
        {
            self::$builds_map = self::getBuildsMap();
        }
        
        if (!self::$builds_map[$chan]) $chan = "SingularityAlpha";
        
        if (!self::$builds_map[$chan][$version]) return "master";
        
        return self::$builds_map[$chan][$version];
    }
}