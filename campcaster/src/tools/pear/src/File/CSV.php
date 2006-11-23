<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * File::CSV
 *
 * PHP versions 4 and 5
 *
 * LICENSE: This source file is subject to version 3.0 of the PHP license
 * that is available through the world-wide-web at the following URI:
 * http://www.php.net/license/3_0.txt.  If you did not receive a copy of
 * the PHP License and are unable to obtain it through the web, please
 * send a note to license@php.net so we can mail you a copy immediately.
 *
 * @category    File
 * @package     File
 * @author      Tomas V.V.Cox <cox@idecnet.com>
 * @author      Helgi Þormar <dufuz@php.net>
 * @copyright   2004-2005 The Authors
 * @license     http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version     CVS: $Id: CSV.php,v 1.24 2005/08/09 08:16:02 dufuz Exp $
 * @link        http://pear.php.net/package/File
 */

require_once 'PEAR.php';
require_once 'File.php';

/**
* File class for handling CSV files (Comma Separated Values), a common format
* for exchanging data.
*
* TODO:
*  - Usage example and Doc
*  - Use getPointer() in discoverFormat
*  - Add a line counter for being able to output better error reports
*  - Store the last error in GLOBALS and add File_CSV::getLastError()
*
* Wish:
*  - Other methods like readAll(), writeAll(), numFields(), numRows()
*  - Try to detect if a CSV has header or not in discoverFormat()
*
* Known Bugs:
* (they has been analyzed but for the moment the impact in the speed for
*  properly handle this uncommon cases is too high and won't be supported)
*  - A field which is composed only by a single quoted separator (ie -> ;";";)
*    is not handled properly
*  - When there is exactly one field minus than the expected number and there
*    is a field with a separator inside, the parser will throw the "wrong count" error
*
* @author Tomas V.V.Cox <cox@idecnet.com>
* @author      Helgi Þormar <dufuz@php.net>
* @package File
*/
class File_CSV
{
    /**
    * This raiseError method works in a different way. It will always return
    * false (an error occurred) but it will call PEAR::raiseError() before
    * it. If no default PEAR global handler is set, will trigger an error.
    *
    * @param string $error The error message
    * @return bool always false
    */
    function raiseError($error)
    {
        // If a default PEAR Error handler is not set trigger the error
        // XXX Add a PEAR::isSetHandler() method?
        if ($GLOBALS['_PEAR_default_error_mode'] == PEAR_ERROR_RETURN) {
            PEAR::raiseError($error, null, PEAR_ERROR_TRIGGER, E_USER_WARNING);
        } else {
            PEAR::raiseError($error);
        }
        return false;
    }

    /**
    * Checks the configuration given by the user
    *
    * @access private
    * @param string &$error The error will be written here if any
    * @param array  &$conf  The configuration assoc array
    * @return string error    Returns a error message
    */
    function _conf(&$error, &$conf)
    {
        // check conf
        if (!is_array($conf)) {
            return $error = 'Invalid configuration';
        }

        if (!isset($conf['fields']) || !is_numeric($conf['fields'])) {
            return $error = 'The number of fields must be numeric (the "fields" key)';
        }

        if (isset($conf['sep'])) {
            if (strlen($conf['sep']) != 1) {
                return $error = 'Separator can only be one char';
            }
        } elseif ($conf['fields'] > 1) {
            return $error = 'Missing separator (the "sep" key)';
        }

        if (isset($conf['quote'])) {
            if (strlen($conf['quote']) != 1) {
                return $error = 'The quote char must be one char (the "quote" key)';
            }
        } else {
            $conf['quote'] = null;
        }

        if (!isset($conf['crlf'])) {
            $conf['crlf'] = "\n";
        }

        if (!isset($conf['eol2unix'])) {
            $conf['eol2unix'] = true;
        }
    }

    /**
    * Return or create the file descriptor associated with a file
    *
    * @param string $file The name of the file
    * @param array  &$conf The configuration
    * @param string $mode The open node (ex: FILE_MODE_READ or FILE_MODE_WRITE)
    * @param boolean $reset if passed as true and resource for the file exists
    *                       than the file pointer will be moved to the beginning
    *
    * @return mixed A file resource or false
    */
    function getPointer($file, &$conf, $mode = FILE_MODE_READ, $reset = false)
    {
        static $resources  = array();
        static $config;
        if (isset($resources[$file])) {
            $conf = $config;
            if ($reset) {
                fseek($resources[$file], 0);
            }
            return $resources[$file];
        }
        File_CSV::_conf($error, $conf);
        if ($error) {
            return File_CSV::raiseError($error);
        }
        $config = $conf;
        PEAR::pushErrorHandling(PEAR_ERROR_RETURN);
        $fp = &File::_getFilePointer($file, $mode);
        PEAR::popErrorHandling();
        if (PEAR::isError($fp)) {
            return File_CSV::raiseError($fp);
        }
        $resources[$file] = $fp;

        if ($mode == FILE_MODE_READ && !empty($conf['header'])) {
            if (!File_CSV::read($file, $conf)) {
                return false;
            }
        }
        return $fp;
    }

