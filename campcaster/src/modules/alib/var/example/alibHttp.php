<?php
/**
 * @author Tomas Hlava <th@red2head.com>
 * @author Paul Baranowski <paul@paulbaranowski.org>
 * @version $Revision$
 * @package Campcaster
 * @subpackage StorageServer
 * @copyright 2006 MDLF, Inc.
 * @license http://www.gnu.org/licenses/gpl.txt
 * @link http://www.campware.org
 */
require_once("alib_h.php");

#echo"<pre>\nGET:\n"; print_r($_GET); echo"POST:\n"; print_r($_POST); exit;

function getPGval($vn, $dfl='')
{
    return (isset($_POST[$vn])?$_POST[$vn]:(isset($_GET[$vn])?$_GET[$vn]:$dfl));
}

$userid = Alib::GetSessUserId($_REQUEST['alibsid']);
$login = Alib::GetSessLogin($_REQUEST['alibsid']);

$redirUrl="alibExTree.php".(($reid=getPGval('reid', '')) ? "?id=$reid":"");
$act = getPGval('act', 'nop');
switch ($act) {
    case "login";
        if ($sessid = Alib::Login($_POST['login'], $_POST['pass'])) {
            setcookie('alibsid', $sessid);
            $redirUrl="alibExTree.php";
        } else {
            $redirUrl="alibExLogin.php"; $_SESSION['alertMsg']='Login failed.';
        }
        break;
    case "logout";
        $r = Alib::Logout($_REQUEST['alibsid']);
        if (PEAR::isError($r)) {
            $_SESSION['alertMsg'] = $r->getMessage().", ".$r->getUserInfo();
        }
        setcookie('alibsid', '');
        $redirUrl="alibExLogin.php";
        break;
    case "addNode";
        if (Alib::CheckPerm($userid, 'addChilds', $_POST['id'])
            && $_POST['type']!=''
            && $_POST['name']!='') {
            $position = ($_POST['position']=='I' ? null : $_POST['position']);
            $oid = M2tree::AddObj($_POST['name'], $_POST['type'], $_POST['id']);
            if (PEAR::isError($oid)) {
                $_SESSION['alertMsg'] =
                    $oid->getMessage().", ".$oid->getUserInfo();
            } else {
                $r = Alib::AddPerm($userid, '_all', $oid);
            }
            if (PEAR::isError($r)) {
                $_SESSION['alertMsg'] = $r->getMessage().", ".$r->getUserInfo();
            }
        } else {
            $_SESSION['alertMsg'] = 'Access denied.';
        }
        break;
    case "deleteNode";
        if (Alib::CheckPerm($userid, 'delete', $_REQUEST['id'])) {
            Alib::RemoveObj($_GET['id']);
        } else {
            $_SESSION['alertMsg'] = 'Access denied.';
        }
        break;
    case "addPerm";
        $a = ObjClasses::IsClass($_POST['id']) ? 'classes':'editPerms';
        $id = ObjClasses::IsClass($_POST['id']) ? '':$_POST['id'];
        if (Alib::CheckPerm($userid, $a, $id)) {
            Alib::AddPerm(
                $_POST['subj'], $_POST['permAction'],
                $_POST['id'], $_POST['allowDeny']
            );
        } else {
            $_SESSION['alertMsg']='Access denied.';
        }
        $redirUrl = "alibExPerms.php".
            (($reid=getPGval('reid', '')) ? "?id=$reid":"");
        break;
    case "removePerm";
        $a = ObjClasses::IsClass($_REQUEST['oid']) ? 'classes':'editPerms';
        $oid = ObjClasses::IsClass($_REQUEST['oid']) ? NULL:$_REQUEST['oid'];
        if (Alib::CheckPerm($userid, $a, $oid)) {
            Alib::RemovePerm($_GET['permid']);
        } else {
            $_SESSION['alertMsg']='Access denied.';
        }
        $redirUrl =
            ($_REQUEST['reurl']==plist ? "alibExPList.php":"alibExPerms.php").
            (($reid=getPGval('reid', '')) ? "?id=$reid":"");
        break;
    case "checkPerm";
        $res = Alib::CheckPerm(
            $_POST['subj'], $_POST['permAction'], $_POST['obj']
        );
        $_SESSION['alertMsg'] = ($res ? "permitted: ":"DENIED: ").
            " {$_POST['permAction']} for ".Subjects::GetSubjName($_POST['subj']).
            " on ".M2tree::GetObjName($_POST['obj']);
        $_SESSION['lastPost']=$_POST;
        $redirUrl = "alibExLogin.php";
        break;
    case "addClass";
        if (Alib::CheckPerm($userid, 'classes')) {
            ObjClasses::AddClass($_POST['name']);
        } else {
            $_SESSION['alertMsg'] = 'Access denied.';
        }
        $redirUrl="alibExCls.php";
        break;
    case "removeClass";
        if (Alib::CheckPerm($userid, 'classes')) {
            ObjClasses::RemoveClassById($_GET['id']);
        } else {
            $_SESSION['alertMsg']='Access denied.';
        }
        $redirUrl = "alibExCls.php";
        break;
    case "addSubj";
        if (Alib::CheckPerm($userid, 'subjects')) {
            Subjects::AddSubj($_POST['login'], $_POST['pass']);
        } else {
            $_SESSION['alertMsg']='Access denied.';
        }
        $redirUrl = "alibExSubj.php";
        break;
    case "removeSubj";
        if (Alib::CheckPerm($userid, 'subjects')) {
            Alib::RemoveSubj($_GET['login']);
        } else {
            $_SESSION['alertMsg']='Access denied.';
        }
        $redirUrl = "alibExSubj.php";
        break;
    case "addSubj2Gr";
        if (Alib::CheckPerm($userid, 'subjects')) {
            Subjects::AddSubjectToGroup($_POST['login'], $_POST['gname']);
        } else {
            $_SESSION['alertMsg'] = 'Access denied.';
        }
        $redirUrl = "alibExSubj.php".
            (($id=getPGval('reid', '')) ? "?id=$reid":"");
        break;
    case "removeSubjFromGr";
        if (Alib::CheckPerm($userid, 'subjects')) {
            Subjects::RemoveSubjectFromGroup($_GET['login'], $_GET['gname']);
        } else {
            $_SESSION['alertMsg']='Access denied.';
        }
        $redirUrl = "alibExSubj.php".
            (($id=getPGval('reid', '')) ? "?id=$reid":"");
        break;
    case "addObj2Class";
        if (Alib::CheckPerm($userid, 'classes')) {
            ObjClasses::AddObjectToClass($_POST['id'], $_POST['oid']);
        } else {
            $_SESSION['alertMsg']='Access denied. X1';
        }
        $redirUrl="alibExCls.php".(($id=getPGval('id', '')) ? "?id=$id":"");
        break;
    case "removeObjFromClass";
        $id = getPGval('id', '');
        if (Alib::CheckPerm($userid, 'classes')) {
            ObjClasses::RemoveObjectFromClass($_GET['oid'], $id);
        } else {
            $_SESSION['alertMsg'] = 'Access denied.';
        }
        $redirUrl = "alibExCls.php".($id ? "?id=$id":"");
        break;
    default:
        $_SESSION['alertMsg']="Unknown method: $act";
}

require_once("alib_f.php");

header("Location: $redirUrl");
?>