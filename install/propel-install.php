<?php
/**
 * @package Campcaster
 * @subpackage StorageServer
 * @copyright 2010 Sourcefabric O.P.S.
 * @license http://www.gnu.org/licenses/gpl.txt
 */

// Do not allow remote execution
$arr = array_diff_assoc($_SERVER, $_ENV);
if (isset($arr["DOCUMENT_ROOT"]) && ($arr["DOCUMENT_ROOT"] != "") ) {
    header("HTTP/1.1 400");
    header("Content-type: text/plain; charset=UTF-8");
    echo "400 Not executable\r\n";
    exit(1);
}

echo "******************************* Install Begin ********************************\n";

require_once(dirname(__FILE__).'/../application/configs/conf.php');
require_once(dirname(__FILE__).'/../application/models/GreenBox.php');
//require_once(dirname(__FILE__).'/../application/models/cron/Cron.php');
require_once(dirname(__FILE__)."/installInit.php");

// Need to check that we are superuser before running this.

echo " *** Database Installation ***\n";

//sudo -u postgres createuser --no-superuser --no-createdb --no-createrole -A -P myuser

// Create the database user
$command = "sudo -u postgres psql postgres --command \"CREATE USER {$CC_CONFIG['dsn']['username']} "
  ." ENCRYPTED PASSWORD '{$CC_CONFIG['dsn']['password']}' LOGIN CREATEDB NOCREATEUSER;\" 2>/dev/null";
//echo $command."\n";
@exec($command, $output, $results);
if ($results == 0) {
  echo "   * User {$CC_CONFIG['dsn']['username']} created.\n";
} else {
  echo "   * User {$CC_CONFIG['dsn']['username']} already exists.\n";
}

$command = "sudo -u postgres createdb {$CC_CONFIG['dsn']['database']} --owner {$CC_CONFIG['dsn']['username']} 2> /dev/null";
//echo $command."\n";
@exec($command, $output, $results);
if ($results == 0) {
  echo "   * Database '{$CC_CONFIG['dsn']['database']}' created.\n";
} else {
  echo "   * Database '{$CC_CONFIG['dsn']['database']}' already exists.\n";
}

// Connect to DB
campcaster_db_connect(true);

// Install postgres scripting language
$langIsInstalled = $CC_DBC->GetOne('SELECT COUNT(*) FROM pg_language WHERE lanname = \'plpgsql\'');
if ($langIsInstalled == '0') {
  echo " * Installing Postgres scripting language\n";
  $sql = "CREATE LANGUAGE 'plpgsql'";
  camp_install_query($sql, false);
} else {
  echo " * Postgres scripting language already installed\n";
}

echo " * Creating database tables\n";
// Put Propel sql files in Database
$command = __DIR__."/../library/propel/generator/bin/propel-gen ../build/ insert-sql";
//echo $command."\n";
@exec($command, $output, $results);


//------------------------------------------------------------------------
// Install default data
//------------------------------------------------------------------------
echo " *** Inserting Default Users ***\n";

// Add the "Station Preferences" group
if (!empty($CC_CONFIG['StationPrefsGr'])) {
    if (!Subjects::GetSubjId('scheduler')) {
        echo "   * Creating group '".$CC_CONFIG['StationPrefsGr']."'...";
        $stPrefGr = Subjects::AddSubj($CC_CONFIG['StationPrefsGr']);
        Subjects::AddSubjectToGroup('root', $CC_CONFIG['StationPrefsGr']);
        echo "done.\n";
    } else {
        echo "   * Skipping: group already exists: '".$CC_CONFIG['StationPrefsGr']."'\n";
    }
}

// Add the root user if it doesnt exist yet.
//$rootUid = Subjects::GetSubjId('root');
//if (!$rootUid) {
//    echo "   * Creating user 'root'...";
//    $rootUid = BasicStor::addSubj("root", $CC_CONFIG['tmpRootPass']);

    // Add root user to the admin group
    //$r = Subjects::AddSubjectToGroup('root', $CC_CONFIG['AdminsGr']);
    //if (PEAR::isError($r)) {
    //return $r;
    //}
//    echo "done.\n";
//} else {
//    echo "   * Skipping: user already exists: 'root'\n";
//}

