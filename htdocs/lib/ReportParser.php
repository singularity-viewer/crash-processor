<?php
/* ReportParser.php
* Parses incoming raw crash reports
*
* Description
* @package Singularity Crash Processor
* @author Latif Khalifa <latifer@streamgrid.net>
* @copyright Copyright &copy; 2012, Latif Khalifa
* 
* Permission is hereby granted, free of charge, to any person obtaining
* a copy of this software and associated documentation files
* (the "Software"), to deal in the Software without restriction, including
* without limitation the rights to use, copy, modify, merge, publish,
* distribute, sublicense, and/or sell copies of the Software, and to permit
* persons to whom the Software is furnished to do so, subject to the
* following conditions:
*
* - The above copyright notice and this permission notice shall be included
* in all copies or substantial portions of the Software.
*
* THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
* EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
* MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.
* IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM,
* DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR
* OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE
* OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
* 
*/

require_once SITE_ROOT.'/lib/llsd_classes.php';
require_once SITE_ROOT.'/lib/llsd_decode.php';

class ReportParser
{
    static $extracted = array();
    
    static function parse($id)
    {
        $q = kl_str_sql("select * from raw_reports where report_id=!i", $id);
        if (!$res = DBH::$db->query($q) OR !$row = DBH::$db->fetchRow($res))
        {
            return array();
        }
        $data = new stdClass;
        DBH::$db->loadFromDbRow($data, $res, $row);
        $data->report = llsd_decode($data->raw_data);
        $data->report["reported"] = $data->reported;
        unset($data->raw_data);
        
        if ($client = $data->report["DebugLog"]["ClientInfo"])
        {
            //var_dump($client);
            $data->report["clientVersion"] = $client["MajorVersion"] . "." . $client["MinorVersion"] . "." . $client["PatchVersion"] . "." .$client["BuildVersion"];
            $data->report["clientChannel"] = str_replace(" ", "", $client["Name"]);
            $data->report["clientArch"] = $client["Architecture"];
        }
        
        // $data->report["raw"] = $row;
        return $data->report;
    }
    
    function setProcessed($id, $status)
    {
        $ret = array();
        $q = kl_str_sql("update raw_reports set processed=!i where report_id=!i", $status, $id);
        
        if ($res = DBH::$db->query($q))
        {
            return true;
        }
        else
        {
            return false;
        }
    }
    
    function deleteRaw($id)
    {
        $q = kl_str_sql("delete from raw_reports where report_id=!i", $id);
        
        if ($res = DBH::$db->query($q))
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    function getUnprocessedIDs()
    {
        $ret = array();
        $q = kl_str_sql("select report_id from raw_reports where processed=!i", 0);
        
        if (!$res = DBH::$db->query($q))
        {
            return $ret;
        }
        
        while ($row = DBH::$db->fetchRow($res))
        {
            $ret[] = (int)$row["report_id"];
        }
        
        return $ret;
    }
    
    function getWorkPath()
    {
        static $p = "";
        
        if ($p) return $p;
        
        return $p = sys_get_temp_dir() . "/extract-syms-" . (string)getmypid();
    }
    
    function getStackTrace($prefix, $dump)
    {
        if (!$dump || !($data = $dump->getData())) return;
        file_put_contents(self::getWorkPath() . "/working.dmp", $data);
        
        $match = SITE_ROOT . "/../incoming_symbols/$prefix-symbols-*.tar.bz2";

        foreach(glob($match) as $file)
        {
            if (!in_array($file, self::$extracted))
            {
                self::$extracted[] = $file;
                print "Unpacking $file\n";
                shell_exec("tar xjf " . escapeshellcmd($file));
            }
        }
        
        $cmd = "minidump_stackwalk -m " . self::getWorkPath() . "/working.dmp "  . self::getWorkPath();
        print "Executing: $cmd\n";
        return shell_exec($cmd);
    }
}