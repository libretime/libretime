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
    Version  : $Revision: 1.11 $
    Location : $ $

------------------------------------------------------------------------------*/
require_once"gbHtml_h.php";

/**
 *  storageServer WWW-form interface
 *
 *  @author  $Author: tomas $
 *  @version $Revision: 1.11 $
 *  @see Alib
 *  @see GreenBox
 */


// debugging utilities:
#header("Content-type: text/plain"); echo"GET:\n"; print_r($_GET); exit;
#header("Content-type: text/plain"); echo"POST:\n"; print_r($_POST); exit;
#header("Content-type: text/plain"); echo"REQUEST:\n"; print_r($_REQUEST); exit;
#header("Content-type: text/plain"); echo"FILES:\n"; print_r($_FILES); exit;
#echo"<pre>$redirUrl\n"; print_r($_REQUEST); exit;

define('BROWSER', "gbHtmlBrowse.php");

$sessid = $_REQUEST[$config['authCookieName']];
$userid = $gb->getSessUserId($sessid);
$login = $gb->getSessLogin($sessid);

$id = (!$_REQUEST['id'] ? $gb->storId : $_REQUEST['id']);

$redirUrl="gbHtmlBrowse.php?id=$id";

switch($_REQUEST['act']){
// --- authentication ---
/**
 *  login
 *
 *  Login to the storageServer.
 *  It set sessid to the cookie with name defined in ../conf.php
 *
 *  @param login string, username
 *  @param pass  string, password
 */
    case"login";
        $sessid = $gb->login($_REQUEST['login'], $_REQUEST['pass']);
        if($sessid && !PEAR::isError($sessid)){
            setcookie($config['authCookieName'], $sessid);
            $redirUrl="gbHtmlBrowse.php";
            $fid = $gb->getObjId($_REQUEST['login'], $gb->storId);
            if(!PEAR::isError($fid)) $redirUrl.="?id=$fid";
        }else{
            $redirUrl="gbHtmlLogin.php"; $_SESSION['alertMsg']='Login failed.';
        }
    break;
/**
 *  logout
 *
 *  Logut from storageServer, takes sessid from cookie
 *
 */
    case"logout";
        $gb->logout($sessid);
        setcookie($config['authCookieName'], '');
        $redirUrl="gbHtmlLogin.php";
    break;

// --- files ---
/**
 *  upload
 *
 *  Provides file upload and store it to the storage
 *
 *  @param filename string, name for the uploaded file
 *  @param mediafile file uploded by HTTP, raw binary media file
 *  @param mdatafile file uploded by HTTP, metadata XML file
 *  @param id int, destination folder id
 */
    case"upload":
        $tmpgunid = md5(
            microtime().$_SERVER['SERVER_ADDR'].rand()."org.mdlf.livesupport"
        );
        $ntmp = "{$gb->bufferDir}/$tmpgunid";
#        $ntmp = tempnam(""{$gb->bufferDir}", 'gbTmp_');
        $mdtmp = "";
        move_uploaded_file($_FILES['mediafile']['tmp_name'], $ntmp);
        chmod($ntmp, 0664);
        if($_FILES['mdatafile']['tmp_name']){
            $mdtmp = "$ntmp.xml";
            if(move_uploaded_file($_FILES['mdatafile']['tmp_name'], $mdtmp)){
                chmod($mdtmp, 0664);
            }
        }
        $r = $gb->putFile($id, $_REQUEST['filename'], $ntmp, $mdtmp, $sessid);
        if(PEAR::isError($r)) $_SESSION['alertMsg'] = $r->getMessage();
        else{
            @unlink($ntmp);
            @unlink($mdtmp);
        }
        $redirUrl = BROWSER."?id=$id";
    break;
/**
 *  newFolder
 *
 *  Create new folder in the storage
 *
 *  @param newname string, name for the new folder
 *  @param id int, destination folder id
 */
    case"newFolder":
        $r = $gb->createFolder($id, $_REQUEST['newname'], $sessid);
        if(PEAR::isError($r)) $_SESSION['alertMsg'] = $r->getMessage();
        $redirUrl = BROWSER."?id=$id";
    break;
/**
 *  rename
 *
 *  Change the name of file or folder
 *
 *  @param newname string, new name for the file or folder
 *  @param id int, destination folder id
 */
    case"rename":
        $parid = $gb->getParent($id);
        $r = $gb->renameFile($id, $_REQUEST['newname'], $sessid);
        if(PEAR::isError($r)) $_SESSION['alertMsg'] = $r->getMessage();
        $redirUrl = BROWSER."?id=$parid";
    break;
/**
 *  move
 *
 *  Move file to another folder
 *  TODO: format of destinantion path should be properly defined
 *
 *  @param newPath string, destination relative path
 *  @param id int, destination folder id
 */
    case"move":
        $newPath = urlencode($_REQUEST['newPath']);
        $did = $gb->getObjIdFromRelPath($id, $newPath);
        if(PEAR::isError($did)){
            $_SESSION['alertMsg'] = $did->getMessage();
            $redirUrl = BROWSER."?id=$parid";
            break;
        }
        $parid = $gb->getParent($id);
        $r = $gb->moveFile($id, $did, $sessid);
        if(PEAR::isError($r)){
            $_SESSION['alertMsg'] = $r->getMessage();
            $redirUrl = BROWSER."?id=$parid";
            break;
        }
        $redirUrl = BROWSER."?id=$did";
    break;
/**
 *  copy
 *
 *  Copy file to another folder
 *  TODO: format of destinantion path should be properly defined
 *
 *  @param newPath string, destination relative path
 *  @param id int, destination folder id
 */
    case"copy":
        $newPath = urldecode($_REQUEST['newPath']);
        $did = $gb->getObjIdFromRelPath($id, $newPath);
        $parid = $gb->getParent($id);
        $r = $gb->copyFile($id, $did, $sessid);
        if(PEAR::isError($r)){
            $_SESSION['alertMsg'] = $r->getMessage();
            #$_SESSION['alertMsg'] = $r->getMessage()." ".$r->getUserInfo();
            $redirUrl = BROWSER."?id=$parid";
        }
        else $redirUrl = BROWSER."?id=$did";
    break;
/**
 *  delete
 *
 *  Delete of stored file
 *
 *  @param id int, local id of deleted file or folder
 */
    case"delete":
        $parid = $gb->getParent($id);
        $r = $gb->deleteFile($id, $sessid);
        if(PEAR::isError($r)) $_SESSION['alertMsg'] = $r->getMessage();
        $redirUrl = BROWSER."?id=$parid";
    break;
/**
 *  getFile
 *
 *  Call access method and show access path.
 *  Example only - not really useable.
 *  TODO: resource should be released by release method call
 *
 *  @param id int, local id of accessed file
 */
    case"getFile":
        $r = $gb->access($id, $sessid);
        if(PEAR::isError($r)) $_SESSION['alertMsg'] = $r->getMessage();
        else echo $r;
        exit;
    break;
/**
 *  getMdata
 *
 *  Show file's metadata as XML
 *
 *  @param id int, local id of stored file
 */
    case"getMdata":
        header("Content-type: text/xml");
        $r = $gb->getMdata($id, $sessid);
        print_r($r);
        exit;
    break;
/**
 *  getInfo
 *
 *  Call getid3 library to analyze media file and show some results
 *
 *  @param
 *  @param
 */
    case"getInfo":
        header("Content-type: text/plain");
        $ia = $gb->analyzeFile($id, $sessid);
        echo"fileformat: {$ia['fileformat']}\n";
        echo"mime: {$ia['mime_type']}\n";
        echo"channels: {$ia['audio']['channels']}\n";
        echo"sample_rate: {$ia['audio']['sample_rate']}\n";
        echo"bits_per_sample: {$ia['audio']['bits_per_sample']}\n";
        echo"channelmode: {$ia['audio']['channelmode']}\n";
        echo"title: {$ia['id3v1']['title']}\n";
        echo"artist: {$ia['id3v1']['artist']}\n";
        echo"comment: {$ia['id3v1']['comment']}\n";
        exit;
    break;

// --- subjs ----
/**
 *  addSubj
 *
 *  Create new user or group (empty pass => create group)
 *
 *  @param login string, login name from new user
 *  @param pass string, password for new user
 */
    case"addSubj";
        $redirUrl="gbHtmlSubj.php";
        if($gb->checkPerm($userid, 'subjects')){
            $res = $gb->addSubj($_REQUEST['login'],
                ($_REQUEST['pass']=='' ? NULL:$_REQUEST['pass'] ));
        }else{
            $_SESSION['alertMsg']='Access denied.';
            break;
        }
        if(PEAR::isError($res)) $_SESSION['alertMsg'] = $res->getMessage();
    break;
/**
 *  removeSubj
 *
 *  Remove existing user or group
 *
 *  @param login string, login name of removed user
 */
    case"removeSubj";
        $redirUrl="gbHtmlSubj.php";
        if($gb->checkPerm($userid, 'subjects')){
            $res = $gb->removeSubj($_REQUEST['login']);
        }else{
            $_SESSION['alertMsg']='Access denied.';
            break;
        }
        if(PEAR::isError($res)) $_SESSION['alertMsg'] = $res->getMessage();
    break;
/**
 *  addSubj2
 *
 *  add subject to group
 *
 *  @param login string, login name of user added to group
 *  @param gname string, group name
 */
    case"addSubj2Gr";
        $redirUrl="gbHtmlSubj.php?id={$_REQUEST['reid']}";
        if($gb->checkPerm($userid, 'subjects')){
            $res = $gb->addSubj2Gr($_REQUEST['login'], $_REQUEST['gname']);
        }else{
            $_SESSION['alertMsg']='Access denied.';
            break;
        }
        if(PEAR::isError($res)) $_SESSION['alertMsg'] = $res->getMessage();
    break;
/**
 *  removeSubjFromGr
 *
 *  remove subject from group
 *
 *  @param login string, login name of user removed from group
 *  @param gname string, group name
 */
    case"removeSubjFromGr";
        $redirUrl="gbHtmlSubj.php?id={$_REQUEST['reid']}";
        if($gb->checkPerm($userid, 'subjects')){
            $res=$gb->removeSubjFromGr($_REQUEST['login'], $_REQUEST['gname']);
        }else{
            $_SESSION['alertMsg']='Access denied.';
            break;
        }
        if(PEAR::isError($res)) $_SESSION['alertMsg'] = $res->getMessage();
    break;
/**
 *  passwd
 *
 *  Change password for specified user
 *
 *  @param uid int, local user id
 *  @param oldpass string, old user password
 *  @param pass string, new password 
 *  @param pass2 string, retype of new password
 */
    case"passwd";
        $redirUrl="gbHtmlSubj.php";
        $ulogin = $gb->getSubjName($_REQUEST['uid']);
        if($userid != $_REQUEST['uid'] &&
            ! $gb->checkPerm($userid, 'subjects')){
            $_SESSION['alertMsg']='Access denied..';
            break;
        }
        if(FALSE === $gb->authenticate($ulogin, $_REQUEST['oldpass'])){
            $_SESSION['alertMsg']='Wrong old pasword.';
            break;
        }
        if($_REQUEST['pass'] !== $_REQUEST['pass2']){
            $_SESSION['alertMsg'] = "Passwords do not match. ".
                "({$_REQUEST['pass']}/{$_REQUEST['pass2']})";
            break;
        }
        $gb->passwd($ulogin, $_REQUEST['oldpass'], $_REQUEST['pass']);
    break;

// --- perms ---
/**
 *  addPerm
 *
 *  Add new permission record
 *
 *  @param subj int, local user/group id
 *  @param permAction string, type of action from set predefined in conf.php
 *  @param id int, local id of file/object
 *  @param allowDeny char, A or D
 */
    case"addPerm";
        $parid = $gb->getparent($_REQUEST['oid']);
        if($gb->checkPerm($userid, 'editPerms', $parid)){
            $gb->addPerm($_REQUEST['subj'], $_REQUEST['permAction'],
                $_REQUEST['id'], $_REQUEST['allowDeny']);
        }else{
            $_SESSION['alertMsg']='Access denied.';
        }
        $redirUrl="gbHtmlPerms.php?id=$id";
    break;
/**
 *  removePerm
 *
 *  Remove permission record
 *
 *  @param permid int, local id of permission record
 */
    case"removePerm";
        $parid = $gb->getparent($_REQUEST['oid']);
        if($gb->checkPerm($userid, 'editPerms', $parid))
            $gb->removePerm($_REQUEST['permid']);
        else $_SESSION['alertMsg']='Access denied.';
        $redirUrl="gbHtmlPerms.php?id=$id";
    break;

    default:
        $_SESSION['alertMsg']="Unknown method: {$_REQUEST['act']}";
        $redirUrl="gbHtmlLogin.php";
}

header("Location: $redirUrl");
?>