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


Layout::header();
?>

<h2>Report Details</h2>

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
    
</table>

<h3>Stack</h3>

<?php for ($threadID = 0; $threadID < count($report->threads); $threadID++): ?>
<table width="99%">
    <tr>
        <th width="20%">Thread <?php echo htmlentities($threadID . ($threadID == $report->crash_thread ? " (crashed)" : "")) ?></th>
        <th>Function</th>
    </tr>  

<?php for ($frameID = 0; $frameID < count($report->threads[$threadID]->frames); $frameID++): $f = $report->threads[$threadID]->frames[$frameID]; ?>
    <tr class="rowhighlight">
        <td><?php echo htmlentities($f->module) ?></td>
        <td><?php echo CrashReport::htmlFrame($f) ?></td>
    </tr>  
<?php endfor ?>

</table>
<br/><br/>
<?php endfor ?>


<h3>Loaded Modules</h3>
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
