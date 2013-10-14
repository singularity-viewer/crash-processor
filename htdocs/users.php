<?php

define("SITE_ROOT", realpath(dirname(__file__)));
require_once SITE_ROOT . "/lib/init.php";
$S->requireAdmin();
$users = User::getAll();

if (isset($_REQUEST["action"]))
{
    $action = $_REQUEST["action"];
    $user_id = (int)$_REQUEST["id"];
    $user = User::get($user_id);
    if ($user && !$user->isAdmin())
    {
        switch ($action)
        {
            case "grant":
                $user->is_allowed = 1;
                $user->update();
                break;
            
            case "revoke":
                $user->is_allowed = 0;
                $user->update();
                break;
            
            case "remove":
                $user->delete();
                break;
        }
    }
    http::redirect("/users.php");
}

Layout::header();
?>

<h3>User Accounts</h3>

<table class="jtable">
    <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Email</th>
        <th>Access</th>
        <th colspan="2">Actions</th>
    </tr>
<?php for ($i=0; $i<count($users); $i++): ?>
    <tr>
        <td style="text-align: right;"><?php echo (int)$users[$i]->user_id ?></td>
        <td><?php echo htmlentities($users[$i]->name); ?></td>
        <td><?php echo htmlentities($users[$i]->email); ?></td>
        <td><?php echo $users[$i]->isAdmin() ? "Admin" : ($users[$i]->isAllowed() ? "Granted" : "No");  ?></td>
<?php
    if ($users[$i]->isAdmin())
    {
        print '<td colspan="2">';
    }
    else
    {
        print "<td>";
        $action = $users[$i]->isAllowed() ? "revoke" : "grant";
        $url = URL_ROOT . "/users.php?action=$action&id=" . (int)$users[$i]->user_id;
        $delete = URL_ROOT . "/users.php?action=remove&id=" . (int)$users[$i]->user_id;
        print "<a class=\"toolbarbutton\" href=\"{$url}\">{$action}</a>";
        print "</td><td>";
        print "<a class=\"toolbarbutton\" href=\"{$delete}\" title=\"Caution: completely deletes information about this user.\" onclick=\"return confirm('Are you sure you want to completely remove this account? Just revoking access works too, and they can create it again by logging with their Google credentials.');\">delete account</a>";
    }
?>
        </td>
    </tr>
<?php endfor ?>
</table>

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
