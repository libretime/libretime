<?php 
// $Id: alibExTestAuth.php,v 1.1 2004/07/23 00:22:13 tomas Exp $
$login = $alib->getSessLogin($_REQUEST['alibsid']);
if(!isset($login)||$login==''){
    $_SESSION['alertMsg'] = "Login required";
    header("Location: alibExLogin.php");
    exit;
}
?>
