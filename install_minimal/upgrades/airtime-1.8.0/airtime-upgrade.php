<?php
/**
 * @package Airtime
 * @subpackage StorageServer
 * @copyright 2010 Sourcefabric O.P.S.
 * @license http://www.gnu.org/licenses/gpl.txt
 */

set_include_path(__DIR__.'/../../../airtime_mvc/library' . PATH_SEPARATOR . get_include_path());
require_once __DIR__.'/../../../airtime_mvc/application/configs/conf.php';
require_once(dirname(__FILE__).'/../../include/AirtimeInstall.php');
require_once(dirname(__FILE__).'/../../include/AirtimeIni.php');

//global $AIRTIME_SRC;
//global $AIRTIME_UTILS;
global $AIRTIME_PYTHON_APPS;

global $CC_CONFIG;

//$AIRTIME_SRC = __DIR__.'/../../../airtime_mvc';
//$AIRTIME_UTILS = __DIR__.'/../../../utils';
$AIRTIME_PYTHON_APPS = __DIR__.'/../../../python_apps';


AirtimeInstall::DbConnect(true);

echo PHP_EOL."*** Updating Database Tables ***".PHP_EOL;

if(AirtimeInstall::DbTableExists('doctrine_migration_versions') === false) {
    $migrations = array('20110312121200', '20110331111708', '20110402164819');
    foreach($migrations as $migration) {
        AirtimeInstall::BypassMigrations(__DIR__, $migration);
    }
}
AirtimeInstall::MigrateTablesToVersion(__DIR__, '20110406182005');

//setting data for new aggregate show length column.
$sql = "SELECT id FROM cc_show_instances";
$show_instances = $CC_DBC->GetAll($sql);

foreach ($show_instances as $show_instance) {
    $sql = "UPDATE cc_show_instances SET time_filled = (SELECT SUM(clip_length) FROM cc_schedule WHERE instance_id = {$show_instance["id"]}) WHERE id = {$show_instance["id"]}";
    $CC_DBC->query($sql);
}
//end setting data for new aggregate show length column.

exec("rm -fr /opt/pypo");
exec("rm -fr /opt/recorder");

const CONF_FILE_AIRTIME = "/etc/airtime/airtime.conf";
const CONF_FILE_PYPO = "/etc/airtime/pypo.cfg";
const CONF_FILE_RECORDER = "/etc/airtime/recorder.cfg";
const CONF_FILE_LIQUIDSOAP = "/etc/airtime/liquidsoap.cfg";

$configFiles = array(AirtimeIni::CONF_FILE_AIRTIME,
                     AirtimeIni::CONF_FILE_PYPO,
                     AirtimeIni::CONF_FILE_RECORDER,
                     AirtimeIni::CONF_FILE_LIQUIDSOAP);


/**
* This function creates the /etc/airtime configuration folder
* and copies the default config files to it.
*/
function CreateIniFiles($suffix)
{
    if (!file_exists("/etc/airtime/")){
        if (!mkdir("/etc/airtime/", 0755, true)){
            echo "Could not create /etc/airtime/ directory. Exiting.";
            exit(1);
        }
    }

    if (!copy(__DIR__."/airtime.conf.$suffix", CONF_FILE_AIRTIME)){
        echo "Could not copy airtime.conf.$suffix to /etc/airtime/. Exiting.";
        exit(1);
    }
    if (!copy(__DIR__."/pypo.cfg.$suffix", CONF_FILE_PYPO)){
        echo "Could not copy pypo.cfg.$suffix to /etc/airtime/. Exiting.";
        exit(1);
    }
    if (!copy(__DIR__."/recorder.cfg.$suffix", CONF_FILE_RECORDER)){
        echo "Could not copy recorder.cfg.$suffix to /etc/airtime/. Exiting.";
        exit(1);
    }
    if (!copy(__DIR__."/liquidsoap.cfg.$suffix", CONF_FILE_LIQUIDSOAP)){
        echo "Could not copy liquidsoap.cfg.$suffix to /etc/airtime/. Exiting.";
        exit(1);
    }
}

