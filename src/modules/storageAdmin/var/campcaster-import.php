<?php
/**
 * Mass import of audio files.
 *
 * @author Tomas Hlava <th@red2head.com>
 * @author Paul Baranowski <paul@paulbaranowski.org>
 * @version $Revision$
 * @package Campcaster
 * @subpackage StorageAdmin
 * @copyright 2006 MDLF, Inc.
 * @license http://www.gnu.org/licenses/gpl.txt
 * @link http://www.campware.org
 */
ini_set('memory_limit', '128M');
set_time_limit(0);
error_reporting(E_ALL);
set_error_handler("camp_import_error_handler", E_ALL & !E_NOTICE);

require_once('conf.php');
require_once("$STORAGE_SERVER_PATH/var/conf.php");
require_once('DB.php');
require_once("$STORAGE_SERVER_PATH/var/GreenBox.php");
require_once('Console/Getopt.php');

function camp_import_error_handler()
{
    echo var_dump(debug_backtrace());
    exit();
}


function printUsage()
{
    echo "There are two ways to import audio files into Campcaster: linking\n";
    echo "or copying.\n";
    echo "\n";
    echo "Linking has the advantage that it will not duplicate any files,\n";
    echo "but you must take care not to move, rename, or delete any of the\n";
    echo "imported files from their current locations on disk.\n";
    echo "\n";
    echo "Copying has the advantage that you can do whatever you like with\n";
    echo "the source files after you import them, but has the disadvantage\n";
    echo "that it requires doubling the hard drive space needed to store\n";
    echo "your files.\n";
    echo "\n";
    echo "Usage:\n";
    echo "  campcaster-import [OPTIONS] FILES_OR_DIRS\n";
    echo "\n";
    echo "Options:\n";
    echo "  -l, --link     Link to specified files.\n";
    echo "                 Saves storage space, but you cannot move, delete,\n";
    echo "                 or rename the original files, otherwise there will\n";
    echo "                 be dead air when Campcaster tries to play the file.\n";
    echo "\n";
    echo "  -c, --copy     Copy the specified files.\n";
    echo "                 This is useful if you are importing from removable media.\n";
    echo "                 If you are importing files on your hard drive, this will\n";
    echo "                 double the disk space required.\n";
    echo "\n";
    echo "  -h, --help     Print this message and exit.\n";
    echo "\n";
}


/**
 * Print error to the screen and keep a count of number of errors.
 *
 * @param PEAR_Error $pearErrorObj
 * @param string $txt
 */
function import_err($p_pearErrorObj, $txt='')
{
    global $g_errors;
    if (PEAR::isError($p_pearErrorObj)) {
    	$msg = $p_pearErrorObj->getMessage()." ".$p_pearErrorObj->getUserInfo();
    }
    echo "\nERROR: $msg\n";
    if (!empty($txt)) {
    	echo "ERROR: $txt\n";
    }
    $g_errors++;
}

/**
 * Import a file or directory into the storage database.
 * If it is a directory, files will be imported recursively.
 *
 * @param string $p_filepath
 *      You can pass in a directory or file name.
 *      This must be the full absolute path to the file, not relative.
 * @param string $p_importMode
 * @param boolean $p_testOnly
 *
 * @return int
 */
