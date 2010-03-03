<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * File
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
 * @author      Richard Heyes <richard@php.net>
 * @author      Tal Peer <tal@php.net>
 * @author      Michael Wallner <mike@php.net>
 * @copyright   2002-2005 The Authors
 * @license     http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version     CVS: $Id: File.php,v 1.38 2007/03/24 16:38:56 dufuz Exp $
 * @link        http://pear.php.net/package/File
 */

/**
 * Requires PEAR
 */
require_once 'PEAR.php';

/**
 * The default number of bytes for reading
 */
if (!defined('FILE_DEFAULT_READSIZE')) {
    define('FILE_DEFAULT_READSIZE', 1024, true);
}

/**
 * The maximum number of bytes for reading lines
 */
if (!defined('FILE_MAX_LINE_READSIZE')) {
    define('FILE_MAX_LINE_READSIZE', 40960, true);
}

/**
 * Whether file locks should block
 */
if (!defined('FILE_LOCKS_BLOCK')) {
    define('FILE_LOCKS_BLOCK', true, true);
}

/**
 * Mode to use for reading from files
 */
define('FILE_MODE_READ', 'rb', true);

/**
 * Mode to use for truncating files, then writing
 */
define('FILE_MODE_WRITE', 'wb', true);

/**
 * Mode to use for appending to files
 */
define('FILE_MODE_APPEND', 'ab', true);

/**
 * Use this when a shared (read) lock is required
 */
define('FILE_LOCK_SHARED', LOCK_SH | (FILE_LOCKS_BLOCK ? 0 : LOCK_NB), true);

/**
 * Use this when an exclusive (write) lock is required
 */
define('FILE_LOCK_EXCLUSIVE', LOCK_EX | (FILE_LOCKS_BLOCK ? 0 : LOCK_NB), true);

/**
 * Class for handling files
 *
 * A class with common functions for writing,
 * reading and handling files and directories
 *
 * @author  Richard Heyes <richard@php.net>
 * @author  Tal Peer <tal@php.net>
 * @author  Michael Wallner <mike@php.net>
 * @access  public
 * @package File
 *
 * @static
 */
class File extends PEAR
{
    /**
     * Destructor
     *
     * Unlocks any locked file pointers and closes all filepointers
     *
     * @access private
     */
    function _File()
    {
        File::closeAll();
    }

    /**
     * Handles file pointers. If a file pointer needs to be opened,
     * it will be. If it already exists (based on filename and mode)
     * then the existing one will be returned.
     *
     * @access  private
     * @param   string  $filename Filename to be used
     * @param   string  $mode Mode to open the file in
     * @param   mixed   $lock Type of lock to use
     * @return  mixed   PEAR_Error on error or file pointer resource on success
     */
    function _getFilePointer($filename, $mode, $lock = false)
    {
        $filePointers = &PEAR::getStaticProperty('File', 'filePointers');

        // Win32 is case-insensitive
        if (OS_WINDOWS) {
            $filename = strtolower($filename);
        }

        // check if file pointer already exists
        if (!isset($filePointers[$filename][$mode]) ||
            !is_resource($filePointers[$filename][$mode])) {

            // check if we can open the file in the desired mode
            switch ($mode)
            {
                case FILE_MODE_READ:
                    if (!preg_match('/^.+(?<!file):\/\//i', $filename) &&
                        !file_exists($filename)) {
                        return PEAR::raiseError("File does not exist: $filename");
                    }
                break;

                case FILE_MODE_APPEND:
                case FILE_MODE_WRITE:
                    if (file_exists($filename)) {
                        if (!is_writable($filename)) {
                            return PEAR::raiseError("File is not writable: $filename");
                        }
                    } elseif (!is_writable($dir = dirname($filename))) {
                        return PEAR::raiseError("Cannot create file in directory: $dir");
                    }
                break;

                default:
                    return PEAR::raiseError("Invalid access mode: $mode");
            }

            // open file
            $filePointers[$filename][$mode] = @fopen($filename, $mode);
            if (!is_resource($filePointers[$filename][$mode])) {
                return PEAR::raiseError('Failed to open file: ' . $filename);
            }
        }

        // lock file
        if ($lock) {
            $lock = $mode == FILE_MODE_READ ? FILE_LOCK_SHARED : FILE_LOCK_EXCLUSIVE;
            $locks = &PEAR::getStaticProperty('File', 'locks');
            if (@flock($filePointers[$filename][$mode], $lock)) {
                $locks[] = &$filePointers[$filename][$mode];
            } elseif (FILE_LOCKS_BLOCK) {
                return PEAR::raiseError("File already locked: $filename");
            } else {
                return PEAR::raiseError("Could not lock file: $filename");
            }
        }

        return $filePointers[$filename][$mode];
    }

