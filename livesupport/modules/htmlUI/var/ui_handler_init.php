<?php
session_start();


## LS classes/functions #############################################
require_once dirname(__FILE__).'/conf.php';
require_once dirname(__FILE__).'/ui_base.inc.php';
require_once dirname(__FILE__).'/ui_handler.class.php';
require_once dirname(__FILE__).'/ui_scratchpad.class.php';
require_once dirname(__FILE__).'/ui_playlist.class.php';
require_once dirname(__FILE__).'/ui_search.class.php';
require_once dirname(__FILE__).'/../../storageServer/var/GreenBox.php';
require_once dirname(__FILE__).'/formmask/general.inc.php';

## well known classes ###############################################
require_once 'DB.php';
require_once 'HTML/QuickForm.php';

#PEAR::setErrorHandling(PEAR_ERROR_TRIGGER, E_USER_WARNING);
#PEAR::setErrorHandling(PEAR_ERROR_CALLBACK, 'errCallBack');
PEAR::setErrorHandling(PEAR_ERROR_RETURN);
#PEAR::setErrorHandling(PEAR_ERROR_PRINT);


## initialize objects ###############################################
$uiHandler      =& new uiHandler($config);
#$uiBase        = new uiBase($config);
$uiBase         =& $uiHandler;
#$uiScratchPad   =& new uiScratchPad(&$uiHandler);
?>
