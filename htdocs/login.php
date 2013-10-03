<?php

define("SITE_ROOT", realpath(dirname(__file__)));
require_once SITE_ROOT . "/lib/init.php";
$assoc_handle_expires = (int)Option::get('assoc_handle_expires');
$now = time();

$assoc_handle = Option::get("assoc_handle");

if (!$assoc_handle || $assoc_handle_expires < $now)
{
    $assoc_handle_expires = time() + 604800;
    $assoc_handle = GoogleOpenID::getAssociationHandle();
    if ($assoc_handle)
    {
        Option::update("assoc_handle_expires", $assoc_handle_expires);
        Option::update("assoc_handle", $assoc_handle);
    }
}

$google_gateway = GoogleOpenID::createRequest(URL_ROOT . "/process_login.php", $handle, true);

Layout::header();
?>

<p>Please use your <a href="<?php echo $google_gateway->getRequestURL(); ?>">Google Account</a> to login.</p>

<?php
Layout::footer();

/*
 * Local variables:
 * tab-width: 4
 * c-basic-offset: 4
 * End:
 * vim600: noet sw=4 ts=4 fdm=marker
 * vim<600: noet sw=4 ts=4
 */
