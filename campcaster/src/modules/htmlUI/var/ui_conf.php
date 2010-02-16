<?php
ini_set('memory_limit', '64M');

// Warning/Error level
define('UI_DEBUG', FALSE);
define('UI_VERBOSE', FALSE);
define('UI_WARNING', TRUE);
define('UI_ERROR', TRUE);

if (UI_DEBUG) {
	error_reporting(E_ALL);
}

define('UI_TESTSTREAM_MU3_TMP', 'img/listen.m3u');

// Local settings
define('UI_DEFAULT_LANGID', 'en_GB');
//define('UI_UPLOAD_LANGID', $_SESSION['langid']);
define('UI_UPLOAD_LANGID', UI_DEFAULT_LANGID);
define('UI_TIMEZONEOFFSET', date('Z'));

// Basic scripts
define('UI_HANDLER', 'ui_handler.php');
define('UI_BROWSER', 'ui_browser.php');

// HTML Form stuff
define('UI_STANDARD_FORM_METHOD', 'POST');
define('UI_INPUT_STANDARD_SIZE', 50);
define('UI_INPUT_STANDARD_MAXLENGTH', 255);
define('UI_TEXTAREA_STANDART_ROWS', 5);
define('UI_TEXTAREA_STANDART_COLS', 32);
define('UI_BUTTON_STYLE', 'button');
define('UI_QFORM_REQUIRED', '../templates/sub/form_required.tpl');
define('UI_QFORM_REQUIREDNOTE', '../templates/sub/form_requirednote.tpl');
define('UI_QFORM_ERROR', '../templates/sub/form_error.tpl');
define('UI_REGEX_URL', '/^(ht|f)tps?:\/\/[^ ]+$/');

// DB ls_pref keys
define('UI_PL_ACCESSTOKEN_KEY', 'playlistToken');
define('UI_SCRATCHPAD_KEY', 'scratchpadContents');
define('UI_SCRATCHPAD_MAXLENGTH_KEY', 'scratchpadMaxlength');
//define('UI_SCRATCHPAD_REGEX', '/^[0-9a-f]{16}:[0-9]{4}-[0-9]{2}-[0-9]{2}$/');

// Session Keys
define('UI_SCRATCHPAD_SESSNAME', 'SCRATCHPAD');
define('UI_STATIONINFO_SESSNAME', 'STATIONINFO');
define('UI_BROWSE_SESSNAME', 'L_BROWSE');
define('UI_SEARCH_SESSNAME', 'L_SEARCH');
define('UI_HUBBROWSE_SESSNAME', 'L_HUBBROWSE');
define('UI_HUBSEARCH_SESSNAME', 'L_HUBSEARCH');
define('UI_TRANSFER_SESSNAME', 'L_TRANSFER');
define('UI_PLAYLIST_SESSNAME', 'PLAYLIST');
define('UI_LOCALIZER_SESSNAME', 'LOCALIZER');
define('UI_CALENDAR_SESSNAME', 'CALENDAR');

// Metadata Keys
define('UI_MDATA_KEY_TITLE', 'dc:title');
define('UI_MDATA_KEY_CREATOR', 'dc:creator');
define('UI_MDATA_KEY_DURATION', 'dcterms:extent');
define('UI_MDATA_KEY_URL', 'ls:url');
define('UI_MDATA_KEY_FORMAT', 'dc:format');
define('UI_MDATA_KEY_DESCRIPTION', 'dc:description');
define('UI_MDATA_KEY_CHANNELS', 'ls:channels');
define('UI_MDATA_KEY_SAMPLERATE', 'ls:samplerate');
define('UI_MDATA_KEY_BITRATE', 'ls:bitrate');
define('UI_MDATA_KEY_ENCODER', 'ls:encoder');
define('UI_MDATA_VALUE_FORMAT_FILE', 'File');
define('UI_MDATA_VALUE_FORMAT_STREAM', 'live stream');

// Search/Browse preferences
define('UI_SIMPLESEARCH_FILETYPE', 'Audioclip');
define('UI_SIMPLESEARCH_OPERATOR', 'OR');
define('UI_SIMPLESEARCH_LIMIT', 10);
define('UI_SIMPLESEARCH_ROWS', 3);
define('UI_SIMPLESEARCH_CAT1', 'dc:title');
define('UI_SIMPLESEARCH_OP1', 'partial');
define('UI_SIMPLESEARCH_CAT2', 'dc:creator');
define('UI_SIMPLESEARCH_OP2', 'partial');
define('UI_SIMPLESEARCH_CAT3', 'dc:source');
define('UI_SIMPLESEARCH_OP3', 'partial');

