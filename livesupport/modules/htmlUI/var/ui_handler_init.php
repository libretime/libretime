<?php
session_start();

## LS classes/functions #############################################
require_once dirname(__FILE__).'/ui_conf.php';
require_once dirname(__FILE__).'/ui_handler.class.php';

## initialize objects ###############################################
$uiHandler      =& new uiHandler($config);
$uiBase         =& $uiHandler;
?>
<html>
<link rel="stylesheet" href="styles.css">
<body>
Your request is processed...
<?php
ob_start();
?>
