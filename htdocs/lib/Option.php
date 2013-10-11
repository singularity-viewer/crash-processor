<?php
/**
* Option.php
* Simple key-value store for various options
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

class Option
{
	private static $optionArray = array();
	
	/**
	 * Inits the option class. Must be called before any other method.
	 */
	public static function init()
	{
		if(!$result = DBH::$db->query("SELECT * FROM options")) {
			return false;
		} else {
			while ($row = DBH::$db->fetchRow($result)) {
				self::$optionArray[$row['name']] = $row['value'];
			}
			return true;
		}
	}

	/**
	 * Updates an option.
	 *
	 * @param string $name Name of the option
	 * @param string $value New value for the option
	 * @return boolean
	 */
	public static function update($name, $value)
	{
		$result = DBH::$db->query(kl_str_sql("DELETE FROM options WHERE name=!s", $name));	
		$result = DBH::$db->query(kl_str_sql("INSERT INTO options (name, value) VALUES(!s,!s)", $name,$value ));	
		self::$optionArray[$name] = $value;
		return $result;
	}


	/**
	 * Gets an option.
	 *
	 * @param string $name Name of the option
	 * @return string Value of the option
	 */
	public static function get($name)
	{
		if (!isset(self::$optionArray[$name])) {
			return NULL;
		}
		return self::$optionArray[$name];
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