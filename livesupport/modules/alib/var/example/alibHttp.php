<?php 
// $Id: alibHttp.php,v 1.1 2004/07/23 00:22:13 tomas Exp $
require_once"alib_h.php";

#header("Content-type: text/plain"); echo"GET:\n"; print_r($_GET); echo"POST:\n"; print_r($_POST); exit;

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
        }else{ $redirUrl="alibExLogin.php"; $_SESSION['alertMsg']='Login failed.'; }
    break;
    case"logout";
        $r = $alib->logout($_REQUEST['alibsid']);
        if(PEAR::isError($r)) $_SESSION['alertMsg'] = $r->getMessage().", ".$r->getUserInfo();
        setcookie('alibsid', '');
        $redirUrl="alibExLogin.php";
    break;
    case"addNode";
        if($alib->checkPerm($userid, 'addChilds', $_POST['id'])
            && $_POST['type']!=''
            && $_POST['name']!=''
        ){
            $oid = $alib->addObj($_POST['name'], $_POST['type'], $_POST['id'], $_POST['position']);
            $alib->addPerm($userid, '_all', $oid);
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
        if($alib->checkPerm($userid, $a, $id))
            $alib->addPerm($_POST['subj'], $_POST['permAction'], $_POST['id'], $_POST['allowDeny']);
        else $_SESSION['alertMsg']='Access denied.';
        $redirUrl="alibExPerms.php".(($reid=getPGval('reid', '')) ? "?id=$reid":"");
    break;
    case"removePerm";
        $a = $alib->isClass($_REQUEST['oid']) ? 'classes':'editPerms';
        $oid = $alib->isClass($_REQUEST['oid']) ? '':$_REQUEST['oid'];
        if($alib->checkPerm($userid, $a, $oid))
            $alib->removePerm($_GET['permid']);
        else $_SESSION['alertMsg']='Access denied.';
        $redirUrl=($_REQUEST['reurl']==plist ? "alibExPList.php":"alibExPerms.php").(($reid=getPGval('reid', '')) ? "?id=$reid":"");
    break;
    case"checkPerm";
        $res = $alib->checkPerm($_POST['subj'], $_POST['permAction'], $_POST['obj']);
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
        $redirUrl="alibExSubj.php".(($id=getPGval('reid', '')) ? "?id=$reid":"");
    break;
    case"removeSubjFromGr";
        if($alib->checkPerm($userid, 'subjects'))
            $alib->removeSubjFromGr($_GET['login'], $_GET['gname']);
        else $_SESSION['alertMsg']='Access denied.';
        $redirUrl="alibExSubj.php".(($id=getPGval('reid', '')) ? "?id=$reid":"");
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