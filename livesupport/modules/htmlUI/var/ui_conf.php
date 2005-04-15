<?php
define('UI_VERBOSE', FALSE);
define('UI_WARNING', TRUE);
define('UI_ERROR',   TRUE);

define('UI_DEFAULT_LANGID', 'en_GB');
#define('UI_TIMEZONE', ' +100');
define('UI_TIMEZONEOFFSET', date('Z'));

define('UI_HANDLER', 'ui_handler.php');
define('UI_BROWSER', 'ui_browser.php');
define('UI_FORM_STANDARD_METHOD', 'POST');
define('UI_INPUT_STANDARD_SIZE', 20);
define('UI_INPUT_STANDARD_MAXLENGTH', 50);
define('UI_TEXTAREA_STANDART_ROWS', 5);
define('UI_TEXTAREA_STANDART_COLS', 17);
define('UI_BUTTON_STYLE', 'button');
define('UI_QFORM_REQUIRED',     '../templates/sub/form_required.tpl');
define('UI_QFORM_REQUIREDNOTE', '../templates/sub/form_requirednote.tpl');
define('UI_QFORM_ERROR',        '../templates/sub/form_error.tpl');
define('UI_SEARCH_MAX_ROWS', 8);
define('UI_REGEX_URL', '/^(ht|f)tps?:\/\/[^ ]+$/');
define('UI_PL_ACCESSTOKEN_KEY', 'playlistToken');
define('UI_SCRATCHPAD_KEY',     'djBagContents');
define('UI_SCRATCHPAD_MAXLENGTH_KEY', 'djBagMaxlength');
#define('UI_SCRATCHPAD_REGEX', '/^[0-9a-f]{16}:[0-9]{4}-[0-9]{2}-[0-9]{2}$/');

## Session Keys
define('UI_SCRATCHPAD_SESSNAME',  'SCRATCHPAD');
define('UI_STATIONINFO_SESSNAME', 'STATIONINFO');
define('UI_SEARCH_SESSNAME',      'L_SEARCH');
define('UI_PLAYLIST_SESSNAME',    'PLAYLIST');
define('UI_BROWSE_SESSNAME',      'L_BROWSE');
define('UI_MDATA_REC_SESSNAME',   'MDATAREC');

## Metadata Keys
define('UI_MDATA_KEY_TITLE',      'dc:title');
define('UI_MDATA_KEY_CREATOR',    'dc:creator');
define('UI_MDATA_KEY_DURATION',   'dcterms:extent');
define('UI_MDATA_KEY_URL',        'ls:url');
define('UI_MDATA_KEY_FORMAT',     'dc:format');
define('UI_MDATA_KEY_DESCRIPTION','dc:description');
define('UI_MDATA_VALUE_FORMAT_FILE',    'File');
define('UI_MDATA_VALUE_FORMAT_STREAM',  'live stream');

## Simple Search Preferences
define('UI_SIMPLESEARCH_FILETYPE',  'File');
define('UI_SIMPLESEARCH_OPERATOR',  'OR');
define('UI_SIMPLESEARCH_LIMIT',     5);
define('UI_SIMPLESEARCH_ROWS',      3);
define('UI_SIMPLESEARCH_CAT1',      'dc:title');
define('UI_SIMPLESEARCH_OP1',       'partial');
define('UI_SIMPLESEARCH_CAT2',      'dc:creator');
define('UI_SIMPLESEARCH_OP2',       'partial');
define('UI_SIMPLESEARCH_CAT3',      'dc:source');
define('UI_SIMPLESEARCH_OP3',       'partial');

## Scheduler / Calendar
define('UI_SCHEDULER_FIRSTWEEKDAY', 1);

require_once dirname(__FILE__).'/../../storageServer/var/conf.php';
## LS classes/functions #############################################
require_once dirname(__FILE__).'/ui_base.inc.php';
require_once dirname(__FILE__).'/ui_scratchpad.class.php';
require_once dirname(__FILE__).'/ui_playlist.class.php';
require_once dirname(__FILE__).'/ui_search.class.php';
require_once dirname(__FILE__).'/ui_browse.class.php';
require_once dirname(__FILE__).'/../../storageServer/var/GreenBox.php';
require_once dirname(__FILE__).'/formmask/generic.inc.php';
require_once dirname(__FILE__).'/ui_calendar.class.php';
require_once dirname(__FILE__).'/ui_scheduler.class.php';

## well known classes ###############################################
require_once 'DB.php';
require_once 'HTML/QuickForm.php';

#PEAR::setErrorHandling(PEAR_ERROR_TRIGGER, E_USER_WARNING);
#PEAR::setErrorHandling(PEAR_ERROR_CALLBACK, 'errCallBack');
PEAR::setErrorHandling(PEAR_ERROR_RETURN);
#PEAR::setErrorHandling(PEAR_ERROR_PRINT);

## extent config
$config['audiofiles'] = array('.mp3' => TRUE,
                              '.wav' => TRUE,
                              '.ogg' => TRUE
                        );
?>