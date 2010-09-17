<?php
/**
 * StorageServer configuration file
 *
 *  configuration structure:
 *
 *  <dl>
 *   <dt>dsn<dd> datasource setting
 *   <dt>tblNamePrefix <dd>prefix for table names in the database
 *   <dt>authCookieName <dd>secret token cookie name
 *   <dt>AdminsGr <dd>name of admin group
 *   <dt>StationPrefsGr <dd>name of station preferences group
 *   <dt>AllGr <dd>name of 'all users' group
 *   <dt>storageDir <dd>main directory for storing binary media files
 *   <dt>bufferDir <dd>directory for temporary files
 *   <dt>transDir <dd>directory for incomplete transferred files
 *   <dt>accessDir <dd>directory for symlinks to accessed files
 *   <dt>validate <dd>enable/disable validator
 *   <dt>useTrash <dd>enable/disable safe delete (move to trash)
 *   <dt>storageUrlPath<dd>path-URL-part of storageServer base dir
 *   <dt>storageXMLRPC<dd>XMLRPC server script address relative to storageUrlPath
 *   <dt>storageUrlHost, storageUrlPort<dd>host and port of storageServer
 *   <dt>archiveUrlPath<dd>path-URL-part of archiveServer base dir
 *   <dt>archiveXMLRPC<dd>XMLRPC server script address relative to archiveUrlPath
 *   <dt>archiveUrlHost, archiveUrlPort<dd>host and port of archiveServer
 *   <dt>archiveAccountLogin, archiveAccountPass <dd>account info
 *           for login to archive
 *   <dt>sysSubjs<dd>system users/groups - cannot be deleted
 *  </dl>
 */

include("campcaster_version.php");

// these are the default values for the config
global $CC_CONFIG;

$CC_CONFIG = array(
    /* ================================================== basic configuration */
    'dsn'           => array(
        'username'      => 'test',
        'password'      => 'test',
        'hostspec'      => 'localhost',
        'phptype'       => 'pgsql',
        'database'      => 'Campcaster-paul',
    ),
    'tblNamePrefix' => 'cc_',
    /* ================================================ storage configuration */
    'authCookieName'=> 'campcaster_session_id',
    //'AdminsGr'      => 'Admins',
    'StationPrefsGr'=> 'StationPrefs',
    //'AllGr'         => 'All',
    'TrashName'     => 'trash_',
    'storageDir'    =>  dirname(__FILE__).'/../../storageServer/var/stor',
    'bufferDir'     =>  dirname(__FILE__).'/../../storageServer/var/stor/buffer',
    'transDir'      =>  dirname(__FILE__).'/../../storageServer/var/trans',
    'accessDir'     =>  dirname(__FILE__).'/../../storageServer/var/access',
    'pearPath'      =>  dirname(__FILE__).'/../../../../usr/lib/pear',
//    'zendPath'      =>  dirname(__FILE__).'/../../../../usr/lib',
    'cronDir'       =>  dirname(__FILE__).'/../../storageServer/var/cron',
    'validate'      =>  TRUE,
    'useTrash'      =>  TRUE,

    /* ==================================================== URL configuration */
    'storageUrlPath'        => '/campcasterStorageServer',
    'storageXMLRPC'         => 'xmlrpc/xrLocStor.php',
    'storageUrlHost'        => 'localhost',
    'storageUrlPort'        => 80,

    /* ================================================ archive configuration */
    'archiveUrlPath'        => '/campcasterStorageServer',
    'archiveXMLRPC'         => 'xmlrpc/xrLocStor.php',
    'archiveUrlHost'        => 'localhost',
//    'archiveUrlHost'        => '192.168.30.166',
    'archiveUrlPort'        => 80,
    'archiveAccountLogin'   => 'root',
    'archiveAccountPass'    => 'q',

    /* ============================================== scheduler configuration */
    'schedulerUrlPath'        => '',
    'schedulerXMLRPC'         => 'RC2',
    'schedulerUrlHost'        => 'localhost',
    'schedulerUrlPort'        => 3344,
    'schedulerPass'           => 'change_me',

    /* ==================================== aplication-specific configuration */
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
    'lockfile'     =>  dirname(__FILE__).'/../../storageServer/var/stor/buffer/cron.lock',
    'cronfile'          => dirname(__FILE__).'/cron/croncall.php',
    'paramdir'          => dirname(__FILE__).'/cron/params',
    'systemPrefId' => "0", // ID for system prefs in prefs table
);

