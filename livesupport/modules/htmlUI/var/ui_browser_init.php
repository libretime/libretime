<?php
session_start();

## LS classes/functions #############################################
require_once dirname(__FILE__).'/ui_conf.php';
require_once dirname(__FILE__).'/ui_browser.class.php';


## well known classes ###############################################
require_once dirname(__FILE__).'/Smarty/libs/Smarty.class.php';
require_once 'HTML/QuickForm/Renderer/ArraySmarty.php';

## initialize objects ###############################################
$Smarty         =& new Smarty;
$uiBrowser      =& new uiBrowser($config);
$uiBase         =& $uiBrowser;

## load Smarty+filters ##############################################
require_once  dirname(__FILE__).'/SmartyExtensions.inc.php';
#$Smarty->load_filter('output', 'trimwhitespace');
$Smarty->load_filter('post', 'template_marker');
$Smarty->load_filter('output', 'localizer');


## some basic things ################################################
$Smarty->assign('UI_BROWSER', UI_BROWSER);
$Smarty->assign('UI_HANDLER', UI_HANDLER);
$Smarty->assign('ACT', $_REQUEST['act']);
$Smarty->assign('CONFIG', $config);
$Smarty->assign('START', array(
                            'id'  => &$uiBrowser->id,
                            'pid' => &$uiBrowser->pid,
                            'fid' => &$uiBrowser->fid,
                            'sessid' => &$uiBrowser->sessid
                           ));
$Smarty->assign('USER', array('sessid' => &$uiBrowser->sessid,
                              'userid' => &$uiBrowser->userid,
                              'login'  => &$uiBrowser->login
                        ));
$uiBrowser->loadStationPrefs($ui_fmask['stationPrefs']);
$Smarty->assign('STATIONPREFS', $uiBrowser->STATIONPREFS);

## retransfer incomplete formdata from SESSION to POST-data #########
if (is_array($_SESSION['retransferFormData'])){
    foreach($_SESSION['retransferFormData'] as $k=>$v){
        $_POST[$k] = $v;
    }
    unset($_SESSION['retransferFormData']);
}
?>