    /**
    * Unquote data
    *
    * @param string $field The data to unquote
    * @param string $quote The quote char
    * @return string the unquoted data
    */
    function unquote($field, $quote)
    {
        // Trim first the string.
        $field = trim($field);
        $quote = trim($quote);

        // Incase null fields (form: ;;)
        if (!strlen($field)) {
            return $field;
        }

        if ($quote && $field{0} == $quote && $field{strlen($field)-1} == $quote) {
            return substr($field, 1, -1);
        }
        return $field;
    }

    /**
    * Reads a row of data as an array from a CSV file. It's able to
    * read memo fields with multiline data.
    *
    * @param string $file   The filename where to write the data
    * @param array  &$conf   The configuration of the dest CSV
    *
    * @return mixed Array with the data read or false on error/no more data
    */
    function readQuoted($file, &$conf)
    {
        if (!$fp = File_CSV::getPointer($file, $conf, FILE_MODE_READ)) {
            return false;
        }

        $buff = $c = '';
        $ret  = array();
        $i = 1;
        $in_quote = false;
        $quote = $conf['quote'];
        $f = $conf['fields'];
        $eol2unix = $conf['eol2unix'];
        while (($ch = fgetc($fp)) !== false) {
            $prev = $c;
            $c = $ch;
            // Common case
            if ($c != $quote && $c != $conf['sep'] && $c != "\n" && $c != "\r") {
                $buff .= $c;
                continue;
            }

            // Start quote.
            if ($quote && $c == $quote &&
                ($prev == $conf['sep'] || $prev == "\n" || $prev === null ||
                 $prev == "\r" || $prev == ''))
            {
                $in_quote = true;
            }

            if ($in_quote) {
                // When ends quote
                if ($c == $conf['sep'] && $prev == $conf['quote']) {
                    $in_quote = false;
                } elseif ($c == "\n" || $c == "\r") {
                    $sub = ($prev == "\r") ? 2 : 1;
                    if ((strlen($buff) >= $sub) &&
                        ($buff{strlen($buff) - $sub} == $quote))
                    {
                        $in_quote = false;
                    }
                }
            }

            if (!$in_quote && ($c == $conf['sep'] || $c == "\n" || $c == "\r") && $prev != '') {
                // More fields than expected
                if (($c == $conf['sep']) && ((count($ret) + 1) == $f)) {
                    // Seek the pointer into linebreak character.
                    while (true) {
                        $c = fgetc($fp);
                        if  ($c == "\n" || $c == "\r") {
                            break;
                        }
                    }

                    // Insert last field value.
                    $ret[] = File_CSV::unquote($buff, $quote);
                    return $ret;
                }

                // Less fields than expected
                if (($c == "\n" || $c == "\r") && ($i != $f)) {
                    // Insert last field value.
                    $ret[] = File_CSV::unquote($buff, $quote);

                    // Pair the array elements to fields count.
                    return array_merge($ret,
                                       array_fill(count($ret),
                                                 ($f - 1) - (count($ret) - 1),
                                                 '')
                    );
                }

                if ($prev == "\r") {
                    $buff = substr($buff, 0, -1);
                }

                // Convert EOL character to Unix EOL (LF).
                if ($eol2unix) {
                    $buff = preg_replace('/(\r\n|\r)$/', "\n", $buff);
                }

                $ret[] = File_CSV::unquote($buff, $quote);
                if (count($ret) == $f) {
                    return $ret;
                }
                $buff = '';
                $i++;
                continue;
            }
            $buff .= $c;
        }
        return !feof($fp) ? $ret : false;
    }

    /**
    * Reads a "row" from a CSV file and return it as an array
    *
    * @param string $file The CSV file
    * @param array  &$conf The configuration of the dest CSV
    *
    * @return mixed Array or false
    */
    function read($file, &$conf)
    {
        if (!$fp = File_CSV::getPointer($file, $conf, FILE_MODE_READ)) {
            return false;
        }
        // The size is limited to 4K
        if (!$line   = fgets($fp, 4096)) {
            return false;
        }

        $fields = $conf['fields'] == 1 ? array($line) : explode($conf['sep'], $line);

        if ($conf['quote']) {
            $last =& $fields[count($fields) - 1];
            // Fallback to read the line with readQuoted when guess
            // that the simple explode won't work right
            if (($last{strlen($last) - 1} == "\n"
                && $last{0} == $conf['quote']
                && $last{strlen(rtrim($last)) - 1} != $conf['quote'])
                ||
                (count($fields) != $conf['fields'])
                // XXX perhaps there is a separator inside a quoted field
                //preg_match("|{$conf['quote']}.*{$conf['sep']}.*{$conf['quote']}|U", $line)
                )
            {
                fseek($fp, -1 * strlen($line), SEEK_CUR);
                return File_CSV::readQuoted($file, $conf);
            } else {
                $last = rtrim($last);
                foreach ($fields as $k => $v) {
                    $fields[$k] = File_CSV::unquote($v, $conf['quote']);
                }
            }
        }

        if (count($fields) != $conf['fields']) {
            File_CSV::raiseError("Read wrong fields number count: '". count($fields) .
                                  "' expected ".$conf['fields']);
            return true;
        }
        return $fields;
    }

