<?php    
require dirname(__FILE__).'/../ui_handler_init.php';

switch($_REQUEST['act']){

    case "login":
        $uiHandler->login($_REQUEST, $ui_fmask["login"]);
    break;

    case "logout":
        $uiHandler->SCRATCHPAD->save();
        $uiHandler->logout();
    break;

    case "signover":
        $uiHandler->SCRATCHPAD->save();
        $uiHandler->logout(TRUE);
    break;

    case "uploadFileM":
        if ($ui_tmpid = $uiHandler->uploadFileM(array_merge($_REQUEST, $_FILES), $uiHandler->id, $ui_fmask["uploadFileM"]))
            $uiHandler->SCRATCHPAD->addItem($ui_tmpid);
    break;

    case "uploadFile":
        if ($ui_tmpid = $uiHandler->uploadFile(array_merge($_REQUEST, $_FILES), $uiHandler->id, $ui_fmask["uploadFile"]))
            $uiHandler->SCRATCHPAD->addItem($ui_tmpid);
    break;

    case "replaceFile":
        $ui_tmpgunid = $uiHandler->gb->_gunidFromId($uiHandler->id);
        if ($uiHandler->delete($uiHandler->id)) {
            $ui_tmpid = $uiHandler->uploadFile(array_merge($_REQUEST, $_FILES), $uiHandler->pid, $ui_fmask["uploadFile"], $ui_tmpgunid);
            $uiHandler->SCRATCHPAD->removeItems($uiHandler->id);
            $uiHandler->SCRATCHPAD->addItem($ui_tmpid);
        }
    break;

    case "addWebstream":
        if ($ui_tmpid = $uiHandler->addWebstream($_REQUEST, $uiHandler->id, $ui_fmask['addWebstream']))
            $uiHandler->SCRATCHPAD->addItem($ui_tmpid);
    break;

    case "replaceWebstream":
        $ui_tmpgunid = $uiHandler->gb->_gunidFromId($uiHandler->id);
        if ($uiHandler->delete($uiHandler->id)) {
            $ui_tmpid = $uiHandler->addWebstream($_REQUEST, $uiHandler->pid, $ui_fmask['addWebstream'], $ui_tmpgunid);
            $uiHandler->SCRATCHPAD->removeItems($uiHandler->id);
            $uiHandler->SCRATCHPAD->addItem($ui_tmpid);
        }
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
        if ($uiHandler->delete($uiHandler->id, $_REQUEST['delOverride']))
            if ($uiHandler->type != 'Folder')
                $uiHandler->SCRATCHPAD->removeItems($uiHandler->id);
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

    case "chgPasswd":
        $uiHandler->chgPasswd($_REQUEST["uid"], $_REQUEST["oldpass"], $_REQUEST["pass"], $_REQUEST["pass2"]);
    break;

    case "addPerm":
        $uiHandler->addPerm($_REQUEST["subj"], $_REQUEST["permAction"], $uiHandler->id, $_REQUEST["allowDeny"]);
    break;

    case "removePerm":
        $uiHandler->removePerm($_REQUEST["permid"], $_REQUEST["oid"]);
    break;

    case "addGroupMember":
        $uiHandler->addSubj2Group($_REQUEST);
    break;

    case "removeGroupMember":
        $uiHandler->removeGroupMember($_REQUEST);
    break;

    case "editSystemPrefs":
        $uiHandler->editSystemPrefs(array_merge($_REQUEST, $_FILES), $ui_fmask["systemPrefs"]);
    break;

    case "editMetaData":
        $uiHandler->editMetaData($uiHandler->id, $_REQUEST);
        $uiHandler->SCRATCHPAD->reLoadM();
    break;

    case "SP.addItem":
        $uiHandler->SCRATCHPAD->addItem($_REQUEST['id']);
        $uiHandler->SCRATCHPAD->setReload();
    break;

    case "SP.removeItem":
        $uiHandler->SCRATCHPAD->removeItems($_REQUEST['id']);
        $uiHandler->SCRATCHPAD->setReload();
    break;

    case "SP.reOrder":
        $uiHandler->SCRATCHPAD->reOrder($_REQUEST['by']);
        $uiHandler->SCRATCHPAD->setReload();
    break;

    case "SEARCH.newsearch":
        $uiHandler->SEARCH->newsearch($_REQUEST);
    break;

    case "SEARCH.reOrder":
        $uiHandler->SEARCH->reOrder($_REQUEST['by']);
    break;

    case "SEARCH.clear":
        $uiHandler->SEARCH->clear();
    break;

    case "SEARCH.setOffset":
        $uiHandler->SEARCH->setOffset($_REQUEST['page']);
    break;

    case "activatePL":
        $uiHandler->PLAYLIST->activate($_REQUEST['id']);
        $uiHandler->PLAYLIST->setReload();
    break;

    default:
        $_SESSION["alertMsg"] = tra("Unknown method: $1", $_REQUEST["act"]);
        header("Location: ".UI_BROWSER.'?popup[]=_reload_parent&popup[]=_close');
        die();
}
if ($uiHandler->alertMsg) $_SESSION['alertMsg'] = $uiHandler->alertMsg;
header('Location: '.$uiHandler->redirUrl);
?>