<?php
require dirname(__FILE__).'/../ui_handler_init.php';

switch($_REQUEST['act']){

    case "login":
        if ($uiHandler->login($_REQUEST, $ui_fmask["login"]) === TRUE) {
            $uiHandler->loadStationPrefs($ui_fmask['stationPrefs'], TRUE);
            $uiHandler->PLAYLIST->reportLookedPL(TRUE);
        }
    break;

    case "logout":
        $uiHandler->SCRATCHPAD->save();
        $uiHandler->PLAYLIST->release();
        $uiHandler->logout();
    break;

    case "signover":
        $uiHandler->SCRATCHPAD->save();
        $uiHandler->PLAYLIST->release();
        $uiHandler->logout(TRUE);
    break;

    case "uploadFileM":
        if ($ui_tmpid = $uiHandler->uploadFileM(array_merge($_REQUEST, $_FILES), $uiHandler->id, $ui_fmask["uploadFileM"]))
            $uiHandler->SCRATCHPAD->addItem($ui_tmpid);
    break;

    case "uploadFile":
        if ($ui_tmpid = $uiHandler->uploadFile(array_merge($_REQUEST, $_FILES), $ui_fmask["file"]))
            $uiHandler->SCRATCHPAD->addItem($ui_tmpid);
    break;

    case "replaceFile":
        $ui_tmpgunid = $uiHandler->gb->_gunidFromId($uiHandler->id);
        if ($uiHandler->delete($uiHandler->id) === TRUE) {
            $ui_tmpid = $uiHandler->uploadFile(array_merge($_REQUEST, $_FILES), $uiHandler->pid, $ui_fmask["file"], $ui_tmpgunid);
            $uiHandler->SCRATCHPAD->removeItems($uiHandler->id);
            $uiHandler->SCRATCHPAD->addItem($ui_tmpid);
        }
    break;

    case "editWebstream":
        if ($_REQUEST['id']) {
            $uiHandler->editWebstream($_REQUEST, $ui_fmask['webstream']);
        } else {
            $ui_tmpid = $uiHandler->addWebstream($_REQUEST, $ui_fmask['webstream']);
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

    case "changeStationPrefs":
        $uiHandler->changeStationPrefs(array_merge($_REQUEST, $_FILES), $ui_fmask["stationPrefs"]);
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

    case "SEARCH.newSearch":
        $uiHandler->SEARCH->newSearch($_REQUEST);
    break;

    case "SEARCH.simpleSearch":
        $uiHandler->SEARCH->simpleSearch($_REQUEST);
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

    case "BROWSE.setCategory":
        $uiHandler->BROWSE->setCategory($_REQUEST);
    break;

    case "BROWSE.setValue":
        $uiHandler->BROWSE->setValue($_REQUEST);
    break;

    case "BROWSE.reOrder":
        $uiHandler->BROWSE->reOrder($_REQUEST['by']);
    break;

    case "BROWSE.clear":
        $uiHandler->BROWSE->clear();
    break;

    case "BROWSE.setOffset":
        $uiHandler->BROWSE->setOffset($_REQUEST['page']);
    break;

    case "BROWSE.setLimit":
        $uiHandler->BROWSE->setLimit($_REQUEST['limit']);
    break;

    case "BROWSE.setFiletype":
        $uiHandler->BROWSE->setFiletype($_REQUEST['filetype']);
    break;

    case "PL.activate":
        if ($uiHandler->PLAYLIST->activate($_REQUEST['id']) === TRUE)
            $uiHandler->SCRATCHPAD->addItem($_REQUEST['id']);
        $uiHandler->PLAYLIST->setReload();
    break;

    case "PL.create":
        if (($ui_tmpid = $uiHandler->PLAYLIST->create($_REQUEST['id'])) !== FALSE)
            $uiHandler->SCRATCHPAD->addItem($ui_tmpid);
        $uiHandler->PLAYLIST->setReload();
    break;

    case "PL.addItem":
        $uiHandler->PLAYLIST->addItem($_REQUEST['id']);
        $uiHandler->PLAYLIST->setReload();
    break;

    case "PL.removeItem":
        $uiHandler->PLAYLIST->removeItem($_REQUEST['id']);
        $uiHandler->PLAYLIST->setReload();
    break;

    case "PL.release":
        $uiHandler->PLAYLIST->release();
        $uiHandler->PLAYLIST->setReload();
    break;

    case "PL.save":
        if (($ui_tmpid = $uiHandler->PLAYLIST->save()) !== FALSE)
            $uiHandler->SCRATCHPAD->addItem($ui_tmpid);
        $uiHandler->PLAYLIST->setReload();
    break;

    case "PL.revert":
        if (($ui_tmpid = $uiHandler->PLAYLIST->revert()) !== FALSE)
            $uiHandler->SCRATCHPAD->addItem($ui_tmpid);
        $uiHandler->PLAYLIST->setReload();
    break;

    case"PL.unlook":
        $uiHandler->PLAYLIST->loadLookedFromPref();
        $uiHandler->PLAYLIST->setReload();
    break;

    case "PL.changeTransition":
        $uiHandler->PLAYLIST->changeTransition($_REQUEST['id'], $_REQUEST['type'], $_REQUEST['duration']);
        $uiHandler->PLAYLIST->setReload();
    break;

    case "PL.moveItem":
        $uiHandler->PLAYLIST->moveItem($_REQUEST['id'], $_REQUEST['pos']);
        $uiHandler->PLAYLIST->setReload();
    break;

    case "PL.editMetaData":
        $uiHandler->PLAYLIST->editMetaData($_REQUEST);
        $uiHandler->SCRATCHPAD->addItem($_REQUEST['id']);
    break;

    case "SCHEDULER.set":
        $uiHandler->SCHEDULER->set($_REQUEST);
        $uiHandler->SCHEDULER->setReload();
    break;

    case "SCHEDULER.uploadPlaylistMethod":
        $uiHandler->SCHEDULER->uploadPlaylistMethod($_REQUEST);
        $uiHandler->SCHEDULER->setReload();
    break;

    case "SCHEDULER.removeFromScheduleMethod":
        $uiHandler->SCHEDULER->removeFromScheduleMethod($_REQUEST);
        $uiHandler->SCHEDULER->setReload();
    break;

    default:
        $uiHandler->_retMsg("Unknown method: $1", $_REQUEST["act"]);
        $uiHandler->redirUrl = UI_BROWSER;
        if ($_REQUEST['was_popup'])
             $uiHandler->redirUrl .= '?popup[]=_reload_parent&popup[]=_close';
}
if ($uiHandler->alertMsg) $_SESSION['alertMsg'] = $uiHandler->alertMsg;
#header('Location: '.$uiHandler->redirUrl);
if (ob_get_contents()) {
    $ui_wait = 5;
}
ob_end_clean
?>
<meta http-equiv="refresh" content="<?php echo $ui_wait ? $ui_wait : 0; ?>; URL=<?php echo $uiHandler->redirUrl; ?>">
</body>
</html>