<?php
// $Id: gbHtmlSubj.php,v 1.1 2004/09/12 21:59:11 tomas Exp $
require_once"gbHtml_h.php";
require_once"gbHtmlTestAuth.php";

switch($_REQUEST['act']){
    case "passwd":
        $type='passwd';
        break;
    default:
    if(isset($_GET['id']) && is_numeric($_GET['id'])){
        $id = $_GET['id']; $type='group';
    }else $type='list';
}

#header("Content-type: text/plain"); print_r($gb->listGroup($id)); exit;

// prefill data structure for template
switch($type){
    case "list":
        $d = array(
            'subj'       => $gb->getSubjectsWCnt(),
            'loggedAs'  => $login
        );
        break;
    case "group":
        $d = array(
            'rows'      => $gb->listGroup($id),
            'id'        => $id,
            'loggedAs'  => $login,
            'gname'     => $gb->getSubjName($id),
            'subj'       => $gb->getSubjects()
        );
        break;
    case "passwd":
        break;
    default:
}
$d['msg'] = $_SESSION['alertMsg']; unset($_SESSION['alertMsg']);

#header("Content-type: text/plain"); print_r($d); echo($list ? 'Y' : 'N')."\n"; exit;
#require_once"gbHtml_f.php";
// template follows:
?>
<html><head>
<title>Storage - user and roles editor</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link rel="stylesheet" type="text/css" href="default.css">
<style type="text/css">
<!--
 #menu { float:right; margin-right:1em; border:1px solid black; background-color:#ddd; padding:2px 1ex; }
 #parent, #parent a { background-color:#888; font-weight:bold; color:white; }
 #tree { width:60%; }
-->
</style>
</head><body>
<div id="menu">
 Logged as: <span class="b"><?php echo$d['loggedAs']?></span><br>
 <a href="gbHttp.php?act=logout">logout</a><br>
 <a href="gbHtmlBrowse.php?act=getHomeDir">Browser</a><br>
</div>
    
<h1>User/Group editor</h1>

<?php switch($type){ case "list":?>
<h3>Subjects:</h3>
<table id="tree" border="0" cellpadding="5">
<tr><td>id</td><td>login</td><td>user/group</td><td></td></tr>
<?php if(is_array($d['subj'])&&count($d['subj'])>0) foreach($d['subj'] as $k=>$c) {?>
  <tr class="<?php echo(($o=1-$o) ? 'odd' : 'ev')?>">
    <td><?php echo$c['id']?></td>
    <td class="b">
        <?if($c['type']=='G'){?>
            <a href="gbHtmlSubj.php?id=<?php echo$c['id']?>"><?php echo$c['login']?></a>
        <?}else{?><?php echo$c['login']?>
        <?}?>
     </td
     <td><?if($c['type']=='G'){?>(G:<?php echo$c['cnt']?>)<?}else{?> (U)<?}?></td>
    <td>
     <a class="lnkbutt" href="gbHttp.php?act=removeSubj&login=<?php echo urlencode($c['login'])?>">remove</a>
     <a class="lnkbutt" href="gbHtmlSubj.php?act=passwd&uid=<?php echo urlencode($c['id'])?>">change password</a>
    </td>
  </tr>
<?php }else{?>
 <tr class="odd"><td colspan="4">none</td></tr>
<?php }?>
</table>

<form action="gbHttp.php" method="post">
Add subject with name:  <input type="text" name="login" value="" size="10">
[and password:  <input type="password" name="pass" value="" size="10">]
<input type="hidden" name="act" value="addSubj">
<input type="submit" value="Do it!">
</form>

<?php break; case "group":?>

<h2>Subjects in group <?php echo$d['gname']?>:</h2>

<table id="tree" border="0" cellpadding="5">
  <tr id="parent">
    <td colspan="5">
        <a href="gbHtmlSubj.php">All subjects</a>
    </td>
  </tr>
<?php if(is_array($d['rows'])&&count($d['rows'])>0) foreach($d['rows'] as $k=>$row) {?>
  <tr class="<?php echo(($o=1-$o) ? 'odd' : 'ev')?>">
    <td><?php echo$row['id']?></td>
    <td class="b">
        <?if($row['type']=='G'){?>
            <a href="gbHtmlSubj.php?id=<?php echo$row['id']?>"><?php echo$row['login']?></a>
        <?}else{?><?php echo$row['login']?>
        <?}?>
     </td
     <td><?if($row['type']=='G'){?> (G)<?}else{?> (U)<?}?></td>
    <td>
     <a class="lnkbutt"
        href="gbHttp.php?act=removeSubjFromGr&login=<?php echo urlencode($row['login'])?>&gname=<?php echo urlencode($d['gname'])?>&reid=<?php echo$d['id']?>">
        removeFromGroup
     </a>
    </td>
  </tr>
<?php }else{?>
 <tr class="odd"><td colspan="3">none</td></tr>
<?php }?>
</table>

<form action="gbHttp.php" method="post">
Add subject
<select name="login">
<?php if(is_array($d['subj'])) foreach($d['subj'] as $k=>$row) {?>
 <option value="<?php echo$row['login']?>"><?php echo$row['login']?></option>
<?}?>
</select>
to group <?php echo$d['gname']?>
<input type="hidden" name="act" value="addSubj2Gr">
<input type="hidden" name="reid" value="<?php echo$d['id']?>">
<input type="hidden" name="gname" value="<?php echo$d['gname']?>">
<input type="submit" value="Do it!">
</form>

<?php break; case "passwd":?>
<form action="gbHttp.php" method="post">
<table>
<tr><td>Old password:</td><td><input type="password" name="oldpass" value=""></td></tr>
<tr><td>New password:</td><td><input type="password" name="pass" value=""></td></tr>
<tr><td>Retype:</td><td><input type="password" name="pass2" value=""></td></tr>
<tr><td colspan="2"><input type="submit" value="Submit"></td></tr>
</table>
<input type="hidden" name="uid" value="<?php echo $_REQUEST['uid']?>">
<input type="hidden" name="act" value="passwd">
</form>
<?php default: }?>

<?php if($d['msg']){?>
<script type="text/javascript">
<!--
 alert('<?php echo$d['msg']?>');
-->
</script>
<?php }?>
</body></html>