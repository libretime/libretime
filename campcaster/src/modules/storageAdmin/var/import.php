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
ini_set('memory_limit', '64M');
set_time_limit(0);
error_reporting(E_ALL);

require_once('conf.php');
require_once("$STORAGE_SERVER_PATH/var/conf.php");
require_once('DB.php');
require_once("$STORAGE_SERVER_PATH/var/GreenBox.php");
require_once('Console/Getopt.php');
require_once('File/Find.php');

function printUsage()
{
    echo "Parameters:\n";
    echo "\n";
    echo "  -t, --test          Test only - do not import, show analyze\n";
    echo "  -h, --help          Print this message and exit.\n";
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
 * @param GreenBox $p_greenbox
 * @param int $p_parentId
 * @param boolean $p_testOnly
 *
 * @return int
 */
function camp_import_audio_file($p_filepath, $p_greenbox, $p_parentId,
    $p_testOnly = false)
{
    global $STORAGE_SERVER_PATH;
    $fileCount = 0;
    $p_filepath = realpath(rtrim($p_filepath));

    if (!file_exists($p_filepath)) {
        echo " * WARNING: File does not exist: $p_filepath\n";
        return 0;
    }

    if (is_dir($p_filepath)) {
        list(,$fileList) = File_Find::maptree($p_filepath);
        foreach ($fileList as $tmpFile) {
            $fileCount += camp_import_audio_file($tmpFile, $p_greenbox, $p_parentId, $p_testOnly);
        }
        return $fileCount;
    }

    // Check for non-supported file type
    if (!preg_match('/\.(ogg|mp3)$/i', $p_filepath, $var)) {
        //echo " * WARNING: File extension not supported - skipping file: $p_filepath\n";
        return 0;
    }
    echo "Importing: $p_filepath\n";

    $md5sum = md5_file($p_filepath);

    // Look up md5sum in database
    $duplicate = StoredFile::RecallByMd5($md5sum);
    if ($duplicate) {
        echo " * File already exists in the database.\n";
        return 0;
    }
    $metadata = camp_get_audio_metadata($p_filepath, $p_testOnly);
    if (PEAR::isError($metadata)) {
    	import_err($metadata);
    	return 0;
    }
    unset($metadata['audio']);
    unset($metadata['playtime_seconds']);

    if (!$p_testOnly) {
        $r = $p_greenbox->bsPutFile($p_parentId, $metadata['ls:filename'], "$p_filepath", "$STORAGE_SERVER_PATH/var/emptyMdata.xml", NULL, 'audioclip', 'file', FALSE);
        if (PEAR::isError($r)) {
        	import_err($r, "Error in bsPutFile()");
        	echo var_export($metadata)."\n";
        	return 0;
        }
        $id = $r;

        $r = $p_greenbox->bsSetMetadataBatch($id, $metadata);
        if (PEAR::isError($r)) {
        	import_err($r, "Error in bsSetMetadataBatch()");
        	echo var_export($metadata)."\n";
        	return 0;
        }
    } else {
        var_dump($infoFromFile);
        echo "======================= ";
        var_dump($metadata);
        echo "======================= ";
    }

    echo " * OK\n";
    $fileCount++;
    return $fileCount;
}

echo "========================\n";
echo "Campcaster Import Script\n";
echo "========================\n";
$g_errors = 0;

$start = intval(date('U'));

PEAR::setErrorHandling(PEAR_ERROR_RETURN);
$CC_DBC = DB::connect($CC_CONFIG['dsn'], TRUE);
if (PEAR::isError($CC_DBC)) {
	echo "ERROR: ".$CC_DBC->getMessage()." ".$CC_DBC->getUserInfo()."\n";
	exit(1);
}
$CC_DBC->setFetchMode(DB_FETCHMODE_ASSOC);

$parsedCommandLine = Console_Getopt::getopt($argv, "th", array("test", "help"));
$cmdLineOptions = $parsedCommandLine[0];
if (count($parsedCommandLine[1]) == 0) {
    printUsage();
    exit;
}

//print_r($parsedCommandLine);
$files = $parsedCommandLine[1];

$testonly = FALSE;
foreach ($cmdLineOptions as $tmpValue) {
    $optionName = $tmpValue[0];
    $optionValue = $tmpValue[1];
    switch ($optionName) {
        case "h":
        case '--help':
            printUsage();
            exit;
        case "t":
        case "--test":
            $testonly = TRUE;
            break;
    }
}

$greenbox = new GreenBox();
$parentId = M2tree::GetObjId(M2tree::GetRootNode());

$filecount = 0;
//print_r($files);
//echo "\n";
if (is_array($files)) {
    foreach ($files as $filepath) {
        $filecount += camp_import_audio_file($filepath, $greenbox, $parentId, $testonly);
    }
}
$end = intval(date('U'));
$time = $end - $start;
if ($time > 0) {
	$speed = round(($filecount+$g_errors)/$time, 1);
} else {
	$speed = "N/A";
}

echo "==========================================================================\n";
echo " * Files ".($testonly ? "analyzed" : "imported").": $filecount in $time s = $speed files/second, errors: $g_errors\n";

echo " * Import completed.\n";

?>