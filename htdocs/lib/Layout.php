<?php
/**
* Layout.php
* Common html for all pages
*
* Description
* @package Replex Crash Processor
* @author Latif Khalifa <latifer@streamgrid.net>
* @copyright Copyright &copy; 2012-2014, Latif Khalifa
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
  
  static function since($since)
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

  function header($toolbar = "")
  {
	global $S;
	
	$menu = array();
	
	if ($S->isAnonymous())
	{
	  $item = new stdClass;
	  $item->label = "Login";
	  $item->link = GoogleLogin::loginURL();
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
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
    <script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js"></script>
	<link rel="apple-touch-icon" sizes="57x57" href="//www.replex.org//apple-touch-icon-57x57.png">
	<link rel="apple-touch-icon" sizes="114x114" href="//www.replex.org//apple-touch-icon-114x114.png">
	<link rel="apple-touch-icon" sizes="72x72" href="//www.replex.org//apple-touch-icon-72x72.png">
	<link rel="apple-touch-icon" sizes="144x144" href="//www.replex.org//apple-touch-icon-144x144.png">
	<link rel="apple-touch-icon" sizes="60x60" href="//www.replex.org//apple-touch-icon-60x60.png">
	<link rel="apple-touch-icon" sizes="120x120" href="//www.replex.org//apple-touch-icon-120x120.png">
	<link rel="apple-touch-icon" sizes="76x76" href="//www.replex.org//apple-touch-icon-76x76.png">
	<link rel="apple-touch-icon" sizes="152x152" href="//www.replex.org//apple-touch-icon-152x152.png">
	<link rel="icon" type="image/png" href="//www.replex.org//favicon-196x196.png" sizes="196x196">
	<link rel="icon" type="image/png" href="//www.replex.org//favicon-160x160.png" sizes="160x160">
	<link rel="icon" type="image/png" href="//www.replex.org//favicon-96x96.png" sizes="96x96">
	<link rel="icon" type="image/png" href="//www.replex.org//favicon-16x16.png" sizes="16x16">
	<link rel="icon" type="image/png" href="//www.replex.org//favicon-32x32.png" sizes="32x32">
	<meta name="msapplication-TileColor" content="#101040">
	<meta name="msapplication-TileImage" content="//www.replex.org/mstile-144x144.png">

    <title>Replex Viewer - Automated Crash Report Processing System</title>
  </head>
  <body>
	<script type="text/javascript">
	  $(function() {
		  $( ".toolbarbutton" ).button();
	  
	  });

	  function scrollDown(elem) {
		var scrollHeight = Math.max(elem[0].scrollHeight, elem[0].clientHeight);
		elem[0].scrollTop = scrollHeight - elem[0].clientHeight;	  
	  }

	</script>

    <div style="padding-top:10px;">
      <div style="display: inline-block;">
	    <a href="<?php echo URL_ROOT ?>"><img src="images/replex_logo_square.png" width="70px" height="70px"/></a>
      </div>
      <div style="display: inline-block; margin-left: 15px; color: #eee; padding-bottom: 10px; vertical-align: bottom;">
	    <a href="<?php echo URL_ROOT ?>" style="font-size: 3em; font-weight: bold;">Replex Viewer</a>
	    <br/>
	    <span style="font-size: 1.6em;">Automated Crash Report Processing</span>
      </div>
    </div>
	
	
    <div id="menubar" class="ui-widget-header ui-corner-all">
	  <div><?php echo $toolbar ?></div>
	  <div>
        <?php for ($i=0; $i<count($menu); $i++): ?>
	    <a class="toolbarbutton" href="<?php echo $menu[$i]->link; ?>"><?php echo htmlspecialchars($menu[$i]->label) ?></a>
        <?php endfor ?>
	  </div>
    </div>

<?php
  }

  static function footer()
  {
  { ?>
       <div style="margin-top:10px; padding: 5px;" class="ui-widget-header ui-corner-all">
	 <div style="float: left; padding: 4px;">
	    &copy; 2013-2014 Replex Viewer Project
	 </div>
	 <div style="text-align: right;">
	    <a class="toolbarbutton" href="http://www.replex.org/">Replex Main Site</a>
	    <a class="toolbarbutton" href="http://www.replex.org/wp/about">About</a>
	    <a class="toolbarbutton" href="http://jira.openmetaverse.org/browse/REPLEX">Issue Tracker</a>
	    <a class="toolbarbutton" href="https://github.com/replex-viewer/replex">Source Tracker</a>
	 </div>
       </div> 
  </body>
</html>
  
<?php
  }
  }
}