    /**
     * Reads an entire file and returns it.
     * Uses file_get_contents if available.
     *
     * @access  public
     * @param   string  $filename Name of file to read from
     * @param   mixed   $lock Type of lock to use
     * @return  mixed   PEAR_Error if an error has occured or a string with the contents of the the file
     */
    function readAll($filename, $lock = false)
    {
        if (false === $file = @file_get_contents($filename)) {
            return PEAR::raiseError("Cannot read file: $filename");
        }
        return $file;
    }

    /**
     * Returns a specified number of bytes of a file.
     * Defaults to FILE_DEFAULT_READSIZE.  If $size is 0, all file will be read.
     *
     * @access  public
     * @param   string  $filename Name of file to read from
     * @param   integer $size Bytes to read
     * @param   mixed   $lock Type of lock to use
     * @return  mixed   PEAR_Error on error or a string which contains the data read
     *                  Will also return false upon EOF
     */
    function read($filename, $size = FILE_DEFAULT_READSIZE, $lock = false)
    {
        static $filePointers;

        if ($size == 0) {
            return File::readAll($filename, $lock);
        }

        if (!isset($filePointers[$filename]) ||
            !is_resource($filePointers[$filename])) {
            $fp = File::_getFilePointer($filename, FILE_MODE_READ, $lock);
            if (PEAR::isError($fp)) {
                return $fp;
            }

            $filePointers[$filename] = $fp;
        } else {
            $fp = $filePointers[$filename];
        }

        return !feof($fp) ? fread($fp, $size) : false;
    }

    /**
     * Writes the given data to the given filename.
     * Defaults to no lock, append mode.
     *
     * @access  public
     * @param   string  $filename Name of file to write to
     * @param   string  $data Data to write to file
     * @param   string  $mode Mode to open file in
     * @param   mixed   $lock Type of lock to use
     * @return  mixed   PEAR_Error on error or number of bytes written to file.
     */
    function write($filename, $data, $mode = FILE_MODE_APPEND, $lock = false)
    {
        $fp = File::_getFilePointer($filename, $mode, $lock);
        if (PEAR::isError($fp)) {
            return $fp;
        }

        if (false === $bytes = @fwrite($fp, $data, strlen($data))) {
            return PEAR::raiseError("Cannot write data: '$data' to file: '$filename'");
        }

        return $bytes;
    }

    /**
     * Reads and returns a single character from given filename
     *
     * @access  public
     * @param   string  $filename Name of file to read from
     * @param   mixed   $lock Type of lock to use
     * @return  mixed   PEAR_Error on error or one character of the specified file
     */
    function readChar($filename, $lock = false)
    {
        return File::read($filename, 1, $lock);
    }

    /**
     * Writes a single character to a file
     *
     * @access  public
     * @param   string  $filename Name of file to write to
     * @param   string  $char Character to write
     * @param   string  $mode Mode to use when writing
     * @param   mixed   $lock Type of lock to use
     * @return  mixed   PEAR_Error on error, or 1 on success
     */
    function writeChar($filename, $char, $mode = FILE_MODE_APPEND, $lock = false)
    {
        $fp = File::_getFilePointer($filename, $mode, $lock);
        if (PEAR::isError($fp)) {
            return $fp;
        }

        if (false === @fwrite($fp, $char, 1)) {
            return PEAR::raiseError("Cannot write data: '$data' to file: '$filename'");
        }

        return 1;
    }

    /**
     * Returns a line of the file (without trailing CRLF).
     * Maximum read line length is FILE_MAX_LINE_READSIZE.
     *
     * @access  public
     * @param   string  $filename Name of file to read from
     * @param   boolean $lock Whether file should be locked
     * @return  mixed   PEAR_Error on error or a string containing the line read from file
     */
    function readLine($filename, $lock = false)
    {
        static $filePointers; // Used to prevent unnecessary calls to _getFilePointer()

        if (!isset($filePointers[$filename]) ||
            !is_resource($filePointers[$filename])) {
            $fp = File::_getFilePointer($filename, FILE_MODE_READ, $lock);
            if (PEAR::isError($fp)) {
                return $fp;
            }

            $filePointers[$filename] = $fp;
        } else {
            $fp = $filePointers[$filename];
        }

        if (feof($fp)) {
            return false;
        }

        return rtrim(fgets($fp, FILE_MAX_LINE_READSIZE), "\r\n");
    }

    /**
     * Writes a single line, appending a LF (by default)
     *
     * @access  public
     * @param   string  $filename Name of file to write to
     * @param   string  $line Line of data to be written to file
     * @param   string  $mode Write mode, can be either FILE_MODE_WRITE or FILE_MODE_APPEND
     * @param   string  $crlf The CRLF your system is using. UNIX = \n Windows = \r\n Mac = \r
     * @param   mixed   $lock Whether to lock the file
     * @return  mixed   PEAR_Error on error or number of bytes written to file (including appended crlf)
     */
    function writeLine($filename, $line, $mode = FILE_MODE_APPEND, $crlf = "\n", $lock = false)
    {
        $fp = File::_getFilePointer($filename, $mode, $lock);
        if (PEAR::isError($fp)) {
            return $fp;
        }

        if (false === $bytes = fwrite($fp, $line . $crlf)) {
            return PEAR::raiseError("Cannot write data: '$data' to file: '$file'");
        }

        return $bytes;
    }

