<?php
header("Content-type: text/html; charset=utf-8");
session_start();

require_once(dirname(__FILE__).'/ui_conf.php');
require_once(dirname(__FILE__).'/ui_handler.class.php');

$uiHandler = new uiHandler($CC_CONFIG);
$uiHandler->init();
$uiBase =& $uiHandler;

//include("../templates/loader/index.tpl");
ob_start();
?>