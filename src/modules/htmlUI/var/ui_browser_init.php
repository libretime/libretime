<?php
header("Content-type: text/html; charset=utf-8");

// CC classes/functions #############################################
require_once(dirname(__FILE__).'/ui_conf.php');
require_once(dirname(__FILE__).'/ui_browser.class.php');
require_once(dirname(__FILE__).'/ui_handler.class.php');

// often used classes ###############################################
require_once(dirname(__FILE__).'/Smarty/libs/Smarty.class.php');
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

if (isset($WHITE_SCREEN_OF_DEATH) && ($WHITE_SCREEN_OF_DEATH == TRUE)) {
    echo __FILE__.':line '.__LINE__.": All includes loaded<br>";
}
# NOTE: You have to load all classes that use session variables BEFORE you make a call to session_start()!!!
session_start();

// initialize objects ###############################################
$Smarty = new Smarty;
$uiBrowser = new uiBrowser($CC_CONFIG);
$uiBrowser->init();

$uiBase =& $uiBrowser;
$jscom = new jscom(array("jscom_wrapper"));
$jscom->handler();


// load Smarty+filters ##############################################
require_once(dirname(__FILE__).'/ui_smartyExtensions.inc.php');
//$Smarty->load_filter('output', 'trimwhitespace');
//$Smarty->load_filter('post', 'template_marker');
$Smarty->load_filter('output', 'localizer');


// some basic things ################################################
foreach (get_defined_constants() as $k=>$v) {
    $Smarty->assign($k, $v);
}

$Smarty->assign('ACT', isset($_REQUEST['act'])?$_REQUEST['act']:null);
$Smarty->assign('CONFIG', $CC_CONFIG);
$Smarty->assign('START', array(
                            'id'        => &$uiBrowser->id,
                            //'pid'       => &$uiBrowser->pid,
                            //'fid'       => &$uiBrowser->fid,
                            'sessid'    => &$uiBrowser->sessid)
                          );
$Smarty->assign('USER', array(
                            'sessid'  => &$uiBrowser->sessid,
                            'userid'  => &$uiBrowser->userid,
                            'login'   => &$uiBrowser->login)
                          );
$uiBrowser->loadStationPrefs($ui_fmask['stationPrefs']);
$Smarty->assign('STATIONPREFS', $uiBrowser->STATIONPREFS);
$Smarty->assign_by_ref('_REQUEST', $_REQUEST);
$Smarty->assign_by_ref('_SESSION', $_SESSION);
// retransfer incomplete formdata from SESSION to POST-data #########
if (isset($_SESSION['retransferFormData']) && is_array($_SESSION['retransferFormData'])) {
    foreach($_SESSION['retransferFormData'] as $k=>$v){
        $_POST[$k] = $v;
    }
    unset($_SESSION['retransferFormData']);
}

if (isset($WHITE_SCREEN_OF_DEATH) && ($WHITE_SCREEN_OF_DEATH == TRUE)) {
    echo __FILE__.':line '.__LINE__.": end of file<br>";
}

?>