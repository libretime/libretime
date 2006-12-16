<?php
/**
 * @author $Author$
 * @version $Revision$
 */

require_once "alib_h.php";
require_once "alibExTestAuth.php";

if(isset($_GET['id']) && is_numeric($_GET['id']))   $id = $_GET['id'];
else   $id = M2tree::GetRootNode();

// prefill data structure for template
$d = array(
    'parid'     => $alib->getParent($id),
    'oname'     => M2tree::GetObjName($id),
    'path'      => M2tree::GetPath($id, 'id, name'),
    'rows'      => M2tree::GetDir($id, 'id, name, type'),
    'addtypes'  => M2tree::GetAllowedChildTypes(M2tree::GetObjType($id)),
    'dump'      => M2tree::DumpTree($id),
    'id'        => $id,
    'loggedAs'  => $login
);
$d['msg'] = preg_replace(array("|\n|","|'|"), array("\\n","\\'"), $_SESSION['alertMsg']); unset($_SESSION['alertMsg']);

#echo"<pre>\n"; var_dump($d['path']);exit;
require_once "alib_f.php";
// template follows:
?>
<html><head>
<title>Alib - tree editor</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link rel="stylesheet" type="text/css" href="default.css">
<style type="text/css">
<!--
 #menu { float:right; margin-right:1em; border:1px solid black; background-color:#ddd; padding:2px 1ex; }
 #current { background-color:#aaa; }
 #parent, #parent a { background-color:#888; font-weight:bold; color:white; }
 #tree { width:60%; }
-->
</style>
</head><body>
<div id="menu">
 Logged as: <span class="b"><?php echo$d['loggedAs']?></span><br>
 <a href="alibHttp.php?act=logout">logout</a><br>
<?php /*?> <a href="alibExPerms.php?id=<?php echo$d['id']?>">Permission editor</a><br><?php */?>
 <a href="alibExCls.php">Class editor</a><br>
 <a href="alibExSubj.php">User/Group editor</a><br>
</div>

<h1>Tree editor</h1>
<h3>Path:
<?php if(is_array($d['path'])) foreach($d['path'] as $k=>$it) {?>
<a <?php if($it["id"]!=$id){?>href="alibExTree.php?id=<?php echo $it["id"]?>"<?php }?>><?php echo$it["name"]?></a><span class="slash">/</span><?php }?>
</h3>

<table id="tree" border="0" cellpadding="5">
  <tr id="current">
    <td colspan="2">Current node: <b><?php echo $d['oname']?></b></dt>
    <td>
     <a class="lnkbutt" href="alibExPerms.php?id=<?php echo$d['id']?>&reid=<?php echo$d['id']?>">permissions</a>
    </td>
  </tr>
  <tr id="parent">
    <td colspan="3">
<?php if(is_numeric($d['parid'])){?><a href="alibExTree.php?id=<?php echo$d['parid']?>">Parent: ..</a>
<?php }else{?>/<?php }?>
    </dt>
  </tr>
<?php if(is_array($d['rows'])&&count($d['rows'])>0) foreach($d['rows'] as $k=>$row) {?>
  <tr class="<?php echo(($o=1-$o) ? 'odd' : 'ev')?>">
    <td><?php echo$row['id']?></td>
    <td><a href="alibExTree.php?id=<?php echo$row['id']?>" class="b"><?php echo$row['name']?></a> (<?php echo$row['type']?>)</td>
    <td>
     <a class="lnkbutt" href="alibHttp.php?act=deleteNode&id=<?php echo$row['id']?>&reid=<?php echo$d['id']?>">delete</a>
     <a class="lnkbutt" href="alibExPerms.php?id=<?php echo$row['id']?>&reid=<?php echo$d['id']?>">permissions</a>
    </td>
  </tr>
<?php }else{?>
 <tr class="odd"><td colspan="3">none</td></tr>
<?php }?>
</table>

<form action="alibHttp.php" method="post">
Add object of type
<select name="type">
<?php if(is_array($d['addtypes'])) foreach($d['addtypes'] as $k=>$row) {?>
 <option value="<?php echo$row?>"><?php echo$row?></option>
<?php }?>
</select>
with name
<input type="text" name="name" value="" size="10">
<select name="position">
<option value="<?php echo$d['id']?>">as first node</option>
<?php if(is_array($d['rows'])) foreach($d['rows'] as $k=>$row) {?>
 <option value="<?php echo$row['id']?>">after <?php echo$row['name']?></option>
<?php }?>
<option value="<?php echo$row['id']?>" selected>as last node</option>
</select>
<input type="hidden" name="act" value="addNode">
<input type="hidden" name="id" value="<?php echo$d['id']?>">
<input type="hidden" name="reid" value="<?php echo$d['id']?>">
<input type="submit" value="Do it!">
</form>

<pre><b>Subtree dump:</b><br><?php echo$d['dump']?></pre>
<?php #php echo"pre">; print_r($d); echo"</pre>";?>

<?php if($d['msg']){?>
<script type="text/javascript">
<!--
 alert('<?php echo$d['msg']?>');
-->
</script>
<?php }?>
</body></html>