function camp_import_audio_file($p_filepath, $p_importMode = null, $p_testOnly = false)
{
    global $STORAGE_SERVER_PATH;
    global $g_fileCount;
    global $g_duplicates;

    // Check parameters
    $p_importMode = strtolower($p_importMode);
    if (!in_array($p_importMode, array("copy", "link"))) {
        return;
    }

    $greenbox = new GreenBox();
    $parentId = M2tree::GetObjId(M2tree::GetRootNode());

    $fileCount = 0;
    $duplicates = 0;

    if (!file_exists($p_filepath)) {
        echo " * WARNING: File does not exist: $p_filepath\n";
        return;
    }

    //echo "Memory usage:".memory_get_usage()."\n";

    // If we are given a directory, get all the files recursively and
    // call this function for each file.
    if (is_dir($p_filepath)) {
        // NOTE: this method of using opendir() is used over other
        // techniques because of its low memory usage.  Both PEAR's
        // File_Find and PHP5's built-in RecursiveDirectoryIterator
        // use about 5500 bytes per file, while this method uses
        // about 1100 bytes per file.
        $d = opendir($p_filepath);
        while (false !== ($file = readdir($d))) {
            if ($file != "." && $file != "..") {
                $path = "$p_filepath/$file";
                camp_import_audio_file($path, $p_importMode, $p_testOnly);
            }
        }
        closedir($d);
        return;
    }

    // Check for non-supported file type
    if (!preg_match('/\.(ogg|mp3)$/i', $p_filepath, $var)) {
        echo "IGNORED: $p_filepath\n";
        //echo " * WARNING: File extension not supported - skipping file: $p_filepath\n";
        return;
    }

    // Set the file permissions to be world-readable
    if ($p_importMode == "link") {
        // Check current file permissions
        $fileperms = fileperms($p_filepath);
        // Explaination of 0x0124:
        // 1 => owner readable
        // 2 => group readable
        // 4 => world readable
        // (see: http://php.net/manual/en/function.fileperms.php)
        $readableByAll = !(($fileperms & 0x0124) ^ 0x0124);
        if (!$readableByAll) {
            $permError = false;
            // Check if we have the ability to change the perms
            if (is_writable($p_filepath)) {
                // Change the file perms
                $fileperms = $fileperms | 0x0124;
                chmod($p_filepath, $fileperms);

                // Check that file perms were changed
                clearstatcache();
                $fileperms = fileperms($p_filepath);
                $readableByAll = !(($fileperms & 0x0124) ^ 0x124);
                if (!$readableByAll) {
                    $permError = true;
                }
            } else {
                $permError = true;
            }
            if ($permError) {
                global $g_errors;
                $g_errors++;
                echo "ERROR: $p_filepath\n"
                    ."       When importing with the '--link' option, files must be set\n"
                    ."       world-readable.  The file permissions for the file cannot be\n"
                    ."       changed.  Check that you are not trying to import from a FAT32\n"
                    ."       drive.  Otherwise, this problem might be fixed by running this \n"
                    ."       script with 'sudo'.\n";
                return;
            }
        }
    }

//    $timeBegin = microtime(true);
    $md5sum = md5_file($p_filepath);
//    $timeEnd = microtime(true);
//    echo " * MD5 time: ".($timeEnd-$timeBegin)."\n";

    // Look up md5sum in database
    $duplicate = StoredFile::RecallByMd5($md5sum);
    if ($duplicate) {
        echo "DUPLICATE: $p_filepath\n";
        $g_duplicates++;
        return;
    }

    echo "Importing: [".sprintf("%05d",$g_fileCount+1)."] $p_filepath\n";

    $metadata = camp_get_audio_metadata($p_filepath, $p_testOnly);
    if (PEAR::isError($metadata)) {
    	import_err($metadata);
    	return;
    }
    // bsSetMetadataBatch doesnt like these values
    unset($metadata['audio']);
    unset($metadata['playtime_seconds']);

    if (!$p_testOnly) {
        if ($p_importMode == "copy") {
            $doCopyFiles = true;
        } elseif ($p_importMode == "link") {
            $doCopyFiles = false;
        }
        $values = array(
            "filename" => $metadata['ls:filename'],
            "filepath" => $p_filepath,
            "metadata" => "$STORAGE_SERVER_PATH/var/emptyMdata.xml",
            "gunid" => NULL,
            "filetype" => "audioclip",
            "md5" => $md5sum,
            "mime" => $metadata['dc:format']
        );
//        $timeBegin = microtime(true);
        $storedFile = $greenbox->bsPutFile($parentId, $values, $doCopyFiles);
        if (PEAR::isError($storedFile)) {
        	import_err($storedFile, "Error in bsPutFile()");
        	echo var_export($metadata)."\n";
        	return;
        }
        $id = $storedFile->getId();
//        $timeEnd = microtime(true);
//        echo " * Store file time: ".($timeEnd-$timeBegin)."\n";

        // Note: the bsSetMetadataBatch() takes up .25 of a second
        // on my 3Ghz computer.  We should try to speed this up.
//        $timeBegin = microtime(true);
        $r = $greenbox->bsSetMetadataBatch($id, $metadata);
        if (PEAR::isError($r)) {
        	import_err($r, "Error in bsSetMetadataBatch()");
        	echo var_export($metadata)."\n";
        	return;
        }
//        $timeEnd = microtime(true);
//        echo " * Metadata store time: ".($timeEnd-$timeBegin)."\n";
    } else {
        echo "==========================================================================\n";
        echo "METADATA\n";
        var_dump($metadata);
    }

    $g_fileCount++;
    return;
}

