<?
require_once"gbHtml_h.php";
#header("Content-type: text/plain"); echo"GET:\n"; print_r($_GET); echo"POST:\n"; print_r($_POST); echo"REQUEST:\n"; print_r($_REQUEST); echo"FILES:\n"; print_r($_FILES); exit;
#echo"<pre>\n"; print_r($_SERVER); exit;

define('BROWSER', "gbHtmlBrowse.php");


$sessid = $_REQUEST[$config['authCookieName']];
$userid = $gb->getSessUserId($sessid);
$login = $gb->getSessLogin($sessid);

#$path = ($_REQUEST['path']=='' ? '/' : $_REQUEST['path']);
#$upath = urlencode($path);
#$id = $gb->_idFromPath($path);
$id = (!$_REQUEST['id'] ? $gb->storId : $_REQUEST['id']);


#if(PEAR::isError($id)){ $_SESSION['msg'] = $id->getMessage(); header("Location: ".BROWSER."?id=$id"); exit; }
$redirUrl="gbHtmlBrowse.php?id=$id";

switch($_REQUEST['act']){
// --- authentication ---
    case"login";
#        echo"<pre>\n"; print_r($_REQUEST); exit;
        $sessid = $gb->login($_REQUEST['login'], $_REQUEST['pass']);
        if($sessid && !PEAR::isError($sessid)){
#            echo"<pre>$sessid\n"; print_r($_REQUEST); exit;
            setcookie($config['authCookieName'], $sessid);
            $redirUrl="gbHtmlBrowse.php";
            $fid = $gb->getObjId($_REQUEST['login'], $gb->storId);
            if(!PEAR::isError($fid)) $redirUrl.="?id=$fid";
        }else{ $redirUrl="gbHtmlLogin.php"; $_SESSION['alertMsg']='Login failed.'; }
#        echo"<pre>$redirUrl\n"; print_r($_REQUEST); exit;
    break;
    case"logout";
        $gb->logout($sessid);
        setcookie($config['authCookieName'], '');
        $redirUrl="gbHtmlLogin.php";
    break;

// --- files ---
    case"upload":
        $tmpgunid = md5(microtime().$_SERVER['SERVER_ADDR'].rand()."org.mdlf.livesupport");
        $ntmp = "{$gb->storageDir}/buffer/$tmpgunid";
#        $ntmp = tempnam(""{$gb->storageDir}/buffer", 'gbTmp_');
        $mdtmp = "";
        move_uploaded_file($_FILES['mediafile']['tmp_name'], $ntmp); chmod($ntmp, 0664);
        if($_FILES['mdatafile']['tmp_name']){
            $mdtmp = "$ntmp.xml";
            if(move_uploaded_file($_FILES['mdatafile']['tmp_name'], $mdtmp)){
                chmod($mdtmp, 0664);
            }
        }
        $r = $gb->putFile($id, $_REQUEST['filename'], $ntmp, $mdtmp, $sessid);
        if(PEAR::isError($r)) $_SESSION['alertMsg'] = $r->getMessage();
        else{
#            $gb->updateMetadataDB($gb->_pathFromId($r), $mdata, $sessid);
            @unlink($ntmp);
            @unlink($mdtmp);
        }
        $redirUrl = BROWSER."?id=$id";
    break;
    case"newFolder":
        $r = $gb->createFolder($id, $_REQUEST['newname'], $sessid);
        if(PEAR::isError($r)) $_SESSION['alertMsg'] = $r->getMessage();
        $redirUrl = BROWSER."?id=$id";
    break;
    case"rename":
        $parid = $gb->getparent($id);
        $r = $gb->renameFile($id, $_REQUEST['newname'], $sessid);
        if(PEAR::isError($r)) $_SESSION['alertMsg'] = $r->getMessage();
        $redirUrl = BROWSER."?id=$parid";
    break;
/* NOT WORKING - sorry */
    case"move":
        $newPath = urlencode($_REQUEST['newPath']);
        $did = $gb->getObjIdFromRelPath($id, $newPath);
        $parid = $gb->getparent($id);
        $r = $gb->moveFile($id, $did, $sessid);
        if(PEAR::isError($r)){
            $_SESSION['alertMsg'] = $r->getMessage();
            $redirUrl = BROWSER."?id=$parid";
        }
        else $redirUrl = BROWSER."?id=$did";
    break;
    case"copy":
        $newPath = urldecode($_REQUEST['newPath']);
        $did = $gb->getObjIdFromRelPath($id, $newPath);
        $parid = $gb->getparent($id);
#        echo"<pre>\n$id\t$newPath\t$did\n"; print_r($did); exit;
        $r = $gb->copyFile($id, $did, $sessid);
        if(PEAR::isError($r)){
            $_SESSION['alertMsg'] = $r->getMessage();
            $redirUrl = BROWSER."?id=$parid";
        }
        else $redirUrl = BROWSER."?id=$did";
    break;
    case"repl":
        $unewpath = urlencode($_REQUEST['newpath']);
        $r = $gb->createReplica($id, $_REQUEST['newpath'], '', $sessid);
        if(PEAR::isError($r)) $_SESSION['alertMsg'] = $r->getMessage();
        $redirUrl = BROWSER."?id=$newparid";
    break;
/* */
    case"delete":
        $parid = $gb->getparent($id);
        $r = $gb->deleteFile($id, $sessid);
        if(PEAR::isError($r)) $_SESSION['alertMsg'] = $r->getMessage();
        $redirUrl = BROWSER."?id=$parid";
    break;
    case"getFile":
#        echo"<pre>$t, $ctype\n"; exit;
#        $r = $gb->getFile($id, $sessid);
        $r = $gb->access($id, $sessid);
        if(PEAR::isError($r)) $_SESSION['alertMsg'] = $r->getMessage();
        else echo $r;
        exit;
    break;
    case"getMdata":
        header("Content-type: text/xml");
        $r = $gb->getMdata($id, $sessid);
        print_r($r);
        exit;
    break;
    case"getInfo":
        header("Content-type: text/plain");
        $ia = $gb->analyzeFile($id, $sessid);
        echo"fileformat: {$ia['fileformat']}\n";
        echo"channels: {$ia['audio']['channels']}\n";
        echo"sample_rate: {$ia['audio']['sample_rate']}\n";
        echo"bits_per_sample: {$ia['audio']['bits_per_sample']}\n";
        echo"channelmode: {$ia['audio']['channelmode']}\n";
        echo"title: {$ia['id3v1']['title']}\n";
        echo"artist: {$ia['id3v1']['artist']}\n";
        echo"comment: {$ia['id3v1']['comment']}\n";
#        echo": {$ia['id3v1']['']}\n";
#        print_r($ia);
        exit;
    break;

// --- subjs ----
    case"addSubj";
        $redirUrl="gbHtmlSubj.php";
        if($gb->checkPerm($userid, 'subjects'))
            $res = $gb->addSubj($_REQUEST['login'], ($_REQUEST['pass']=='' ? NULL:$_REQUEST['pass'] ));
        else{ $_SESSION['alertMsg']='Access denied.'; break; }
        if(PEAR::isError($res)) $_SESSION['alertMsg'] = $res->getMessage();
    break;
    case"removeSubj";
        $redirUrl="gbHtmlSubj.php";
        if($gb->checkPerm($userid, 'subjects'))
            $res = $gb->removeSubj($_REQUEST['login']);
        else{ $_SESSION['alertMsg']='Access denied.'; break; }
        if(PEAR::isError($res)) $_SESSION['alertMsg'] = $res->getMessage();
    break;
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
            $_SESSION['alertMsg']="Passwords do not match. ({$_REQUEST['pass']}/{$_REQUEST['pass2']})";
            break;
        }
        $gb->passwd($ulogin, $_REQUEST['oldpass'], $_REQUEST['pass']);
    break;

// --- perms ---
    case"addPerm";
        if($gb->checkPerm($userid, 'editPerms', $_REQUEST['id']))
            $gb->addPerm($_REQUEST['subj'], $_REQUEST['permAction'], $_REQUEST['id'], $_REQUEST['allowDeny']);
        else $_SESSION['alertMsg']='Access denied.';
        $redirUrl="gbHtmlPerms.php?id=$id";
    break;
    case"removePerm";
        if($gb->checkPerm($userid, 'editPerms', $_REQUEST['oid']))
            $gb->removePerm($_GET['permid']);
        else $_SESSION['alertMsg']='Access denied.';
        $redirUrl="gbHtmlPerms.php?id=$id";
    break;

    default:
        $_SESSION['alertMsg']="Unknown method: {$_REQUEST['act']}";
        $redirUrl="gbHtmlLogin.php";
}

#echo"<pre>$redirUrl\n"; print_r($_REQUEST); exit;
header("Location: $redirUrl");
?>