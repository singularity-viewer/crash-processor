<?php
/**
* Layout.php
* Common html for all pages
*
* Description
* @package Singularity Crash Processor
* @author Latif Khalifa <latifer@streamgrid.net>
* @copyright Copyright &copy; 2012, Latif Khalifa
* 
* Permission is hereby granted, free of charge, to any person obtaining
* a copy of this software and associated documentation files
* (the "Software"), to deal in the Software without restriction, including
* without limitation the rights to use, copy, modify, merge, publish,
* distribute, sublicense, and/or sell copies of the Software, and to permit
* persons to whom the Software is furnished to do so, subject to the
* following conditions:
*
* - The above copyright notice and this permission notice shall be included
* in all copies or substantial portions of the Software.
*
* THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
* EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
* MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.
* IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM,
* DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR
* OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE
* OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
* 
*/

class Layout
{

  function since($since)
  {
    $since = time() - $since;
    $chunks = array(
		    array(60 * 60 * 24 * 365 , 'year'),
		    array(60 * 60 * 24 * 30 , 'month'),
		    array(60 * 60 * 24 * 7, 'week'),
		    array(60 * 60 * 24 , 'day'),
		    array(60 * 60 , 'hour'),
		    array(60 , 'minute'),
		    array(1 , 'second')
		    );

    for ($i = 0, $j = count($chunks); $i < $j; $i++) {
      $seconds = $chunks[$i][0];
      $name = $chunks[$i][1];
      if (($count = floor($since / $seconds)) != 0) {
	break;
      }
    }

    $print = ($count == 1) ? '1 '.$name : "$count {$name}s";
    return $print;
  }

  function header()
  {
	global $S;
	
	$menu = array();
	
	if ($S->isAnonymous())
	{
	  $item = new stdClass;
	  $item->label = "Login";
	  $item->link = "/login.php";
	  $menu[] = $item;
	}
	else
	{
	  if ($S->user->isAllowed())
	  {
		$item = new stdClass;
		$item->label = "Crash reports";
		$item->link = "/crashes.php";
		$menu[] = $item;
  
		$item = new stdClass;
		$item->label = "Statistics";
		$item->link = "/statistics.php";
		$menu[] = $item;
	  }
	  
	  if ($S->user->isAdmin())
	  {
		$item = new stdClass;
		$item->label = "Users";
		$item->link = "/users.php";
		$menu[] = $item;
	  }

 	  $item = new stdClass;
	  $item->label = "My Account ({$S->user->email})";
	  $item->link = "/account.php";
	  $menu[] = $item;

 	  $item = new stdClass;
	  $item->label = "Logout";
	  $item->link = "/logout.php";
	  $menu[] = $item;
	}
	
	?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
    <link rel="stylesheet" type="text/css" href="<?php print URL_ROOT ?>/css/singularity/jquery-ui-1.10.3.custom.css"/>
    <link rel="stylesheet" type="text/css" href="<?php print URL_ROOT ?>/css/singularity/singularity.css"/>
    <link rel="shortcut icon" href="<?php print IMG_ROOT ?>/favicon.ico" type="image/x-icon" />
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
    <script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js"></script>
    <title>Automated Crash Report Processing System</title>

<script type="text/javascript">
//<![CDATA[
$(function() {
    $( ".toolbarbutton" ).button();

/*    
    $( ".rowhighlight" ).on("mousedown", function(e) {
      $(this).addClass("ui-state-disabled");
    });
*/

});
//]]>
</script>

  </head>
  <body>
    <div style="padding-top:20px;">
      <div style="display: inline-block;">
	<a href="<?php echo URL_ROOT ?>"><img src="images/singularity_icon.png" width="150px" height="150px"/></a>
      </div>
      <div style="display: inline-block;color: #eee; padding: 55px 0 0 30px; vertical-align: top;">
	<a href="<?php echo URL_ROOT ?>" style="font-size: 4em; font-weight: bold;">Singularity Viewer</a>
	<br/>
	<span style="font-size: 1.6em;">Automated Crash Report Processing</span>
      </div>
    </div>
    <div id="menubar" style="text-align: right; margin-top:10px; margin-bottom:10px; padding: 5px;" class="ui-widget-header ui-corner-all">
      <?php for ($i=0; $i<count($menu); $i++): ?>
	<a class="toolbarbutton" href="<?php echo $menu[$i]->link; ?>"><?php echo htmlspecialchars($menu[$i]->label) ?></a>
      <?php endfor ?>
    </div>

<?php
  }

  function footer()
  {
  { ?>
       <div style="margin-top:10px; padding: 5px;" class="ui-widget-header ui-corner-all">
	 <div style="float: left; padding: 4px;">
	    &copy; 2013 Singularity Viewer Project
	 </div>
	 <div style="text-align: right;">
	    <a class="toolbarbutton" href="http://www.singularityviewer.org/">Singularity Main Site</a>
	    <a class="toolbarbutton" href="http://www.singularityviewer.org/about">About</a>
	    <a class="toolbarbutton" href="http://code.google.com/p/singularity-viewer/issues/">Issue Tracker</a>
	    <a class="toolbarbutton" href="https://github.com/singularity-viewer/SingularityViewer">Source Tracker</a>
	 </div>
       </div> 
  </body>
</html>
  
<?php
  }
  }
}
