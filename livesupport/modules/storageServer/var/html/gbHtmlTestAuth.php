<?php
// $Id: gbHtmlTestAuth.php,v 1.2 2004/09/21 00:27:41 tomas Exp $
$login = $gb->getSessLogin($_REQUEST[$config['authCookieName']]);
if(!isset($login)||$login==''){
    $_SESSION['alertMsg'] = "Login required";
    header("Location: gbHtmlLogin.php");
    exit;
}
?>
