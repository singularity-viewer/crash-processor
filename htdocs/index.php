<?php

define("SITE_ROOT", realpath(dirname(__file__)));
require_once SITE_ROOT . "/lib/init.php";

Layout::header();
?>

<p>This application is used for analyzing crash reports and statitstics for the Singularity
Viewer project.</p>

<p>Access to this tool is granted to the members of the development team.
The main goal is to identify the most common problems and improve the expierience
for the users of Singularity Viewer.</p>

<?php

if (!$S->isAnonymous())
{
    if ($S->user->isAllowed())
    {
        print '<p><strong>Your account has been granted access.</strong></p>';
    }
    else
    {
        print '<p><strong>Your account has no access to the system at this time.</strong></p>';
    }
}
Layout::footer();

/*
 * Local variables:
 * tab-width: 4
 * c-basic-offset: 4
 * End:
 * vim600: noet sw=4 ts=4 fdm=marker
 * vim<600: noet sw=4 ts=4
 */
