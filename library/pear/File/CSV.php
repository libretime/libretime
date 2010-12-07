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
 * @version     CVS: $Id: CSV.php,v 1.41 2007/05/20 12:25:14 dufuz Exp $
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
*  - Try to detect if a CSV has header or not in discoverFormat() (not possible with CSV)
*
* Known Bugs:
* (they has been analyzed but for the moment the impact in the speed for
*  properly handle this uncommon cases is too high and won't be supported)
*  - A field which is composed only by a single quoted separator (ie -> ;";";)
*    is not handled properly
*  - When there is exactly one field minus than the expected number and there
*    is a field with a separator inside, the parser will throw the "wrong count" error
*
* Info about CSV and links to other sources
* http://www.shaftek.org/publications/drafts/mime-csv/draft-shafranovich-mime-csv-00.html#appendix
*
* @author Tomas V.V.Cox <cox@idecnet.com>
* @author Helgi Þormar <dufuz@php.net>
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

        if (!isset($conf['fields']) || !(int)$conf['fields']) {
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
        static $resources = array();
        static $config;
        if (isset($resources[$file][$mode])) {
            $conf = $config;
            if ($reset) {
                fseek($resources[$file][$mode], 0);
            }
            return $resources[$file][$mode];
        }
        File_CSV::_conf($error, $conf);
        if ($error) {
            return File_CSV::raiseError($error);
        }
        $config = $conf;
        PEAR::pushErrorHandling(PEAR_ERROR_RETURN);
        $fp = File::_getFilePointer($file, $mode);
        PEAR::popErrorHandling();
        if (PEAR::isError($fp)) {
            return File_CSV::raiseError($fp);
        }
        $resources[$file][$mode] = $fp;
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

        // excel compat
        if ($field[0] == '=' && $field[1] == '"') {
            $field = str_replace('="', '"', $field);
        }

        $field_len = strlen($field);
        if ($quote && $field[0] == $quote && $field[$field_len - 1] == $quote) {
            // Get rid of escaping quotes
            $new = $prev = $c = '';
            for ($i = 0; $i < $field_len; ++$i) {
                $prev = $c;
                $c = $field[$i];
                // Deal with escaping quotes
                if ($c == $quote && $prev == $quote) {
                    $c = '';
                }

                $new .= $c;
            }
            $field = substr($new, 1, -1);
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

        $buff = $old = $prev = $c = '';
        $ret  = array();
        $i = 1;
        $in_quote = false;
        $quote = $conf['quote'];
        $f     = $conf['fields'];
        $sep   = $conf['sep'];
        while (false !== $ch = fgetc($fp)) {
            $old  = $prev;
            $prev = $c;
            $c    = $ch;

            // Common case
            if ($c != $quote && $c != $sep && $c != "\n" && $c != "\r") {
                $buff .= $c;
                continue;
            }

            // Start quote.
            if (
                $in_quote === false &&
                $quote && $c == $quote &&
                (
                 $prev == $sep || $prev == "\n" || $prev === null ||
                 $prev == "\r" || $prev == '' || $prev == ' '
                 || $prev == '=' //excel compat
                )
            ) {
                $in_quote = true;
                // excel compat, removing the = part but only if we are in a quote
                if ($prev == '=') {
                    $buff{strlen($buff) - 1} = '';
                }
            }

            if ($in_quote) {

                // When does the quote end, make sure it's not double quoted
                if ($c == $sep && $prev == $quote && $old != $quote) {
                    $in_quote = false;
                } elseif ($c == $sep && $buff == $quote.$quote) {
                    // In case we are dealing with double quote but empty value
                    $in_quote = false;
                } elseif ($c == "\n" || $c == "\r") {
                    $sub = ($prev == "\r") ? 2 : 1;
                    $buff_len = strlen($buff);
                    if (
                        $buff_len >= $sub &&
                        $buff[$buff_len - $sub] == $quote
                    ) {
                        $in_quote = false;
                    }
                }
            }

            if (!$in_quote && ($c == $sep || $c == "\n" || $c == "\r") && $prev != '') {
                // More fields than expected
                if ($c == $sep && (count($ret) + 1) == $f) {
                    // Seek the pointer into linebreak character.
                    while (true) {
                        $c = fgetc($fp);
                        if  ($c == "\n" || $c == "\r" || $c == '') {
                            break;
                        }
                    }

                    // Insert last field value.
                    $ret[] = File_CSV::unquote($buff, $quote);
                    return $ret;
                }

                // Less fields than expected
                if (($c == "\n" || $c == "\r") && $i != $f) {
                    // Insert last field value.
                    $ret[] = File_CSV::unquote($buff, $quote);
                    if (count($ret) == 1 && empty($ret[0])) {
                        return array();
                    }

                    // Pair the array elements to fields count. - inserting empty values
                    $ret_count = count($ret);
                    $sum = ($f - 1) - ($ret_count - 1);
                    $data = array_merge($ret, array_fill($ret_count, $sum, ''));
                    return $data;
                }

                if ($prev == "\r") {
                    $buff = substr($buff, 0, -1);
                }

                // Convert EOL character to Unix EOL (LF).
                if ($conf['eol2unix']) {
                    $buff = preg_replace('/(\r\n|\r)$/', "\n", $buff);
                }

                $ret[] = File_CSV::unquote($buff, $quote);
                if (count($ret) == $f) {
                    return $ret;
                }
                $buff = '';
                ++$i;
                continue;
            }
            $buff .= $c;
        }

        /* If it's the end of the file and we still have something in buffer
         * then we process it since files can have no CL/FR at the end
         */
        $feof = feof($fp);
        if ($feof && !in_array($buff, array("\r", "\n", "\r\n")) && strlen($buff) > 0) {
            $ret[] = File_CSV::unquote($buff, $quote);
            if (count($ret) == $f) {
                return $ret;
            }
        }

        return !$feof ? $ret : false;
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
        static $headers = array();
        if (!$fp = File_CSV::getPointer($file, $conf, FILE_MODE_READ)) {
            return false;
        }

        // The size is limited to 4K
        if (!$line = fgets($fp, 4096)) {
            return false;
        }

        $fields = $conf['fields'] == 1 ? array($line) : explode($conf['sep'], $line);

        $nl = array("\n", "\r", "\r\n");
        if (in_array($fields[count($fields) - 1], $nl)) {
            array_pop($fields);
        }

        $field_count = count($fields);
        $last =& $fields[$field_count - 1];
        $len = strlen($last);
        if (
            $field_count != $conf['fields'] ||
            $conf['quote'] &&
            (
             $len !== 0 && $last[$len - 1] == "\n"
             &&
                (
                    ($last[0] == $conf['quote']
                    && $last[strlen(rtrim($last)) - 1] != $conf['quote'])
                    ||
                    // excel support
                    ($last[0] == '=' && $last[1] == $conf['quote'])
                    ||
                    // if the row has spaces before the quote
                    preg_match('|^\s+'.preg_quote($conf['quote']) .'|Ums', $last, $match)
                )
            )
            // XXX perhaps there is a separator inside a quoted field
            //preg_match("|{$conf['quote']}.*{$conf['sep']}.*{$conf['quote']}|U", $line)
        ) {
            fseek($fp, -1 * strlen($line), SEEK_CUR);
            return File_CSV::readQuoted($file, $conf);
        } else {
            foreach ($fields as $k => $v) {
                $fields[$k] = File_CSV::unquote($v, $conf['quote']);
            }
        }

        if (isset($conf['header']) && empty($headers)) {
            // read the first row and assign to $headers
            $headers = $fields;
            return $headers;
        }

        if ($field_count != $conf['fields']) {
            File_CSV::raiseError("Read wrong fields number count: '". $field_count .
                                  "' expected ".$conf['fields']);
            return true;
        }

        if (!empty($headers)) {
            $tmp = array();
            foreach ($fields as $k => $v) {
                $tmp[$headers[$k]] = $v;
            }
            $fields = $tmp;
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
        if ($str === null) {
            $str = '_NULL_';
        }
        if ($str === '') {
            $str = 'Empty string';
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

        $field_count = count($fields);
        if ($field_count != $conf['fields']) {
            File_CSV::raiseError("Wrong fields number count: '". $field_count .
                                  "' expected ".$conf['fields']);
            return true;
        }

        $write = '';
        for ($i = 0; $i < $field_count; ++$i) {
            // only quote if the field contains a sep
            if (!is_numeric($fields[$i]) && $conf['quote']
                && isset($conf['sep']) && strpos($fields[$i], $conf['sep'])
            ) {
                $write .= $conf['quote'] . $fields[$i] . $conf['quote'];
            } else {
                $write .= $fields[$i];
            }

            $write .= ($i < ($field_count - 1)) ? $conf['sep']: $conf['crlf'];
        }

        if (!fwrite($fp, $write, strlen($write))) {
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
        if (!$fp = @fopen($file, 'rb')) {
            return File_CSV::raiseError("Could not open file: $file");
        }

        // Set auto detect line ending for Mac EOL support
        $oldini = ini_get('auto_detect_line_endings');
        if ($oldini != '1') {
            ini_set('auto_detect_line_endings', '1');
        }

        // Take the first 30 lines and store the number of ocurrences
        // for each separator in each line
        $lines = '';
        for ($i = 0; $i < 30 && $line = fgets($fp, 4096); $i++) {
            $lines .= $line;
        }
        fclose($fp);

        if ($oldini != '1') {
            ini_set('auto_detect_line_endings', $oldini);
        }

        $seps = array("\t", ';', ':', ',');
        $seps = array_merge($seps, $extraSeps);
        $matches = array();
        $quotes = '"\'';
        
	    $lines = str_replace('""', '', $lines);
        while ($lines != ($newLines = preg_replace('|((["\'])[^"]*(\2))|', '\2_\2', $lines))){
            $lines = $newLines;
        }

        $eol   = strpos($lines, "\r") ? "\r" : "\n";
        $lines = explode($eol, $lines);
        foreach ($lines as $line) {
            $orgLine = $line;
            foreach ($seps as $sep) {
                $line = preg_replace("|^[^$quotes$sep]*$sep*([$quotes][^$quotes]*[$quotes])|sm", '_', $orgLine);
                // Find all seps that are within qoutes
                ///FIXME ... counts legitimit lines as bad ones

                 // In case there's a whitespace infront the field
                $regex = '|\s*?';
                 // Match the first quote (optional), also optionally match = since it's excel stuff
                $regex.= "(?:\=?[$quotes])";
                $regex.= '(.*';
                // Don't match a sep if we are inside a quote
                // also don't accept the sep if it has a quote on the either side
                ///FIXME has to be possible if we are inside a quote! (tests fail because of this)
                $regex.= "(?:[^$quotes])$sep(?:[^$quotes])";
                $regex.= '.*)';
                // Close quote (if it's present) and the sep (optional, could be end of line)
                $regex.= "(?:[$quotes](?:$sep?))|Ums";
                preg_match_all($regex, $line, $match);
                // Finding all seps, within quotes or not
                $sep_count = substr_count($line, $sep);
                // Real count
                $matches[$sep][] = $sep_count - count($match[0]);
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
        $sep = key($amount);

        $conf['fields'] = $fields[$sep] + 1;
        $conf['sep']    = $sep;

        // Test if there are fields with quotes around in the first 30 lines
        $quote  = null;

        $string = implode('', $lines);
        foreach (array('"', '\'') as $q) {
            if (preg_match_all("|$sep(?:\s*?)(\=?[$q]).*([$q])$sep|Us", $string, $match)) {
                if ($match[1][0] == $match[2][0]) {
                    $quote = $match[1][0];
                    break;
                }
            }

            if (
                preg_match_all("|^(\=?[$q]).*([$q])$sep{0,1}|Ums", $string, $match)
                || preg_match_all("|(\=?[$q]).*([$q])$sep\s$|Ums", $string, $match)
            ) {
                if ($match[1][0] == $match[2][0]) {
                    $quote = $match[1][0];
                    break;
                }
            }
        }

        $conf['quote'] = $quote;
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