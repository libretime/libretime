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
    Location : $URL$

------------------------------------------------------------------------------*/
require_once "alib_h.php";

#echo"<pre>\nGET:\n"; print_r($_GET); echo"POST:\n"; print_r($_POST); exit;

function getPGval($vn, $dfl='')
{
    return (isset($_POST[$vn])?$_POST[$vn]:(isset($_GET[$vn])?$_GET[$vn]:$dfl));
}

$userid = $alib->getSessUserId($_REQUEST['alibsid']);
$login = $alib->getSessLogin($_REQUEST['alibsid']);

$redirUrl="alibExTree.php".(($reid=getPGval('reid', '')) ? "?id=$reid":"");
$act = getPGval('act', 'nop');
switch($act)
{
    case"login";
        if($sessid = $alib->login($_POST['login'], $_POST['pass'])){
            setcookie('alibsid', $sessid);
            $redirUrl="alibExTree.php";
        }else{
            $redirUrl="alibExLogin.php"; $_SESSION['alertMsg']='Login failed.';
        }
    break;
    case"logout";
        $r = $alib->logout($_REQUEST['alibsid']);
        if(PEAR::isError($r)){
            $_SESSION['alertMsg'] = $r->getMessage().", ".$r->getUserInfo();
        }
        setcookie('alibsid', '');
        $redirUrl="alibExLogin.php";
    break;
    case"addNode";
        if($alib->checkPerm($userid, 'addChilds', $_POST['id'])
            && $_POST['type']!=''
            && $_POST['name']!=''
        ){
            $position = ($_POST['position']=='I' ? null : $_POST['position']);
            $oid = $alib->addObj(
                $_POST['name'], $_POST['type'], $_POST['id'], $position
            );
            if(PEAR::isError($oid)){
                $_SESSION['alertMsg'] =
                    $oid->getMessage().", ".$oid->getUserInfo();
            }else $r = $alib->addPerm($userid, '_all', $oid);
            if(PEAR::isError($r)){
                $_SESSION['alertMsg'] = $r->getMessage().", ".$r->getUserInfo();
            }
        }else $_SESSION['alertMsg']='Access denied.';
    break;
    case"deleteNode";
        if($alib->checkPerm($userid, 'delete', $_REQUEST['id']))
            $alib->removeObj($_GET['id']);
        else $_SESSION['alertMsg']='Access denied.';
    break;
    case"addPerm";
        $a = $alib->isClass($_POST['id']) ? 'classes':'editPerms';
        $id = $alib->isClass($_POST['id']) ? '':$_POST['id'];
        if($alib->checkPerm($userid, $a, $id)){
            $alib->addPerm(
                $_POST['subj'], $_POST['permAction'],
                $_POST['id'], $_POST['allowDeny']
            );
        }else $_SESSION['alertMsg']='Access denied.';
        $redirUrl = "alibExPerms.php".
            (($reid=getPGval('reid', '')) ? "?id=$reid":"");
    break;
    case"removePerm";
        $a = $alib->isClass($_REQUEST['oid']) ? 'classes':'editPerms';
        $oid = $alib->isClass($_REQUEST['oid']) ? NULL:$_REQUEST['oid'];
        if($alib->checkPerm($userid, $a, $oid))
            $alib->removePerm($_GET['permid']);
        else $_SESSION['alertMsg']='Access denied.';
        $redirUrl =
            ($_REQUEST['reurl']==plist ? "alibExPList.php":"alibExPerms.php").
            (($reid=getPGval('reid', '')) ? "?id=$reid":"");
    break;
    case"checkPerm";
        $res = $alib->checkPerm(
            $_POST['subj'], $_POST['permAction'], $_POST['obj']
        );
        $_SESSION['alertMsg'] = ($res ? "permitted: ":"DENIED: ").
            " {$_POST['permAction']} for ".$alib->getSubjName($_POST['subj']).
            " on ".$alib->getObjName($_POST['obj']);
        $_SESSION['lastPost']=$_POST;
        $redirUrl="alibExLogin.php";
    break;
    case"addClass";
        if($alib->checkPerm($userid, 'classes'))
            $alib->addClass($_POST['name']);
        else $_SESSION['alertMsg']='Access denied.';
        $redirUrl="alibExCls.php";
    break;
    case"removeClass";
        if($alib->checkPerm($userid, 'classes'))
            $alib->removeClassById($_GET['id']);
        else $_SESSION['alertMsg']='Access denied.';
        $redirUrl="alibExCls.php";
    break;
    case"addSubj";
        if($alib->checkPerm($userid, 'subjects'))
            $alib->addSubj($_POST['login'], $_POST['pass']);
        else $_SESSION['alertMsg']='Access denied.';
        $redirUrl="alibExSubj.php";
    break;
    case"removeSubj";
        if($alib->checkPerm($userid, 'subjects'))
            $alib->removeSubj($_GET['login']);
        else $_SESSION['alertMsg']='Access denied.';
        $redirUrl="alibExSubj.php";
    break;
    case"addSubj2Gr";
        if($alib->checkPerm($userid, 'subjects'))
            $alib->addSubj2Gr($_POST['login'], $_POST['gname']);
        else $_SESSION['alertMsg']='Access denied.';
        $redirUrl = "alibExSubj.php".
            (($id=getPGval('reid', '')) ? "?id=$reid":"");
    break;
    case"removeSubjFromGr";
        if($alib->checkPerm($userid, 'subjects'))
            $alib->removeSubjFromGr($_GET['login'], $_GET['gname']);
        else $_SESSION['alertMsg']='Access denied.';
        $redirUrl = "alibExSubj.php".
            (($id=getPGval('reid', '')) ? "?id=$reid":"");
    break;
    case"addObj2Class";
        if($alib->checkPerm($userid, 'classes'))
            $alib->addObj2Class($_POST['id'], $_POST['oid']);
        else $_SESSION['alertMsg']='Access denied. X1';
        $redirUrl="alibExCls.php".(($id=getPGval('id', '')) ? "?id=$id":"");
    break;
    case"removeObjFromClass";
        $id=getPGval('id', '');
        if($alib->checkPerm($userid, 'classes'))
            $alib->removeObjFromClass($_GET['oid'], $id);
        else $_SESSION['alertMsg']='Access denied.';
        $redirUrl="alibExCls.php".($id ? "?id=$id":"");
    break;
    default:
        $_SESSION['alertMsg']="Unknown method: $act";
}

require_once"alib_f.php";

header("Location: $redirUrl");
?>