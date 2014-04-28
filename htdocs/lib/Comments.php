<?php

class Comments
{
    static function updateCommentCount($id)
    {
        $q = kl_str_sql("update signature set has_comments=(select count(*) as count from comment where signature_id=!i) where id=!i", $id, $id);
        DBH::$db->query($q);
        Memc::flush();
    }

    static function addSignatureComment($id, $text)
    {
        global $S;
        $q = kl_str_sql("insert into comment (signature_id, user_id, comment) values (!i, !i, !s)", $id, $S->user_id, $text);
        DBH::$db->query($q);
        self::updateCommentCount($id);
    }
    
    static function getSignatureComments($id)
    {
        $ret = array();
        $q = kl_str_sql("select c.*, u.name, u.email from comment c join users u on c.user_id = u.user_id where signature_id=!i order by id asc;", $id);
        if (!$res = DBH::$db->query($q)) return;
        
        while ($row = DBH::$db->fetchRow($res))
        {
            $c = new stdClass;
            DBH::$db->loadFromDbRow($c, $res, $row);
            $ret[] = $c;
        }
        
        return $ret;
    }
    
    static function delSignatureComment($id, $del_id)
    {
        $q = kl_str_sql("delete from comment where id=!i", $del_id);
        DBH::$db->query($q);
        self::updateCommentCount($id);
    }

    static function renderComments($id)
    {
        global $S;
        $comments = self::getSignatureComments($id);
        
        foreach($comments as $c):
            $from = $c->name . " <" . $c->email . ">" . " " . date("r", $c->commented);
            $comment = Markdown::defaultTransform($c->comment);
            $del = "";
            if ($S->user->is_admin || $S->user_id == $c->user_id)
            {
                $del = "<a class=\"del_comment\" data-id=\"{$c->id}\">Delete comment</a> ";
            }
            $gid = md5($c->email);
            $avatar = (USE_SSL ? "https://secure.gravatar.com" : "http://www.gravatar.com") .  "/avatar/$gid?r=x&amp;d=mm&amp;s=48";
            
    ?>
    <div>
    <div style="float: left; display: inline-block; ">
        <img style="border-radius: 5px; margin-top: 3px; border: 1px solid #444; filter: alpha(opacity=80); opacity: 0.8;" src="<?php echo $avatar ?>" />
    </div>
    <div class="ui-corner-all" style="margin: 1em 0; background-color: #252525; margin-left: 60px; margin-right: 20px;">
        <div style="padding: 5px; border-bottom: 1px solid #444; vertical-align: middle;">
            <?php echo $del . htmlentities($from) ?>
        </div>
        <div style="padding: 5px"><?php echo $comment ?></div>
    </div>
    </div>
    <?php
        endforeach;
    }
    
    
    
    
    
    
    
    static function renderCommentPanel($id)
    {


?>
<script>
    var signature_id = <?php echo $id ?>;

    function updateDelLinks(){
        $(".del_comment").each(function() {
            var $link = $(this);
            var id = $link.data("id");
            
            $link.button({
                text: false,
                icons: { primary: "ui-icon-close" },
            })
            .on("click", function(){
                if (confirm("Delete comment?")) {

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
        .on("click", function(e){
            
            e.preventDefault();

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
                   updateDelLinks();
                   scrollDown($('#comments_scroller'));
                },
            });
        });
        
        updateDelLinks();
        scrollDown($('#comments_scroller'));
        
    });
</script>

<div id="comments_frame">
    <p>Comments for crash signature <?php echo $id ?></p>
    <div id="comments_scroller" style="max-height: 300px; overflow-y: auto;"><?php echo self::renderComments($id) ?></div>
    <div id="new_comment">
        <form id="comment_submit_form" action="comments.php">
            <input type="submit" id="add_comment" value="Add comment" />
            <textarea id="comment_input" class="ui-widget-content" style="width: 100%; display: block; height: 100px; margin-top: 5px;"></textarea>
            <input type="hidden" name="signature_id" value="<?php echo $id ?>" />
            <input type="hidden" name="action" value="add_comment" />
        </form>
    </div>
</div>

<?php




    }

}