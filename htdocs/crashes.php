<?php

define("SITE_ROOT", realpath(dirname(__file__)));
require_once SITE_ROOT . "/lib/init.php";
$S->requireUser();

function lk($id, $txt)
{
    echo '<a href="'. URL_ROOT . '/report_detail.php?id=' . $id . '">' . htmlentities($txt) . '</a>';
}

$filter = new SearchFilter();
$total = CrashReport::getTotal($filter);
$reports = CrashReport::getReports($filter);
Layout::header();

$filter->render();
?>

<script>
    $(function() {
        var tag = $("<div></div>");
        var $dialog = tag.dialog({
            autoOpen: false,
            show: 100,
            hide: 50,
            resizable: false,
            minHeight: 0,
        });
        $(".ui-dialog-titlebar-close", $dialog.parent()).hide();
        $dialog.css('padding','0px 0px 0px 4px');
        
        var sumbmitted = [];
        var data = [];
        
        $(".rowhighlight").each(function() {
            var $link = $(this);
            var id = $link.data("id");
            var signature_id = $link.data("signature-id");

            function setDialog(report_id) {
                if (data[report_id]) {
                    tag.html(data[report_id]);
                } else {
                    if (!sumbmitted[report_id])
                    {
                        sumbmitted[report_id] = true;
                        tag.html("Loading ...");

                        $.ajax({
                            url: "report_tip.php?id=" + id + "&signature_id=" + signature_id,
                            success: function(res){
                                data[report_id] = res;
                                tag.html(res);
                            },
                            error: function(){
                                sumbmitted[report_id] = false;
                                tag.html("Failed to fetch data");
                            },
                        });
                    }
                }
            };

            function getPos(e) {
                var x = e.pageX;
                var dwidth = $dialog.dialog("option", "width");
                if (x < $(window).width() - dwidth - 40) {
                    x += 15;
                } else {
                    x -= dwidth + 40;
                }
                
                var y = e.pageY - $(document).scrollTop();
                var dheight = $dialog.height();
                if (dheight < 70) dheight = 70;
                //console.debug("y=" + y + "; wheight=" + $(window).height() + ": dheight=" + dheight);
                if (y < $(window).height() - dheight - 40)
                {
                    y += 15;
                }
                else
                {
                    y -= dheight + 40;
                }
                return [x, y];
            };
            
            $link.on("mouseenter", function(e) {
                setDialog($link.data("id"));
                $dialog.dialog("option","position", getPos(e));
                $dialog.dialog("option","title", "Crash signature " + signature_id);
                $link.delay(300).queue(function() {
                    if (!$dialog.dialog("isOpen"))
                    {
                        $dialog.dialog("open");
                    }
                    $link.dequeue();
                });
            });
            $link.on("mousemove", function(e) {
                //console.debug(e.x);
                $dialog.dialog("option","position", getPos(e));
            });
            $link.on("mouseleave", function() {
                $link.clearQueue();
                if ($dialog.dialog("isOpen"))
                {
                    $dialog.dialog("close");
                }
            });
        });
    });
</script>
<p>Reports <strong><?php echo $total ?></strong></p>

<table width="100%" class="jtable">
    <tr>
        <th>ID</th>
        <th>Version</th>
        <th>Operating System</th>
        <th>GPU</th>
        <th>Grid (region)</th>
        <th></th>
    </tr>
<?php for ($i=0; $i<count($reports); $i++): ?>
    <tr class="rowhighlight" data-signature-id="<?php echo $reports[$i]->signature_id ?>" data-id="<?php echo $reports[$i]->id ?>">
        <td><?php lk($reports[$i]->id, $reports[$i]->id) ?></td>
        <td><?php lk($reports[$i]->id, $reports[$i]->client_channel . " " . $reports[$i]->client_version) ?></td>
        <td><?php lk($reports[$i]->id, $reports[$i]->os) ?></td>
        <td><?php lk($reports[$i]->id, $reports[$i]->gpu) ?></td>
        <td><?php lk($reports[$i]->id, $reports[$i]->grid . " (" . $reports[$i]->region . ")") ?></td>
        <td style="text-align: right"><a href="<?php echo URL_ROOT . "/crashes.php?signature_id=" . $reports[$i]->signature_id ?>">Similar</a></td>
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
