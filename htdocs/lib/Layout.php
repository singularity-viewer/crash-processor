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
    <link rel="stylesheet" type="text/css" href="<?php print URL_ROOT ?>/singularity.css"/>
    <link rel="shortcut icon" href="<?php print IMG_ROOT ?>/favicon.ico" type="image/x-icon" />
    <title>Singularity Viewer Automated Build System</title>

<!--script type="text/javascript">
//<![CDATA[
//]]>
</script-->

  </head>
  <body>
      <div id="everything">
      <div id="page-wrapper">
      <div id="header"></div>
      <div class="container"><a href="<?php print URL_ROOT ?>" style="font-size: 20px;">Automated Crash Report Processing System</a><br/>
	  <?php for ($i=count($menu) - 1; $i>=0; $i--): ?>
		<div class="menuitem"><a href="<?php echo $menu[$i]->link; ?>"><?php echo htmlspecialchars($menu[$i]->label) ?></a></div>
	  <?php endfor ?>
	  <br/><br/>
  

<?php
  }

  function footer()
  {
  { ?>
       </div><!-- container -->
       <div class="container">
        <table style="width: 100%; border: none; padding: 0;"><tr>
         <td class="bottom-links"><a href="http://www.singularityviewer.org/">Singularity Main Site</a></td>
         <td class="bottom-links"><a href="http://www.singularityviewer.org/about">About</a></td>
         <td class="bottom-links"><a href="http://code.google.com/p/singularity-viewer/issues/">Issue Tracker</a></td>
         <td class="bottom-links"><a href="https://github.com/singularity-viewer/SingularityViewer">Source Tracker</a></td>
      <td width="50%" style="text-align: right;">&copy; 2013 Singularity Viewer Project</td>
        </tr></table>
       </div> 
      </div><!-- everything -->
    </div><!-- page-wrapper -->
  </body>
</html>
  
<?php
  }
  }
}
