<?php
/**
 * ArchiveServer configuration file
 */

include(dirname(__FILE__)."/../../storageServer/var/campcaster_version.php");

/**
 *  configuration structure:
 *
 *  <dl>
 *   <dt>dsn<dd> datasource setting
 *   <dt>tblNamePrefix <dd>prefix for table names in the database
 *   <dt>authCookieName <dd>secret token cookie name
 *   <dt>storageDir <dd>main directory for storing binary media files
 *   <dt>bufferDir <dd>directory for temporary files
 *   <dt>transDir <dd>directory for incomplete transferred files
 *   <dt>accessDir <dd>directory for symlinks to accessed files
 *   <dt>isArchive <dd>local/central flag
 *   <dt>validate <dd>enable/disable validator
 *   <dt>useTrash <dd>enable/disable safe delete (move to trash)
 *          (FALSE on archiveServer)
 *   <dt>storageUrlPath<dd>path-URL-part of storageServer base dir
 *      (on central archive side: storage=archive)
 *   <dt>storageXMLRPC<dd>XMLRPC server script address relative to storageUrlPath
 *   <dt>storageUrlHost, storageUrlPort<dd>host and port of storageServer
 *   <dt>archiveUrlPath<dd>path-URL-part of archiveServer base dir
 *   <dt>archiveXMLRPC<dd>XMLRPC server script address relative to archiveUrlPath
 *   <dt>archiveUrlHost, archiveUrlPort<dd>host and port of archiveServer
 *  </dl>
 */

// these are the default values for the config

$CC_CONFIG = array(
    /* ================================================== basic configuration */
    'dsn'           => array(
        'username'      => 'test',
        'password'      => 'test',
        'hostspec'      => 'localhost',
        'phptype'       => 'pgsql',
        'database'      => 'Campcaster-test',
    ),
    'tblNamePrefix' => 'as_',

    /* ================================================ storage configuration */
    'authCookieName'=> 'assid',
    'AdminsGr'      => 'Admins',
    'StationPrefsGr'=> '',
    'AllGr'         => 'All',
    'storageDir'    =>  dirname(__FILE__).'/../../archiveServer/var/stor',
    'bufferDir'     =>  dirname(__FILE__).'/../../archiveServer/var/stor/buffer',
    'transDir'      =>  dirname(__FILE__).'/../../archiveServer/var/trans',
    'accessDir'     =>  dirname(__FILE__).'/../../archiveServer/var/access',
    'pearPath'      =>  dirname(__FILE__).'/../../../../usr/lib/pear',
    'isArchive'     =>  TRUE,
    'validate'      =>  TRUE,
    'useTrash'      =>  FALSE,

    /* ==================================================== URL configuration */
    // on central archive side: archive is the storage !
    'storageUrlPath'        => '/campcasterArchiveServer',
    'storageXMLRPC'         => 'xmlrpc/xrArchive.php',
    'storageUrlHost'        => 'localhost',
    'storageUrlPort'        => 80,
    // have to be another remote archive:
    #'archiveUrlPath'        => '/campcasterArchiveServer',
    #'archiveXMLRPC'         => 'xmlrpc/xrArchive.php',
    #'archiveUrlHost'        => 'localhost',
    #'archiveUrlPort'        => 80,

    /* ==================================== aplication-specific configuration */
    'objtypes'      => array(
        'RootNode'      => array('Folder'),
        'Storage'       => array('Folder', 'File', 'Replica'),
        'Folder'        => array('Folder', 'File', 'Replica'),
        'File'          => array(),
        'audioclip'     => array(),
        'playlist'      => array(),
        'Replica'       => array(),
    ),
    'allowedActions'=> array(
        'RootNode'      => array('classes', 'subjects'),
        'Folder'        => array('editPrivs', 'write', 'read'),
        'File'          => array('editPrivs', 'write', 'read'),
        'audioclip'     => array('editPrivs', 'write', 'read'),
        'playlist'      => array('editPrivs', 'write', 'read'),
        'Replica'       => array('editPrivs', 'write', 'read'),
        '_class'        => array('editPrivs', 'write', 'read'),
    ),
    'allActions'    =>  array(
        'editPrivs', 'write', 'read', 'classes', 'subjects'
    ),

    /* ============================================== auxiliary configuration */
    'RootNode'      => 'RootNode',
    'tmpRootPass'   => 'q',
);

// Add database table names
$CC_CONFIG['filesTable'] = $CC_CONFIG['tblNamePrefix'].'files';
$CC_CONFIG['mdataTable'] = $CC_CONFIG['tblNamePrefix'].'mdata';
$CC_CONFIG['accessTable'] = $CC_CONFIG['tblNamePrefix'].'access';
$CC_CONFIG['permTable'] = $CC_CONFIG['tblNamePrefix'].'perms';
$CC_CONFIG['sessTable'] = $CC_CONFIG['tblNamePrefix'].'sess';
$CC_CONFIG['subjTable'] = $CC_CONFIG['tblNamePrefix'].'subjs';
$CC_CONFIG['smembTable'] = $CC_CONFIG['tblNamePrefix'].'smemb';
$CC_CONFIG['classTable'] = $CC_CONFIG['tblNamePrefix'].'classes';
$CC_CONFIG['cmembTable'] = $CC_CONFIG['tblNamePrefix'].'cmemb';
$CC_CONFIG['treeTable'] = $CC_CONFIG['tblNamePrefix'].'tree';
$CC_CONFIG['structTable'] = $CC_CONFIG['tblNamePrefix'].'struct';
$CC_CONFIG['transTable'] = $CC_CONFIG['tblNamePrefix'].'trans';
$CC_CONFIG['prefTable'] = $CC_CONFIG['tblNamePrefix'].'pref';

$CC_CONFIG['sysSubjs'] = array(
    'root', $CC_CONFIG['AdminsGr'], $CC_CONFIG['AllGr'], $CC_CONFIG['StationPrefsGr']
);
$old_ip = get_include_path();
set_include_path('.'.PATH_SEPARATOR.$CC_CONFIG['pearPath'].PATH_SEPARATOR.$old_ip);

//
// See if a ~/.campcaster/archiveServer.conf.php exists, and
// overwrite the settings from there, if any.
//
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
    $home_conf = $fileowner_homedir . '/.campcaster/archiveServer.conf.php';
    if (file_exists($home_conf)) {
        $default_config = $CC_CONFIG;
        include($home_conf);
        $user_config = $CC_CONFIG;
        $CC_CONFIG = $user_config + $default_config;
    }
}

?>