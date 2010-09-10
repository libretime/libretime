<?php
header("Content-type: text/html; charset=utf-8");

require_once(dirname(__FILE__).'/ui_conf.php');
require_once(dirname(__FILE__).'/ui_handler.class.php');
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
require_once(dirname(__FILE__).'/ui_twitter.class.php');

# NOTE: You have to load all classes that use session variables BEFORE you make a call to session_start()!!!
session_start();

$uiHandler = new uiHandler($CC_CONFIG);
$uiHandler->init();
$uiBase =& $uiHandler;

//include("../templates/loader/index.tpl");
ob_start();
?>