    /**
     * This rewinds a filepointer to the start of a file
     *
     * @access  public
     * @param   string  $filename The filename
     * @param   string  $mode Mode the file was opened in
     * @return  mixed   PEAR Error on error, true on success
     */
    function rewind($filename, $mode)
    {
        $fp = File::_getFilePointer($filename, $mode);
        if (PEAR::isError($fp)) {
            return $fp;
        }

        if (!@rewind($fp)) {
            return PEAR::raiseError("Cannot rewind file: $filename");
        }

        return true;
    }

    /**
     * Closes all open file pointers
     *
     * @access  public
     * @return  void
     */
    function closeAll()
    {
        $locks = &PEAR::getStaticProperty('File', 'locks');
        $filePointers = &PEAR::getStaticProperty('File', 'filePointers');

        // unlock files
        for ($i = 0, $c = count($locks); $i < $c; $i++) {
            is_resource($locks[$i]) and @flock($locks[$i], LOCK_UN);
        }

        // close files
        if (!empty($filePointers)) {
            foreach ($filePointers as $fname => $modes) {
                foreach (array_keys($modes) as $mode) {
                    if (is_resource($filePointers[$fname][$mode])) {
                        @fclose($filePointers[$fname][$mode]);
                    }
                    unset($filePointers[$fname][$mode]);
                }
            }
        }
    }

    /**
     * This closes an open file pointer
     *
     * @access  public
     * @param   string  $filename The filename that was opened
     * @param   string  $mode Mode the file was opened in
     * @return  mixed   PEAR Error on error, true otherwise
     */
    function close($filename, $mode)
    {
        $filePointers = &PEAR::getStaticProperty('File', 'filePointers');

        if (OS_WINDOWS) {
            $filename = strToLower($filename);
        }

        if (!isset($filePointers[$filename][$mode])) {
            return true;
        }

        $fp = $filePointers[$filename][$mode];
        unset($filePointers[$filename][$mode]);

        if (is_resource($fp)) {
            // unlock file
            @flock($fp, LOCK_UN);
            // close file
            if (!@fclose($fp)) {
                return PEAR::raiseError("Cannot close file: $filename");
            }
        }

        return true;
    }

    /**
     * This unlocks a locked file pointer.
     *
     * @access  public
     * @param   string  $filename The filename that was opened
     * @param   string  $mode Mode the file was opened in
     * @return  mixed   PEAR Error on error, true otherwise
     */
    function unlock($filename, $mode)
    {
        $fp = File::_getFilePointer($filename, $mode);
        if (PEAR::isError($fp)) {
            return $fp;
        }

        if (!@flock($fp, LOCK_UN)) {
            return PEAR::raiseError("Cacnnot unlock file: $filename");
        }

        return true;
    }

    /**
     * @deprecated
     */
    function stripTrailingSeparators($path, $separator = DIRECTORY_SEPARATOR)
    {
        if ($path === $separator) {
            return $path;
        }
        return rtrim($path, $separator);
    }

    /**
     * @deprecated
     */
    function stripLeadingSeparators($path, $separator = DIRECTORY_SEPARATOR)
    {
        if ($path === $separator) {
            return $path;
        }
        return ltrim($path, $separator);
    }

    /**
     * @deprecated Use File_Util::buildPath() instead.
     */
    function buildPath($parts, $separator = DIRECTORY_SEPARATOR)
    {
        require_once 'File/Util.php';
        return File_Util::buildPath($parts, $separator);
    }

    /**
     * @deprecated Use File_Util::skipRoot() instead.
     */
    function skipRoot($path)
    {
        require_once 'File/Util.php';
        return File_Util::skipRoot($path);
    }

    /**
     * @deprecated Use File_Util::tmpDir() instead.
     */
    function getTempDir()
    {
        require_once 'File/Util.php';
        return File_Util::tmpDir();
    }

    /**
     * @deprecated Use File_Util::tmpFile() instead.
     */
    function getTempFile($dirname = null)
    {
        require_once 'File/Util.php';
        return File_Util::tmpFile($dirname);
    }

    /**
     * @deprecated Use File_Util::isAbsolute() instead.
     */
    function isAbsolute($path)
    {
        require_once 'File/Util.php';
        return File_Util::isAbsolute($path);
    }

    /**
     * @deprecated Use File_Util::relativePath() instead.
     */
    function relativePath($path, $root, $separator = DIRECTORY_SEPARATOR)
    {
        require_once 'File/Util.php';
        return File_Util::relativePath($path, $root, $separator);
    }

    /**
     * @deprecated Use File_Util::realpath() instead.
     */
    function realpath($path, $separator = DIRECTORY_SEPARATOR)
    {
        require_once 'File/Util.php';
        return File_Util::realpath($path, $separator);
    }
}

PEAR::registerShutdownFunc(array('File', '_File'));

?>
