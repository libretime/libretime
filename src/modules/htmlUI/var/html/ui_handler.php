<?php
require_once(dirname(__FILE__).'/../ui_handler_init.php');
require_once("../Input.php");

if (get_magic_quotes_gpc()) {
    $_REQUEST = Input::CleanMagicQuotes($_REQUEST);
}

switch ($_REQUEST['act']) {
    case "login":
	    if ($uiHandler->login($_REQUEST, $ui_fmask["login"]) === TRUE) {
	        $uiHandler->loadStationPrefs($ui_fmask['stationPrefs'], TRUE);
	        # $uiHandler->PLAYLIST->reportLookedPL();
	        $uiHandler->PLAYLIST->loadLookedFromPref();
	    }
	    include('../templates/loader/index.tpl');
	    include('../templates/popup/_reload_parent.tpl');
	    include('../templates/popup/_close.tpl');
	    exit;

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

    // file/webstream handling
    case "addFileData":
	    if (($ui_tmpid = $uiHandler->uploadFile(array_merge($_REQUEST, $_FILES), $ui_fmask["file"])) !== FALSE) {
	    	$uiHandler->SCRATCHPAD->addItem($ui_tmpid);
	    }
	    break;

    case "addWebstreamData":
	    $ui_tmpid = $uiHandler->addWebstream($_REQUEST, $ui_fmask['webstream']);
	    $uiHandler->SCRATCHPAD->addItem($ui_tmpid);
	    break;

    case "addWebstreamMData":
    case "editWebstreamData":
	    $uiHandler->editWebstream($_REQUEST, $ui_fmask['webstream']);
	    $uiHandler->SCRATCHPAD->reloadMetadata();
	    break;

    case "editMetaData":
	    $uiHandler->editMetaData($_REQUEST);
	    $uiHandler->SCRATCHPAD->reloadMetadata();
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
	    if ($uiHandler->delete($_REQUEST['id'], $_REQUEST['delOverride'])) {
	        if ($uiHandler->type != 'Folder') {
	            $uiHandler->SCRATCHPAD->removeItems($_REQUEST['id']);
	        }
	    }
	    break;

    case "addPerm":
	    $uiHandler->addPerm($_REQUEST["subj"], $_REQUEST["permAction"], $uiHandler->id, $_REQUEST["allowDeny"]);
	    break;

    case "removePerm":
	    $uiHandler->removePerm($_REQUEST["permid"], $_REQUEST["oid"]);
	    break;

    case "SUBJECTS.addSubj":
	    $uiHandler->SUBJECTS->addSubj($_REQUEST);
	    break;

    case "SUBJECTS.removeSubj":
	    $uiHandler->SUBJECTS->removeSubj($_REQUEST);
	    break;

    case "SUBJECTS.addSubj2Gr":
	    $uiHandler->SUBJECTS->addSubj2Gr($_REQUEST);
	    break;

    case "SUBJECTS.removeSubjFromGr":
	    $uiHandler->SUBJECTS->removeSubjFromGr($_REQUEST);
	    break;

    case "SUBJECTS.chgPasswd":
	    $uiHandler->SUBJECTS->chgPasswd($_REQUEST);
	    break;

    case "changeStationPrefs":
	    $uiHandler->changeStationPrefs(array_merge($_REQUEST, $_FILES), $ui_fmask["stationPrefs"]);
	    $uiHandler->redirUrl = UI_BROWSER."?act=changeStationPrefs";
	    break;

    case "SP.addItem":
	    $uiHandler->SCRATCHPAD->addItem($_REQUEST['id']);
	    $uiHandler->SCRATCHPAD->setReload();
	    break;

    case "SP.removeItem":
	    $uiHandler->SCRATCHPAD->removeItems($_REQUEST['id']);
	    $uiHandler->SCRATCHPAD->setReload();
	    break;

    case "SP.reorder":
	    $uiHandler->SCRATCHPAD->reorder($_REQUEST['by']);
	    $uiHandler->SCRATCHPAD->setReload();
	    break;

    case "SEARCH.newSearch":
	    $uiHandler->SEARCH->newSearch($_REQUEST);
	    break;

    case "SEARCH.simpleSearch":
	    $uiHandler->SEARCH->simpleSearch($_REQUEST);
	    break;

    case "SEARCH.reorder":
	    $uiHandler->SEARCH->reorder($_REQUEST['by']);
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

    case "BROWSE.reorder":
	    $uiHandler->BROWSE->reorder($_REQUEST['by']);
	    break;

    case "BROWSE.setDefaults":
	    $uiHandler->BROWSE->setDefaults(TRUE);
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

    case "HUBBROWSE.setCategory":
	    $uiHandler->HUBBROWSE->setCategory($_REQUEST);
	    break;

    case "HUBBROWSE.setValue":
	    $uiHandler->HUBBROWSE->setValue($_REQUEST);
	    break;

    case "HUBBROWSE.reorder":
	    $uiHandler->HUBBROWSE->reorder($_REQUEST['by']);
	    break;

    case "HUBBROWSE.setDefaults":
	    $uiHandler->HUBBROWSE->setDefaults(TRUE);
	    break;

    case "HUBBROWSE.setOffset":
	    $uiHandler->HUBBROWSE->setOffset($_REQUEST['page']);
	    break;

    case "HUBBROWSE.setLimit":
	    $uiHandler->HUBBROWSE->setLimit($_REQUEST['limit']);
	    break;

    case "HUBBROWSE.setFiletype":
	    $uiHandler->HUBBROWSE->setFiletype($_REQUEST['filetype']);
	    break;

    case "HUBSEARCH.newSearch":
	    $uiHandler->HUBSEARCH->newSearch($_REQUEST);
	    break;

    case "HUBSEARCH.reorder":
	    $uiHandler->HUBSEARCH->reorder($_REQUEST['by']);
	    break;

    case "HUBSEARCH.clear":
	    $uiHandler->HUBSEARCH->clear();
	    break;

    case "HUBSEARCH.setOffset":
	    $uiHandler->HUBSEARCH->setOffset($_REQUEST['page']);
	    break;

    case "TRANSFERS.reorder":
	    $uiHandler->TRANSFERS->reorder($_REQUEST['by']);
	    break;

    case "TRANSFERS.setOffset":
	    $uiHandler->TRANSFERS->setOffset($_REQUEST['page']);
	    break;

    case "TR.pause":
    case "TR.resume":
    case "TR.cancel":
	    $ids = '';
	    if (is_array($_REQUEST['id'])) {
	        foreach ($_REQUEST['id'] as $id) {
	            $ids .= '&id[]='.$id;
	        }
	    } else {
	        $ids = '&id='.$_REQUEST['id'];
	    }
	    //echo '<XMP>_REQUEST:'; print_r($_REQUEST); echo "</XMP>\n";
	    $uiHandler->redirUrl = UI_BROWSER."?popup[]={$_REQUEST['act']}{$ids}";
	    break;

    case "TR.cancelConfirm":
	    //echo '<XMP>_REQUEST:'; print_r($_REQUEST); echo "</XMP>\n";
	    $uiHandler->TRANSFERS->doTransportAction($_REQUEST['id'],'cancel');
	    $uiHandler->redirUrl = UI_BROWSER.'?popup[]=_reload_parent&popup[]=_close';
	    break;

    case "PL.activate":
	    if ($uiHandler->PLAYLIST->activate($_REQUEST['id']) === TRUE) {
	    	$uiHandler->SCRATCHPAD->addItem($_REQUEST['id']);
	    }
	    $uiHandler->PLAYLIST->setRedirect();
	    break;

    case "PL.create":
    	$ids = (isset($_REQUEST['id']) ? $_REQUEST['id'] : null);
	    if (($ui_tmpid = $uiHandler->PLAYLIST->create($ids)) !== FALSE) {
	        if ($ids) {
	        	$uiHandler->SCRATCHPAD->addItem($ids);
	        }
	        $uiHandler->SCRATCHPAD->addItem($ui_tmpid);
	    }
	    $uiHandler->PLAYLIST->setRedirect('_2PL.editMetaData');
	    break;

    case "PL.addItem":
        if (isset($_REQUEST['id'])) {
    	    if ($uiHandler->PLAYLIST->addItem($_REQUEST['id'], $_REQUEST['playlength']) !== FALSE) {
    	    	$uiHandler->SCRATCHPAD->addItem($_REQUEST['id']);
    	    }
        }
	    $uiHandler->PLAYLIST->setReload();
	    break;

    case "PL.setClipLength":
	    $uiHandler->PLAYLIST->setClipLength($_REQUEST['elemId'], $ui_fmask['PL.setClipLength']);
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
	    if (($ui_tmpid = $uiHandler->PLAYLIST->save()) !== FALSE) {
	    	$uiHandler->SCRATCHPAD->addItem($ui_tmpid);
	    }
	    $uiHandler->PLAYLIST->setReload();
	    break;

    case "PL.revert":
	    if (($ui_tmpid = $uiHandler->PLAYLIST->revert()) !== FALSE) {
	    	$uiHandler->SCRATCHPAD->addItem($ui_tmpid);
	    }
	    $uiHandler->PLAYLIST->setReload();
	    break;

    case "PL.revertANDclose":
	    $uiHandler->PLAYLIST->revert();
	    $uiHandler->PLAYLIST->release();
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

    case "PL.reorder":
	    $uiHandler->PLAYLIST->reorder($_REQUEST['pl_items']);
	    $uiHandler->PLAYLIST->setReturn();
	    break;

    case "PL.reArrange":
	    $uiHandler->PLAYLIST->reorder($_REQUEST['pl_items']);
	    $uiHandler->PLAYLIST->setReload();
	    break;

    case "PL.editMetaData":
	    $uiHandler->PLAYLIST->editMetaData($_REQUEST);
	    $uiHandler->SCRATCHPAD->addItem($_REQUEST['id']);
	    break;

    case "PL.deleteActive":
	    if (($ui_tmpid = $uiHandler->PLAYLIST->deleteActive()) !== FALSE) {
	    	$uiHandler->SCRATCHPAD->removeItems($ui_tmpid);
	    }
	    $uiHandler->PLAYLIST->setReload();
	    break;

    case "PL.export":
	    $uiHandler->redirUrl = UI_BROWSER."?popup[]=PL.redirect2DownloadExportedFile&id={$_REQUEST['id']}&playlisttype={$_REQUEST['playlisttype']}&exporttype={$_REQUEST['exporttype']}";
	    break;

    case "PL.import":
	    //echo '_FILES:'; print_r($_FILES);
	    $importedPlaylist = $uiHandler->gb->importPlaylistOpen($uiHandler->sessid);
	    //echo 'importPlaylistOpen:'; print_r($importedPlaylist);
	    copy($_FILES['playlist']['tmp_name'],$importedPlaylist['fname']);
	    $uiHandler->gb->importPlaylistClose($importedPlaylist['token']);
	    $uiHandler->redirUrl = UI_BROWSER."?act=PL.import";
	    break;

    case "SCHEDULER.set":
	    $uiHandler->SCHEDULER->set($_REQUEST);
	    $uiHandler->SCHEDULER->setReload();
	    break;

    case "SCHEDULER.setScheduleAtTime":
	    $uiHandler->SCHEDULER->setScheduleAtTime($_REQUEST);
	    $uiHandler->SCHEDULER->setClose();
	    break;

    case "SCHEDULER.addItem":
	    $uiHandler->SCHEDULER->uploadPlaylistMethod($_REQUEST);
	    $uiHandler->SCHEDULER->setReload();
	    break;

    case "SCHEDULER.removeItem":
	    $uiHandler->SCHEDULER->removeFromScheduleMethod($_REQUEST['scheduleId']);
	    $uiHandler->SCHEDULER->setReload();
	    break;

    case "SCHEDULER.startDaemon":
	    $uiHandler->SCHEDULER->startDaemon(TRUE);
	    $uiHandler->SCHEDULER->setReload();
	    break;

    case "SCHEDULER.stopDaemon":
	    $uiHandler->SCHEDULER->stopDaemon(TRUE);
	    $uiHandler->SCHEDULER->setReload();
	    break;

    case 'SCHEDULER.scheduleExportOpen':
        // Make sure days are always 2 digits.
	    $_REQUEST['fromDay'] = (strlen($_REQUEST['fromDay'])>1)?$_REQUEST['fromDay']:'0'.$_REQUEST['fromDay'];
	    $_REQUEST['toDay'] = (strlen($_REQUEST['toDay'])>1)?$_REQUEST['toDay']:'0'.$_REQUEST['toDay'];

	    $fromTime = $_REQUEST['fromYear'].'-'.$_REQUEST['fromMonth'].'-'.$_REQUEST['fromDay'].' '
	               .$_REQUEST['fromHour'].':'.$_REQUEST['fromMinute'].':00';
	    $toTime = $_REQUEST['toYear'].'-'.$_REQUEST['toMonth'].'-'.$_REQUEST['toDay'].' '
	              .$_REQUEST['toHour'].':'.$_REQUEST['toMinute'].':00';
	    //echo '<XMP style="background:yellow;">';echo "fromTime:$fromTime | toTime:$toTime";echo'</XMP>'."\n";
	    $uiHandler->SCHEDULER->scheduleExportOpen($fromTime, $toTime);
	    $uiHandler->redirUrl = UI_BROWSER.'?act=SCHEDULER';
	    break;

    case 'SCHEDULER.setImportFile':
	    $uiHandler->SCHEDULER->scheduleImportOpen($_REQUEST['target']);
	    $uiHandler->redirUrl = UI_BROWSER.'?act=SCHEDULER';
	    break;

    case 'BACKUP.createBackupOpen':
	    $uiHandler->EXCHANGE->createBackupOpen();
	    $uiHandler->redirUrl = UI_BROWSER.'?act=BACKUP';
	    break;

    case 'BACKUP.copy2target':
	    $uiHandler->EXCHANGE->copy2target($_REQUEST['target']);
	    $uiHandler->redirUrl = UI_BROWSER.'?act=BACKUP';
	    break;

    case 'BACKUP.createBackupClose':
	    $uiHandler->EXCHANGE->createBackupClose();
	    $uiHandler->redirUrl = UI_BROWSER.'?act=BACKUP';
	    break;

    case 'RESTORE.setBackupFileToRestore':
	    $uiHandler->EXCHANGE->backupRestoreOpen($_REQUEST['target']);
	    $uiHandler->redirUrl = UI_BROWSER.'?act=RESTORE';
	    break;

    case 'RESTORE.backupRestoreClose':
	    $uiHandler->EXCHANGE->backupRestoreClose();
	    $uiHandler->redirUrl = UI_BROWSER.'?act=RESTORE';
	    break;

    case 'SESSION.CLEAR':
	    $_SESSION = array();
	    die();
	    break;
	    
	case 'twitter.saveSettings':
        $uiHandler->TWITTER->saveSettings();
        $uiHandler->redirUrl = UI_BROWSER.'?act=twitter.settings';
	    break;

    case NULL:
	    if ($uiHandler->userid) {
	        $uiHandler->_retMsg('The uploaded file is bigger than allowed in system settings. See "Help", chapter "Troubleshooting" for more information.');
	    }
	    $uiHandler->redirUrl = UI_BROWSER;
	    if ($_REQUEST['is_popup']) {
	    	$uiHandler->redirUrl = UI_BROWSER.'?popup[]=_reload_parent&popup[]=_close';
	    }
	    break;

    default:
	    $uiHandler->_retMsg(tra('Unknown method: $1', $_REQUEST['act']));
	    $uiHandler->redirUrl = UI_BROWSER;
	    if ($_REQUEST['is_popup']) {
	    	$uiHandler->redirUrl = UI_BROWSER.'?popup[]=_reload_parent&popup[]=_close';
	    }
	    break;
}

if ($uiHandler->alertMsg) {
    $_SESSION['alertMsg'] = $uiHandler->alertMsg;
}
//$ui_wait = 0;
//if (ob_get_contents()) {
//    $ui_wait = 10;
//}
ob_end_clean();
if (isset($_REQUEST['target'])) {
	header('Location: ui_browser.php?act='.$_REQUEST['target']);
} else {
	header("Location: ".$uiHandler->redirUrl);
}
exit;
?>