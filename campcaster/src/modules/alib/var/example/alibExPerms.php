<?php
/**
 * @author Tomas Hlava <th@red2head.com>
 * @author Paul Baranowski <paul@paulbaranowski.org>
 * @version  $Revision$
 * @package Campcaster
 * @subpackage StorageServer
 * @copyright 2006 MDLF, Inc.
 * @license http://www.gnu.org/licenses/gpl.txt
 * @link http://www.campware.org
 */

require_once("alib_h.php");
require_once("alibExTestAuth.php");

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = $_GET['id'];
} else {
    $id = M2tree::GetRootNode();
}

// prefill data structure for template
if (!ObjClasses::IsClass($id)) {
    $d = array(
        'path'      => M2tree::GetPath($id, 'id,name'),
        'perms'     => Alib::GetObjPerms($id),
        'actions'   => Alib::GetAllowedActions(M2tree::GetObjType($id)),
        'subjects'  => Subjects::GetSubjects(),
        'id'        => $id,
        'loggedAs'  => $login
    );
} else {
    $d = array(
        'path'      => '',
        'name'      => ObjClasses::GetClassName($id),
        'perms'     => Alib::GetObjPerms($id),
        'actions'   => Alib::GetAllowedActions('_class'),
        'subjects'  => Subjects::GetSubjects(),
        'id'        => $id,
        'loggedAs'  => $login
    );
}
$d['msg'] = $_SESSION['alertMsg']; unset($_SESSION['alertMsg']);

require_once("alib_f.php");
// template follows:
?>
<html><head>
<title>Alib - permission editor</title>
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
 Logged as: <span class="b"><?php echo$d['loggedAs']?></span><br>
 <a href="alibHttp.php?act=logout">Logout</a><br>
<?php if(is_array($d['path'])){?>
 <a href="alibExTree.php?id=<?php echo$d['id']?>">Tree editor</a><br>
<?php }else{?>
 <a href="alibExTree.php">Tree editor</a><br>
<?php }?>
 <a href="alibExCls.php">Class editor</a><br>
 <a href="alibExSubj.php">User/Group editor</a><br>
</div>

<h1>Permission editor</h1>
<?php if(is_array($d['path'])){?>
<h2><a href="alibExTree.php?id=<?php echo$d['id']?>" title="Tree editor">Path</a>:
 <?php foreach($d['path'] as $k=>$it) {?>
<a <?php if($it["id"]!=$id){?>href="?id=<?php echo $it["id"]?>"<?php }?>><?php echo$it["name"]?></a><span class="slash">/</span>
 <?php }?>
<?php }else{?>Class <a href="alibExCls.php?id=<?php echo$d['id']?>"><?php echo$d['name']?></a>
<?php }?>
</h2>

<table id="tbl" border="0" cellpadding="5">
<?php if(is_array($d['perms'])&&count($d['perms'])>0) foreach($d['perms'] as $k=>$row) {?>
  <tr class="<?php echo(($o=1-$o) ? 'odd' : 'ev')?>">
    <td class="b"><a href="alibExPList.php?id=<?php echo$row['subj']?>"><?php echo$row['login']?></a></td>
    <td class="b"><?php echo$row['action']?></td>
    <td><?php echo($row['type']=='A' ? 'allow' : ($row['type']=='D' ? '<b>deny</b>' : $row['type']))?></td>
    <td>
     <a class="lnkbutt" href="alibHttp.php?act=removePerm&permid=<?php echo$row['permid']?>&oid=<?php echo$d['id']?>&reid=<?php echo$d['id']?>">delete</a>
    </td>
  </tr>
<?php }else{?>
 <tr class="odd"><td colspan="4">none</td></tr>
<?php }?>
</table>

<form action="alibHttp.php" method="post">
Add permission
<select name="allowDeny">
 <option value="A">Allow</option>
 <option value="D">Deny</option>
</select>
for action
<select name="permAction">
 <option value="_all">all</option>
<?php if(is_array($d['actions'])) foreach($d['actions'] as $k=>$it) {?>
 <option value="<?php echo$it?>"><?php echo$it?></option>
<?php }?>
</select>
to subject
<select name="subj">
<?php if(is_array($d['subjects'])) foreach($d['subjects'] as $k=>$it) {?>
 <option value="<?php echo$it['id']?>"><?php echo$it['login']?></option>
<?php }?>
</select>
<input type="hidden" name="act" value="addPerm">
<input type="hidden" name="reid" value="<?php echo$d['id']?>">
<input type="hidden" name="id" value="<?php echo$d['id']?>">
<input type="submit" value="Do it!">
</form>

<?php if($d['msg']){?>
<script type="text/javascript">
<!--
 alert('<?php echo$d['msg']?>');
-->
</script><noscript><hr><b><?php echo$d['msg']?></b></hr></noscript>
<?php }?>
</body></html>