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

$sid = $_GET['subj'];

$all = M2tree::GetAllObjects();
foreach (ObjClasses::GetClasses() as $cl) {
    $all[] = array('name'=>$cl['cname']." (class)", 'id'=>$cl['id']);
}

foreach ($all as $it) {
    $aa = array();
    foreach (Alib::GetAllActions() as $a) {
        $aa[$a] = $r = Alib::CheckPerm($sid, $a, $it['id']);
        if (PEAR::isError($r)) {
            echo $r->getMessage()." ".$r->getUserInfo()."\n"; 
            exit; 
        }
    }
    $m[] = array($it['name'], $aa);
}
#echo"<pre>\n"; var_dump($m);
$u = Subjects::GetSubjName($sid);

?>
<html><head>
<title>ALib - permission matrix</title>
<link rel="stylesheet" type="text/css" href="default.css">
</head><body>
<h2>Permission matrix</h2>
<h2>User: <?php echo$u?></h2>
<table style="border:1px solid black">
<tr class="ev"><th>object</th>
<?php foreach (Alib::GetAllActions() as $a){?>
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
<pre><?php echo M2tree::DumpTree()?></pre>
</body></html>
