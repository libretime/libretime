<?php
header("Content-type: text/html; charset=utf-8");
// CC classes/functions #############################################
require_once(dirname(__FILE__).'/ui_conf.php');
require_once(dirname(__FILE__).'/ui_browser.class.php');
require_once(dirname(__FILE__).'/ui_handler.class.php');

// often used classes ###############################################
require_once(dirname(__FILE__).'/../3rd_party/php/propel/runtime/lib/Propel.php');
require_once(dirname(__FILE__).'/../3rd_party/php/Smarty/libs/Smarty.class.php');
require_once('HTML/QuickForm/Renderer/ArraySmarty.php');
require_once(dirname(__FILE__).'/ui_scratchpad.class.php');
require_once(dirname(__FILE__).'/ui_search.class.php');
require_once(dirname(__FILE__).'/ui_browse.class.php');
require_once(dirname(__FILE__).'/ui_hubBrowse.class.php');
require_once(dirname(__FILE__).'/ui_hubSearch.class.php');
require_once(dirname(__FILE__).'/ui_playlist.class.php');
require_once(dirname(__FILE__).'/ui_scheduler.class.php');
require_once(dirname(__FILE__).'/ui_subjects.class.php');
require_once(dirname(__FILE__).'/ui_exchange.class.php');
require_once(dirname(__FILE__).'/ui_transfers.class.php');
require_once(dirname(__FILE__).'/ui_calendar.class.php');
require_once(dirname(__FILE__).'/ui_jscom.php');
require_once(dirname(__FILE__).'/ui_twitter.class.php');
require_once(dirname(__FILE__).'/init_load_once.php');

if (isset($WHITE_SCREEN_OF_DEATH) && ($WHITE_SCREEN_OF_DEATH == TRUE)) {
    echo __FILE__.':line '.__LINE__.": All includes loaded<br>";
}


?>