define('UI_SEARCH_MAX_ROWS', 8);
define('UI_SEARCH_DEFAULT_LIMIT', 10);
define('UI_SEARCHRESULTS_DELTA', 4);

define('UI_BROWSERESULTS_DELTA', 4);
define('UI_BROWSE_DEFAULT_KEY_1', 'dc:type');
define('UI_BROWSE_DEFAULT_KEY_2', 'dc:creator');
define('UI_BROWSE_DEFAULT_KEY_3', 'dc:source');
define('UI_BROWSE_DEFAULT_LIMIT', 10);

define('UI_HUB_POLLING_FREQUENCY', 3);

// Scheduler / Calendar
define('UI_SCHEDULER_FIRSTWEEKDAY', 1);
define('UI_SCHEDULER_DEFAULT_VIEW', 'day');
define('UI_SCHEDULER_PAUSE_PL2PL', '0 seconds');
define('UI_SCHEDULER_IMPORTTOKEN_KEY', 'schedulerImportToken');
define('UI_SCHEDULER_EXPORTTOKEN_KEY', 'schedulerExportToken');

// File types
define('UI_FILETYPE_ANY', 'all');
define('UI_FILETYPE_PLAYLIST', 'playlist');
define('UI_FILETYPE_AUDIOCLIP', 'audioClip');
define('UI_FILETYPE_WEBSTREAM', 'webstream');

// Playlist elements
define('UI_PL_ELEM_PLAYLIST', 'playlistElement');
define('UI_PL_ELEM_FADEINFO', 'fadeInfo');
define('UI_PL_ELEM_FADEIN', 'fadeIn');
define('UI_PL_ELEM_FADEOUT', 'fadeOut');

// Export/Import
define('UI_BACKUPTOKEN_KEY', 'backupToken');
define('UI_RESTORETOKEN_KEY', 'restoreToken');

require_once(dirname(__FILE__).'/../../storageServer/var/conf.php');
define('UI_VERSION', CAMPCASTER_VERSION);
define('UI_VERSION_FULLNAME', 'Campcaster '.UI_VERSION);
define('UI_COPYRIGHT_DATE', CAMPCASTER_COPYRIGHT_DATE);

// extent config
$CC_CONFIG = array_merge($CC_CONFIG,
    array(
        'file_types'    => array(
                            '.mp3',
                            '.ogg'
                            //'.wav',
                            //'.flac',
                            //'.aac'
        ),
        'stream_types'  => array(
                            'application/ogg',
                            'audio/mpeg',
                            'audio/x-mpegurl'
        ),
        'languages'     => array(
                            'ar_JO' => 'Arabic(JO)',
                            'am_AM' => 'Armenian(AM)',
                            'en_GB' => 'English (GB)',
                            'en_US' => 'English (US)',
                            'es_CO' => 'Español (CO)',
                            'cz_CZ' => 'Česky (CZ)',
                            'de_DE' => 'Deutsch (DE)',
                            'hu_HU' => 'Magyar (HU)',
                            'nl_NL' => 'Nederlands (NL)',
                            'sr_CS' => 'Srpski (CS)',
                            'ru_RU' => 'Russia(RU)'
        ),
    )
);

require_once(dirname(__FILE__).'/ui_base.inc.php');
require_once(dirname(__FILE__).'/../../storageServer/var/GreenBox.php');
require_once(dirname(__FILE__).'/formmask/generic.inc.php');

require_once('DB.php');
require_once('HTML/QuickForm.php');

// Connect to the database
$CC_DBC = DB::connect($CC_CONFIG['dsn'], TRUE);
if (PEAR::isError($CC_DBC)) {
    echo "Could not connect to database.  Your current configuration is:<br>";
    echo "<table border=1>";
    echo "<tr><td>Host name:</td><td>".$CC_CONFIG['dsn']['hostspec']."</td></tr>";
    echo "<tr><td>Database name:</td><td>".$CC_CONFIG['dsn']['database']."</td></tr>";
    echo "<tr><td>User name:</td><td>".$CC_CONFIG['dsn']['username']."</td></tr>";
    echo "</table>";
    exit;
}
$CC_DBC->setFetchMode(DB_FETCHMODE_ASSOC);

//PEAR::setErrorHandling(PEAR_ERROR_TRIGGER, E_USER_WARNING);
//PEAR::setErrorHandling(PEAR_ERROR_CALLBACK, 'errCallBack');
PEAR::setErrorHandling(PEAR_ERROR_RETURN);
//PEAR::setErrorHandling(PEAR_ERROR_PRINT);
?>
