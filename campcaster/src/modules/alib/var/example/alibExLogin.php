<?php
/**
 * @author $Author$
 * @version  $Revision$
 */
require_once "alib_h.php";

// prefill data structure for template
$d = array(
    'users'     => Subjects::GetSubjects(),
    'actions'   => Alib::GetAllActions(),
    'objects'   => M2tree::GetAllObjects(),
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
require_once "alib_f.php";
// template follows:
?>
<html><head>
<title>Alib - example login</title>
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
 }
-->
</script>
</head><body>

<div id="help">
 Test accounts/pass:
 <ul style="margin:1px 0px">
  <li><a href="javascript:preloadLogin('root', 'q')">root/q</a></li>
  <li><a href="javascript:preloadLogin('test1', 'a')">test1/a</a></li>
  <li><a href="javascript:preloadLogin('test2', 'a')">test2/a</a></li>
  <li><a href="javascript:preloadLogin('test3', 'a')">test3/a</a></li>
  <li><a href="javascript:preloadLogin('test4', 'a')">test4/a</a></li>
 </ul>
</div>

<h1>ALib - tests/example</h1>

<form action="alibHttp.php" method="post" id="loginform">
<table>
<tr><td>Login:</td><td><input type="text" name="login"></td></tr>
<tr><td>Password:</td><td><input type="password" name="pass"></td></tr>
<tr><td colspan="2"><input type="hidden" name="act" value="login">
<input type="submit" value="Go!">
</td></tr>
</table>
</form>
<hr>

<form action="alibHttp.php" method="post">
Permission test:<br>
Subject: <select name="subj">
<?php if(is_array($d['users'])) foreach($d['users'] as $k=>$u) {?>
<option value="<?php echo$u['id']?>"<?php echo($d['lastSubj']==$u['id'] ? ' selected':'')?>><?php echo$u['login']?></option>
<?php }?>
</select>
action: <select name="permAction">
<?php if(is_array($d['actions'])) foreach($d['actions'] as $k=>$a) {?>
<option value="<?php echo$a?>"<?php echo($d['lastAction']==$a ? ' selected':'')?>><?php echo$a?></option>
<?php }?>
</select>
object: <select name="obj">
<?php if(is_array($d['objects'])) foreach($d['objects'] as $k=>$o) {?>
<option value="<?php echo$o['id']?>"<?php echo($d['lastObj']==$o['id'] ? ' selected':'')?>><?php echo$o['name']?></option>
<?php }?>
</select>
<input type="hidden" name="act" value="checkPerm">
<input type="submit" value="Go!">
</form>
<hr>

<form action="alibExPMatrix.php" method="get">
Permission matrix for subject: <select name="subj">
<?php if(is_array($d['users'])) foreach($d['users'] as $k=>$u) {?>
<option value="<?php echo$u['id']?>"<?php echo($d['lastSubj']==$u['id'] ? ' selected':'')?>><?php echo$u['login']?></option>
<?php }?>
</select>
<input type="submit" value="Go!">
</form>

<hr>

<!--<a href="../install.php?ak=inst">reset db + test all</a><br/>-->

<?php if($d['msg']){ //error message printing: ?>
<script type="text/javascript">
<!--
 alert('<?php echo$d['msg']?>');
-->
</script>
<?php }?>
<body></html>