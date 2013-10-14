<?php

define("SITE_ROOT", realpath(dirname(__file__)));
require_once SITE_ROOT . "/lib/init.php";
if ($S->isAnonymous())
{
    $S->loginRedirect();    
}
$user = $S->user;

if (isset($_POST["user_id"]))
{
    $user->name = $_POST["name"];
    $msg = $user->update() ? "Information updated" : "Update failed";
    http::redirect("/account.php?msg=" . urlencode($msg));
}
Layout::header();
?>

<h3>Account Details</h3>

<?php if (isset($_GET["msg"])): ?>
<strong><?php echo htmlentities($_GET["msg"]); ?></strong>
<?php endif ?>

<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
    <input type="hidden" name="user_id" value="<?php echo (int)$user->user_id; ?>" />
    <table class="jtable">
        <tr>
            <td>Name</td>
            <td><input class="ui-widget-content" type="text" name="name" size="30" value="<?php echo htmlentities($user->name); ?>" />&nbsp;
            <input class="ui-widget-content toolbarbutton" type="submit" name="save_name" value="Save" /></td>
        </tr>
        <tr>
            <td>Email</td>
            <td><?php echo htmlentities($user->email); ?></td>
        </tr>
        <tr>
            <td>Access Granted</td>
            <td><?php echo $user->isAllowed() ? "yes" : "no" ?></td>
        </tr>
        <tr>
            <td>Admin Privileges</td>
            <td><?php echo $user->isAdmin() ? "yes" : "no" ?></td>
        </tr>
    </table>
</form>

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
