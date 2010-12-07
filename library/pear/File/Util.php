<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * File::Util
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
 * @author      Michael Wallner <mike@php.net>
 * @copyright   2004-2005 Michael Wallner
 * @license     http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version     CVS: $Id: Util.php,v 1.25 2007/02/20 14:19:08 mike Exp $
 * @link        http://pear.php.net/package/File
 */

/**#@+
 * Sorting Constants
 */
define('FILE_SORT_NONE',    0);
define('FILE_SORT_REVERSE', 1);
define('FILE_SORT_NAME',    2);
define('FILE_SORT_SIZE',    4);
define('FILE_SORT_DATE',    8);
define('FILE_SORT_RANDOM',  16);
/**#@-*/

/**#@+
 * Listing Constants
 */
define('FILE_LIST_FILES',   1);
define('FILE_LIST_DIRS',    2);
define('FILE_LIST_DOTS',    4);
define('FILE_LIST_ALL',     FILE_LIST_FILES | FILE_LIST_DIRS | FILE_LIST_DOTS);
/**#@-*/

/**
 * @ignore
 */
define('FILE_WIN32', defined('OS_WINDOWS') ? OS_WINDOWS : !strncasecmp(PHP_OS, 'win', 3));

/**
 * File_Util
 *
 * File and directory utility functions.
 *
 * @access  public
 * @static
 */
class File_Util
{
    /**
     * Returns a string path built from the array $pathParts. Where a join
     * occurs multiple separators are removed. Joins using the optional
     * separator, defaulting to the PHP DIRECTORY_SEPARATOR constant.
     *
     * @static
     * @access  public
     * @param   array   $parts Array containing the parts to be joined
     * @param   string  $separator The directory seperator
     */
    function buildPath($parts, $separator = DIRECTORY_SEPARATOR)
    {
        $qs = '/^'. preg_quote($separator, '/') .'+$/';
        for ($i = 0, $c = count($parts); $i < $c; $i++) {
            if (!strlen($parts[$i]) || preg_match($qs, $parts[$i])) {
                unset($parts[$i]);
            } elseif (0 == $i) {
                $parts[$i] = rtrim($parts[$i], $separator);
            } elseif ($c - 1 == $i) {
                $parts[$i] = ltrim($parts[$i], $separator);
            } else {
                $parts[$i] = trim($parts[$i], $separator);
            }
        }
        return implode($separator, $parts);
    }

    /**
     * Returns a path without leading / or C:\. If this is not
     * present the path is returned as is.
     *
     * @static
     * @access  public
     * @param   string  $path The path to be processed
     * @return  string  The processed path or the path as is
     */
    function skipRoot($path)
    {
        if (File_Util::isAbsolute($path)) {
            if (FILE_WIN32) {
                return substr($path, $path{3} == '\\' ? 4 : 3);
            }
            return ltrim($path, '/');
        }
        return $path;
    }

    /**
     * Returns the temp directory according to either the TMP, TMPDIR, or
     * TEMP env variables. If these are not set it will also check for the
     * existence of /tmp, %WINDIR%\temp
     *
     * @static
     * @access  public
     * @return  string  The system tmp directory
     */
    function tmpDir()
    {
        if (FILE_WIN32) {
            if (isset($_ENV['TEMP'])) {
                return $_ENV['TEMP'];
            }
            if (isset($_ENV['TMP'])) {
                return $_ENV['TMP'];
            }
            if (isset($_ENV['windir'])) {
                return $_ENV['windir'] . '\\temp';
            }
            if (isset($_ENV['SystemRoot'])) {
                return $_ENV['SystemRoot'] . '\\temp';
            }
            if (isset($_SERVER['TEMP'])) {
                return $_SERVER['TEMP'];
            }
            if (isset($_SERVER['TMP'])) {
                return $_SERVER['TMP'];
            }
            if (isset($_SERVER['windir'])) {
                return $_SERVER['windir'] . '\\temp';
            }
            if (isset($_SERVER['SystemRoot'])) {
                return $_SERVER['SystemRoot'] . '\\temp';
            }
            return '\temp';
        }
        if (isset($_ENV['TMPDIR'])) {
            return $_ENV['TMPDIR'];
        }
        if (isset($_SERVER['TMPDIR'])) {
            return $_SERVER['TMPDIR'];
        }
        return '/tmp';
    }

