<?php
/**
 * @package Campware
 */

/**
 * Since the XML_Serializer package is not yet stable,
 * we must use our own package.  The package has a bug fix applied
 * that is required for the Localizer XML files to work.
 */
require_once 'XML/Serializer.php';
require_once 'XML/Unserializer.php';

global $g_localizerConfig;

// Maintenance Mode allow to add languages and edit default language
$g_localizerConfig['MAINTENANCE'] = TRUE;

// Debug Mode to display additinal info
$g_localizerConfig['DEBUG'] = FALSE;

// The default language, which forms the keys for 
// all other languages.
$g_localizerConfig['DEFAULT_LANGUAGE'] = 'default_DEFAULT';

// Filename prefix for translation files.
$g_localizerConfig['FILENAME_PREFIX'] = 'locals';

// Order keys alphabetically?
$g_localizerConfig['ORDER_KEYS'] = FALSE;

// Delete unsed key from non-default language files on sync?
$g_localizerConfig['DELETE_UNUSED_ON_SYNC'] = TRUE; 

// Filename prefix for the global translation file -
// a file that is always loaded with the particular 
// locals file.
#$g_localizerConfig['FILENAME_PREFIX_GLOBAL'] = 'globals';

// Set to a specific type if your code is using that type.
// Currently supported types are 'gs' and 'xml'.
// You can also set this to the empty string and the code
// will do its best to figure out the current type.
$g_localizerConfig['DEFAULT_FILE_TYPE'] = 'xml';

// The top-level directory to the set of directories
// that need translation files.
#$g_localizerConfig['BASE_DIR'] = $_SERVER['DOCUMENT_ROOT'].'/admin-files';
$g_localizerConfig['BASE_DIR'] = dirname(__FILE__).'/';

// The top-level directory to the set of directories
// that need translation files.
#$g_localizerConfig['TRANSLATION_DIR'] = $_SERVER['DOCUMENT_ROOT'].'/admin-files/lang';
$g_localizerConfig['TRANSLATION_DIR'] = dirname(__FILE__).'/lang/';

// Name of the XML file that contains the list of supported languages.
$g_localizerConfig['LANGUAGE_METADATA_FILENAME'] = 'languages.xml';

// File encoding for XML files.
$g_localizerConfig['FILE_ENCODING'] = 'UTF-8';

// For the interface - the relative path (from DOCUMENT_ROOT)
// of the icons directory
#global $Campsite;
#$g_localizerConfig['ICONS_DIR'] = $Campsite['ADMIN_IMAGE_BASE_URL'];
$g_localizerConfig['ICONS_DIR'] = 'icon/';

// The size of the input fields for the admin interface.
$g_localizerConfig['INPUT_SIZE'] = 70;

// List supported file types, in order of preference.
$g_localizerConfig['FILE_TYPES'] = array('xml', 'gs');

$g_localizerConfig['LOADED_FILES'] = array();

// Map of prefixes to directory names.
$mapPrefixToDir = array();
$mapPrefixToDir[""] = null;
#$mapPrefixToDir["globals"] = null;

//////////////////////////////////////////////////////////////////////////////////////////////////////////////
// mapPrefixToDir = array(                                                                                  //
//  localisation_filename = array(                                                                          //
//      path         = start search here (relativre from localizer install folder                           //
//      depth        = how deep is recursive search from path                                               //
//      filePatterns = array of matching file names                                                         //
//      execlPattern = pattern string of files which have not to be scanned (e.g. hidden files, .cvs)       //
//      funcPatterns = array of patterns descriping string which have to identified and position of match   //
//      display      = screen name displayed in select menu                                                 //
//////////////////////////////////////////////////////////////////////////////////////////////////////////////
$g_localizerConfig['mapPrefixToDir'] = array(
    'application'   => array(
            'path'          => '../',
            'depth'         => 1,
            'filePatterns'  => array('/(.*).php/'),
            'execlPattern'  => '/(\/\.|^\.)/',
            'funcPatterns'  => array('/_retMsg( )*\(( )*\'([^\']*)\'/iU' => 3, '/(put|get)gs( )*\(( )*"([^"]*)"/iU' => 3), 
            'display'       => 'Application'
    ),
    'templates'     => array(
            'path'          => '../templates',
            'depth'         => 2,
            'filePatterns'  => array('/(.*).tpl/'),
            'execlPattern'  => '/(\/\.|^\.)/',
            'funcPatterns'  => array('/##([^{}]*)##/U' => 1, '/{tra[ ]*str=\'([^\']*)\'.*}/iU' => 1),
            'display'       => 'Templates'
    ),
    'masks'         => array(
            'path'          => '../formmask',
            'depth'         => 1,
            'filePatterns'  => array('/(.*).php/'),
            'execlPattern'  => '/(\/\.|^\.)/',
            'funcPatterns'  => array('/[\'"]label[\'"] *=> *[\'"]([^\'"]+)[\'"]/iU' => 1),
            'display'       => 'Forms'
    ),
    /*
    'localizer'     => array(
            'path'          => '/',
            'depth'         => 1,
            'filePatterns'  => array('/(.*).php/'),
            'execlPattern'  => '/(\/\.|^\.)/',
            'funcPatterns'  => array('/(put|get)gs( )*\(( )*\'([^\']*)\'/iU' => 3, '/(put|get)gs( )*\(( )*"([^"]*)"/iU' => 3),
            'display'       => 'Localizer itself'
    )
    */
);   

$g_localizerConfig["MAP_PREFIX_TO_DIR"] = $mapPrefixToDir;
unset($mapPrefixToDir);

?>