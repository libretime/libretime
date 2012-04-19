<?php
$values = parse_ini_file('/etc/airtime/airtime.conf', true);

// Name of the web server user
$CC_CONFIG['webServerUser'] = $values['general']['web_server_user'];
$CC_CONFIG['phpDir'] = $values['general']['airtime_dir'];
$CC_CONFIG['rabbitmq'] = $values['rabbitmq'];

$CC_CONFIG['baseUrl'] = $values['general']['base_url'];
$CC_CONFIG['basePort'] = $values['general']['base_port'];

// Database config
$CC_CONFIG['dsn']['username'] = $values['database']['dbuser'];
$CC_CONFIG['dsn']['password'] = $values['database']['dbpass'];
$CC_CONFIG['dsn']['hostspec'] = $values['database']['host'];
$CC_CONFIG['dsn']['phptype'] = 'pgsql';
$CC_CONFIG['dsn']['database'] = $values['database']['dbname'];

$CC_CONFIG['soundcloud-connection-retries'] = $values['soundcloud']['connection_retries'];
$CC_CONFIG['soundcloud-connection-wait'] = $values['soundcloud']['time_between_retries'];

require_once($CC_CONFIG['phpDir'].'/application/configs/constants.php');
require_once($CC_CONFIG['phpDir'].'/application/configs/conf.php');

$CC_CONFIG['phpDir'] = $values['general']['airtime_dir'];

// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
        get_include_path(),
        realpath($CC_CONFIG['phpDir'] . '/library')
)));

require_once($CC_CONFIG['phpDir'].'/application/models/StoredFile.php');
require_once($CC_CONFIG['phpDir'].'/application/models/Preference.php');
require_once($CC_CONFIG['phpDir'].'/application/models/MusicDir.php');

set_include_path($CC_CONFIG['phpDir'].'/library' . PATH_SEPARATOR . get_include_path());
require_once($CC_CONFIG['phpDir'].'/application/models/Soundcloud.php');

set_include_path($CC_CONFIG['phpDir']."/application/models" . PATH_SEPARATOR . get_include_path());
require_once($CC_CONFIG['phpDir']."/library/propel/runtime/lib/Propel.php");
Propel::init($CC_CONFIG['phpDir']."/application/configs/airtime-conf.php");

require_once 'propel/runtime/lib/Propel.php';
Propel::init($CC_CONFIG['phpDir']."/application/configs/airtime-conf-production.php");

if(count($argv) != 2){
    exit;
}

$id = $argv[1];
$file = Application_Model_StoredFile::Recall($id);
// set id with -2 which is indicator for processing
$file->setSoundCloudFileId(SOUNDCLOUD_PROGRESS);
$file->uploadToSoundCloud();

