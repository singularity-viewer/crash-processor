<?php

define("NO_SESSION", 1);
define("SITE_ROOT", realpath(dirname(__file__)));
require_once SITE_ROOT . "/lib/init.php";

$blacklist = array("1.8.3.5282");

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
        ReportParser::deleteRaw($id);
        continue;
    }

    if (!($version = $r["clientVersion"])|| !($chan = $r["clientChannel"]))
    {
        ReportParser::deleteRaw($id);
        continue;
    }

    if (in_array($version, $blacklist))
    {
        ReportParser::deleteRaw($id);
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
    $rp = $nr != 1 ? "reports" : "report";
    //IRCNotify::send("#SingularityViewer", "[CrashProcessor] $nr new $rp. http://crash.singularityviewer.org/");
}

Session::GC();
