<?php
session_start();

## LS classes/functions #############################################
require_once dirname(__FILE__).'/conf.php';
require_once dirname(__FILE__).'/ui_handler.class.php';

## initialize objects ###############################################
$uiHandler      =& new uiHandler($config);
$uiBase         =& $uiHandler;

ob_start();
?>
