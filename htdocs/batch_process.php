<?php

define("SITE_ROOT", realpath(dirname(__file__)));
require_once SITE_ROOT . "/lib/init.php";

set_time_limit(0);

if (PHP_SAPI != "cli")
{
    header("Content-Type: text/plain");
}

function rrmdir($dir)
{
    foreach(glob($dir . '/*') as $file)
    {
        if(is_dir($file))
            rrmdir($file);
        else
            unlink($file);
    }
    rmdir($dir);
}

$reports = ReportParser::getUnprocessedIDs();

print "Working dir set to " . ReportParser::getWorkPath() . "\n";
mkdir(ReportParser::getWorkPath());
chdir(ReportParser::getWorkPath());

$nr = 0;

foreach($reports as $id)
{
    print "Processing report {$id} \n";
    $r = ReportParser::parse($id);
    $miniDump = $r["Minidump"];
    if (!$miniDump || !($miniDump->getData()))
    {
        ReportParser::setProcessed($id, 1);
        continue;
    }

    if (!($version = $r["clientVersion"])|| !($chan = $r["clientChannel"]))
    {
        ReportParser::setProcessed($id, 1);
        continue;
    }

    $stacktrace = ReportParser::getStackTrace("$chan-$version", $r["Minidump"]);
    $crash = new CrashReport();
    $crash->init($id, $r, $stacktrace);
    if ($crash->save())
    {
        $crash->updateSignature();
        $crash->saveSignature();
        $nr++;
        ReportParser::setProcessed($id, 1);
    }
}

rrmdir(ReportParser::getWorkPath());

if ($nr)
{
    Memc::flush();
    IRCNotify::send("#SingularityViewer", "[CrashProcessor] $nr new reports. http://crash.singularityviewer.org/crashes.php ");
}
