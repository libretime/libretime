<?php
/**
 * @author $Author$
 * @version $Revision$
 */
$login = $alib->getSessLogin($_REQUEST['alibsid']);
if(!isset($login)||$login==''){
    $_SESSION['alertMsg'] = "Login required";
    header("Location: alibExLogin.php");
    exit;
}

?>