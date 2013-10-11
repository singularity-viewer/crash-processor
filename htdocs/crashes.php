<?php

define("SITE_ROOT", realpath(dirname(__file__)));
require_once SITE_ROOT . "/lib/init.php";
$S->requireUser();

function lk($id, $txt)
{
    echo '<a href="'. URL_ROOT . '/report_detail.php?id=' . $id . '">' . htmlentities($txt) . '</a>';
}

$filter = new SearchFilter();
$total = CrashReport::getTotal($filter);
$reports = CrashReport::getReports($filter);
Layout::header();

$filter->render();
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
    <tr class="rowhighlight">
        <td><?php lk($reports[$i]->id, $reports[$i]->id) ?></td>
        <td><?php lk($reports[$i]->id, $reports[$i]->client_channel . " " . $reports[$i]->client_version) ?></td>
        <td><?php lk($reports[$i]->id, $reports[$i]->os) ?></td>
        <td><?php lk($reports[$i]->id, $reports[$i]->gpu) ?></td>
        <td><?php lk($reports[$i]->id, $reports[$i]->grid . " (" . $reports[$i]->region . ")") ?></td>
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
