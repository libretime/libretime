<?php
/**
 * @package Airtime
 * @subpackage StorageServer
 * @copyright 2010 Sourcefabric O.P.S.
 * @license http://www.gnu.org/licenses/gpl.txt
 */

set_include_path(__DIR__.'/../../../airtime_mvc/library' . PATH_SEPARATOR . get_include_path());

const CONF_FILE_AIRTIME = "/etc/airtime/airtime.conf";
const CONF_FILE_PYPO = "/etc/airtime/pypo.cfg";
const CONF_FILE_RECORDER = "/etc/airtime/recorder.cfg";
const CONF_FILE_LIQUIDSOAP = "/etc/airtime/liquidsoap.cfg";

const CONF_DIR_BINARIES = "/usr/lib/airtime";
const CONF_DIR_STORAGE = "/srv/airtime";
const CONF_DIR_WWW = "/var/www/airtime";
const CONF_DIR_LOG = "/var/log/airtime";

global $AIRTIME_SRC;
global $AIRTIME_UTILS;
global $AIRTIME_PYTHON_APPS;

global $CC_CONFIG;

$AIRTIME_SRC = __DIR__.'/../../../airtime_mvc';
$AIRTIME_UTILS = __DIR__.'/../../../utils';
$AIRTIME_PYTHON_APPS = __DIR__.'/../../../python_apps';

$configFiles = array(CONF_FILE_AIRTIME,
                     CONF_FILE_PYPO,
                     CONF_FILE_RECORDER,
                     CONF_FILE_LIQUIDSOAP);

$CC_CONFIG = array(
    // prefix for table names in the database
    'tblNamePrefix' => 'cc_',

    /* ================================================ storage configuration */

    'soundcloud-client-id' => '2CLCxcSXYzx7QhhPVHN4A',
    'soundcloud-client-secret' => 'pZ7beWmF06epXLHVUP1ufOg2oEnIt9XhE8l8xt0bBs',

    "rootDir" => __DIR__."/../..",
    'pearPath'      =>  dirname(__FILE__).'/../../library/pear',
    'zendPath'      =>  dirname(__FILE__).'/../../library/Zend',
    'phingPath'      =>  dirname(__FILE__).'/../../library/phing'
);

//$CC_CONFIG = Config::loadConfig($CC_CONFIG);

// Add database table names
$CC_CONFIG['playListTable'] = $CC_CONFIG['tblNamePrefix'].'playlist';
$CC_CONFIG['playListContentsTable'] = $CC_CONFIG['tblNamePrefix'].'playlistcontents';
$CC_CONFIG['filesTable'] = $CC_CONFIG['tblNamePrefix'].'files';
$CC_CONFIG['accessTable'] = $CC_CONFIG['tblNamePrefix'].'access';
$CC_CONFIG['permTable'] = $CC_CONFIG['tblNamePrefix'].'perms';
$CC_CONFIG['sessTable'] = $CC_CONFIG['tblNamePrefix'].'sess';
$CC_CONFIG['subjTable'] = $CC_CONFIG['tblNamePrefix'].'subjs';
$CC_CONFIG['smembTable'] = $CC_CONFIG['tblNamePrefix'].'smemb';
$CC_CONFIG['prefTable'] = $CC_CONFIG['tblNamePrefix'].'pref';
$CC_CONFIG['scheduleTable'] = $CC_CONFIG['tblNamePrefix'].'schedule';
$CC_CONFIG['playListTimeView'] = $CC_CONFIG['tblNamePrefix'].'playlisttimes';
$CC_CONFIG['showSchedule'] = $CC_CONFIG['tblNamePrefix'].'show_schedule';
$CC_CONFIG['showDays'] = $CC_CONFIG['tblNamePrefix'].'show_days';
$CC_CONFIG['showTable'] = $CC_CONFIG['tblNamePrefix'].'show';
$CC_CONFIG['showInstances'] = $CC_CONFIG['tblNamePrefix'].'show_instances';

