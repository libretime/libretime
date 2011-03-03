<?php
define('AIRTIME_VERSION', '1.7.0 alpha');
define('AIRTIME_COPYRIGHT_DATE', '2010-2011');

// These are the default values for the config.
global $CC_CONFIG;
$values = load_airtime_config();

// ********************************** 
// ***** START CUSTOMIZING HERE *****
// ********************************** 

// Set the location where you want to store all of your audio files.
//
// For example:
// $baseFilesDir = '/home/john/radio-files';
$baseFilesDir = __DIR__.'/../../files';

$CC_CONFIG = array(

    // Set the URL of your installation
 	'storageUrlHost'        => 'localhost',
    'storageUrlPort'        => 80,

    // Name of the web server user
    'webServerUser' => 'www-data',

// ***********************************************************************
	// STOP CUSTOMIZING HERE
	//
	// You don't need to touch anything below this point. 
	// ***********************************************************************

    'baseFilesDir' => $baseFilesDir,
    // main directory for storing binary media files
    'storageDir'    =>  "$baseFilesDir/stor",

	// Database config
    'dsn' => $values['database'],

    // prefix for table names in the database
    'tblNamePrefix' => 'cc_',

    /* ================================================ storage configuration */

    'apiKey' => $values['api_key'],
    'apiPath' => '/api/',

    "rootDir" => __DIR__."/../..",
    'pearPath'      =>  dirname(__FILE__).'/../../library/pear',
    'zendPath'      =>  dirname(__FILE__).'/../../library/Zend',
    'phingPath'      =>  dirname(__FILE__).'/../../library/phing',

    // name of admin group
    //'AdminsGr'      => 'Admins',

    // name of station preferences group
    'StationPrefsGr'=> 'StationPrefs',

    // name of 'all users' group
    //'AllGr'         => 'All',

    /* ==================================== application-specific configuration */
    'objtypes'      => array(
        'Storage'       => array(/*'Folder',*/ 'File' /*, 'Replica'*/),
        'File'          => array(),
        'audioclip'     => array(),
        'playlist'      => array(),
    ),
    'allowedActions'=> array(
        'File'          => array('editPrivs', 'write', 'read'),
        'audioclip'     => array('editPrivs', 'write', 'read'),
        'playlist'      => array('editPrivs', 'write', 'read'),
    ),
    'allActions'    =>  array(
        'editPrivs', 'write', 'read', 'subjects'
    ),

    /* =================================================== cron configuration */
    'cronUserName'      => 'www-data',
#    'lockfile'          => dirname(__FILE__).'/cron/cron.lock',
    'lockfile'     =>  dirname(__FILE__).'/stor/buffer/cron.lock',
    'cronfile'          => dirname(__FILE__).'/cron/croncall.php',
    'paramdir'          => dirname(__FILE__).'/cron/params',
    'systemPrefId' => "0", // ID for system prefs in prefs table
);

// Add database table names
$CC_CONFIG['playListTable'] = $CC_CONFIG['tblNamePrefix'].'playlist';
$CC_CONFIG['playListContentsTable'] = $CC_CONFIG['tblNamePrefix'].'playlistcontents';
$CC_CONFIG['filesTable'] = $CC_CONFIG['tblNamePrefix'].'files';
$CC_CONFIG['accessTable'] = $CC_CONFIG['tblNamePrefix'].'access';
$CC_CONFIG['permTable'] = $CC_CONFIG['tblNamePrefix'].'perms';
$CC_CONFIG['sessTable'] = $CC_CONFIG['tblNamePrefix'].'sess';
$CC_CONFIG['subjTable'] = $CC_CONFIG['tblNamePrefix'].'subjs';
$CC_CONFIG['smembTable'] = $CC_CONFIG['tblNamePrefix'].'smemb';
$CC_CONFIG['transTable'] = $CC_CONFIG['tblNamePrefix'].'trans';
$CC_CONFIG['prefTable'] = $CC_CONFIG['tblNamePrefix'].'pref';
$CC_CONFIG['scheduleTable'] = $CC_CONFIG['tblNamePrefix'].'schedule';
$CC_CONFIG['backupTable'] = $CC_CONFIG['tblNamePrefix'].'backup';
$CC_CONFIG['playListTimeView'] = $CC_CONFIG['tblNamePrefix'].'playlisttimes';
$CC_CONFIG['showSchedule'] = $CC_CONFIG['tblNamePrefix'].'show_schedule';
$CC_CONFIG['showDays'] = $CC_CONFIG['tblNamePrefix'].'show_days';
$CC_CONFIG['showTable'] = $CC_CONFIG['tblNamePrefix'].'show';
$CC_CONFIG['showInstances'] = $CC_CONFIG['tblNamePrefix'].'show_instances';

$CC_CONFIG['playListSequence'] = $CC_CONFIG['playListTable'].'_id';
$CC_CONFIG['filesSequence'] = $CC_CONFIG['filesTable'].'_id';
$CC_CONFIG['transSequence'] = $CC_CONFIG['transTable'].'_id';
$CC_CONFIG['prefSequence'] = $CC_CONFIG['prefTable'].'_id';
$CC_CONFIG['permSequence'] = $CC_CONFIG['permTable'].'_id';
$CC_CONFIG['subjSequence'] = $CC_CONFIG['subjTable'].'_id';
$CC_CONFIG['smembSequence'] = $CC_CONFIG['smembTable'].'_id';

// System users/groups - they cannot be deleted
$CC_CONFIG['sysSubjs'] = array(
    'root', /*$CC_CONFIG['AdminsGr'],*/ /*$CC_CONFIG['AllGr'],*/ $CC_CONFIG['StationPrefsGr']
);

// Add libs to the PHP path
$old_include_path = get_include_path();
set_include_path('.'.PATH_SEPARATOR.$CC_CONFIG['pearPath']
					.PATH_SEPARATOR.$CC_CONFIG['zendPath']
					.PATH_SEPARATOR.$old_include_path);

function load_airtime_config(){
	$ini_array = parse_ini_file(dirname(__FILE__).'/../../build/airtime.conf', true);
		
	return array(
            'database' => array(   
                'username'      => $ini_array['database']['dbuser'],
                'password'      => $ini_array['database']['dbpass'],
                'hostspec'      => $ini_array['database']['host'],
                'phptype'       => 'pgsql',
                'database'      => $ini_array['database']['dbname']),
            'api_key' => array($ini_array['general']['api_key'])
        );
}
