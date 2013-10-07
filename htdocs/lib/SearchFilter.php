<?php

class SearchFilter
{
    public $os;
    public $chan;
    public $version;
    public $grid;
    public $sort_by;
    public $sort_order;
    public static $sort_keys = array("date", "os", "version", "grid");
    public static $sort_orders = array("asc", "desc");
    public $limit = 100;
    public $offset = 0;
    public $page = 0;
    
    function SearchFilter()
    {
        if (in_array($_GET["sort_by"], self::$sort_keys))
        {
            $this->sort_by = $_GET["sort_by"];
        }

        if (in_array($_GET["sort_order"], self::$sort_orders))
        {
            $this->sort_by = $_GET["sort_order"];
        }
        
        if (strlen($_GET["os"]))
        {
            $this->os = $_GET["os"];
        }

        if (strlen($_GET["chan"]))
        {
            $this->chan = $_GET["chan"];
        }

        if (strlen($_GET["version"]))
        {
            $this->version = $_GET["version"];
        }

        if (strlen($_GET["page"]))
        {
            $this->page = $_GET["page"];
        }

        if (strlen($_GET["grid"]))
        {
            $this->grid = $_GET["grid"];
        }
    }
    
    function getWhere()
    {
        $cond = array();
        if ($this->os) $cond[] = kl_str_sql("os_type=!s", $this->os);
        if ($this->version) $cond[] = kl_str_sql("client_version=!s", $this->version);
        if ($this->chan) $cond[] = kl_str_sql("client_channel=!s", $this->chan);
        if ($this->grid) $cond[] = kl_str_sql("grid=!s", $this->grid);
        if (!count($cond)) return "";
        return "where " . implode(" and ", $cond);
    }
    
    function getVersions()
    {
        global $DB;
        
        $ret = array();
        $where = $this->chan ? kl_str_sql("where client_channel=!s", $this->chan) : '';
        $q = "select distinct client_version from reports $where order by client_version desc";
        if (!$res = $DB->query($q))
        {
            return $ret;
        }
        
        while ($row = $DB->fetchRow($res))
        {
            $ret[] = $row["client_version"];
        }
        
        return $ret;
    }
    
    function getGrids()
    {
        global $DB;
        
        $ret = array();
        $q = "select distinct grid from reports order by grid asc";
        if (!$res = $DB->query($q))
        {
            return $ret;
        }
        
        while ($row = $DB->fetchRow($res))
        {
            $ret[] = $row["grid"];
        }
        
        return $ret;
    }

    function render()
    {
        $ver = $this->getVersions();
        $grids = $this->getGrids();
?>
<script>
  $(function() {
    $( ".radio" )
    .buttonset()
    .click(function() {
       $(this).closest("form").submit();
    });
  });

</script>

<form method="get">
<div style="display: inline-block;" class="ui-widget ui-corner-all ui-widget-content">
    <div class="ui-widget-header" style="padding: 5px">Filter</div>

    <div style="display: inline-block;">
        <div class="radio" style="padding: 10px">
            Channel<br />
            <input type="radio" id="chan1" name="chan" value="" <?php echo !$this->chan ? 'checked="checked"' : '' ?>/><label for="chan1">All</label>
            <input type="radio" id="chan2" name="chan" value="Singularity" <?php echo $this->chan == "Singularity" ? 'checked="checked"' : '' ?>/><label for="chan2">Singularity</label>
            <input type="radio" id="chan3" name="chan" value="SingularityAlpha" <?php echo $this->chan == "SingularityAlpha" ? 'checked="checked"' : '' ?>/><label for="chan3">SingularityAlpha</label>
        </div>
    </div>
    
    <div style="display: inline-block;" style="padding: 10px">
        Version<br/>
        <select class="ui-widget-content" name="version" onchange="this.form.submit();" style="width: 100px;"> 
            <option value="" <?php echo !$this->version ? 'selected="selected"' : '' ?>>All</option>
<?php
for($i = 0; $i < count($ver); $i++)
{
    $sel = $this->version == $ver[$i] ? ' selected="selected"' : '';
    print '<option value="' . htmlentities($ver[$i]) . '"' . $sel . '>' . htmlentities($ver[$i]). '</option>';
}
?>
        </select>
    </div>

    <div style="display: inline-block;">
        <div class="radio" style="padding: 10px">
            Operating system<br />
            <input type="radio" id="os_all" name="os" value="" <?php echo !$this->os ? 'checked="checked"' : '' ?>/><label for="os_all">All</label>
            <input type="radio" id="os_windows" name="os" value="Windows NT" <?php echo $this->os == "Windows NT" ? 'checked="checked"' : '' ?>/><label for="os_windows">Windows</label>
            <input type="radio" id="os_linux" name="os" value="Linux" <?php echo $this->os == "Linux" ? 'checked="checked"' : '' ?>/><label for="os_linux">Linux</label>
            <input type="radio" id="os_mac" name="os" value="Mac OS X" <?php echo $this->os == "Mac OS X" ? 'checked="checked"' : '' ?> /><label for="os_mac">Mac</label>
        </div>
    </div>

    <div style="display: inline-block;">
        Grid<br/>
        <select class="ui-widget-content" name="grid" onchange="this.form.submit();" style="width: 200px; margin: 0px 20px 0 0; "> 
            <option value="" <?php echo !$this->grid ? 'selected="selected"' : '' ?>>All</option>
<?php
for($i = 0; $i < count($grids); $i++)
{
    $sel = $this->grid == $grids[$i] ? ' selected="selected"' : '';
    print '<option value="' . htmlentities($grids[$i]) . '"' . $sel . '>' . htmlentities($grids[$i]). '</option>';
}
?>
        </select>
    </div>

</div>
</form>
        
<?php
    }
    
}