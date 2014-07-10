<?php

define("SITE_ROOT", realpath(dirname(__file__)));
require_once SITE_ROOT . "/lib/init.php";


if (!isset($_GET["code"]) || !GoogleLogin::verifyLogin($_GET["code"]))
{
    http::redirect("/login_failed.php");
}

$user_identity = GoogleLogin::userID();
$user_email = GoogleLogin::userEmail();

$user = User::getByLogin($user_identity);

if (!$user)
{
	var_dump($user_email);
	$user_tmp = User::getByEmail($user_email);
	if($user_tmp /* && (!$user->login) || ($user->login == "")*/)
	{
		$user = $user_tmp;
		$user->login = $user_identity;
		$user->update();
	}
}

if (!$user)
{
    $user = new User();
    $user->email = $user_email;
    $user->is_admin = 0;
    $user->is_allowed = 0;
    $user->login = $user_identity;
    if (!$user->save())
    {
        http::redirect("/login_failed.php");
    }
}

$S->user = $user;
$S->user_id = $user->user_id;
$S->authenticated = 1;
$S->update();

if (isset($_GET['caller']))
{
    http::redirect($_GET['caller']);
}
http::redirect('/');

/*
 * Local variables:
 * tab-width: 4
 * c-basic-offset: 4
 * End:
 * vim600: noet sw=4 ts=4 fdm=marker
 * vim<600: noet sw=4 ts=4
 */
