<?php
// $Id: gbHtmlLogin.php,v 1.2 2004/09/29 01:37:40 tomas Exp $
require_once"gbHtml_h.php";

// prefill data structure for template
$d = array(
    'users'     => $gb->getSubjects(),
    'actions'   => $gb->getAllActions(),
    'objects'   => $gb->getAllObjects(),
    'msg'       => $_SESSION['alertMsg']
);
unset($_SESSION['alertMsg']);

// forms prefill:
if(is_array($_SESSION['lastPost'])) $d = array_merge($d, array(
    'lastSubj'  => $_SESSION['lastPost']['subj'],
    'lastAction'=> $_SESSION['lastPost']['permAction'],
    'lastObj'   => $_SESSION['lastPost']['obj']
));
unset($_SESSION['lastPost']);

#header("Content-type: text/plain"); print_r($d); exit;
#require_once"gbHtml_f.php";
// template follows:
?>
<html><head>
<title>Archive - login</title>
<link rel="stylesheet" type="text/css" href="default.css">
<style type="text/css">
<!--
 #help { float:right; margin-right:1em; border:1px solid black; background-color:#ddd; padding:2px 1ex; }
 -->
</style>
<script type="text/javascript">
<!--
 function preloadLogin(u, p)
 {
    var f=document.getElementById('loginform');
    f.login.value=u;
    f.pass.value=p;
    f.submit();
 }
-->
</script>
</head><body>

<div id="help">
 Test accounts/pass:
 <ul style="margin:1px 0px">
  <li><a href="javascript:preloadLogin('root', 'q')">root/q</a></li>
 </ul>
</div>

<h1>Archive - login</h1>

<form action="gbHttp.php" method="post" id="loginform">
<table>
<tr><td>Login:</td><td><input type="text" name="login"></td></tr>
<tr><td>Password:</td><td><input type="password" name="pass"></td></tr>
<tr><td colspan="2"><input type="hidden" name="act" value="login">
<input type="submit" value="Go!">
</td></tr>
</table>
</form>

<?php if($d['msg']){ //error message printing: ?>
<script type="text/javascript">
<!--
 alert('<?php echo$d['msg']?>');
-->
</script>
<?php }?>
<body></html>