<?php
// $Id: alibExPMatrix.php,v 1.1 2004/07/23 00:22:13 tomas Exp $
require_once"alib_h.php";

$sid=$_GET['subj'];

foreach($alib->getAllObjects() as $it){
 $aa=array();
 foreach($alib->getAllActions() as $a){
  $aa[$a]=$alib->checkPerm($sid, $a, $it['id']);
#  if(PEAR::isError($aa[$a])){ errCallback($aa[$a]); }
 }
 $m[]=array($it['name'], $aa);
}
$u=$alib->getSubjName($sid);

?>
<html><head>
<title>ALib - permission matrix</title>
<link rel="stylesheet" type="text/css" href="default.css">
</head><body>
<h2>Permission matrix</h2>
<h2>User: <?php echo$u?></h2>
<table style="border:1px solid black">
<tr class="ev"><th>object</th>
<?php foreach($alib->getAllActions() as $a){?>
<th><?php echo$a?></th>
<?php }?>
</tr>
<?php if(is_array($m)) foreach($m as $k=>$v){ list($obj, $aa)=$v;?>
<tr class="<?php echo(($o=1-$o) ? 'odd' : 'ev')?>">
 <td><?php echo$obj?></td>
 <?php foreach($aa as $pr){?>
  <td><?php echo($pr ? 'Y' : '-')?></td>
 <?php }?>
</tr>
<?php }?>
</table>

<a href="javascript:back()">Back</a>
<hr>
Tree dump:
<pre><?php echo$alib->dumpTree()?></pre>
</body></html>
