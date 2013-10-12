<?php

define("SITE_ROOT", realpath(dirname(__file__)));
require_once SITE_ROOT . "/lib/init.php";
$S->requireUser();

$filter = new SearchFilter();
$stats = new CrashStats($filter);

Layout::header();
$filter->render();
?>
<script>
    $(function() {
        $( "#tabs" ).tabs();
        $("div.ui-tabs-panel").css('padding','0px');
    });
</script>
<br/>
<div id="tabs">
    <ul>
        <li><a href="#tab-1">Top Crashers</a></li>
        <li><a href="#tab-2">Graphics Cards</a></li>
        <li><a href="#tab-3">Operating Systems</a></li>
        <li><a href="#tab-4">Regions</a></li>
    </ul>


    <!-- top crashers tab -->
    <div id="tab-1">
        <p>Working on it</p>
    </div>
    <!-- /top crashers tab -->


    <!-- gpu tab -->
    <div id="tab-2">
<?php
    function rl_g($r)
    {
        global $filter;
        return URL_ROOT . "/crashes.php?" . $filter->getURLArgs() . "&gpu=" . urlencode($r->gpu);
    }
    $gpus = $stats->getGPUStats();
    $c = count($gpus);
    if ($c) :
?>
        <table class="jtable noborder">
            <tr>
                <th>Nr. reports</th>
                <th>GPU Identifier</th>
            </tr>
<?php foreach($gpus as $r): ?>
            <tr class="rowhighlight">
                <td style="text-align: right"><a href="<?php echo rl_g($r) ?>"><?php echo htmlentities($r->nr) ?></a></td>
                <td><a href="<?php echo rl_g($r) ?>"><?php echo htmlentities($r->gpu) ?></a></td>
            </tr>
<?php endforeach ?>
        </table>

<?php endif ?>
    </div>
    <!-- /gpu tab -->


    <!-- os tab -->
    <div id="tab-3">
<?php
    function rl_o($r)
    {
        global $filter;
        return URL_ROOT . "/crashes.php?" . $filter->getURLArgs() . "&os=" . urlencode($r->os_type);
    }
    $oses = $stats->getOSStats();
    $c = count($oses);
    if ($c) :
?>
        <table class="jtable noborder">
            <tr>
                <th>Nr. reports</th>
                <th>Operating System Type</th>
            </tr>
<?php foreach($oses as $r): ?>
            <tr class="rowhighlight">
                <td style="text-align: right"><a href="<?php echo rl_o($r) ?>"><?php echo htmlentities($r->nr) ?></a></td>
                <td><a href="<?php echo rl_o($r) ?>"><?php echo htmlentities($r->os_type) ?></a></td>
            </tr>
<?php endforeach ?>
        </table>

<?php endif ?>
    </div>
    <!-- /os tab -->


    <!-- regions tab -->
    <div id="tab-4">
<?php
    function rl($r)
    {
        global $filter;
        return URL_ROOT . "/crashes.php?" . $filter->getURLArgs() . "&region=" . urlencode($r->region) . "&grid=" . urlencode($r->grid);
    }
    $regions = $stats->getRegionStats();
    $c = count($regions);
    if ($c) :
?>
        <table class="jtable noborder">
            <tr>
                <th>Nr. reports</th>
                <th>Region</th>
                <th>Grid</th>
            </tr>
<?php foreach($regions as $r): ?>
            <tr class="rowhighlight">
                <td style="text-align: right"><a href="<?php echo rl($r) ?>"><?php echo htmlentities($r->nr) ?></a></td>
                <td><a href="<?php echo rl($r) ?>"><?php echo htmlentities($r->region) ?></a></td>
                <td><a href="<?php echo rl($r) ?>"><?php echo htmlentities($r->grid) ?></a></td>
            </tr>
<?php endforeach ?>
        </table>

<?php endif ?>
    </div>
    <!-- /regions tab -->
</div>

<?php
Layout::footer();


/*
 * Local variables:
 * tab-width: 4
 * c-basic-offset: 4
 * End:
 * vim600: noet sw=4 ts=4 fdm=marker
 * vim<600: noet sw=4 ts=4
 */
