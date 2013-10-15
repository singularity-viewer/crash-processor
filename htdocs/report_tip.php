<?php

define("SITE_ROOT", realpath(dirname(__file__)));
require_once SITE_ROOT . "/lib/init.php";
$S->requireUser();

print CrashStats::renderSignature((int)$_GET["signature_id"]);