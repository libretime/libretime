<?php
require_once dirname(__FILE__).'/../ui_browser_init.php';

if (is_array($_REQUEST['popup'])){
    foreach ($_REQUEST['popup'] as $val) {
        switch ($val) {
            case "_reload_parent":
                $Smarty->display('popup/_reload_parent.tpl');
            break;

            case "_close":
                $Smarty->display('popup/_close.tpl');
            break;

            case "_clear_parent":
                $Smarty->display('popup/_clear_parent.tpl');
            break;

            case "login":
                $Smarty->assign('login', $uiBrowser->login($Smarty, $ui_fmask));
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
        }
    }
    die();
};

if ($uiBrowser->userid) {
  $Smarty->assign('showMenuTop', TRUE);
  $Smarty->assign('SCRATCHPAD', $uiBrowser->SCRATCHPAD->get());
  $Smarty->assign('PLAYLIST',   $uiBrowser->PLAYLIST->get());  

  switch ($_REQUEST['act']){
    case "fileBrowse":
        $Smarty->assign('structure', $uiBrowser->getStructure($uiBrowser->fid));

        $Smarty->assign('fileBrowse', TRUE);
        if ($_REQUEST['tree']=='Y')
            $Smarty->assign('showTree', TRUE);
        else
            $Smarty->assign('showObjects', TRUE);
        $Smarty->assign('delOverride', $_REQUEST['delOverride']);
    break;

    case "permissions":
        $Smarty->assign('structure', $uiBrowser->getStructure($uiBrowser->id));

        $Smarty->assign('permissions', $uiBrowser->permissions($uiBrowser->id));
        $Smarty->assign('fileBrowse', TRUE);
    break;


    case "uploadFileM":
        $Smarty->assign('structure', $uiBrowser->getStructure($uiBrowser->id));

        $Smarty->assign('uploadform', $uiBrowser->uploadFileM($ui_fmask['uploadFileM'], $uiBrowser->id));
    break;


    case "uploadFile":
        $Smarty->assign('structure', $uiBrowser->getStructure($uiBrowser->id));

        $Smarty->assign('uploadform',  $uiBrowser->uploadFile($ui_fmask['uploadFile'], $uiBrowser->id, $_REQUEST['replace']));
    break;


    case "addWebstream":
        $Smarty->assign('structure', $uiBrowser->getStructure($uiBrowser->id));

        $Smarty->assign('uploadform',  $uiBrowser->addWebstream($ui_fmask['addWebstream'], $uiBrowser->id, $_REQUEST['replace']));
    break;


    case "editMetaData":
        $Smarty->assign('structure', $uiBrowser->getStructure($uiBrowser->id));

        $Smarty->assign('editMetaData', $uiBrowser->editMetaData($uiBrowser->id, TRUE));
    break;


    case "SEARCH":
        if (is_array($uiBrowser->SEARCH->criteria) ){
            $Smarty->assign('searchres', $uiBrowser->SEARCH->results);
            $Smarty->assign('showSearchRes', TRUE);
        };

        $Smarty->assign('searchform', $uiBrowser->SEARCH->form($uiBrowser->id, $ui_fmask));
        $Smarty->assign('showSearchForm', TRUE);

        break;

    case "subjects":
    case "addUser":
    case "addGroup":
        $Smarty->assign('subjects', $uiBrowser->getSubjects());
        switch($_REQUEST['act']) {
            case "addUser":  $Smarty->assign('addSubjectForm', $uiBrowser->getAddSubjectForm($ui_fmask['addUser']));  break;
            case "addGroup": $Smarty->assign('addSubjectForm', $uiBrowser->getAddSubjectForm($ui_fmask['addGroup'])); break;
        }
        $Smarty->assign('showSubjects', TRUE);
    break;

    case "chgPasswd":
        $Smarty->assign('chgPasswd', $uiBrowser->chgPasswd($_REQUEST['uid'], $ui_fmask['chgPasswd']));
        $Smarty->assign('showSubjects', TRUE);
    break;

    case "groupMembers":
        $Smarty->assign('groupMembers', $uiBrowser->groupMembers($uiBrowser->id));
        $Smarty->assign('addGroupMember', $uiBrowser->addGroupMember($uiBrowser->id));
        $Smarty->assign('showSubjects', TRUE);
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
        $Smarty->assign('_analyzeFile', $uiBrowser->_analyzeFile($uiBrowser->id, 'text'));
        $Smarty->assign('showFile', TRUE);
    break;

    case "changeStationPrefs":
        $Smarty->assign('dynform', $uiBrowser->changeStationPrefs($ui_fmask['stationPrefs']));
        $Smarty->assign('changeStationPrefs', TRUE);
    break;

    case "PL.display":
        $Smarty->assign('playlist', $uiBrowser->PLAYLIST->get());
    break;
  }
}

$Smarty->display('main.tpl');
?>