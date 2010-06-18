<?php
require_once(dirname(__FILE__).'/../ui_browser_init.php');

if (UI_DEBUG === TRUE) {
	$Smarty->assign('DEBUG', TRUE);
}

// Defaults.  Theses also prevent warnings from coming up in the
// master panel template when debugging.
$Smarty->assign('showScheduler', FALSE);
$Smarty->assign('fileList', FALSE);
$Smarty->assign('act', null);
$Smarty->assign('showLibrary', FALSE);
$Smarty->assign('showSubjects', FALSE);
$Smarty->assign('showFile', FALSE);
$Smarty->assign('editItem', null);
$Smarty->assign('changeStationPrefs', FALSE);
$Smarty->assign('PL_simpleManagement', FALSE);
$Smarty->assign('showBackup', FALSE);

if (isset($_REQUEST['popup']) && is_array($_REQUEST['popup'])){
    foreach ($_REQUEST['popup'] as $val) {
        switch ($val) {
            case "jscom":
	            $Smarty->display('jscom.tpl');
	            break;

            case "_reload_parent":
	            $Smarty->display('popup/_reload_parent.tpl');
	            break;

            case "_close":
	            $Smarty->display('popup/_close.tpl');
	            break;

            case "_clear_parent":
	            $Smarty->display('popup/_clear_parent.tpl');
	            break;

            case "_2PL.simpleManagement":
	            $Smarty->assign('target', 'PL.simpleManagement');
	            $Smarty->display('popup/_redirector.tpl');
	            break;

            case "_2PL.editMetaData":
	            $Smarty->assign('target', 'PL.editMetaData');
	            $Smarty->display('popup/_redirector.tpl');
	            break;

            case "_2changeStationPrefs":
	            $Smarty->assign('target', 'changeStationPrefs');
	            $Smarty->display('popup/_redirector.tpl');
	            break;

            case "_2SCHEDULER":
	            $Smarty->assign('target', 'SCHEDULER');
	            $Smarty->display('popup/_redirector.tpl');
	            break;

            case "login":
	            $Smarty->assign('dynform', $uiBrowser->login($ui_fmask));
	            $Smarty->display('popup/login.tpl');
	            break;

            case "logout":
	            $Smarty->assign('logouttype', 'logout');
	            $Smarty->display('popup/logout.tpl');
	            break;

            case "signover_1":
	            $Smarty->assign('logouttype', 'signover');
	            $Smarty->display('popup/logout.tpl');
	            break;

            case "signover_2":
	            $Smarty->assign('loginform', $uiBrowser->loginform($Smarty, $ui_fmask));
	            $Smarty->display('popup/login.tpl');
	            break;

            case "deleteItem":
	            if (is_array($_REQUEST['id'])) {
	                foreach ($_REQUEST['id'] as $i) {
	                    $idstr .= '&id[]='.$i;
	                }
	                $Smarty->assign('filecount', count($_REQUEST['id']));
	                $Smarty->assign('idstr', $idstr);
	            } else {
	                $Smarty->assign('filename', $uiBrowser->getMetadataValue($_REQUEST['id'], UI_MDATA_KEY_TITLE));
	            }
	            $Smarty->display('popup/deleteItem.tpl');
	            break;

            case "PL.changeTransition";
	            $Smarty->assign('dynform', $uiBrowser->PLAYLIST->changeTransitionForm($_REQUEST['id'], $_REQUEST['type'], $ui_fmask['PL.changeTransition']));
	            $Smarty->display('popup/PLAYLIST.changeTransition.tpl');
	            break;

            case "PL.changeAllTransitions";
	            $Smarty->assign('dynform', $uiBrowser->PLAYLIST->changeAllTransitionsForm($ui_fmask['PL.changeTransition']));
	            $Smarty->display('popup/PLAYLIST.changeAllTransitions.tpl');
	            break;

            case "PL.confirmDelete":
	            $Smarty->display('popup/PLAYLIST.confirmDelete.tpl');
	            break;

            case "PL.confirmRevert":
	            $Smarty->display('popup/PLAYLIST.confirmRevert.tpl');
	            break;

            case "PL.confirmRelease":
	            $Smarty->display('popup/PLAYLIST.confirmRelease.tpl');
	            break;

            case "PL.arrangeItems":
	            $Smarty->display('popup/PLAYLIST.arrangeItems.tpl');
	            break;

            case "PL.setClipLength":
	            $Smarty->assign('dynform', $uiBrowser->PLAYLIST->setClipLengthForm($_REQUEST['id'], $_REQUEST['elemId'], $ui_fmask['PL.setClipLength']));
	            $Smarty->display('popup/PLAYLIST.setClipLength.tpl');
	            break;

            case "PL.export":
	            $Smarty->assign('dynform',$uiBrowser->PLAYLIST->exportForm($_REQUEST['id'],$ui_fmask['PL.export']));
	            $Smarty->display('popup/PLAYLIST.export.tpl');
	            break;

            case "PL.redirect2DownloadExportedFile":
	            $Smarty->assign('href', UI_BROWSER."?popup[]=PL.downloadExportedFile&id={$_REQUEST['id']}&playlisttype={$_REQUEST['playlisttype']}&exporttype={$_REQUEST['exporttype']}");
	            $Smarty->display('popup/PLAYLIST.downloadExportedFile.tpl');
	            break;

            case "PL.downloadExportedFile":
	            $exportedPlaylist = $uiBrowser->gb->exportPlaylistOpen($uiBrowser->sessid,
	            			BasicStor::GunidFromId($_REQUEST['id']),
	            			$_REQUEST['playlisttype'],
	            		    $_REQUEST['exporttype']=='playlistOnly'?true:false);
	            $fp = fopen($exportedPlaylist['fname'],'r');
	            if (is_resource($fp)) {
	                header("Content-Type: application/octet-stream");
	                header("Content-Length: " . filesize($exportedPlaylist['fname']));
	                header('Content-Disposition: attachment; filename="playlist.tar"');
	                header("Content-Transfer-Encoding: binary\n");
	                fpassthru($fp);
	                   $uiBrowser->gb->exportPlaylistClose($exportedPlaylist['token']);
	            }
	            //$Smarty->display('popup/PLAYLIST.downloadExportedFile.tpl');
	            break;

            case "SCHEDULER.addItem":
	            $Smarty->display('popup/SCHEDULER.addItem.tpl');
	            break;

            case "SCHEDULER.removeItem":
	            $Smarty->assign('playlistName', $uiBrowser->getMetadataValue($_REQUEST['playlistId'], UI_MDATA_KEY_TITLE));
	            $Smarty->display('popup/SCHEDULER.removeItem.tpl');
	            break;

            case "SUBJECTS.confirmRemoveSubj":
	            $Smarty->display('popup/SUBJECTS.confirmRemoveSubj.tpl');
	            break;

            case "testStream":
	            $Smarty->assign('data', $uiBrowser->testStream($_REQUEST['url']));
	            $Smarty->display('popup/testStream.tpl');
	            break;

            case "listen2Audio":
	            $Smarty->assign('data', $uiBrowser->listen2Audio($_REQUEST['id']));
	            $Smarty->display('popup/listen2Audio.tpl');
	            break;

            case "help":
	            $Smarty->display('popup/help.tpl');
	            break;

            case 'BACKUP.setLocation':
	            if ($_REQUEST['cd']) {
	                $uiBrowser->EXCHANGE->setFolder($_REQUEST['cd']);
	            }
	            $Smarty->assign('isRestore',$_REQUEST['isRestore']);
	            $Smarty->display('backup/fileBrowser.tpl');
	            break;

            case 'BACKUP.setFile':
	            $Smarty->assign('isFile',$uiBrowser->EXCHANGE->setFile($_REQUEST['file']));
	            $Smarty->assign('isRestore',$_REQUEST['isRestore']);
	            $Smarty->display('backup/fileBrowser.tpl');
	            break;

            case 'BACKUP.createBackupDownload':
	            $uiBrowser->EXCHANGE->createBackupDownload();
	            break;

            case 'TR.confirmUpload2Hub':
	            $uiBrowser->TRANSFERS->upload2Hub($_REQUEST['id']);
	            $Smarty->display('popup/TR.confirmTransfer.tpl');
	            break;

            case 'TR.confirmDownloadFromHub':
	            $uiBrowser->TRANSFERS->downloadFromHub($uiBrowser->sessid, $_REQUEST['gunid']);
	            $Smarty->display('popup/TR.confirmTransfer.tpl');
	            break;

            case 'TR.pause':
	            $uiBrowser->TRANSFERS->doTransportAction($_REQUEST['id'],'pause');
	            $Smarty->display('popup/TR.pauseTransfer.tpl');
	            break;

            case 'TR.cancel':
	            $ids = '';
	            if (is_array($_REQUEST['id'])) {
	                foreach ($_REQUEST['id'] as $id) {
	                    $ids .= '&id[]='.$id;
	                }
	            } else {
	                $ids = '&id='.$_REQUEST['id'];
	            }
	            $Smarty->assign('tansferIDs',$ids);
	            $Smarty->display('popup/TR.cancelTransfer.tpl');
	            break;

            case 'TR.resume':
	            $uiBrowser->TRANSFERS->doTransportAction($_REQUEST['id'],'resume');
	            $Smarty->display('popup/TR.resumeTransfer.tpl');
	            break;

            case 'HUBBROWSE.getResults':
       	        //$HUBBROWSE = new uiHubBrowse($uiBrowser);

	            if (isset($_REQUEST['trtokid'])) {
	                $Smarty->assign('trtokid', $_REQUEST['trtokid']);
	                //if ($HUBBROWSE->getSearchResults($_REQUEST['trtokid'])) {
	                if ($uiBrowser->HUBBROWSE->getSearchResults($_REQUEST['trtokid'])) {
	                    $Smarty->assign('results', true);
	                } else {
	                    $Smarty->assign('results', false);
	                }
	            } else {
	                //$Smarty->assign('trtokid', $HUBBROWSE->searchDB());
	                $Smarty->assign('trtokid', $uiBrowser->HUBBROWSE->searchDB());
	                $Smarty->assign('results', false);
	            }
	            $Smarty->assign('polling_frequency', UI_HUB_POLLING_FREQUENCY);
	            $Smarty->assign('_prefix', 'HUBBROWSE');
	            $Smarty->display('popup/HUB.getResults.tpl');
	            break;

            case 'HUBSEARCH.getResults':
	            if (isset($_REQUEST['trtokid']) && $_REQUEST['trtokid']) {
	                $Smarty->assign('trtokid',$_REQUEST['trtokid']);
	                $r = $uiBrowser->HUBSEARCH->getSearchResults($_REQUEST['trtokid'], FALSE);
                    if ( PEAR::isError($r) && ($r->getCode() != TRERR_NOTFIN) ) {
                        break;
                    }
	                if ($r) {
	                    $Smarty->assign('results',true);
	                } else {
	                    $Smarty->assign('results',false);
	                }
	            } else {
	                $Smarty->assign('trtok',true);
	            }
	            $Smarty->assign('polling_frequency',UI_HUB_POLLING_FREQUENCY);
	            $Smarty->assign('_prefix','HUBSEARCH');
	            $Smarty->display('popup/HUB.getResults.tpl');
	            break;
        }
    }
    die();
};

