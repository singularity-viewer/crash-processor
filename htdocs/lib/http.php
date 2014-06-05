<?php
/**
* http.php
* Common http operations such as redirects, etc.
*
* Description
* @package Replex Crash Processor
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

class http
{
	public static function redirectAbsolute($url)
	{
		Header("Location: $url");
		Header("HTTP/1.0 302 Found");
		print "<!DOCTYPE HTML PUBLIC \"-//IETF//DTD HTML 2.0//EN\">
<HTML><HEAD>
<TITLE>302 Found</TITLE>
</HEAD><BODY>
<H1>Found</H1>
The document was found <A HREF=\"$url\">here</A>.<P>
</BODY></HTML>
";
		exit;
	}


	public static function redirect($url)
	{
		http::redirectAbsolute(URL_ROOT . $url);

	}

	public static function notFound()
	{
		header("HTTP/1.1 404 Not Found");
		print "<!DOCTYPE HTML PUBLIC \"-//IETF//DTD HTML 2.0//EN\">
<HTML><HEAD>
<TITLE>404 Not Found</TITLE>
</HEAD><BODY>
<H1>Not Found</H1>
The requested URL was  not found on this server.<P>
<HR>
<ADDRESS>{$_SERVER['SERVER_SIGNATURE']}</ADDRESS>
</BODY></HTML>
";
		exit;
	}

	public static function notAllowed()
	{
		header("HTTP/1.1 405 Method Not Allowed");
		print "<!DOCTYPE HTML PUBLIC \"-//IETF//DTD HTML 2.0//EN\">
<HTML><HEAD>
<TITLE>405 Method Not Allowed</TITLE>
</HEAD><BODY>
<H1>Method Not Allowed</H1>
The requested HTTP method is not allowed for this URL.<P>
<HR>
<ADDRESS>{$_SERVER['SERVER_SIGNATURE']}</ADDRESS>
</BODY></HTML>
";
		exit;
	}

	public static function notModified()
	{
		header("HTTP/1.1 304 Not Modified");
		print "<!DOCTYPE HTML PUBLIC \"-//IETF//DTD HTML 2.0//EN\">
<HTML><HEAD>
<TITLE>304 Not Modified</TITLE>
</HEAD><BODY>
<H1>Not Modified</H1>
The requested URL has not been modified.<P>
<HR>
<ADDRESS>{$_SERVER['SERVER_SIGNATURE']}</ADDRESS>
</BODY></HTML>
";
		exit;
	}

	public static function noCache()
	{
		header("Expires: Mon, 21 Jan 1980 06:01:01 GMT");
		header("Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0");
		header("Pragma: no-cache");
	}
	
	/**
	 * Send data for download.
	 *
	 * @param string $filename
	 * @param string $data
	 */
	public static function sendDownload($filename, $data)
	{
		header('Content-disposition: attachment; filename=' . urlencode($filename));
		echo $data;
	}

}
/*
* Local variables:
* tab-width: 4
* c-basic-offset: 4
* End:
* vim600: sw=4 ts=4 fdm=marker
* vim<600: sw=4 ts=4
*/
?>
