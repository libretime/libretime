<?php
session_start();

## LS classes/functions #############################################
require_once dirname(__FILE__).'/conf.php';
require_once dirname(__FILE__).'/ui_base.inc.php';
require_once dirname(__FILE__).'/ui_browser.class.php';
require_once dirname(__FILE__).'/ui_scratchpad.class.php';
require_once dirname(__FILE__).'/ui_playlist.class.php'; #
require_once dirname(__FILE__).'/ui_search.class.php';
require_once dirname(__FILE__).'/../../storageServer/var/GreenBox.php';
require_once dirname(__FILE__).'/formmask/general.inc.php';

## well known classes ###############################################
require_once dirname(__FILE__).'/Smarty/libs/Smarty.class.php';

require_once 'DB.php';
require_once 'HTML/QuickForm.php';
require_once 'HTML/QuickForm/Renderer/ArraySmarty.php';

#PEAR::setErrorHandling(PEAR_ERROR_TRIGGER, E_USER_WARNING);
#PEAR::setErrorHandling(PEAR_ERROR_CALLBACK, 'errCallBack');
PEAR::setErrorHandling(PEAR_ERROR_RETURN);
#PEAR::setErrorHandling(PEAR_ERROR_PRINT);


## initialize objects ###############################################
$Smarty         =& new Smarty;
$uiBrowser      =& new uiBrowser($config);
#$uiBase            = new uiBase($config);
$uiBase         =& $uiBrowser;
#$uiScratchPad   = new uiScratchPad(&$uiBrowser);


## load Smarty+filters ##############################################
require_once  dirname(__FILE__).'/SmartyExtensions.inc.php';
#$Smarty->load_filter('output', 'trimwhitespace');
#$Smarty->load_filter('post', 'template_marker');
$Smarty->load_filter('post', 'localizer');


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