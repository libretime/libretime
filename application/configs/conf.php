<?php
define('CAMPCASTER_VERSION', '1.6.0-alpha');
define('CAMPCASTER_COPYRIGHT_DATE', '2010');

// These are the default values for the config.
global $CC_CONFIG;

$CC_CONFIG = array(
    // Database config
    'dsn'           => load_db_config(),

    // Name of the web server user
    'webServerUser' => 'www-data',

    // prefix for table names in the database
    'tblNamePrefix' => 'cc_',

    /* ================================================ storage configuration */

    'apiKey' => array('AAA'),

    'apiPath' => "/api/",

    'baseFilesDir' => __DIR__."/../../files",
    // main directory for storing binary media files
    'storageDir'    =>  __DIR__.'/../../files/stor',

    // directory for temporary files
 	'bufferDir'     =>  __DIR__.'/../../files/stor/buffer',

    // directory for incomplete transferred files
 	'transDir'      =>  __DIR__.'/../../files/trans',

    // directory for symlinks to accessed files
    'accessDir'     =>  __DIR__.'/../../files/access',
    'cronDir'       =>  __DIR__.'/../../files/cron',

    "rootDir" => __DIR__."/../..",
    'pearPath'      =>  dirname(__FILE__).'/../../library/pear',
    'zendPath'      =>  dirname(__FILE__).'/../../library/Zend',
    'phingPath'      =>  dirname(__FILE__).'/../../library/phing',

     // secret token cookie name
    'authCookieName'=> 'campcaster_session_id',

    // name of admin group
    //'AdminsGr'      => 'Admins',

    // name of station preferences group
    'StationPrefsGr'=> 'StationPrefs',

    // name of 'all users' group
    //'AllGr'         => 'All',
    'TrashName'     => 'trash_',

    // enable/disable validator
    'validate'      =>  TRUE,

    // enable/disable safe delete (move to trash)
    'useTrash'      =>  FALSE,

    /* ==================================================== URL configuration */
    // path-URL-part of storageServer base dir
 	'storageUrlPath'        => '/campcaster/backend',

    // XMLRPC server script address relative to storageUrlPath
 	'storageXMLRPC'         => 'xmlrpc/xrLocStor.php',

    // host and port of storageServer
 	'storageUrlHost'        => 'localhost',
    'storageUrlPort'        => 80,

    /* ================================================ remote link configuration */
    // path-URL-part of remote server base dir
    'archiveUrlPath'        => '/campcaster/backend',

    // XMLRPC server script address relative to archiveUrlPath
    'archiveXMLRPC'         => 'xmlrpc/xrLocStor.php',

    // host and port of archiveServer
    'archiveUrlHost'        => 'localhost',
//    'archiveUrlHost'        => '192.168.30.166',
    'archiveUrlPort'        => 80,

    // account info for login to archive
    'archiveAccountLogin'   => 'root',
    'archiveAccountPass'    => 'q',

    /* ============================================== scheduler configuration */
    'schedulerUrlPath'        => '',
    'schedulerXMLRPC'         => 'RC2',
    'schedulerUrlHost'        => 'localhost',
    'schedulerUrlPort'        => 3344,
    'schedulerPass'           => 'change_me',

    /* ==================================== application-specific configuration */
    'objtypes'      => array(
        'Storage'       => array(/*'Folder',*/ 'File' /*, 'Replica'*/),
        'File'          => array(),
        'audioclip'     => array(),
        'playlist'      => array(),
//        'Replica'       => array(),
    ),
    'allowedActions'=> array(
        'File'          => array('editPrivs', 'write', 'read'),
        'audioclip'     => array('editPrivs', 'write', 'read'),
        'playlist'      => array('editPrivs', 'write', 'read'),
//        'Replica'       => array('editPrivs', 'write', 'read'),
//        '_class'        => array('editPrivs', 'write', 'read'),
    ),
    'allActions'    =>  array(
        'editPrivs', 'write', 'read', /*'classes',*/ 'subjects'
    ),

    /* ============================================== auxiliary configuration */
    'tmpRootPass'   => 'q',

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

//$dsn = $CC_CONFIG['dsn'];
//$CC_DBC = DB::connect($dsn, TRUE);
//if (PEAR::isError($CC_DBC)) {
//	echo "ERROR: ".$CC_DBC->getMessage()." ".$CC_DBC->getUserInfo()."\n";
//	exit(1);
//}
//$CC_DBC->setFetchMode(DB_FETCHMODE_ASSOC);
function load_db_config(){
	$ini_array = parse_ini_file(dirname(__FILE__).'/../../build/database.conf', true);
		
	return array(        
        'username'      => $ini_array['database']['dbuser'],
        'password'      => $ini_array['database']['dbpass'],
        'hostspec'      => $ini_array['database']['host'],
        'phptype'       => 'pgsql',
        'database'      => $ini_array['database']['dbname']);
}

?>
