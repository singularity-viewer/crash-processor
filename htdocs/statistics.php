<?php

define("SITE_ROOT", realpath(dirname(__file__)));
require_once SITE_ROOT . "/lib/init.php";
$S->requireUser();
$filter = new SearchFilter();

Layout::header();
$filter->render();
?>
<script>
    $(function() {
        $( "#tabs" ).tabs({ active: 1 });
        // $("div.ui-tabs-panel").css('padding','0px');
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
    </div>
    <!-- /top crashers tab -->


    <!-- gpu tab -->
    <div id="tab-2">
    </div>
    <!-- /gpu tab -->


    <!-- os tab -->
    <div id="tab-3">
    </div>
    <!-- /os tab -->


    <!-- regions tab -->
    <div id="tab-4">
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
