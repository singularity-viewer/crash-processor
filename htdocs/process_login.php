<?php

define("SITE_ROOT", realpath(dirname(__file__)));
require_once SITE_ROOT . "/lib/init.php";

$google_response = GoogleOpenID::getResponse(); 
$success = $google_response->success();//true or false
if (!$success)
{
    http::redirect("/login_failed.php");
}

$user_identity = $google_response->identity();//the user's ID
$user_email = $google_response->email();//the user's email

$user = User::getByLogin($user_identity);

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
