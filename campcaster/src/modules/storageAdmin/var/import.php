<?php
/**
 * @author $Author$
 * @version $Revision$
 */
ini_set('memory_limit', '64M');
set_time_limit(30);
header("Content-type: text/plain");
echo "\n#StorageServer import script:\n";
//echo date('H:i:s')."\n";
$start = intval(date('U'));

require_once('conf.php');
require_once("$storageServerPath/var/conf.php");
require_once('DB.php');
require_once("$storageServerPath/var/GreenBox.php");

//PEAR::setErrorHandling(PEAR_ERROR_PRINT, "%s<hr>\n");
PEAR::setErrorHandling(PEAR_ERROR_RETURN);
//PEAR::setErrorHandling(PEAR_ERROR_DIE, "%s\n");
$CC_DBC = DB::connect($CC_CONFIG['dsn'], TRUE);
if (PEAR::isError($CC_DBC)) {
	echo "ERROR: ".$CC_DBC->getMessage()." ".$CC_DBC->getUserInfo()."\n";
	exit(1);
}
$CC_DBC->setFetchMode(DB_FETCHMODE_ASSOC);
$gb = new GreenBox();

$testonly = (isset($argv[1]) && $argv[1] == '-t');

$g_errors = 0;
$filecount = 0;

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


$r = M2tree::GetObjId('import', $gb->storId);
if (PEAR::isError($r)) {
	echo "ERROR: ".$r->getMessage()." ".$r->getUserInfo()."\n";
	exit(1);
}
if (is_null($r)) {
    $r = $gb->bsCreateFolder($gb->storId, 'import');
    if (PEAR::isError($r)) {
    	echo "ERROR: ".$r->getMessage()." ".$r->getUserInfo()."\n";
    	exit(1);
    }
}
$parid = $r;


$stdin = fopen('php://stdin', 'r');
while ($filename = fgets($stdin, 2048)) {
    $filename = rtrim($filename);
    if (!preg_match('/\.(ogg|wav|mp3|mpg|mpeg)$/', strtolower($filename), $var)) {
        // echo "File extension not supported - skipping file\n";
        continue;
    }
    echo "$filename:   ";

    $mdata = camp_get_audio_metadata($filename, $testonly);
    if (PEAR::isError($mdata)) {
    	import_err($mdata);
    	continue;
    }
    unset($mdata['audio']);
    unset($mdata['playtime_seconds']);

    if (!$testonly) {
        $r = $gb->bsPutFile($parid, $mdata['ls:filename'], "$filename", "$storageServerPath/var/emptyMdata.xml", NULL, 'audioclip');
        if (PEAR::isError($r)) {
        	import_err($r, "Error in bsPutFile()");
        	echo var_export($mdata)."\n";
        	continue;
        }
        $id = $r;

        $r = $gb->bsSetMetadataBatch($id, $mdata);
        if (PEAR::isError($r)) {
        	import_err($r, "Error in bsSetMetadataBatch()");
        	echo var_export($mdata)."\n";
        	continue;
        }
    } else {
        var_dump($infoFromFile);
        echo "======================= ";
        var_dump($mdata);
        echo "======================= ";
    }

    echo "OK\n";
    $filecount++;
}

fclose($stdin);
$end = intval(date('U'));
//echo date('H:i:s')."\n";
$time = $end - $start;
if ($time > 0) {
	$speed = round(($filecount+$g_errors)/$time, 1);
} else {
	$speed = "N/A";
}
echo " Files ".($testonly ? "analyzed" : "imported").": $filecount, in $time s, $speed files/s, errors: $g_errors\n";
?>