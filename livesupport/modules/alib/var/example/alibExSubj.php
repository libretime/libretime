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
 
 
    Author   : $Author: tomas $
    Version  : $Revision: 1.2 $
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/alib/var/example/alibExSubj.php,v $

------------------------------------------------------------------------------*/
require_once "alib_h.php";
require_once "alibExTestAuth.php";

if(isset($_GET['id']) && is_numeric($_GET['id'])){   $id = $_GET['id']; $list=false; }
else $list=true;

// prefill data structure for template
if($list){
    $d = array(
        'subj'       => $alib->getSubjectsWCnt(),
        'loggedAs'  => $login
    );
}else{
    $d = array(
        'rows'      => $alib->listGroup($id),
        'id'        => $id,
        'loggedAs'  => $login,
        'gname'     => $alib->getSubjName($id),
        'subj'       => $alib->getSubjects()
    );
}
$d['msg'] = $_SESSION['alertMsg']; unset($_SESSION['alertMsg']);

require_once "alib_f.php";
// template follows:
?>
<html><head>
<title>Alib - subjects editor</title>
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
<?php if(!$list){?>
 <a href="alibExPList.php?id=<?php echo$d['id']?>">Perms editor</a><br>
<?php }?>
 <a href="alibExCls.php">Class editor</a><br>
</div>
    
<h1>User/Group editor</h1>

<?php if($list){?>
<h3>Subjects:</h3>
<table id="tree" border="0" cellpadding="5">
<?php if(is_array($d['subj'])&&count($d['subj'])>0) foreach($d['subj'] as $k=>$c) {?>
  <tr class="<?php echo(($o=1-$o) ? 'odd':'ev')?>">
    <td><?php echo$c['id']?></td>
    <td class="b">
        <?php if($c['type']=='G'){?>
            <a href="alibExSubj.php?id=<?php echo$c['id']?>"><?php echo$c['login']?></a>
        <?php }else{?><?php echo$c['login']?>
        <?php }?>
     </td
     <td><?php if($c['type']=='G'){?>(G:<?php echo$c['cnt']?>)<?php }else{?> (U)<?php }?></td>
    <td>
     <a class="lnkbutt" href="alibHttp.php?act=removeSubj&login=<?php echo urlencode($c['login'])?>">delete</a>
<?php /*?>     <a class="lnkbutt" href="alibExPerms.php?id=<?php echo$c['id']?>&reid=<?php echo$d['id']?>">permissions</a><?php */?>
     <a class="lnkbutt" href="alibExPMatrix.php?subj=<?php echo$c['id']?>">permsMatrix</a>
     <a class="lnkbutt" href="alibExPList.php?id=<?php echo$c['id']?>">permsList</a>
    </td>
  </tr>
<?php }else{?>
 <tr class="odd"><td colspan="4">none</td></tr>
<?php }?>
</table>

<form action="alibHttp.php" method="post">
Add subject with name:  <input type="text" name="login" value="" size="10">
[and password:  <input type="password" name="pass" value="" size="10">]
<input type="hidden" name="act" value="addSubj">
<input type="submit" value="Do it!">
</form>

<?php }else{?>

<h2>Subjects in group <?php echo$d['gname']?>:</h2>

<table id="tree" border="0" cellpadding="5">
  <tr id="parent">
    <td colspan="5">
        <a href="alibExSubj.php">All subjects</a>
    </td>
  </tr>
<?php if(is_array($d['rows'])&&count($d['rows'])>0) foreach($d['rows'] as $k=>$row) {?>
  <tr class="<?php echo(($o=1-$o) ? 'odd':'ev')?>">
    <td><?php echo$row['id']?></td>
    <td class="b">
        <?php if($row['type']=='G'){?>
            <a href="alibExSubj.php?id=<?php echo$row['id']?>"><?php echo$row['login']?></a>
        <?php }else{?><?php echo$row['login']?>
        <?php }?>
     </td
     <td><?php if($row['type']=='G'){?> (G)<?php }else{?> (U)<?php }?></td>
    <td>
     <a class="lnkbutt"
        href="alibHttp.php?act=removeSubjFromGr&login=<?php echo urlencode($row['login'])?>&gname=<?php echo urlencode($d['gname'])?>&reid=<?php echo$d['id']?>">
        removeFromGroup
     </a>
<?php /*?>     <a class="lnkbutt" href="alibExPerms.php?id=<?php echo$row['id']?>&reid=<?php echo$d['id']?>">permissions</a><?php */?>
    </td>
  </tr>
<?php }else{?>
 <tr class="odd"><td colspan="3">none</td></tr>
<?php }?>
</table>

<form action="alibHttp.php" method="post">
Add subject
<select name="login">
<?php if(is_array($d['subj'])) foreach($d['subj'] as $k=>$row) {?>
 <option value="<?php echo$row['login']?>"><?php echo$row['login']?></option>
<?php }?>
</select>
to group <?php echo$d['gname']?>
<input type="hidden" name="act" value="addSubj2Gr">
<input type="hidden" name="reid" value="<?php echo$d['id']?>">
<input type="hidden" name="gname" value="<?php echo$d['gname']?>">
<input type="submit" value="Do it!">
</form>

<?php }?>

<?php if($d['msg']){?>
<script type="text/javascript">
<!--
 alert('<?php echo$d['msg']?>');
-->
</script>
<?php }?>
</body></html>