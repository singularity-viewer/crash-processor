<?php

define("SITE_ROOT", realpath(dirname(__file__)));
require_once SITE_ROOT . "/lib/init.php";

$google_gateway = Layout::getLoginGateway();
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