if ($uiBrowser->userid) {
    $action = isset($_REQUEST['act']) ? $_REQUEST['act'] : null;
    switch ($action) {
        case "fileList":
	        $Smarty->assign('structure', $uiBrowser->getStructure($uiBrowser->fid));
	        $Smarty->assign('fileList', TRUE);

	        if ($_REQUEST['tree'] == 'Y') {
	        	$Smarty->assign('showTree', TRUE);
	        } else{
	        	$Smarty->assign('showObjects', TRUE);
	        }

	        $Smarty->assign('delOverride', $_REQUEST['delOverride']);
	        break;

        case "permissions":
	        $Smarty->assign('structure',   $uiBrowser->getStructure($uiBrowser->id));
	        $Smarty->assign('permissions', $uiBrowser->permissions($uiBrowser->id));
	        $Smarty->assign('fileList', TRUE);
	        break;

        case "uploadFileM":
	        $Smarty->assign('structure', $uiBrowser->getStructure($uiBrowser->id));
	        $Smarty->assign('uploadform', $uiBrowser->uploadFileM($ui_fmask['uploadFileM'], $uiBrowser->id));
	        break;

        case "addFileData":
        case "addFileMData":
	        $Smarty->assign('structure', $uiBrowser->getStructure($uiBrowser->id));
	        $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : null;
	        $langId = isset($_REQUEST['curr_langid']) ? $_REQUEST['curr_langid'] : null;
	        $Smarty->assign('editItem', array('type' => 'audioclip',
	                                          'id' => $id,
	                                          'folderId' => $uiBrowser->fid,
	                                          'curr_langid' => $langId));
	        break;

        case "addWebstreamData":
        case "addWebstreamMData":
	        $Smarty->assign('structure', $uiBrowser->getStructure($uiBrowser->id));
	        $Smarty->assign('editItem', array('type' => 'webstream', 'id' => $_REQUEST['id'], 'folderId' => $uiBrowser->fid, 'curr_langid' => $_REQUEST['curr_langid']));
	        break;

        case "editItem":
	        $uiBrowser->SCRATCHPAD->addItem($_REQUEST['id']);
	        $Smarty->assign('structure', $uiBrowser->getStructure($uiBrowser->id));
	        $Smarty->assign('editItem', array('type' => $uiBrowser->type, 'id' => $_REQUEST['id'], 'folderId' => $uiBrowser->fid, 'curr_langid' => $_REQUEST['curr_langid']));
	        break;

        case "SEARCH":
	        $Smarty->assign('searchForm', $uiBrowser->SEARCH->searchForm($uiBrowser->id, $ui_fmask));
	        $Smarty->assign('showLibrary', TRUE);
	        break;

        case "BROWSE":
	        #echo '<XMP>uiBrowser->BROWSE->getResult():'; print_r($uiBrowser->BROWSE->getResult()); echo "</XMP>\n";
	        $Smarty->assign('browseForm', $uiBrowser->BROWSE->browseForm($uiBrowser->id, $ui_fmask));
	        $Smarty->assign('showLibrary', TRUE);
	        break;

        case "HUBSEARCH":
	        #echo '<XMP>_REQUEST:'; print_r($_REQUEST); echo "</XMP>\n";
	        #$Smarty->assign('searchForm', $uiBrowser->HUBSEARCH->searchForm($uiBrowser->id, $ui_fmask));
	        $Smarty->assign('hubSearchForm', $uiBrowser->HUBSEARCH->searchForm($uiBrowser->id, $ui_fmask));
	        $Smarty->assign('showLibrary', TRUE);
	        $Smarty->assign('isHub', TRUE);
	        break;

        case "HUBBROWSE":
       	    //$HUBBROWSE = new uiHubBrowse($uiBrowser);
	        //$Smarty->assign('hubBrowseForm', $HUBBROWSE->browseForm($uiBrowser->id, $ui_fmask));
	        $Smarty->assign('hubBrowseForm', $uiBrowser->HUBBROWSE->browseForm($uiBrowser->id, $ui_fmask));
	        $Smarty->assign('showLibrary', TRUE);
	        $Smarty->assign('isHub', TRUE);
	        break;

        case "TRANSFERS":
	        $Smarty->assign('transfersForm', TRUE);
	        $Smarty->assign('showLibrary', TRUE);
	        break;

        case "getFile":
	        $Smarty->assign('fData', $uiBrowser->getFile($uiBrowser->id));
	        $Smarty->assign('showFile', TRUE);
	        break;

        case "getMData":
	        $Smarty->assign('fMetaData', $uiBrowser->getMdata($uiBrowser->id));
	        $Smarty->assign('showFile', TRUE);
	        break;

        case "_analyzeFile":
	        $Smarty->assign('_analyzeFile', $uiBrowser->analyzeFile($uiBrowser->id, 'text'));
	        $Smarty->assign('showFile', TRUE);
	        break;

        case "changeStationPrefs":
	        $Smarty->assign('dynform', $uiBrowser->changeStationPrefs($ui_fmask['stationPrefs']));
	        $Smarty->assign('changeStationPrefs', TRUE);
	        break;

        case "PL.simpleManagement":
	        $Smarty->assign('PL_simpleManagement', TRUE);
	        break;

        case "PL.editMetaData":
	        $Smarty->assign('PL_editMetaData', TRUE);
	        $Smarty->assign('_PL', array('curr_langid' => $_REQUEST['curr_langid']));
	        $Smarty->assign('PL_simpleManagement', TRUE);
	        break;

        case "PL.import":
	        $Smarty->assign('dynform', $uiBrowser->PLAYLIST->importForm($_REQUEST['id'], $ui_fmask['PL.import']));
	        $Smarty->assign('PL_import', TRUE);
	        $Smarty->assign('PL_simpleManagement', TRUE);
	        break;

        case "SCHEDULER":
	        $Smarty->assign('showScheduler', TRUE);
	        break;

        case "SUBJECTS":
        case "SUBJECTS.manageGroupMember":
        case "SUBJECTS.addUser":
        case "SUBJECTS.addGroup":
        case "SUBJECTS.remSubj":
        case "SUBJECTS.chgPasswd":
	        $Smarty->assign('showSubjects', TRUE);
	        $Smarty->assign('act', $action);
	        break;

        case "BACKUP":
        case "RESTORE":
        case "BACKUP.schedule":
        case "SCHEDULER.import":
        case "SCHEDULER.export":
	        $Smarty->assign('act', $action);
	        break;
	        
	    case "twitter.settings":
	        $Smarty->assign('dynform', $uiBrowser->TWITTER->getSettingsForm());
	        $Smarty->assign('twitter', array(
	           'samplefeed' => uiTwitter::twitterify($uiBrowser->TWITTER->getFeed(true)),
	           'samplefeed_length' => strlen($uiBrowser->TWITTER->getFeed(true)))
	        );
	        $Smarty->assign('act', $action);
	        break;
    }

    if ($action != 'SCHEDULER') {
        $Smarty->assign('simpleSearchForm',   $uiBrowser->SEARCH->simpleSearchForm($ui_fmask['simplesearch']));
    }
}

$Smarty->display('main.tpl');
?>