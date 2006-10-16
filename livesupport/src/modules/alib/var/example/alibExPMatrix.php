<?php
/**
 * @author $Author$
 * @version  $Revision$
 */
require_once "alib_h.php";

$sid=$_GET['subj'];

$all = $alib->getAllObjects();
foreach($alib->getClasses() as $cl)
    $all[] = array('name'=>$cl['cname']." (class)", 'id'=>$cl['id']);

foreach($all as $it){
 $aa=array();
 foreach($alib->getAllActions() as $a){
  $aa[$a] = $r = $alib->checkPerm($sid, $a, $it['id']);
  if(PEAR::isError($r)){
    echo $r->getMessage()." ".$r->getUserInfo()."\n"; exit; }
 }
 $m[]=array($it['name'], $aa);
}
#echo"<pre>\n"; var_dump($m);
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
  <td><?php echo($pr===TRUE ? 'Y' : '-')?></td>
 <?php }?>
</tr>
<?php }?>
</table>

<a href="javascript:back()">Back</a>
<hr>
Tree dump:
<pre><?php echo$alib->dumpTree()?></pre>
</body></html>
