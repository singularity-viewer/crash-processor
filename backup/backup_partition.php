#!/usr/bin/php
<?php

define("NO_SESSION", 1);
define("SITE_ROOT", realpath(dirname(__file__) . "/../htdocs"));
require_once SITE_ROOT . "/lib/init.php";

class PartitionArchiver
{
    var $keep = 10000;
    var $db_name;
    var $db_user;
    var $db_pass;
    var $db_host;
    var $part_delimiter;
    var $prefix = "singularity_part_";
    var $suffix = ".sql.7z";
    var $part_id;
    var $filename;
    var $cmd;
    
    function PartitionArchiver($name, $user, $pass, $host)
    {
        $this->db_name = $name;
        $this->db_user = $user;
        $this->db_pass = $pass;
        $this->db_host = $host;
        
        $this->cmd = "mysqldump"
            . " -u" . escapeshellarg($this->db_user)
            . " -p" . escapeshellarg($this->db_pass)
            . ($this->db_host ? " -h" . escapeshellarg($this->db_host) : "")
            . " " . escapeshellarg($this->db_name);
    }
    
    function setFilename()
    {
        $files = glob($this->prefix . "*" . $this->suffix);
        if (count($files) === 0)
        {
            $this->part_id = 0;
        }
        else
        {
            $max = 0;
            for ($i =0 ; $i < count($files); $i++)
            {
                if (preg_match("%" . preg_quote($this->prefix) . '(\d+)' . preg_quote($this->suffix) . "%", $files[$i], $m))
                {
                    $nr = (int)$m[1];
                    if ($max < $nr) $max = $nr;
                }
            }
            
            $this->part_id = $max;
        }
        
        ++$this->part_id;
        
        $this->filename = $this->prefix . sprintf("%05d", $this->part_id) . $this->suffix;
    }
    
    function tableExists($table)
    {
        $q = kl_str_sql("select count(1) as nr from information_schema.tables where table_schema=!s and table_name=!s", $this->db_name, $table);

        if (!$res = DBH::$db->query($q) OR !$row = DBH::$db->fetchRow($res))
        {
            return false;
        }

        $id = $row["nr"];
        return $id == "1";
    }
    
    function getDelimiterID()
    {
        $q = "select id from reports order by id desc limit 1 offset " . $this->keep;

        if (!$res = DBH::$db->query($q) OR !$row = DBH::$db->fetchRow($res))
        {
            return false;
        }

        $id = $row["id"];
        
        return $id;
        
    }
    
    function backupToS3()
    {
        system("s3cmd --rr sync --recursive --delete-removed --exclude '.git/*' --exclude 'logs/*'  " . escapeshellarg(realpath(dirname(__file__) . "/../")) . " s3://singularity-backup/crash-site/");
    }
   
    function archivePart()
    {
        $retval = 0;
        system($this->cmd . ' history_reports history_raw_reports | 7z a -si ' . escapeshellarg($this->filename) . ' >/dev/null 2>&1', $retval);
        if ($retval !== 0) return false;
        if (!$res = DBH::$db->query("drop table history_reports")) return false;
        if (!$res = DBH::$db->query("drop table history_raw_reports")) return false;
    }
    
    function partition()
    {
        chdir(realpath(dirname(__file__)));
        $this->part_delimiter = $this->getDelimiterID();
        if (!$this->part_delimiter) return;
        $this->setFileName();
        
        if ($this->tableExists("history_reports") || $this->tableExists("history_raw_reports"))
        {
            if (!$this->archivePart()) return false;
        }
    }
}

$archiver = new PartitionArchiver($DB_NAME, $DB_USER, $DB_PASS, $DB_HOST);
$archiver->partition();
var_dump($archiver);