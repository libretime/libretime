<?php
/*------------------------------------------------------------------------------

    Copyright (c) 2004 Media Development Loan Fund
 
    This file is part of the LiveSupport project.
    http://livesupport.campware.org/
    To report bugs, send an e-mail to bugs@campware.org
 
    LiveSupport is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.
  
    LiveSupport is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.
 
    You should have received a copy of the GNU General Public License
    along with LiveSupport; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 
 
    Author   : $Author$
    Version  : $Revision$
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/alib/var/example/alibExPList.php,v $

------------------------------------------------------------------------------*/
require_once "alib_h.php";
require_once "alibExTestAuth.php";

if(isset($_GET['id']) && is_numeric($_GET['id'])){   $id = $_GET['id']; }
else $id=1;

// prefill data structure for template
    $d = array(
        'rows'      => $alib->getSubjPerms($id),
        'id'        => $id,
        'loggedAs'  => $login,
        'actions'   => $alib->getAllActions(),
        'name'      => $alib->getSubjName($id)
    );
    $d['msg'] = $_SESSION['alertMsg']; unset($_SESSION['alertMsg']);

require_once "alib_f.php";
// template follows:
?>
<html><head>
<title>Alib - permission list</title>
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
 <a href="alibHttp.php?act=logout">logout</a><br>
 <a href="alibExTree.php">Tree editor</a><br>
 <a href="alibExCls.php">Class editor</a><br>
 <a href="alibExSubj.php">User/group editor</a><br>
</div>
    
<h1>Subject permission list</h1>

<h2>Permissions for subject <?php echo$d['name']?>:</h2>

<table id="tree" border="0" cellpadding="5">
  <tr id="parent">
    <td colspan="4">
        <a href="alibExSubj.php">All subjects</a>
    </td>
  </tr>
<?php if(is_array($d['rows'])&&count($d['rows'])>0) foreach($d['rows'] as $k=>$row) {?>
  <tr class="<?php echo(($o=1-$o) ? 'odd' : 'ev')?>">
    <td><a class="b" href="alibExPerms.php?id=<?php echo$row['obj']?>"><?php echo$row['name']?></a>
        (<?php echo($row['otype']=='C' ? 'class' : $row['otype'])?>)
    </td
    <td class="b"><?php echo$row['action']?></td>
    <td><?php echo($row['type']=='A' ? 'allow' : ($row['type']=='D' ? '<b>deny</b>' : $row['type']))?></td>
    <td>
     <a class="lnkbutt" href="alibHttp.php?act=removePerm&permid=<?php echo$row['permid']?>&oid=<?php echo$row['obj']?>&reurl=plist&reid=<?php echo$d['id']?>">delete</a>
    </td>
  </tr>
<?php }else{?>
 <tr class="odd"><td colspan="4">none</td></tr>
<?php }?>
</table>

<?php if($d['msg']){?>
<script type="text/javascript">
<!--
 alert('<?php echo$d['msg']?>');
-->
</script>
<?php }?>
</body></html>