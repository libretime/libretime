#!/usr/bin/env php
<?php
$values = parse_ini_file('/etc/airtime/airtime.conf', true);

// Name of the web server user
$CC_CONFIG['webServerUser'] = $values['general']['web_server_user'];
$CC_CONFIG['phpDir'] = $values['general']['airtime_dir'];
$CC_CONFIG['rabbitmq'] = $values['rabbitmq'];

$CC_CONFIG['baseUrl'] = $values['general']['base_url'];
$CC_CONFIG['basePort'] = $values['general']['base_port'];

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

require_once($CC_CONFIG['phpDir'].'/application/configs/constants.php');
require_once($CC_CONFIG['phpDir'].'/application/configs/conf.php');

$CC_CONFIG['phpDir'] = $values['general']['airtime_dir'];

require_once($CC_CONFIG['phpDir'].'/application/models/Users.php');
require_once($CC_CONFIG['phpDir'].'/application/models/StoredFile.php');
require_once($CC_CONFIG['phpDir'].'/application/models/Playlist.php');
require_once($CC_CONFIG['phpDir'].'/application/models/Schedule.php');
require_once($CC_CONFIG['phpDir'].'/application/models/Shows.php');
require_once($CC_CONFIG['phpDir'].'/application/models/Preference.php');

//Pear classes.
set_include_path($CC_CONFIG['phpDir'].'/library/pear' . PATH_SEPARATOR . get_include_path());
require_once('DB.php');
$CC_DBC = DB::connect($CC_CONFIG['dsn'], FALSE);
if (PEAR::isError($CC_DBC)) {
    /*echo $CC_DBC->getMessage().PHP_EOL;
    echo $CC_DBC->getUserInfo().PHP_EOL;
    echo "Database connection problem.".PHP_EOL;
    echo "Check if database '{$CC_CONFIG['dsn']['database']}' exists".
        " with corresponding permissions.".PHP_EOL;*/
    if ($p_exitOnError) {
        exit(1);
    }
} else {
    //echo "* Connected to database".PHP_EOL;
    $CC_DBC->setFetchMode(DB_FETCHMODE_ASSOC);
}
if(Application_Model_Preference::GetSupportFeedback() == '1'){
    $infoArray = Application_Model_Preference::GetSystemInfo(true);
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_URL, 'http://stat.sourcefabric.org/index.php?p=airtime');
    
    $data = json_encode($infoArray);
    
    $dataArray = array("data" => $data );
    
    curl_setopt($ch, CURLOPT_POSTFIELDS, $dataArray);
    $result = curl_exec($ch);
}
?>
