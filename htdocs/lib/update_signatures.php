#!/usr/bin/php
<?php

define("SITE_ROOT", realpath(dirname(__file__) . '/..'));
require_once SITE_ROOT . "/lib/init.php";

$q = "select * from reports where signature_id is null or signature_id=0";
//$q = "select * from reports";
DBH::$db->begin();
//DBH::$db->query("delete from signature");
//DBH::$db->query("ALTER TABLE signature AUTO_INCREMENT = 1");
$res = DBH::$db->query($q);
$sig = array();
while ($row = DBH::$db->fetchRow($res))
{
    $r = new CrashReport;
    DBH::$db->loadFromDbRow($r, $res, $row);
    $r->parseStackTrace();
    $r->updateSignature();
    print "Updating signature for {$r->id}\n";
    $r->saveSignature();
}
DBH::$db->commit();
Memc::flush();