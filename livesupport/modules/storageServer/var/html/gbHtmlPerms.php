<?php
// $Id: gbHtmlPerms.php,v 1.1 2004/09/12 21:59:09 tomas Exp $
require_once"gbHtml_h.php";
require_once"gbHtmlTestAuth.php";

$userid = $gb->getSessUserId($_REQUEST[$config['authCookieName']]);
$login = $gb->getSessLogin($_REQUEST[$config['authCookieName']]);

$id = (!$_REQUEST['id'] ? $gb->storId : $_REQUEST['id']);

#header("Content-type: text/plain"); print_r($_REQUEST); exit;
#header("Content-type: text/plain"); echo $gb->dumpTree($id, '    ')."\n"; exit;

// prefill data structure for template
$tpldata = array(
    'pathdata'  => $gb->getPath($id),
    'perms'     => $gb->getObjPerms($id),
    'actions'   => $gb->getAllowedActions($gb->getObjType($id)),
    'subjects'  => $gb->getSubjects(),
    'id'        => $id,
    'loggedAs'  => $login,
);
$tpldata['msg'] = $_SESSION['alertMsg']; unset($_SESSION['alertMsg']);

#header("Content-type: text/plain"); print_r($tpldata); exit;


#require_once"gbHtml_f.php";
// template follows:
?>
<html><head>
<title>Storage - permission editor</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link rel="stylesheet" type="text/css" href="default.css">
<style type="text/css">
<!--
 #menu { float:right; margin-right:1em; border:1px solid black; background-color:#ddd; padding:2px 1ex; }
 #tbl { width:60%; }
-->
</style>
</head><body>
<div id="menu">
 Logged as: <span class="b"><?php echo$tpldata['loggedAs']?></span><br>
 <a href="gbHttp.php?act=logout">Logout</a><br>
<?php if(is_array($tpldata['pathdata'])){?>
 <a href="gbHtmlBrowse.php?id=<?php echo$id?>">Browser</a><br>
<?php }else{?>
 <a href="gbHtmlBrowse.php">Tree editor</a><br>
<?php }?>
 <a href="gbHtmlSubj.php">User/Group editor</a><br>
</div>
    
<h1>Permission editor</h1>
<?php if(is_array($tpldata['pathdata'])){?>
<h2><a href="gbHtmlBrowse.php?id=<?=$id?>" title="Tree editor">Path</a>:
 <?php foreach($tpldata['pathdata'] as $k=>$it) {?>
  <?php echo$it["name"]?></a><span class="slash">/</span>
 <?php }?>
<?php }?>
</h2>

<table id="tbl" border="0" cellpadding="5">
<tr><td>subject name</td><td>action</td><td>permission</td><td></td></tr>
<?php if(is_array($tpldata['perms'])&&count($tpldata['perms'])>0) foreach($tpldata['perms'] as $k=>$row) {
    $da=($row['type']=='A' ? 'allow' : ($row['type']=='D' ? '<b>deny</b>' : $row['type']));?>
  <tr class="<?php echo(($o=1-$o) ? 'odd' : 'ev')?>">
    <td class="b"><a <?#href="alibExPList.php?id=<?php echo$row['subj']? >"?>><?php echo$row['login']?></a></td>
    <td class="b"><?php echo$row['action']?></td>
    <td><?php echo$da?></td>
    <td>
     <a href="gbHttp.php?act=removePerm&permid=<?php echo$row['permid']?>&oid=<?php echo$tpldata['id']?>&id=<?php echo$id?>"
        class="lnkbutt" onClick="return confirm('Delete permission &quot;<?=$da?>&nbsp;<?=$row['action']?>&quot; for user <?php echo$row['login']?>?')">remove</a>
    </td>
  </tr>
<?php }else{?>
 <tr class="odd"><td colspan="4">none</td></tr>
<?php }?>
</table>

<form action="gbHttp.php" method="post">
Add permission
<select name="allowDeny">
 <option value="A">Allow</option>
 <option value="D">Deny</option>
</select>
for action
<select name="permAction">
 <option value="_all">all</option>
<?php if(is_array($tpldata['actions'])) foreach($tpldata['actions'] as $k=>$it) {?>
 <option value="<?php echo$it?>"><?php echo$it?></option>
<?}?>
</select>
to subject
<select name="subj">
<?php if(is_array($tpldata['subjects'])) foreach($tpldata['subjects'] as $k=>$it) {?>
 <option value="<?php echo$it['id']?>"><?php echo$it['login']?></option>
<?}?>
</select>
<input type="hidden" name="act" value="addPerm">
<input type="hidden" name="id" value="<?php echo$tpldata['id']?>">
<input type="submit" value="Do it!">
</form>

<?php if($tpldata['msg']){?>
<script type="text/javascript">
<!--
 alert('<?php echo$tpldata['msg']?>');
-->
</script><noscript><hr><b><?php echo$tpldata['msg']?></b></hr></noscript>
<?php }?>
</body></html>