<?php
date_default_timezone_set("UTC");

$webRoot = apache_getenv("DOCUMENT_ROOT");
//require_once $webRoot . "/../application/configs/conf.php";
//$CC_CONFIG = Config::getConfig();

require_once($webRoot.'/application/configs/constants.php');
require_once($webRoot.'/application/configs/conf.php');

// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
        get_include_path(),
        realpath($webRoot . '/library')
)));

require_once($webRoot.'/application/common/Database.php');
require_once($webRoot.'/application/models/StoredFile.php');
require_once($webRoot.'/application/models/Preference.php');
require_once($webRoot.'/application/models/MusicDir.php');
require_once($webRoot.'/application/common/OsPath.php');

set_include_path($webRoot.'/library' . PATH_SEPARATOR . get_include_path());
require_once($webRoot.'/application/models/Soundcloud.php');

set_include_path($webRoot."/application/models" . PATH_SEPARATOR . get_include_path());
require_once 'propel/runtime/lib/Propel.php';
Propel::init($webRoot."/application/configs/airtime-conf-production.php");

if(count($argv) != 2){
    exit;
}

$id = $argv[1];

$file = Application_Model_StoredFile::RecallById($id);
// set id with -2 which is indicator for processing
$file->setSoundCloudFileId(SOUNDCLOUD_PROGRESS);
$file->uploadToSoundCloud();
