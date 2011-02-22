<?php

// Do not allow remote execution
$arr = array_diff_assoc($_SERVER, $_ENV);
if (isset($arr["DOCUMENT_ROOT"]) && ($arr["DOCUMENT_ROOT"] != "") ) {
    header("HTTP/1.1 400");
    header("Content-type: text/plain; charset=UTF-8");
    echo "400 Not executable\r\n";
    exit(1);
}

require_once('../application/configs/conf.php');
require_once('../install/installInit.php');
require_once('../application/models/StoredFile.php');

function printUsage() {

    global $CC_CONFIG;

    echo "Usage:\n";
    echo "  ./CleanStor [OPTION] \n";
    echo "\n";
    echo "Options:\n";
    echo "  -c, --clean     Removes all broken links from the storage server\n";
   	echo "                 and empties all missing file information from the database.\n";
    echo "\n";
    echo "  -e, --empty     Removes all files from the storage server \n";
    echo "                 and empties all relevant information from the database.\n\n";
    echo "Storage server: ". $CC_CONFIG["storageDir"] ."\n\n\n";
}

function camp_clean_files($p_path) {
    if (!empty($p_path) && (strlen($p_path) > 4)) {
        list($dirList,$fileList) = File_Find::maptree($p_path);

        $array_mus;
        foreach ($fileList as $filepath) {

            if (@substr($filepath, strlen($filepath) - 3) != "xml") {
                $array_mus[] = $filepath;
            }
        }

        foreach ($array_mus as $audio_file) {

            if (@is_link($audio_file) && !@stat($audio_file)) {

                //filesystem clean up.
                @unlink($audio_file);
                echo "unlinked $audio_file\n";
                @unlink($audio_file . ".xml");
                echo "unlinked " . $audio_file . ".xml\n";
                @rmdir(@dirname($audio_file));
                echo "removed dir " . @dirname($audio_file) . "\n";

                //database clean up.
                $stored_audio_file = StoredFile::RecallByGunid(@basename($audio_file));
                $stored_audio_file->delete();
            }
        }

    }
}

function camp_remove_files($p_path) {

    if (!empty($p_path) && (strlen($p_path) > 4)) {
        list($dirList,$fileList) = File_Find::maptree($p_path);

        foreach ($fileList as $filepath) {
            echo " * Removing $filepath\n";
            @unlink($filepath);
           	echo "done.\n";
        }
        foreach ($dirList as $dirpath) {
            echo " * Removing $dirpath\n";
            @rmdir($dirpath);
           	echo "done.\n";
        }
    }
}

function camp_empty_db($db) {
    global $CC_CONFIG;

    if (!PEAR::isError($db)) {
        if (camp_db_table_exists($CC_CONFIG['filesTable'])) {
            echo " * Deleting from database table ".$CC_CONFIG['filesTable']."\n";
            $sql = "DELETE FROM ".$CC_CONFIG['filesTable'];
            camp_install_query($sql, false);
        }
        else {
            echo " * Skipping: database table ".$CC_CONFIG['filesTable']."\n";
        }
    }
}


global $CC_CONFIG;

$CC_DBC = DB::connect($CC_CONFIG['dsn'], TRUE);
$CC_DBC->setFetchMode(DB_FETCHMODE_ASSOC);

if ($argc != 2){
    printUsage();
    exit(1);
}

switch($argv[1]){

    case '-e':
    case '--empty':
        camp_empty_db($CC_DBC);
        camp_remove_files($CC_CONFIG['storageDir']);
        break;
    case '-c':
    case '--clean':
        camp_clean_files($CC_CONFIG['storageDir']);
        break;
    default:
        printUsage();

}

