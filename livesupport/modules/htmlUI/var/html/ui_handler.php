<?php
require dirname(__FILE__).'/../ui_handler_init.php';

switch($_REQUEST['act']){

    case "login":
        $uiHandler->login($_REQUEST, $ui_fmask["loginform"]);
    break;

    case "logout":
        $uiHandler->logout();
    break;

    case "signover":
        $uiHandler->logout(TRUE);
    break;

    case "upload":     ## media- and metadata file together #####
        $uiHandler->upload(array_merge($_REQUEST, $_FILES), $uiHandler->id, $ui_fmask["upload"]);
    break;

    case "uploadFile": ## just media file #######################
        $uiHandler->uploadFile(array_merge($_REQUEST, $_FILES), $uiHandler->id, $ui_fmask["uploadFile"]);
    break;

    case "newFolder":
        $uiHandler->newFolder($_REQUEST["newname"], $uiHandler->id);
    break;

    case "rename":
        $uiHandler->rename($_REQUEST["newname"], $uiHandler->id);
    break;

    case "move":
        $uiHandler->move($_REQUEST["newPath"], $uiHandler->id);
    break;

    case "copy":
        $uiHandler->copy($_REQUEST["newPath"], $uiHandler->id);
    break;

    case "delete":
        $uiHandler->delete($uiHandler->id, $_REQUEST["delOverride"]);
    break;

    case "addUser":
        $uiHandler->addSubj($_REQUEST, $ui_fmask["addUser"]);
    break;

    case "addGroup":
        $uiHandler->addSubj($_REQUEST, $ui_fmask["addGroup"]);
    break;

    case "removeSubj":
        $uiHandler->removeSubj($_REQUEST["login"]);
    break;

    case "changePasswd":
        $uiHandler->passwd($_REQUEST["uid"], $_REQUEST["oldpass"], $_REQUEST["pass"], $_REQUEST["pass2"]);
    break;

    case "addPerm":
        $uiHandler->addPerm($_REQUEST["subj"], $_REQUEST["permAction"], $uiHandler->id, $_REQUEST["allowDeny"]);
    break;

    case "removePerm":
        $uiHandler->removePerm($_REQUEST["permid"], $_REQUEST["oid"]);
    break;

    case "addSubj2Group":
        $uiHandler->addSubj2Group($_REQUEST["login"], $_REQUEST["gname"], $_REQUEST["reid"]);
    break;

    case "removeSubjFromGr":
        $uiHandler->removeSubjFromGr($_REQUEST["login"], $_REQUEST["gname"], $_REQUEST["reid"]);
    break;

    case "systemPrefs":
        $uiHandler->storeSystemPrefs(array_merge($_REQUEST, $_FILES), $ui_fmask["systemPrefs"]);
    break;

    case "editMetaDataValues":
        $uiHandler->storeMetaData($_REQUEST, $ui_fmask["mData"]);
    break;

    case "add2SP":
        $uiHandler->add2SP($uiHandler->id);
    break;

    case "remFromSP":
        $uiHandler->remFromSP($uiHandler->id);
    break;

    default:
        $_SESSION["alertMsg"] = $uiHandler->tra("Unknown method: ").$_REQUEST["act"];
        header("Location: ".UI_BROWSER);
        die();
}
if ($uiHandler->alertMsg) $_SESSION['alertMsg'] = $uiHandler->alertMsg;
header('Location: '.$uiHandler->redirUrl);
?>
