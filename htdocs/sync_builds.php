<?php

define("NO_SESSION", 1);
define("SITE_ROOT", realpath(dirname(__file__)));
require_once SITE_ROOT . "/lib/init.php";

set_time_limit(0);

if (PHP_SAPI != "cli")
{
    header("Content-Type: text/plain");
}

if (!$remote_map = json_decode(file_get_contents("http://alpha.singularityviewer.org/alpha/builds_map.php"))) return;

$existing = CrashReport::getBuildsMap();

$nr = 0;
foreach ($remote_map as $build)
{
    if (!$existing[$build->chan][$build->version])
    {
        $nr++;
        $q = kl_str_sql("insert into builds (build_nr, chan, version, hash, modified) values (!i, !s, !s, !s, !s)",
                        $build->build_nr,
                        $build->chan,
                        $build->version,
                        $build->hash,
                        $build->modified);
        DBH::$db->query($q);
    }
}
if ($nr)
{
    print "Added $nr builds\n";
    Memc::flush();
}