    /**
     * Returns a temporary filename using tempnam() and File::tmpDir().
     *
     * @static
     * @access  public
     * @param   string  $dirname Optional directory name for the tmp file
     * @return  string  Filename and path of the tmp file
     */
    function tmpFile($dirname = null)
    {
        if (!isset($dirname)) {
            $dirname = File_Util::tmpDir();
        }
        return tempnam($dirname, 'temp.');
    }

    /**
     * Returns boolean based on whether given path is absolute or not.
     *
     * @static
     * @access  public
     * @param   string  $path Given path
     * @return  boolean True if the path is absolute, false if it is not
     */
    function isAbsolute($path)
    {
        if (preg_match('/(?:\/|\\\)\.\.(?=\/|$)/', $path)) {
            return false;
        }
        if (FILE_WIN32) {
            return preg_match('/^[a-zA-Z]:(\\\|\/)/', $path);
        }
        return ($path{0} == '/') || ($path{0} == '~');
    }

    /**
     * Checks for a file's existence, taking the current include path 
     * into consideration
     * 
     * This method can be called statically
     * (e.g., File_Util::isIncludable('config.php'))
     *
     * @param string $file
     * @param string $sep the directory separator (optional)
     * @return string the includable path
     * @access public
     * @static     
     */
    function isIncludable($file, $sep = DIRECTORY_SEPARATOR)
    {
        foreach ((array) explode(PATH_SEPARATOR, ini_get('include_path')) as $path) {
            if (file_exists($path .= $sep . $file)) {
                return $path;
            }
        }
        if (file_exists($file)) {
            return $file;
        }
        return NULL;
    }
    
    /**
     * Get path relative to another path
     *
     * @static
     * @access  public
     * @return  string
     * @param   string  $path
     * @param   string  $root
     * @param   string  $separator
     */
    function relativePath($path, $root, $separator = DIRECTORY_SEPARATOR)
    {
        $path = File_Util::realpath($path, $separator);
        $root = File_Util::realpath($root, $separator);
        $dirs = explode($separator, $path);
        $comp = explode($separator, $root);

        if (FILE_WIN32) {
            if (strcasecmp($dirs[0], $comp[0])) {
                return $path;
            }
            unset($dirs[0], $comp[0]);
        }

        foreach ($comp as $i => $part) {
            if (isset($dirs[$i]) && $part == $dirs[$i]) {
                unset($dirs[$i], $comp[$i]);
            } else {
                break;
            }
        }

        return str_repeat('..' . $separator, count($comp)) . implode($separator, $dirs);
    }

    /**
     * Get real path (works with non-existant paths)
     *
     * @static
     * @access  public
     * @return  string
     * @param   string  $path
     * @param   string  $separator
     */
    function realPath($path, $separator = DIRECTORY_SEPARATOR)
    {
        if (!strlen($path)) {
            return $separator;
        }

        $drive = '';
        if (FILE_WIN32) {
            $path = preg_replace('/[\\\\\/]/', $separator, $path);
            if (preg_match('/([a-zA-Z]\:)(.*)/', $path, $matches)) {
                $drive = $matches[1];
                $path  = $matches[2];
            } else {
                $cwd   = getcwd();
                $drive = substr($cwd, 0, 2);
                if ($path{0} !== $separator{0}) {
                    $path  = substr($cwd, 3) . $separator . $path;
                }
            }
        } elseif ($path{0} !== $separator) {
            $path = getcwd() . $separator . $path;
        }

        $dirStack = array();
        foreach (explode($separator, $path) as $dir) {
            if (strlen($dir) && $dir !== '.') {
                if ($dir == '..') {
                    array_pop($dirStack);
                } else {
                    $dirStack[] = $dir;
                }
            }
        }

        return $drive . $separator . implode($separator, $dirStack);
    }

    /**
     * Check whether path is in root path
     *
     * @static
     * @access  public
     * @return  bool
     * @param   string  $path
     * @param   string  $root
     */
    function pathInRoot($path, $root)
    {
        static $realPaths = array();

        if (!isset($realPaths[$root])) {
            $realPaths[$root] = File_Util::realPath($root);
        }

        return false !== strstr(File_Util::realPath($path), $realPaths[$root]);
    }

