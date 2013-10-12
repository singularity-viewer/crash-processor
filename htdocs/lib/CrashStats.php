<?php

class CrashStats
{
    var $filter;
    
    function __construct($filter = null)
    {
        $this->filter = $filter;
    }
    
    function setFilter($filter)
    {
        $this->filter = $filter;
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