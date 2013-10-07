<?php

define("SITE_ROOT", realpath(dirname(__file__)));
require_once SITE_ROOT . "/lib/init.php";
$S->requireUser();

$total = CrashReport::getTotal();
$reports = CrashReport::getReports();
Layout::header();
?>

<p>Reports <strong><?php echo $total ?></strong></p>

<table width="100%" class="jtable">
    <tr>
        <th>ID</th>
        <th>Version</th>
        <th>Operating System</th>
        <th>GPU</th>
        <th>Grid (region)</th>
    </tr>
<?php for ($i=0; $i<count($reports); $i++): ?>
    <tr class="rowhighlight hand" onclick="window.location.href='report_detail?id=<?php echo $reports[$i]->id ?>'">
        <td><?php echo (int)$reports[$i]->id ?></td>
        <td><?php echo htmlspecialchars($reports[$i]->client_channel . " " . $reports[$i]->client_version) ?></td>
        <td><?php echo htmlspecialchars($reports[$i]->os) ?></td>
        <td><?php echo htmlspecialchars($reports[$i]->gpu) ?></td>
        <td><?php echo htmlspecialchars($reports[$i]->grid . " (" . $reports[$i]->region . ")") ?></td>
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
