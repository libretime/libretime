<?php

// Do not allow remote execution
$arr = array_diff_assoc($_SERVER, $_ENV);
if (isset($arr["DOCUMENT_ROOT"]) && ($arr["DOCUMENT_ROOT"] != "") ) {
    header("HTTP/1.1 400");
    header("Content-type: text/plain; charset=UTF-8");
    echo "400 Not executable\r\n";
    exit(1);
}

set_include_path('/var/www/airtime/library' . PATH_SEPARATOR . get_include_path());
set_include_path('/var/www/airtime/application/models' . PATH_SEPARATOR . get_include_path());
require_once('/var/www/airtime/application/configs/conf.php');
require_once('/var/www/airtime/application/models/StoredFile.php');
require_once('DB.php');
require_once 'propel/runtime/lib/Propel.php';
Propel::init('/var/www/airtime/application/configs/airtime-conf.php');


/**
 *
 * Look through all the files in the database and remove the rows
 * that have no associated file.
 *
 * @return int
 * 		The total number of files that were missing.
 */
function airtime_clean_files() {
    $count = 0;
    $files = StoredFile::GetAll();
    foreach ($files as $file) {
        if (($file["ftype"] == "audioclip") && !@file_exists($file["filepath"])) {
            echo " * Removing metadata for id ".$file["id"].":".PHP_EOL;
            echo "   * File path: ".$file["filepath"].PHP_EOL;
            echo "   * Track title: ".$file["track_title"].PHP_EOL;
            echo "   * Artist: ".$file["artist_name"].PHP_EOL;
            echo "   * Album: ".$file["album_title"].PHP_EOL;
            StoredFile::deleteById($file["id"]);
            $count++;
        }
    }
    return $count;
}

function airtime_empty_db($db)
{
    global $CC_CONFIG, $CC_DBC;

    // NOTE: order matter here.
    echo " * Clearing schedule table...".PHP_EOL;
    Schedule::deleteAll();

    // Ugly hack
    echo " * Resetting show instance times to zero...".PHP_EOL;
    $sql = "UPDATE cc_show_instances SET time_filled='00:00:00'";
    $CC_DBC->query($sql);

    echo " * Clearing playlist table...".PHP_EOL;
    Playlist::deleteAll();

    echo " * Clearing files table...".PHP_EOL;
    $result = StoredFile::deleteAll();
    if (PEAR::isError($result)) {
        echo $result->getMessage().PHP_EOL;
    }
}


global $CC_CONFIG;

require_once('Zend/Loader/Autoloader.php');
$autoloader = Zend_Loader_Autoloader::getInstance();

try {
    $opts = new Zend_Console_Getopt(
        array(
            'help|h' => 'Displays usage information.',
            'clean|c' => 'Removes all audio file metadata from the database that does not have a matching file in the filesystem.',
            'empty|e' => 'Removes all files and playlists from Airtime.'
        )
    );
    $opts->parse();
} catch (Zend_Console_Getopt_Exception $e) {
    exit($e->getMessage() .PHP_EOL. $e->getUsageMessage());
}

if (isset($opts->h)) {
    echo PHP_EOL;
    echo $opts->getUsageMessage();
    echo "Storage directory: ". realpath($CC_CONFIG["storageDir"]).PHP_EOL.PHP_EOL;
    exit;
}

// Need to check that we are superuser before running this.
if (exec("whoami") != "root") {
    echo PHP_EOL."You must be root to use this script.".PHP_EOL.PHP_EOL;
    exit(1);
}

$CC_DBC = DB::connect($CC_CONFIG['dsn'], TRUE);
$CC_DBC->setFetchMode(DB_FETCHMODE_ASSOC);

if (isset($opts->e)) {
    echo PHP_EOL;
    airtime_empty_db($CC_DBC);
    echo "Done.".PHP_EOL.PHP_EOL;
} elseif (isset($opts->c)) {
    $count = airtime_clean_files($CC_CONFIG['storageDir']);
    if ($count == 0) {
        echo PHP_EOL."All file metadata in the database is linked to a real file.  Nothing to be done.".PHP_EOL.PHP_EOL;
    } else {
        echo PHP_EOL."Total rows removed: $count".PHP_EOL;
    }
} else {
    echo PHP_EOL;
    echo $opts->getUsageMessage();
    echo "Storage directory: ". realpath($CC_CONFIG["storageDir"]).PHP_EOL.PHP_EOL;
}