// Add database table names
$CC_CONFIG['playListTable'] = $CC_CONFIG['tblNamePrefix'].'playlist';
$CC_CONFIG['playListContentsTable'] = $CC_CONFIG['tblNamePrefix'].'playlistcontents';
$CC_CONFIG['filesTable'] = $CC_CONFIG['tblNamePrefix'].'files';
$CC_CONFIG['mdataTable'] = $CC_CONFIG['tblNamePrefix'].'mdata';
$CC_CONFIG['accessTable'] = $CC_CONFIG['tblNamePrefix'].'access';
$CC_CONFIG['permTable'] = $CC_CONFIG['tblNamePrefix'].'perms';
$CC_CONFIG['sessTable'] = $CC_CONFIG['tblNamePrefix'].'sess';
$CC_CONFIG['subjTable'] = $CC_CONFIG['tblNamePrefix'].'subjs';
$CC_CONFIG['smembTable'] = $CC_CONFIG['tblNamePrefix'].'smemb';
$CC_CONFIG['transTable'] = $CC_CONFIG['tblNamePrefix'].'trans';
$CC_CONFIG['prefTable'] = $CC_CONFIG['tblNamePrefix'].'pref';
$CC_CONFIG['playlogTable'] = $CC_CONFIG['tblNamePrefix'].'playlog';
$CC_CONFIG['scheduleTable'] = $CC_CONFIG['tblNamePrefix'].'schedule';
$CC_CONFIG['backupTable'] = $CC_CONFIG['tblNamePrefix'].'backup';

$CC_CONFIG['playListSequence'] = $CC_CONFIG['playListTable'].'_id';
$CC_CONFIG['filesSequence'] = $CC_CONFIG['filesTable'].'_id';
$CC_CONFIG['transSequence'] = $CC_CONFIG['transTable'].'_id';
$CC_CONFIG['prefSequence'] = $CC_CONFIG['prefTable'].'_id';
$CC_CONFIG['permSequence'] = $CC_CONFIG['permTable'].'_id';
$CC_CONFIG['subjSequence'] = $CC_CONFIG['subjTable'].'_id';
$CC_CONFIG['smembSequence'] = $CC_CONFIG['smembTable'].'_id';
$CC_CONFIG['mdataSequence'] = $CC_CONFIG['mdataTable'].'_id';

$CC_CONFIG['sysSubjs'] = array(
    'root', /*$CC_CONFIG['AdminsGr'],*/ /*$CC_CONFIG['AllGr'],*/ $CC_CONFIG['StationPrefsGr']
);
$old_include_path = get_include_path();
set_include_path('.'.PATH_SEPARATOR.$CC_CONFIG['pearPath'].PATH_SEPARATOR.$old_include_path);

// see if a ~/.campcaster/storageServer.conf.php exists, and
// overwrite the settings from there if any

$this_file = null;
if (isset($_SERVER["SCRIPT_FILENAME"])) {
    $this_file = $_SERVER["SCRIPT_FILENAME"];
} elseif(isset($argv[0])) {
    $this_file = $argv[0];
}
if (!is_null($this_file)) {
    $fileowner_id = fileowner($this_file);
    $fileowner_array = posix_getpwuid($fileowner_id);
    $fileowner_homedir = $fileowner_array['dir'];
    $fileowner_name = $fileowner_array['name'];
    $home_conf = $fileowner_homedir . '/.campcaster/storageServer.conf.php';
    if (file_exists($home_conf)) {
        $default_config = $CC_CONFIG;
        $developer_name = $fileowner_name;
        include($home_conf);
        $user_config = $CC_CONFIG;
        $CC_CONFIG = $user_config + $default_config;
    }
}

// workaround for missing folders
foreach (array('storageDir', 'bufferDir', 'transDir', 'accessDir', 'pearPath', 'cronDir') as $d) {
    $test = file_exists($CC_CONFIG[$d]);
    if ( $test === FALSE ) {
        @mkdir($CC_CONFIG[$d], 02775);
        if (file_exists($CC_CONFIG[$d])) {
            $rp = realpath($CC_CONFIG[$d]);
            //echo " * Directory $rp created\n";
        } else {
            echo " * Failed creating {$CC_CONFIG[$d]}\n";
            exit(1);
        }
    } else {
        $rp = realpath($CC_CONFIG[$d]);
    }
    $CC_CONFIG[$d] = $rp;
}

?>