$CC_CONFIG['playListSequence'] = $CC_CONFIG['playListTable'].'_id';
$CC_CONFIG['filesSequence'] = $CC_CONFIG['filesTable'].'_id';
$CC_CONFIG['prefSequence'] = $CC_CONFIG['prefTable'].'_id';
$CC_CONFIG['permSequence'] = $CC_CONFIG['permTable'].'_id';
$CC_CONFIG['subjSequence'] = $CC_CONFIG['subjTable'].'_id';
$CC_CONFIG['smembSequence'] = $CC_CONFIG['smembTable'].'_id';

// Add libs to the PHP path
$old_include_path = get_include_path();
set_include_path('.'.PATH_SEPARATOR.$CC_CONFIG['pearPath']
					.PATH_SEPARATOR.$CC_CONFIG['zendPath']
					.PATH_SEPARATOR.$old_include_path);

function LoadConfig($CC_CONFIG) {
    $values = parse_ini_file(CONF_FILE_AIRTIME, true);

    // Name of the web server user
    $CC_CONFIG['webServerUser'] = $values['general']['web_server_user'];
    $CC_CONFIG['phpDir'] = $values['general']['airtime_dir'];
    $CC_CONFIG['rabbitmq'] = $values['rabbitmq'];

    //$CC_CONFIG['baseUrl'] = $values['general']['base_url'];
    //$CC_CONFIG['basePort'] = $values['general']['base_port'];

    $CC_CONFIG['baseFilesDir'] = $values['general']['base_files_dir'];
    // main directory for storing binary media files
    $CC_CONFIG['storageDir'] =  $values['general']['base_files_dir']."/stor";

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

/**
 * This function creates the /etc/airtime configuration folder
 * and copies the default config files to it.
 */
function CreateIniFiles()
{
    global $AIRTIME_SRC;
    global $AIRTIME_PYTHON_APPS;

    if (!file_exists("/etc/airtime/")){
        if (!mkdir("/etc/airtime/", 0755, true)){
            echo "Could not create /etc/airtime/ directory. Exiting.";
            exit(1);
        }
    }

    if (!copy($AIRTIME_SRC."/build/airtime.conf", CONF_FILE_AIRTIME)){
        echo "Could not copy airtime.conf to /etc/airtime/. Exiting.";
        exit(1);
    }
    if (!copy($AIRTIME_PYTHON_APPS."/pypo/pypo.cfg", CONF_FILE_PYPO)){
        echo "Could not copy pypo.cfg to /etc/airtime/. Exiting.";
        exit(1);
    }
    if (!copy($AIRTIME_PYTHON_APPS."/show-recorder/recorder.cfg", CONF_FILE_RECORDER)){
        echo "Could not copy recorder.cfg to /etc/airtime/. Exiting.";
        exit(1);
    }
    if (!copy($AIRTIME_PYTHON_APPS."/pypo/scripts/liquidsoap.cfg", CONF_FILE_LIQUIDSOAP)){
        echo "Could not copy liquidsoap.cfg to /etc/airtime/. Exiting.";
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

function InstallPhpCode()
{
    global $CC_CONFIG;
    global $AIRTIME_SRC;
    echo "* Installing PHP code to ".$CC_CONFIG['phpDir'].PHP_EOL;
    exec("mkdir -p ".$CC_CONFIG['phpDir']);
    exec("cp -R ".$AIRTIME_SRC."/* ".$CC_CONFIG['phpDir']);

}

function InstallBinaries()
{
    global $AIRTIME_UTILS;
    echo "* Installing binaries to ".CONF_DIR_BINARIES.PHP_EOL;
    exec("mkdir -p ".CONF_DIR_BINARIES);
    exec("cp -R ".$AIRTIME_UTILS." ".CONF_DIR_BINARIES);
}

// Backup the config files
$suffix = date("Ymdhis")."-1.8.1";
foreach ($configFiles as $conf) {
    if (file_exists($conf)) {
        echo "Backing up $conf to $conf$suffix.bak".PHP_EOL;
        exec("cp $conf $conf$suffix.bak");
    }
}

CreateIniFiles();
echo "* Initializing INI files".PHP_EOL;
MergeConfigFiles($configFiles, $suffix);

$CC_CONFIG = LoadConfig($CC_CONFIG);

InstallPhpCode();
InstallBinaries();