    /**
     * List Directory
     *
     * The final argument, $cb, is a callback that either evaluates to true or
     * false and performs a filter operation, or it can also modify the
     * directory/file names returned.  To achieve the latter effect use as
     * follows:
     *
     * <code>
     * <?php
     * function uc(&$filename) {
     *     $filename = strtoupper($filename);
     *     return true;
     * }
     * $entries = File_Util::listDir('.', FILE_LIST_ALL, FILE_SORT_NONE, 'uc');
     * foreach ($entries as $e) {
     *     echo $e->name, "\n";
     * }
     * ?>
     * </code>
     *
     * @static
     * @access  public
     * @return  array
     * @param   string  $path
     * @param   int     $list
     * @param   int     $sort
     * @param   mixed   $cb
     */
    function listDir($path, $list = FILE_LIST_ALL, $sort = FILE_SORT_NONE, $cb = null)
    {
        if (!strlen($path) || !is_dir($path)) {
            return null;
        }

        $entries = array();
        for ($dir = dir($path); false !== $entry = $dir->read(); ) {
            if ($list & FILE_LIST_DOTS || $entry{0} !== '.') {
                $isRef = ($entry === '.' || $entry === '..');
                $isDir = $isRef || is_dir($path .'/'. $entry);
                if (    ((!$isDir && $list & FILE_LIST_FILES)   ||
                         ($isDir  && $list & FILE_LIST_DIRS))   &&
                        (!is_callable($cb) ||
                            call_user_func_array($cb, array(&$entry)))) {
                    $entries[] = (object) array(
                        'name'  => $entry,
                        'size'  => $isDir ? null : filesize($path .'/'. $entry),
                        'date'  => filemtime($path .'/'. $entry),
                    );
                }
            }
        }
        $dir->close();

        if ($sort) {
            $entries = File_Util::sortFiles($entries, $sort);
        }

        return $entries;
    }

    /**
     * Sort Files
     *
     * @static
     * @access  public
     * @return  array
     * @param   array   $files
     * @param   int     $sort
     */
    function sortFiles($files, $sort)
    {
        if (!$files) {
            return array();
        }

        if (!$sort) {
            return $files;
        }

        if ($sort === 1) {
            return array_reverse($files);
        }

        if ($sort & FILE_SORT_RANDOM) {
            shuffle($files);
            return $files;
        }

        $names = array();
        $sizes = array();
        $dates = array();

        if ($sort & FILE_SORT_NAME) {
            $r = &$names;
        } elseif ($sort & FILE_SORT_DATE) {
            $r = &$dates;
        } elseif ($sort & FILE_SORT_SIZE) {
            $r = &$sizes;
        } else {
            asort($files, SORT_REGULAR);
            return $files;
        }

        $sortFlags = array(
            FILE_SORT_NAME => SORT_STRING,
            FILE_SORT_DATE => SORT_NUMERIC,
            FILE_SORT_SIZE => SORT_NUMERIC,
        );

        foreach ($files as $file) {
            $names[] = $file->name;
            $sizes[] = $file->size;
            $dates[] = $file->date;
        }

        if ($sort & FILE_SORT_REVERSE) {
            arsort($r, $sortFlags[$sort & ~1]);
        } else {
            asort($r, $sortFlags[$sort]);
        }

        $result = array();
        foreach ($r as $i => $f) {
            $result[] = $files[$i];
        }

        return $result;
    }

    /**
     * Switch File Extension
     *
     * @static
     * @access  public
     * @return  string|array
     * @param   string|array    $filename
     * @param   string          $to new file extension
     * @param   string          $from change only files with this extension
     * @param   bool            $reverse change only files not having $from extension
     */
    function switchExt($filename, $to, $from = null, $reverse = false)
    {
        if (is_array($filename)) {
            foreach ($filename as $key => $file) {
                $filename[$key] = File_Util::switchExt($file, $to, $from);
            }
            return $filename;
        }

        if ($len = strlen($from)) {
            $ext = substr($filename, -$len - 1);
            $cfn = FILE_WIN32 ? 'strcasecmp' : 'strcmp';
            if (!$reverse == $cfn($ext, '.'. $from)) {
                return $filename;
            }
            return substr($filename, 0, -$len - 1) .'.'. $to;
        }

        if ($pos = strpos($filename, '.')) {
            return substr($filename, 0, $pos) .'.'. $to;
        }

        return $filename .'.'. $to;
    }
}

?>
