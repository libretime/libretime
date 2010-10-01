<?php
# NOTE: You have to load all classes that use session variables BEFORE you make a call to session_start()!!!
session_start();

// initialize objects ###############################################
$Smarty = new Smarty;
$Smarty->caching = false;
$Smarty->template_dir = dirname(__FILE__).'/templates/';
$Smarty->compile_dir  = dirname(__FILE__).'/templates_c/';
//$Smarty->config_dir   = '/web/www.example.com/guestbook/configs/';
//$Smarty->cache_dir    = '/web/www.example.com/guestbook/cache/';

$uiBrowser = new uiBrowser($CC_CONFIG);
$uiBrowser->init();

$uiHandler = new uiHandler($CC_CONFIG);
$uiHandler->init();
$uiBase =& $uiHandler;

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

if (isset($_SESSION["USER_ERROR"])) {
  $Smarty->assign('USER_ERROR', $_SESSION["USER_ERROR"]);
  unset($_SESSION["USER_ERROR"]);
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

?>