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

$uiBrowser->loadSystemPrefs($ui_fmask['systemPrefs']);
$Smarty->assign('systemPrefs', $uiBrowser->systemPrefs); #print_r($uiBrowser->systemPrefs);

if ($uiBrowser->userid) {
  $Smarty->assign('showMenuTop', TRUE);
  $Smarty->assign('ScratchPad', $uiBrowser->SP->get());

  switch ($_REQUEST['act']){
    default:
        $Smarty->assign('structure', $uiBrowser->getStructure($uiBrowser->id));
        $Smarty->assign('showPath', TRUE);

        if ($_REQUEST['tree']=='Y') {
            $Smarty->assign('showTree', TRUE);
        } else {
            $Smarty->assign('showObjects', TRUE);
        }
        $Smarty->assign('delOverride', $_REQUEST['delOverride']);
        #$Smarty->assign('obj_types', array('Folder'=>'D', 'File'=>'F', 'Replica'=>'R'));
        break;

    case "permissions":
        $Smarty->assign('structure', $uiBrowser->getStructure($uiBrowser->id));
        $Smarty->assign('showPath', TRUE);

        $Smarty->assign('perms', $uiBrowser->permissions($uiBrowser->id));
        $Smarty->assign('permissions', TRUE);
    break;


    case "uploadFileM":
        $Smarty->assign('structure', $uiBrowser->getStructure($uiBrowser->id));
        $Smarty->assign('showPath', TRUE);

        $Smarty->assign('uploadform', $uiBrowser->uploadFileM($uiBrowser->id, $ui_fmask['uploadFileM']));
    break;


    case "uploadFile":
        $Smarty->assign('structure', $uiBrowser->getStructure($uiBrowser->id));
        $Smarty->assign('showPath', TRUE);

        $Smarty->assign('uploadform',  $uiBrowser->uploadFile($uiBrowser->id, $ui_fmask['uploadFile']));
    break;


    case "addWebstream":
        $Smarty->assign('structure', $uiBrowser->getStructure($uiBrowser->id));
        $Smarty->assign('showPath', TRUE);

        $Smarty->assign('uploadform',  $uiBrowser->addWebstream($uiBrowser->id, $ui_fmask['addWebstream']));
    break;


    case "editFile":
        $Smarty->assign('structure', $uiBrowser->getStructure($uiBrowser->id));
        $Smarty->assign('showPath', FALSE);

        $Smarty->assign('uploadform',  $uiBrowser->uploadFile($uiBrowser->id, $ui_fmask['uploadFile']));
    break;


    case "search":
        if($_REQUEST['doSearch']) {
            $Smarty->assign('searchres', $uiBrowser->getSearchRes($uiBrwoser->id, $_REQUEST));
            $Smarty->assign('showSearchRes', TRUE);
        }


        $Smarty->assign('searchform', $uiBrowser->getSearchForm($uiBrowser->id, $_REQUEST, $ui_fmask));
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

    case "editMetaData":
        $Smarty->assign('editMetaData', $uiBrowser->editMetaData($uiBrowser->id, $ui_fmask['editMetaData'], TRUE));
    break;

    case "_analyzeFile":
        $Smarty->assign('_analyzeFile', $uiBrowser->_analyzeFile($uiBrowser->id, 'text'));
        $Smarty->assign('showFile', TRUE);
    break;

    case "editSystemPrefs":
        $Smarty->assign('dynform', $uiBrowser->systemPrefsForm($ui_fmask['systemPrefs']));
        $Smarty->assign('editSystemPrefs', TRUE);
    break;
  }
}

$Smarty->display('main.tpl');
?>
