<?php

define("NO_SESSION", 1);
define("SITE_ROOT", realpath(dirname(__file__)));
require_once SITE_ROOT . "/lib/init.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST")
{
    http::notAllowed();
}
$report_id = 0;
$report = file_get_contents("php://input");
if (strlen($report))
{
    $query = kl_str_sql('insert into raw_reports(raw_data) values (!s)', $report);
    if ($res = $DB->query($query))
    {
	$report_id = $DB->insertID();
    }
}


header("Content-Type: application/llsd+xml");
print '<?xml version="1.0" ?><llsd><map><key>message</key><string>Report saved with report_id=' . $report_id . '</string><key>success</key><boolean>true</boolean></map></llsd>';
/*
 * Local variables:
 * tab-width: 4
 * c-basic-offset: 4
 * End:
 * vim600: noet sw=4 ts=4 fdm=marker
 * vim<600: noet sw=4 ts=4
 */
