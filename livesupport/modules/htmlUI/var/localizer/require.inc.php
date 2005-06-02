<?php
require_once('PEAR.php');
require_once('DB.php');
require_once('File.php');
require_once('File/Find.php');
require_once('XML/Serializer.php');
require_once('XML/Unserializer.php');

require_once('display.inc.php');
require_once('data.inc.php');
require_once('helpfunctions.inc.php');

define('_DEFAULT_LANG_', 'en_UK');
define('_PREFIX_',       'locals');
define('_PREFIX_GLOBAL_','globals');
define('_PREFIX_HIDE_',  '.');
define('_ICONS_DIR_',     '.icons');
define('_LANG_BASE_',    'xml');
define('_DENY_HTML_',    FALSE);
define('_ENCODING_',     'UTF-8');
define('_START_DIR_', '../');
define('_MAINTAINANCE_', TRUE);
define('_PARENT_FRAME_', '_parent');
define('_PANEL_FRAME_',  'panel');
define('_MENU_FRAME_',   'menu');
define('_FRAME_SCRIPT_', 'index.php');
define('_PANEL_SCRIPT_', 'main.php');
define('_MENU_SCRIPT_',  'menu.php');
#define('_MENU_SCRIPT_',  'menu_static.php');
?>