function ReadPythonConfig($p_filename)
{
    $values = array();

    $lines = file($p_filename);
    $n=count($lines);
    for ($i=0; $i<$n; $i++) {
        if (strlen($lines[$i]) && !in_array(substr($lines[$i], 0, 1), array('#', PHP_EOL))){
             $info = explode("=", $lines[$i]);
             $values[trim($info[0])] = trim($info[1]);
         }
    }

    return $values;
}

function UpdateIniValue($p_filename, $p_property, $p_value)
{
    $lines = file($p_filename);
    $n=count($lines);
    foreach ($lines as &$line) {
        if ($line[0] != "#"){
            $key_value = split("=", $line);
            $key = trim($key_value[0]);

            if ($key == $p_property){
                $line = "$p_property = $p_value".PHP_EOL;
            }
        }
    }

    $fp=fopen($p_filename, 'w');
    for($i=0; $i<$n; $i++){
        fwrite($fp, $lines[$i]);
    }
    fclose($fp);
}

function MergeConfigFiles($configFiles, $suffix)
{
    foreach ($configFiles as $conf) {
        if (file_exists("$conf$suffix.bak")) {

            if($conf === CONF_FILE_AIRTIME) {
                // Parse with sections
                $newSettings = parse_ini_file($conf, true);
                $oldSettings = parse_ini_file("$conf$suffix.bak", true);
            }
            else {
                $newSettings = ReadPythonConfig($conf);
                $oldSettings = ReadPythonConfig("$conf$suffix.bak");
            }

            $settings = array_keys($newSettings);

            foreach($settings as $section) {
                if(isset($oldSettings[$section])) {
                    if(is_array($oldSettings[$section])) {
                        $sectionKeys = array_keys($newSettings[$section]);
                        foreach($sectionKeys as $sectionKey) {
                            if(isset($oldSettings[$section][$sectionKey])) {
                                UpdateIniValue($conf, $sectionKey, $oldSettings[$section][$sectionKey]);
                            }
                        }
                    }
                    else {
                        UpdateIniValue($conf, $section, $oldSettings[$section]);
                    }
                }
            }
        }
    }
}

function LoadConfig($CC_CONFIG) {
    $values = parse_ini_file(CONF_FILE_AIRTIME, true);

    // Name of the web server user
    $CC_CONFIG['webServerUser'] = $values['general']['web_server_user'];
    $CC_CONFIG['phpDir'] = $values['general']['airtime_dir'];
    $CC_CONFIG['rabbitmq'] = $values['rabbitmq'];

    $CC_CONFIG['baseFilesDir'] = $values['general']['base_files_dir'];
    // main directory for storing binary media files
    $CC_CONFIG['storageDir'] = $values['general']['base_files_dir']."/stor";

    // Database config
    $CC_CONFIG['dsn']['username'] = $values['database']['dbuser'];
    $CC_CONFIG['dsn']['password'] = $values['database']['dbpass'];
    $CC_CONFIG['dsn']['hostspec'] = $values['database']['host'];
    $CC_CONFIG['dsn']['phptype'] = 'pgsql';
    $CC_CONFIG['dsn']['database'] = $values['database']['dbname'];

    $CC_CONFIG['apiKey'] = array($values['general']['api_key']);

    $CC_CONFIG['soundcloud-connection-retries'] = $values['soundcloud']['connection_retries'];
    $CC_CONFIG['soundcloud-connection-wait'] = $values['soundcloud']['time_between_retries'];

    return $CC_CONFIG;
}

// Backup the config files
$suffix = date("Ymdhis")."-1.8.0";
foreach ($configFiles as $conf) {
    if (file_exists($conf)) {
        echo "Backing up $conf to $conf$suffix.bak".PHP_EOL;
        exec("cp $conf $conf$suffix.bak");
    }
}

$default_suffix = "180";
CreateIniFiles($default_suffix);
echo "* Initializing INI files".PHP_EOL;
MergeConfigFiles($configFiles, $suffix);

$CC_CONFIG = LoadConfig($CC_CONFIG);
