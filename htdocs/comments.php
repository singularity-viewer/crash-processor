<?php

define("SITE_ROOT", realpath(dirname(__file__)));
require_once SITE_ROOT . "/lib/init.php";
$S->requireUser();

$ajax = (int)$_REQUEST["ajax"];
$signature_id = (int)$_REQUEST["signature_id"];
$stats = new CrashStats($filter);

if (false === $r = $stats->getSignature($signature_id))
{
    print "<p>No such signature</p>";
    return;
}

if ($_POST["action"] == "add_comment")
{
    if (trim($_POST["comment"]))
    {
        Comments::addSignatureComment($signature_id, $_POST["comment"]);
    }
    Comments::renderComments($signature_id);
    return;
}
if ($_POST["action"] == "del_comment")
{
    Comments::delSignatureComment($signature_id, $_POST["delete_id"]);
    Comments::renderComments($signature_id);
    return;
}

if (!$ajax) Layout::header();
Comments::renderCommentPanel($signature_id);
if (!$ajax) Layout::footer();
