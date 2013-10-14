<?php

define("SITE_ROOT", realpath(dirname(__file__)));
require_once SITE_ROOT . "/lib/init.php";
$S->requireUser();

$stats = new CrashStats($filter);

if (false === $r = $stats->getSignature((int)$_GET["signature_id"]))
{
    print "<p>No such signature</p>";
}

$parts = explode("|", $r->signature);
$txt = "";
if ($parts[1]) $txt .= preg_replace("/((::|&lt;|&gt;|,|\\(|\\)))/", "<wbr/>\\1<wbr/>", htmlentities($parts[1]));
if ($txt) $txt .= "<br/><br/>";
if ($parts[2]) $txt .= preg_replace("/((::|&lt;|&gt;|,|\\(|\\)))/", "<wbr/>\\1<wbr/>", htmlentities($parts[2]));

print "<p>Module: " . $parts[0];
if ($txt) print "<br/><br/>" . $txt . "</p>";