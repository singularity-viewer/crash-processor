<?php

define("SITE_ROOT", realpath(dirname(__file__)));
require_once SITE_ROOT . "/lib/init.php";
http::redirectAbsolute( GoogleLogin::loginURL());
