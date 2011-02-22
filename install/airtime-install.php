<?php
/**
 * @package Airtime
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

require_once(dirname(__FILE__).'/../application/configs/conf.php');
require_once(dirname(__FILE__).'/../application/models/GreenBox.php');
require_once(dirname(__FILE__).'/installInit.php');

function checkIfRoot(){
    // Need to check that we are superuser before running this.
    if(exec("whoami") != "root"){
      echo "Must be root user.\n";
      exit(1);
    }
}

// Need to check if build.properties project home is set correctly.
function setBuildPropertiesPath(){
    $property = 'project.home';
    $lines = file('../build/build.properties');
    foreach ($lines as $key => &$line) {
        if ($property == substr($line, 0, strlen($property))){
            $line = $property." = ".realpath(__dir__.'/../')."\n";
        }
    }

    $fp=fopen('../build/build.properties', 'w');
    foreach($lines as $key => $line){
        fwrite($fp, $line);
    }
    fclose($fp);
}

function directorySetup($CC_CONFIG){
 //------------------------------------------------------------------------
// Install storage directories
//------------------------------------------------------------------------
echo " *** Directory Setup ***\n";
    foreach (array('baseFilesDir', 'storageDir') as $d) {
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
}



checkIfRoot();
setBuildPropertiesPath();

echo "******************************** Install Begin *********************************\n";

echo " *** Database Installation ***\n";


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
$command = __DIR__."/../library/propel/generator/bin/propel-gen ../build/ insert-sql 2>propel-error.log";
@exec($command, $output, $results);

directorySetup($CC_CONFIG);

echo "   * Setting dir permissions...\n";
install_setDirPermissions($CC_CONFIG["storageDir"]);

echo " * Importing sample audio clips \n";
$command = __DIR__."/../utils/airtime-import --copy ../audio_samples/ > /dev/null";
@exec($command, $output, $results);

$command = "python ".__DIR__."/../pypo/install/pypo-install.py";
system($command);
echo "******************************* Install Complete *******************************\n";

