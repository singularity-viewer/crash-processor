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

function renderComments($comments)
{
    global $S;
    foreach($comments as $c):
        $from = $c->name . " <" . $c->email . ">" . " " . date("r", $c->commented);
        $comment = Markdown::defaultTransform($c->comment);
        $del = "";
        if ($S->user->is_admin || $S->user_id == $c->user_id)
        {
            $del = "<a class=\"del_comment\" data-id=\"{$c->id}\">Delete comment</a> ";
        }
?>
<div class="ui-corner-all" style="margin: 2em 0; background-color: #252525;">
    <div style="padding: 5px; border-bottom: 1px solid #444; vertical-align: middle;">
        <?php echo $del . htmlentities($from) ?>
    </div>
    <div style="padding: 5px"><?php echo $comment ?></div>
</div>   
<?php
    endforeach;
}

if ($_POST["action"] == "add_comment")
{
    if (trim($_POST["comment"]))
    {
        $stats->addSignatureComment($signature_id, $_POST["comment"]);
    }
    renderComments($stats->getSignatureComments($signature_id));
    return;
}
if ($_POST["action"] == "del_comment")
{
    $stats->delSignatureComment($signature_id, $_POST["delete_id"]);
    renderComments($stats->getSignatureComments($signature_id));
    return;
}




$comments = $stats->getSignatureComments($signature_id);
if (!$ajax) Layout::header();
?>
<script>
    var signature_id = <?php echo $signature_id ?>;

    function updateDelLinks(){
        $(".del_comment").each(function() {
            var $link = $(this);
            var id = $link.data("id");
            
            $link.button({
                text: false,
                icons: { primary: "ui-icon-close" },
            })
            .on("click", function(){
                if (true || confirm("Delete comment?")) {

                    data = {
                        ajax: 1,
                        action: "del_comment",
                        signature_id: signature_id,
                        delete_id: id,
                    };
        
                    $.ajax({
                        type: "POST",
                        url: "comments.php",
                        data: data,
                        success: function(res) {
                           $("#comments_scroller").html(res);
                           $('#comments_scroller').scrollTop($('#comments_scroller')[0].scrollHeight);
                           updateDelLinks();
                        },
                    });
                }
                
            });

        });
        
    };
    
    $(function(){
        $("#add_comment").button()
        .on("click", function(){

            data = {
                ajax: 1,
                action: "add_comment",
                signature_id: signature_id,
                comment: $("#comment_input").val(),
            };

            $.ajax({
                type: "POST",
                url: "comments.php",
                data: data,
                success: function(res) {
                   $("#comments_scroller").html(res);
                   $('#comments_scroller').scrollTop($('#comments_scroller')[0].scrollHeight);
                   updateDelLinks();
                },
            });
        });
        
        $('#comments_scroller').scrollTop($('#comments_scroller')[0].scrollHeight);
        
        updateDelLinks();
    });
</script>

<!--div id="comments_frame" style="height: 100%;"-->
    <div id="comments_scroller" style="height: 300px; overflow-y: auto;"><?php echo renderComments($comments) ?></div>
    <div id="new_comment" style="height: 100px;">
        <a id="add_comment">Add comment</a>
        <textarea id="comment_input" class="ui-widget-content" style="display: block; width: 100%; height: 100%; margin-top: 5px;"></textarea>
    </div>
    <div style="height: 30px;"></div>
<!--/div-->

<?php

if (!$ajax) Layout::footer();