    /**
    * Internal use only, will be removed in the future
    *
    * @param string $str The string to debug
    * @access private
    */
    function _dbgBuff($str)
    {
        if (strpos($str, "\r") !== false) {
            $str = str_replace("\r", "_r_", $str);
        }
        if (strpos($str, "\n") !== false) {
            $str = str_replace("\n", "_n_", $str);
        }
        if (strpos($str, "\t") !== false) {
            $str = str_replace("\t", "_t_", $str);
        }
        echo "buff: ($str)\n";
    }

    /**
    * Writes a struc (array) in a file as CSV
    *
    * @param string $file   The filename where to write the data
    * @param array  $fields Ordered array with the data
    * @param array  &$conf   The configuration of the dest CSV
    *
    * @return bool True on success false otherwise
    */
    function write($file, $fields, &$conf)
    {
        if (!$fp = File_CSV::getPointer($file, $conf, FILE_MODE_WRITE)) {
            return false;
        }
        if (count($fields) != $conf['fields']) {
            File_CSV::raiseError("Wrong fields number count: '". count($fields) .
                                  "' expected ".$conf['fields']);
            return true;
        }
        $write = '';
        for ($i = 0; $i < count($fields); $i++) {
            if (!is_numeric($fields[$i]) && $conf['quote']) {
                $write .= $conf['quote'] . $fields[$i] . $conf['quote'];
            } else {
                $write .= $fields[$i];
            }
            if ($i < (count($fields) - 1)) {
                $write .= $conf['sep'];
            } else {
                $write .= $conf['crlf'];
            }
        }
        if (!fwrite($fp, $write)) {
            return File_CSV::raiseError('Can not write to file');
        }
        return true;
    }

    /**
    * Discover the format of a CSV file (the number of fields, the separator
    * and if it quote string fields)
    *
    * @param string the CSV file name
    * @param array extra separators that should be checked for.
    * @return mixed Assoc array or false
    */
    function discoverFormat($file, $extraSeps = array())
    {
        if (!$fp = @fopen($file, 'r')) {
            return File_CSV::raiseError("Could not open file: $file");
        }
        $seps = array("\t", ';', ':', ',');
        $seps = array_merge($seps, $extraSeps);
        $matches = array();

        // Set auto detect line ending for Mac EOL support if < PHP 4.3.0.
        $phpver = version_compare('4.3.0', phpversion(), '<');
        if ($phpver) {
            $oldini = ini_get('auto_detect_line_endings');
            ini_set('auto_detect_line_endings', '1');
        }

        // Take the first 10 lines and store the number of ocurrences
        // for each separator in each line

        $lines = file($file);
        if (count($lines) > 10) {
            $lines = array_slice($lines, 0, 10);
        }

        if ($phpver) {
            ini_set('auto_detect_line_endings', $oldini);
        }

        foreach ($lines as $line) {
            foreach ($seps as $sep) {
                $matches[$sep][] = substr_count($line, $sep);
            }
        }

        $final = array();
        // Group the results by amount of equal ocurrences
        foreach ($matches as $sep => $res) {
            $times = array();
            $times[0] = 0;
            foreach ($res as $k => $num) {
                if ($num > 0) {
                    $times[$num] = (isset($times[$num])) ? $times[$num] + 1 : 1;
                }
            }
            arsort($times);

            // Use max fields count.
            $fields[$sep] = max(array_flip($times));
            $amount[$sep] = $times[key($times)];
        }

        arsort($amount);
        $sep    = key($amount);

        $conf['fields'] = $fields[$sep] + 1;
        $conf['sep']    = $sep;

        // Test if there are fields with quotes arround in the first 5 lines
        $quotes = '"\'';
        $quote  = null;
        if (count($lines) > 5) {
            $lines = array_slice($lines, 0, 5);
        }

        foreach ($lines as $line) {
            if (preg_match("|$sep([$quotes]).*([$quotes])$sep|U", $line, $match)) {
                if ($match[1] == $match[2]) {
                    $quote = $match[1];
                    break;
                }
            }
            if (preg_match("|^([$quotes]).*([$quotes])$sep{0,1}|", $line, $match)
                || preg_match("|([$quotes]).*([$quotes])$sep\s$|Us", $line, $match))
            {
                if ($match[1] == $match[2]) {
                    $quote = $match[1];
                    break;
                }
            }
        }
        $conf['quote'] = $quote;
        fclose($fp);
        // XXX What about trying to discover the "header"?
        return $conf;
    }

    /**
     * Front to call getPointer and moving the resource to the
     * beginning of the file
     * Reset it if you like.
     *
     * @param string $file The name of the file
     * @param array  &$conf The configuration
     * @param string $mode The open node (ex: FILE_MODE_READ or FILE_MODE_WRITE)
     *
     * @return boolean true on success false on failure
     */
    function resetPointer($file, &$conf, $mode)
    {
        if (!File_CSV::getPointer($file, $conf, $mode, true)) {
            return false;
        }

        return true;
    }
}
?>