<?php

class CrashStats
{
    var $filter;
    
    function __construct($filter = null)
    {
        if ($filter)
        {
            $this->filter = $filter;
        }
        else
        {
            $this->filter = new SearchFilter;
        }
    }
    
    function setFilter($filter)
    {
        $this->filter = $filter;
    }

    function getNumTopCrashers()
    {
        $ret = 0;
        $where = $this->filter->getWhere();
        $q = "select count(1) as total from (select s.id from reports r join signature s on r.signature_id = s.id $where group by signature_id) as reports";
        if (false !== $cached = Memc::getq($q)) return $cached;

        if (!$res = DBH::$db->query($q))
        {
            return $ret;
        }
        
        if ($row = DBH::$db->fetchRow($res))
        {
            $ret = (int)$row["total"];
        }
        
        Memc::setq($q, $ret);
        return $ret;
    }
    
    function getTopCrashers()
    {
        $ret = array();
        $where = $this->filter->getWhere();
        $q = "select count(r.id) as nr, s.id as signature_id, s.signature as signature_text, s.has_comments from reports r join signature s on r.signature_id = s.id $where group by signature_id order by nr desc";
        $q .= kl_str_sql(" limit !i offset !i", $this->filter->limit, $this->filter->offset);
        if (false !== $cached = Memc::getq($q)) return $cached;

        if (!$res = DBH::$db->query($q))
        {
            return $ret;
        }
        
        while ($row = DBH::$db->fetchRow($res))
        {
            $r = new stdClass;
            DBH::$db->loadFromDbRow($r, $res, $row);
            $ret[] = $r;
        }
        
        Memc::setq($q, $ret);
        return $ret;
    }
    
    static function getSignature($id)
    {
        $ret = new stdClass;
        $q = kl_str_sql("select * from signature where id=!i", $id);

        if (!$res = DBH::$db->query($q))
        {
            return false;
        }
        
        if ($row = DBH::$db->fetchRow($res))
        {
            DBH::$db->loadFromDbRow($ret, $res, $row);
            return $ret;
        }
        
        return false;
    }
    
    static function renderSignature($id)
    {
        if (!$r = self::getSignature($id)) return "";

        $parts = explode("|", $r->signature);
        $txt = "";
        if ($parts[1]) $txt .= preg_replace("/((::|&lt;|&gt;|,|\\(|\\)))/", "<wbr/>\\1<wbr/>", htmlentities($parts[1]));
        if ($txt) $txt .= "<br/><br/>";
        if ($parts[2]) $txt .= preg_replace("/((::|&lt;|&gt;|,|\\(|\\)))/", "<wbr/>\\1<wbr/>", htmlentities($parts[2]));
        
        $ret = "<p>Module: " . $parts[0];
        if ($txt) $ret .= "<br/><br/>" . $txt . "</p>";
        
        return $ret;
    }
    
    function getGPUStats()
    {
        $ret = array();
        $where = $this->filter->getWhere();
        if ($where)
        {
            $where .= " and ";
        }
        else
        {
            $where = "where ";
        }
        $where .= "gpu is not null";
        $q = "select count(id) as nr, gpu from reports $where group by gpu order by nr desc";
        $q .= kl_str_sql(" limit !i", $this->filter->limit);
        if (false !== $cached = Memc::getq($q)) return $cached;
        
        if (!$res = DBH::$db->query($q)) return $ret;
        
        while ($row = DBH::$db->fetchRow($res))
        {
            $o = new stdClass;
            DBH::$db->loadFromDbRow($o, $res, $row);
            $ret[] = $o;
        }
        
        Memc::setq($q, $ret);
        return $ret;
    }    

    function getOSStats()
    {
        $ret = array();
        $where = $this->filter->getWhere();
        if ($where)
        {
            $where .= " and ";
        }
        else
        {
            $where = "where ";
        }
        $where .= "os_type is not null";
        $q = "select count(id) as nr, os_type from reports $where group by os_type order by nr desc";
        $q .= kl_str_sql(" limit !i", $this->filter->limit);
        if (false !== $cached = Memc::getq($q)) return $cached;
        
        if (!$res = DBH::$db->query($q)) return $ret;
        
        while ($row = DBH::$db->fetchRow($res))
        {
            $o = new stdClass;
            DBH::$db->loadFromDbRow($o, $res, $row);
            $ret[] = $o;
        }
        
        Memc::setq($q, $ret);
        return $ret;
    }    

    function getRegionStats()
    {
        $ret = array();
        $where = $this->filter->getWhere();
        if ($where)
        {
            $where .= " and ";
        }
        else
        {
            $where = "where ";
        }
        $where .= "region is not null and grid is not null";
        $q = "select count(id) as nr, region, grid from reports $where group by region, grid order by nr desc";
        $q .= kl_str_sql(" limit !i", $this->filter->limit);
        if (false !== $cached = Memc::getq($q)) return $cached;
        
        if (!$res = DBH::$db->query($q)) return $ret;
        
        while ($row = DBH::$db->fetchRow($res))
        {
            $o = new stdClass;
            DBH::$db->loadFromDbRow($o, $res, $row);
            $ret[] = $o;
        }
        
        Memc::setq($q, $ret);
        return $ret;
    }
}