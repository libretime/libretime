<?
// $Id: gbHtmlTestAuth.php,v 1.1 2004/09/12 21:59:11 tomas Exp $
$login = $gb->getSessLogin($_REQUEST[$config['authCookieName']]);
if(!isset($login)||$login==''){
    $_SESSION['alertMsg'] = "Login required";
    header("Location: gbHtmlLogin.php");
    exit;
}
?>
