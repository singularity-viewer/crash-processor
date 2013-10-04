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
    
    function htmlFrame($f)
    {
        static $urlBase = "https://github.com/singularity-viewer/SingularityViewer/blob/master";
        
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
    function getTotal()
    {
        global $DB;
        
        if (!$res = $DB->query("select count(id) as total from reports") OR !$row = $DB->fetchRow($res))
        {
            return 0;
        }
        else
        {
            return $row["total"];
        }
    }
    
    function getReports($offset = 0, $limit = 100)
    {
        global $DB;
        
        $ret = array();
        if (!$res = $DB->query(kl_str_sql("select * from reports order by id desc limit !i offset !i", $limit, $offset)))
        {
            return $ret;
        }
        
        while ($row = $DB->fetchRow($res))
        {
            $r = new CrashReport;
            $DB->loadFromDbRow($r, $res, $row);
            $r->parseStackTrace($r->raw_stacktrace);
            $ret[] = $r;
        }
        
        return $ret;
    }
    
    function getReport($id)
    {
        global $DB;
        
        $ret = array();
        if (!$res = $DB->query(kl_str_sql("select * from reports where id=!i", $id)))
        {
            return null;
        }
        
        if ($row = $DB->fetchRow($res))
        {
            $r = new CrashReport;
            $DB->loadFromDbRow($r, $res, $row);
            $r->parseStackTrace($r->raw_stacktrace);
            return $r;
        }
        
        return null;
    }

    function delete()
    {
        global $DB;
        $DB->query(kl_str_sql("delete from reports where id=!i", $this->id));
    }
    
    function save()
    {
        global $DB;
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
        if ($res = $DB->query($q))
        {
            return true;
        }
        else
        {
            return false;
        }
    }
    
    function parseStackTrace($stacktrace)
    {
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
}