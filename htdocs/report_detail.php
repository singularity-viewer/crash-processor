<?php

define("SITE_ROOT", realpath(dirname(__file__)));
require_once SITE_ROOT . "/lib/init.php";
$S->requireUser();

$report = CrashReport::getReport((int)$_GET["id"]);
if (!$report)
{
    Layout::header();
    print "<h2>Report Details</h2><p>No such report</p>";
    Layout::footer();
    return;    
}


$full = ReportParser::parse($report->id);
//print_r (array_keys($full));

Layout::header();
?>
<script>
    $(function() {
        $( "#tabs" ).tabs();
        $("div.ui-tabs-panel").css('padding','0px');
    });
</script>

<div id="tabs">
<ul>
    <li><a href="#tabs-0">Stack</a></li>
    <li><a href="#tabs-1">Details</a></li>
    <li><a href="#tabs-2">Modules</a></li>
<?php if (strlen($full["StatsLog"])): ?>
    <li><a href="#tabs-3">Stats</a></li>
<?php endif ?>
<?php if (strlen($full["SecondLifeLog"])): ?>
    <li><a href="#tabs-4">Log</a></li>
<?php endif ?>
<?php if (strlen($full["SettingsXml"])): ?>
    <li><a href="#tabs-5">Settings</a></li>
<?php endif ?>
    <li><a href="#tabs-6">Comments</a></li>
</ul>

<!-- Stacks tab -->
<div id="tabs-0">
<script>
$(function() {
    $( "#accordion" )
        .accordion({ collapsible: true, animate: false, heightStyle: "content", active: <?php echo $report->crash_thread ?> });
    $("div.ui-accordion-content").css('padding','0px');
 });
</script>
<div id="accordion">
 
<?php for ($threadID = 0; $threadID < count($report->threads); $threadID++): ?>
<h3>Thread <?php echo htmlentities($threadID . ($threadID == $report->crash_thread ? " (crashed)" : "")) ?></h3>
<div>
<table width="100%">
<?php for ($frameID = 0; $frameID < count($report->threads[$threadID]->frames); $frameID++): $f = $report->threads[$threadID]->frames[$frameID]; ?>
    <tr>
        <td width="5%" style="text-align: right;"><?php echo $frameID ?></td>
        <td width="20%"><?php echo htmlentities($f->module) ?></td>
        <td width="75%"><?php echo CrashReport::htmlFrame($f, $report->client_channel, $report->client_version) ?></td>
    </tr>
<?php endfor ?>
</table>
</div>
<?php endfor ?>
</div>
</div>
<!-- Stacks tab -->

<!-- Details tab -->
<div id="tabs-1">
<table>
    <tr>
        <th>ID</th>
        <td><?php echo (int)$report->id ?></td>
    </tr>
    <tr>
        <th>Reported</th>
        <td><?php echo date("r", (int)$report->reported) ?></td>
    </tr>
    <tr>
        <th>Channel</th>
        <td><?php echo htmlentities($report->client_channel) ?></td>
    </tr>
    <tr>
        <th>Version</th>
        <td><?php echo htmlentities($report->client_version) ?></td>
    </tr>
    <tr>
        <th>OS Type</th>
        <td><?php echo htmlentities($report->os_type) ?></td>
    </tr>
    <tr>
        <th>OS String</th>
        <td><?php echo htmlentities($report->os) ?></td>
    </tr>
    <tr>
        <th>OS Version</th>
        <td><?php echo htmlentities($report->os_version) ?></td>
    </tr>
    <tr>
        <th>Graphics Card</th>
        <td><?php echo htmlentities($report->gpu) ?></td>
    </tr>
    <tr>
        <th>OpenGL Version</th>
        <td><?php echo htmlentities($report->opengl_version) ?></td>
    </tr>
    <tr>
        <th>Grid</th>
        <td><?php echo htmlentities($report->grid) ?></td>
    </tr>
    <tr>
        <th>Region</th>
        <td><?php echo htmlentities($report->region) ?></td>
    </tr>
    <tr>
        <th>Crash Reason</th>
        <td><?php echo htmlentities($report->crash_reason . " at " . $report->crash_address . " thread " . $report->crash_thread) ?></td>
    </tr>
    <tr>
        <th>Crash Type</th>
        <td><a href="crashes.php?signature_id=<?php echo $report->signature_id ?>"><?php echo $report->signature_id ?></a></td>
    </tr>
    <tr>
        <th>Minidump</th>
        <td><a href="download.php/singularity<?php echo (int)$report->id ?>.dmp?report_id=<?php echo (int)$report->id ?>">Download</a></td>
    </tr>
</table>
</div>
<!-- Details tab -->


<!-- Modules tab -->
<div class="jtable" id="tabs-2">
<table>
    <tr>
        <th>Name</th>
        <th>Version</th>
    </tr>
<?php for ($i = 0; $i < count($report->modules); $i++): ?>
    <tr class="rowhighlight">
        <td><?php echo htmlentities($report->modules[$i]->name) ?></td>
        <td><?php echo htmlentities($report->modules[$i]->version) ?></td>
    </tr>
<?php endfor ?>
</table>
</div>
<!-- Modules tab -->


<!-- Stats tab -->
<?php if (strlen($full["StatsLog"])): ?>
<div id="tabs-3">
<pre>
<?php echo htmlentities($full["StatsLog"]) ?>
</pre>
</div>
<?php endif ?>
<!-- Stats tab -->

<!-- Log tab -->
<?php if (strlen($full["SecondLifeLog"])): ?>
<div id="tabs-4">
<pre>
<?php echo htmlentities($full["SecondLifeLog"]) ?>
</pre>
</div>
<?php endif ?>
<!-- Log tab -->

<!-- Settings tab -->
<?php if (strlen($full["SettingsXml"])): ?>
<div id="tabs-5">
<pre>
<?php echo htmlentities($full["SettingsXml"]) ?>
</pre>
</div>
<?php endif ?>
<!-- Settings tab -->


<!-- comments tab -->
<div id="tabs-6">
</div>
<!-- comments tab -->

<div id="tabs_footer" style="max-width: 800px; padding: 10px;">
    <?php Comments::renderCommentPanel($report->signature_id) ?>
</div>

</div> <!-- end of tabs panel -->

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