// Create the user named 'scheduler'.
if (!Subjects::GetSubjId('scheduler')) {
    echo "   * Creating user 'scheduler'...";
    $subid = Subjects::AddSubj('scheduler', $CC_CONFIG['schedulerPass']);
    $res = Alib::AddPerm($subid, 'read', '0', 'A');
    //$r = Subjects::AddSubjectToGroup('scheduler', $CC_CONFIG['AllGr']);
    echo "done.\n";
} else {
    echo "   * Skipping: user already exists: 'scheduler'\n";
}

// Need to add 'scheduler' to group StationPrefs
Subjects::AddSubjectToGroup('scheduler', $CC_CONFIG['StationPrefsGr']);


//------------------------------------------------------------------------
// Install storage directories
//------------------------------------------------------------------------
echo " *** Directory Setup ***\n";
foreach (array('baseFilesDir', 'storageDir', /*'bufferDir', 'transDir', 'accessDir',*/ 'cronDir') as $d) {
    $test = file_exists($CC_CONFIG[$d]);
    if ( $test === FALSE ) {
        @mkdir($CC_CONFIG[$d], 02775);
        if (file_exists($CC_CONFIG[$d])) {
            $rp = realpath($CC_CONFIG[$d]);
            echo "   * Directory $rp created\n";
        } else {
            echo "   * Failed creating {$CC_CONFIG[$d]}\n";
            exit(1);
        }
    } elseif (is_writable($CC_CONFIG[$d])) {
        $rp = realpath($CC_CONFIG[$d]);
        echo "   * Skipping directory already exists: $rp\n";
    } else {
        $rp = realpath($CC_CONFIG[$d]);
        echo "   * WARNING: Directory already exists, but is not writable: $rp\n";
        //exit(1);
    }
    $CC_CONFIG[$d] = $rp;
}

//------------------------------------------------------------------------
// Storage directory writability test
//------------------------------------------------------------------------

//echo "   * Testing writability of ".$CC_CONFIG['storageDir']."...";
//if (!($fp = @fopen($CC_CONFIG['storageDir']."/_writeTest", 'w'))) {
//    echo "\nPlease make directory {$CC_CONFIG['storageDir']} writeable by your webserver".
//        "\nand run install again\n\n";
//    exit(1);
//} else {
//    fclose($fp);
//    unlink($CC_CONFIG['storageDir']."/_writeTest");
//}
//echo "done.\n";


//
// Make sure the Smarty Templates Compiled directory has the right perms
//
echo "   * Setting dir permissions...\n";
//install_setDirPermissions($CC_CONFIG["smartyTemplateCompiled"]);
install_setDirPermissions($CC_CONFIG["storageDir"]);
//install_setDirPermissions($CC_CONFIG["bufferDir"]);
//install_setDirPermissions($CC_CONFIG["transDir"]);
//install_setDirPermissions($CC_CONFIG["accessDir"]);


//------------------------------------------------------------------------
// Install Cron job
//------------------------------------------------------------------------
//$m = '*/2';
//$h ='*';
//$dom = '*';
//$mon = '*';
//$dow = '*';
//$command = realpath("{$CC_CONFIG['cronDir']}/transportCron.php");
//$old_regex = '/transportCron\.php/';
//echo " * Install storageServer cron job...\n";
//
//$cron = new Cron();
//$access = $cron->openCrontab('write');
//if ($access != 'write') {
//    do {
//        $r = $cron->forceWriteable();
//    } while ($r);
//}
//
//foreach ($cron->ct->getByType(CRON_CMD) as $id => $line) {
//    if (preg_match($old_regex, $line['command'])) {
//        echo "    * Removing old entry: ".$line['command']."\n";
//        $cron->ct->delEntry($id);
//    }
//}
//echo "    * Adding new entry: ".$command."\n";
//$cron->ct->addCron($m, $h, $dom, $mon, $dow, $command);
//$cron->closeCrontab();
//echo "   Done.\n";

echo " * Importing sample audio clips \n";
$command = __DIR__."/../utils/campcaster-import --copy ../audio_samples/ > /dev/null";
@exec($command, $output, $results);
echo "****************************** Install Complete ******************************\n";

?>