$DEBUG_IMPORT = false;

echo "========================\n";
echo "Campcaster Import Script\n";
echo "========================\n";
$g_errors = 0;
//print_r($argv);
$start = intval(date('U'));

if ($DEBUG_IMPORT) {
    $testonly = false;
    $importMode = "link";
    $files = array("/home/paul/music/Tom Petty/Anthology - Through the Years disc 2/13 - Into The Great Wide Open.ogg");
    $dsn =  array('username' => 'test',
                  'password' => 'test',
                  'hostspec' => 'localhost',
                  'phptype' => 'pgsql',
                  'database' => 'Campcaster-paul');
} else {
    $dsn = $CC_CONFIG['dsn'];
}
//PEAR::setErrorHandling(PEAR_ERROR_RETURN);
PEAR::setErrorHandling(PEAR_ERROR_CALLBACK, "camp_import_error_handler");
$CC_DBC = DB::connect($dsn, TRUE);
if (PEAR::isError($CC_DBC)) {
	echo "ERROR: ".$CC_DBC->getMessage()." ".$CC_DBC->getUserInfo()."\n";
	exit(1);
}
$CC_DBC->setFetchMode(DB_FETCHMODE_ASSOC);

if (!$DEBUG_IMPORT) {
    $parsedCommandLine = Console_Getopt::getopt($argv, "thcld", array("test", "help", "copy", "link", "dir="));
    //print_r($parsedCommandLine);
    if (PEAR::isError($parsedCommandLine)) {
        printUsage();
        exit(1);
    }
    $cmdLineOptions = $parsedCommandLine[0];
    if (count($parsedCommandLine[1]) == 0) {
        printUsage();
        exit;
    }

    $files = $parsedCommandLine[1];

    $testonly = FALSE;
    $importMode = null;
    $currentDir = null;
    foreach ($cmdLineOptions as $tmpValue) {
        $optionName = $tmpValue[0];
        $optionValue = $tmpValue[1];
        switch ($optionName) {
            case "h":
            case '--help':
                printUsage();
                exit;
            case "c":
            case "--copy":
                $importMode = "copy";
                break;
            case 'l':
            case '--link':
                $importMode = "link";
                break;
            case '--dir':
                $currentDir = $optionValue;
                break;
            case "t":
            case "--test":
                $testonly = TRUE;
                break;
        }
    }
}

if (is_null($importMode)) {
    printUsage();
    exit(0);
}

global $g_fileCount;
global $g_duplicates;
if (is_array($files)) {
    foreach ($files as $filepath) {
        // absolute path
        if (($filepath[0] == "/") || ($filepath[0] == "~")) {
            $fullPath = realpath($filepath);
        } elseif (!is_null($currentDir)) {
            $fullPath = realpath("$currentDir/$filepath");
        } else {
            $fullPath = null;
        }

        if (empty($fullPath)) {
            echo "ERROR: I cant find the given file: $filepath\n\n";
            exit;
        }
        camp_import_audio_file($fullPath, $importMode, $testonly);
    }
}
$end = intval(date('U'));
$time = $end - $start;
if ($time > 0) {
	$speed = round(($g_fileCount+$g_duplicates)/$time, 1);
} else {
	$speed = ($g_fileCount+$g_duplicates);
}

echo "==========================================================================\n";
echo " *** Import mode: $importMode\n";
echo " *** Files imported: $g_fileCount\n";
echo " *** Duplicate files (not imported): $g_duplicates\n";
if ($g_errors > 0) {
    echo " *** Errors: $g_errors\n";
}
echo " *** Total: ".($g_fileCount+$g_duplicates)." files in $time seconds = $speed files/second.\n";
echo "==========================================================================\n";

?>