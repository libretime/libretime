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
                $Smarty->assign('loginform', $uiBrowser->loginform($Smarty, $ui_fmask));
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

$Smarty->assign('statusbar', $uiBrowser->getStationInfo());

if ($uiBrowser->userid) {
  $Smarty->assign('showMenuTop', TRUE);
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
        $Smarty->assign('structure', $uiBrowser->getStructure($uiBrowser->id, $_REQUEST['act']=='getHomeDir' ? TRUE : FALSE));
        $Smarty->assign('showPath', TRUE);

        $Smarty->assign('perms', $uiBrowser->getPermissions($uiBrowser->id));
        $Smarty->assign('showPermissions', TRUE);
    break;


    case "newfile":
        $Smarty->assign('structure', $uiBrowser->getStructure($uiBrowser->id, $_REQUEST['act']=='getHomeDir' ? TRUE : FALSE));
        $Smarty->assign('showPath', TRUE);

        $Smarty->assign('newfileform', $uiBrowser->getNewFileForm($uiBrowser->id, $ui_fmask['upload']));
        $Smarty->assign('showNewFileForm', TRUE);
        break;


    case "upload_1":
        $Smarty->assign('structure', $uiBrowser->getStructure($uiBrowser->id, $_REQUEST['act']=='getHomeDir' ? TRUE : FALSE));
        $Smarty->assign('showPath', FALSE);

        $Smarty->assign('uploadform',  $uiBrowser->getUploadFileForm($uiBrowser->id, $ui_fmask['upload_1']));
        $Smarty->assign('showUploadForm', TRUE);
        break;

    case "upload_2":
        $Smarty->assign('structure', $uiBrowser->getStructure($uiBrowser->id, $_REQUEST['act']=='getHomeDir' ? TRUE : FALSE));
        $Smarty->assign('showPath', FALSE);
                     
        $Smarty->assign('mDataForm', $uiBrowser->getMetaDataForm($uiBrowser->id, $ui_fmask['mData'], FALSE, $uiBrowser->getInfo($id, 'array')));
        $Smarty->assign('showMetaDataForm', TRUE);
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

    case "passwd":
        $Smarty->assign('changePassForm', $uiBrowser->getChangePasswdForm($_REQUEST['uid'], $ui_fmask['chgPasswd']));
        $Smarty->assign('showSubjects', TRUE);
    break;

    case "groups":
        $Smarty->assign('groups', $uiBrowser->getGroups($uiBrowser->id));
        $Smarty->assign('addSubj2GroupForm', $uiBrowser->getSubj2GroupForm($uiBrowser->id));
        $Smarty->assign('showSubjects', TRUE);
    break;

    case "getFile":
        $Smarty->assign('fData', $uiBrowser->getFile($uiBrowser->id));
        $Smarty->assign('showFile', TRUE);
    break;

    case "getMdata":
        $Smarty->assign('fMetaData', $uiBrowser->getMdata($uiBrowser->id));
        $Smarty->assign('showFile', TRUE);
    break;

    case "editMetaDataValues":
        $Smarty->assign('mDataForm', $uiBrowser->getMetaDataForm($uiBrowser->id, $ui_fmask['mData'], TRUE));
        $Smarty->assign('showMetaDataForm', TRUE);
    break;

    case "getInfo":
        $Smarty->assign('fInfo', $uiBrowser->getInfo($uiBrowser->id));
        $Smarty->assign('showFile', TRUE);
    break;

    case "systemPrefs":
        $Smarty->assign('dynform', $uiBrowser->systemPrefs($ui_fmask['systemPrefs']));
        $Smarty->assign('showSystemPrefs', TRUE);
    break;
  }
}

$Smarty->display('main.tpl');
?>
