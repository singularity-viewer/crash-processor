<?php
/* ReportParser.php
* Parses incoming raw crash reports
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

require_once SITE_ROOT.'/lib/llsd_classes.php';
require_once SITE_ROOT.'/lib/llsd_decode.php';

class ReportParser
{
    function parse($id)
    {
        global $DB;
        $q = kl_str_sql("select * from raw_reports where report_id=!i", $id);
        if (!$res = $DB->query($q) OR !$row = $DB->fetchRow($res))
        {
            return;
        }
        $data = new stdClass;
        $DB->loadFromDbRow($data, $res, $row);
        $data->report = llsd_decode($data->raw_data);
        unset($data->raw_data);
        return $data;
    }
}