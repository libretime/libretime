<?php
/**
 * @author $Author$
 * @version $Revision$
 */
$login = Alib::GetSessLogin($_REQUEST['alibsid']);
if(!isset($login)||$login==''){
    $_SESSION['alertMsg'] = "Login required";
    header("Location: alibExLogin.php");
    exit;
}

?>