<?php

define("SITE_ROOT", realpath(dirname(__file__)));
require_once SITE_ROOT . "/lib/init.php";
$S->requireUser();

$filter = new SearchFilter();
$stats = new CrashStats($filter);

Layout::header($filter->renderPaginator($stats->getNumTopCrashers()));
$filter->render();
?>
<script>
    $(function() {
        $( "#tabs" ).tabs();
        $("div.ui-tabs-panel").css('padding','0px');
        
        var tag = $("<div></div>");
        var $dialog = tag.dialog({
            modal: true,
            autoOpen: false,
            width: 800,
            height: "auto",
            position: "top",
        });
        
        $(".comment_link").each(function() {
            var $link = $(this);
            var signature_id = $link.data("signature-id");
            
            $link.on("click", function(e) {
                e.preventDefault();
                
                //$dialog.dialog("option", "title", "Crash signature " + signature_id);
                $dialog.dialog("open");
                
                $.ajax({
                    url: "comments.php?ajax=1&signature_id=" + signature_id,
                    success: function(res) {
                        tag.html(res);
                    },
                    error: function() {
                        tag.html("<p>Failed to fetch the comment section for this signature</p>");
                    },
                });
            });
        });


    });
</script>
<br/>
<div id="tabs">
    <ul>
        <li><a href="#tab-1">Top Crashers</a></li>
        <li><a href="#tab-2">Graphics Cards</a></li>
        <li><a href="#tab-3">Operating Systems</a></li>
        <li><a href="#tab-4">Regions</a></li>
    </ul>


    <!-- top crashers tab -->
    <div id="tab-1">
<?php
    function rl_s($r)
    {
        global $filter;
        return URL_ROOT . "/crashes.php?signature_id=" . urlencode($r->signature_id);
    }
    $sigs = $stats->getTopCrashers();
    $c = count($sigs);
    if ($c) :
?>
        <table class="jtable noborder" style="width: 100%">
            <tr>
                <th style="width:  3%">Nr</th>
                <th style="width:  7%">&nbsp;</th>
                <th style="width: 10%">Module</th>
                <th style="width: 80%">Stack Top</th>
            </tr>
<?php
    foreach($sigs as $r):
        $parts = explode("|", $r->signature_text);
        $txt = "";
        if ($parts[1]) $txt .= preg_replace("/((::|&lt;|&gt;|,|\\(|\\)))/", "<wbr/>\\1<wbr/>", htmlentities($parts[1]));
        if ($txt) $txt .= "<br/><br/>";
        if ($parts[2]) $txt .= preg_replace("/((::|&lt;|&gt;|,|\\(|\\)))/", "<wbr/>\\1<wbr/>", htmlentities($parts[2]));
        if (!$txt) $txt = "&nbsp;";
        $ctext = "Comments";
        if ($r->has_comments)
        {
            $ctext .= "&nbsp;({$r->has_comments})";
        }
?>
            <tr class="rowhighlight">
                <td style="text-align: right"><a href="<?php echo rl_s($r) ?>"><?php echo htmlentities($r->nr) ?></a></td>
                <td><a href="<?php echo URL_ROOT . "/comments.php?signature_id=" . $r->signature_id ?>" class="comment_link" data-signature-id="<?php echo htmlentities($r->signature_id) ?>"><?php echo $ctext ?></a></td>
                <td><a href="<?php echo rl_s($r) ?>"><?php echo htmlentities($parts[0]) ?></a></td>
                <td><a href="<?php echo rl_s($r) ?>"><?php echo $txt ?></a></td>
            </tr>
<?php endforeach ?>
        </table>

<?php endif ?>
    </div>
    <!-- /top crashers tab -->


    <!-- gpu tab -->
    <div id="tab-2">
<?php
    function rl_g($r)
    {
        global $filter;
        return URL_ROOT . "/crashes.php?gpu=" . urlencode($r->gpu);
    }
    $gpus = $stats->getGPUStats();
    $c = count($gpus);
    if ($c) :
?>
        <table class="jtable noborder">
            <tr>
                <th>Nr. reports</th>
                <th>GPU Identifier</th>
            </tr>
<?php foreach($gpus as $r): ?>
            <tr class="rowhighlight">
                <td style="text-align: right"><a href="<?php echo rl_g($r) ?>"><?php echo htmlentities($r->nr) ?></a></td>
                <td><a href="<?php echo rl_g($r) ?>"><?php echo htmlentities($r->gpu) ?></a></td>
            </tr>
<?php endforeach ?>
        </table>

<?php endif ?>
    </div>
    <!-- /gpu tab -->


    <!-- os tab -->
    <div id="tab-3">
<?php
    function rl_o($r)
    {
        global $filter;
        return URL_ROOT . "/crashes.php?os=" . urlencode($r->os_type);
    }
    $oses = $stats->getOSStats();
    $c = count($oses);
    if ($c) :
?>
        <table class="jtable noborder">
            <tr>
                <th>Nr. reports</th>
                <th>Operating System Type</th>
            </tr>
<?php foreach($oses as $r): ?>
            <tr class="rowhighlight">
                <td style="text-align: right"><a href="<?php echo rl_o($r) ?>"><?php echo htmlentities($r->nr) ?></a></td>
                <td><a href="<?php echo rl_o($r) ?>"><?php echo htmlentities($r->os_type) ?></a></td>
            </tr>
<?php endforeach ?>
        </table>

<?php endif ?>
    </div>
    <!-- /os tab -->


    <!-- regions tab -->
    <div id="tab-4">
<?php
    function rl($r)
    {
        global $filter;
        return URL_ROOT . "/crashes.php?region=" . urlencode($r->region) . "&grid=" . urlencode($r->grid);
    }
    $regions = $stats->getRegionStats();
    $c = count($regions);
    if ($c) :
?>
        <table class="jtable noborder">
            <tr>
                <th>Nr. reports</th>
                <th>Region</th>
                <th>Grid</th>
            </tr>
<?php foreach($regions as $r): ?>
            <tr class="rowhighlight">
                <td style="text-align: right"><a href="<?php echo rl($r) ?>"><?php echo htmlentities($r->nr) ?></a></td>
                <td><a href="<?php echo rl($r) ?>"><?php echo htmlentities($r->region) ?></a></td>
                <td><a href="<?php echo rl($r) ?>"><?php echo htmlentities($r->grid) ?></a></td>
            </tr>
<?php endforeach ?>
        </table>

<?php endif ?>
    </div>
    <!-- /regions tab -->
</div>

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
