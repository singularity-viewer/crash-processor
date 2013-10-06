<?php
define("SITE_ROOT", realpath(dirname(__file__)));
require_once SITE_ROOT . "/lib/init.php";
$S->requireUser();

$r = ReportParser::parse((int)$_GET["report_id"]);

$miniDump = $r["Minidump"];
if (!$miniDump || !($miniDump->getData()))
{
    Layout::header();
    print "<h2>Minidump Download</h2><p>No such report</p>";
    Layout::footer();
    return;    
}

header("Content-Type: application/octet-stream");
http::sendDownload("singularity" . ((int)$_GET["report_id"]) . ".dmp", $miniDump->getData());