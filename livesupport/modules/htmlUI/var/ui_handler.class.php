<?php

define('ACTION_BASE', '/actions' ) ;

/**
 *  uiHandler class
 *
 *  LiveSupport HTML User Interface module
 */
class uiHandler extends uiBase {
#    var $redirUrl;
#    var $alertMsg;

    // --- class constructor ---
    /**
     *  uiBrowser
     *
     *  Initialize a new Browser Class
     *  Call uiBase constructor
     *
     *  @param $config array, configurartion data
     */
    function uiHandler(&$config)
    {
        $this->uiBase($config);
    }


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
    function login(&$formdata, &$mask)
    {
        if ($this->_validateForm($formdata, $mask)) {
            $sessid = $this->gb->login($formdata['login'], $formdata['pass']);
            if($sessid && !PEAR::isError($sessid)){
                setcookie($this->config['authCookieName'], $sessid);

                $fid = $this->gb->getObjId($formdata['login'], $this->gb->storId);
                if(!PEAR::isError($fid)) $this->redirUrl = UI_BROWSER.'?popup[]=_reload_parent&popup[]=_close';
            }else{
                $this->_retMsg('Login failed.');
                $_SESSION['retransferFormData']['login']=$formdata['login'];
                $this->redirUrl = UI_BROWSER.'?popup[]=login';
            }
        }

    }

    /**
     *  logout
     *
     *  Logut from storageServer, takes sessid from cookie
     *
     *  @param $trigger_login boolean, trigger login popup after logout
     *
     */
    function logout($trigger_login = FALSE)
    {
        $this->gb->logout($this->sessid);
        setcookie($this->config['authCookieName'], '');
        session_destroy();

        if ($trigger_login)
             $this->redirUrl = UI_BROWSER.'?popup[]=_reload_parent&popup[]=login';
        else $this->redirUrl = UI_BROWSER.'?popup[]=_reload_parent&popup[]=_close';
    }

    // --- files ---
    /**
     *  uploadFileM
     *
     *  Provides file upload and store it to the storage
     *
     *  @param filename string, name for the uploaded file
     *  @param mediafile file uploded by HTTP, raw binary media file
     *  @param mdatafile file uploded by HTTP, metadata XML file
     *  @param id int, destination folder id
     */
    function uploadFileM(&$formdata, $id, &$mask)
    {
    if ($this->_isFolder($id)) {
            if ($this->_validateForm($formdata, $mask)) {
                $tmpgunid = md5(
                    microtime().$_SERVER['SERVER_ADDR'].rand()."org.mdlf.livesupport"
                );
                $ntmp = $this->gb->bufferDir.'/'.$tmpgunid;
                $mdtmp = "";
                move_uploaded_file($formdata['mediafile']['tmp_name'], $ntmp);
                chmod($ntmp, 0664);
                if($formdata['mdatafile']['tmp_name']){
                    $mdtmp = "$ntmp.xml";
                    if(move_uploaded_file($formdata['mdatafile']['tmp_name'], $mdtmp)){
                        chmod($mdtmp, 0664);
                    }
                }
                $r = $this->gb->putFile($id, $formdata['mediafile']['name'], $ntmp, $mdtmp, $this->sessid);
                if(PEAR::isError($r)) {
                    $this->_retMsg($r->getMessage());
                    $this->redirUrl = UI_BROWSER."?act=uploadFileM&id=".$id;
                    return FALSE;
                } else{
                    @unlink($ntmp);
                    @unlink($mdtmp);
                    $this->redirUrl = UI_BROWSER."?id=".$id;
                    return $r;
                }
             } else {
                $this->redirUrl = UI_BROWSER."?act=uploadFileM&id=".$id;
                return FALSE;
             }
        } else {
            $this->redirUrl = UI_BROWSER.'?id='.$this->gb->getParent($id);
            return FALSE;
        }
    }


    /**
     *  uploadFile
     *
     *  Provides file upload and store it to the storage
     *
     *  @param formdata array, submitted text and file
     *  @param id int, destination folder id
     */
    function uploadFile(&$formdata, $id, &$mask)
    {
        if ($this->_isFolder($id)) {
            if ($this->_validateForm($formdata, $mask)) {
                $tmpgunid = md5(
                    microtime().$_SERVER['SERVER_ADD3R'].rand()."org.mdlf.livesupport"
                );
                $ntmp = $this->gb->bufferDir.'/'.$tmpgunid;
                $mdtmp = "";
                move_uploaded_file($formdata['mediafile']['tmp_name'], $ntmp);
                chmod($ntmp, 0664);

                $r = $this->gb->putFile($id, $formdata['mediafile']['name'], $ntmp, NULL, $this->sessid);
                if(PEAR::isError($r)) {
                    $this->_retMsg($r->getMessage());
                    $this->redirUrl = UI_BROWSER."?act=uploadFile&id=$id";
                    return FALSE;
                } else{
                    @unlink($ntmp);
                    @unlink($mdtmp);
                    $this->redirUrl = UI_BROWSER."?act=editMetaData&id=$r";
                    $this->gb->replaceMetadata($r, $this->_analyzeFile($r, 'xml'), 'string', $this->sessid);
                    return $r;
                }
            } else {
                $this->redirUrl = UI_BROWSER."?act=uploadFile&id=$id";
                return FALSE;
            }
        } else {
            $this->redirUrl = UI_BROWSER.'?id='.$this->gb->getParent($id);
            return FALSE;
        }
    }


    /**
     *  addWebstream
     *
     *  Provides file upload and store it to the storage
     *
     *  @param formdata array, submitted text and file
     *  @param id int, destination folder id
     */
    function addWebstream(&$formdata, $id, &$mask)
    {
        if ($this->_isFolder($id)) {
            if ($this->_validateForm($formdata, $mask)) {

                $r = $this->gb->storeWebstream($id, $formdata['name'], NULL, $this->sessid, NULL, $formdata['url']);
                if(PEAR::isError($r)) {
                    $this->_retMsg($r->getMessage());
                    $this->redirUrl = UI_BROWSER."?act=addWebstream&id=$id";
                    return FALSE;
                    }
                else{
                    $data = $this->_dateArr2Str($formdata);
                    $this->gb->setMDataValue($r, 'dc:title', $this->sessid, $data['name']);
                    $this->gb->setMDataValue($r, 'dcterms:extent', $this->sessid, $data['duration']);
                    $this->redirUrl = UI_BROWSER."?act=editMetaData&id=$r";
                    return $r;
                }
            } else {
                $this->redirUrl = UI_BROWSER."?act=addWebstream&id=$id";
                return FALSE;
            }
        } else {
            $this->redirUrl = UI_BROWSER.'?id='.$this->gb->getParent($id);
            return FALSE;
        }
    }


    /**
     *  newFolder
     *
     *  Create new folder in the storage
     *
     *  @param newname string, name for the new folder
     *  @param id int, destination folder id
     */
    function newFolder($newname, $id)
    {
        if ($this->_isFolder($id)) {
            $r = $this->gb->createFolder($id, $newname, $this->sessid);
            if(PEAR::isError($r))
                $this->_retMsg($r->getMessage());
            $this->redirUrl = UI_BROWSER.'?id='.$id;
        } else {
            $this->redirUrl = UI_BROWSER.'?id='.$this->gb->getParent($id);
        }
    }

    /**
     *  rename
     *
     *  Change the name of file or folder
     *
     *  @param newname string, new name for the file or folder
     *  @param id int, destination folder id
     */
    function rename($newname, $id)
    {
        $parid = $this->gb->getparent($this->id);
        $r = $this->gb->renameFile($id, $newname, $this->sessid);
        if(PEAR::isError($r)) $this->_retMsg($r->getMessage());
        $this->redirUrl = UI_BROWSER."?id=$parid";
    }

    /**
     *  move
     *
     *  Move file to another folder
     *  TODO: format of destinantion path should be properly defined
     *
     *  @param newPath string, destination relative path
     *  @param id int, destination folder id
     */
    function move($newPath, $id)
    {
        $newPath = urlencode($newPath);
        $did = $this->gb->getObjIdFromRelPath($id, $newPath);
        $parid = $this->gb->getparent($id);
        $r = $this->gb->moveFile($id, $did, $this->sessid);
        if(PEAR::isError($r)){
            $this->_retMsg($r->getMessage());
            $this->redirUrl  = UI_BROWSER."?id=$parid";
        }
        else $this->redirUrl = UI_BROWSER."?id=$did";
    }

    /**
     *  copy
     *
     *  Copy file to another folder
     *  TODO: format of destinantion path should be properly defined
     *
     *  @param newPath string, destination relative path
     *  @param id int, destination folder id
     */
    function copy($newPath, $id)
    {
        $newPath = urldecode($newPath);
        $did = $this->gb->getObjIdFromRelPath($id, $newPath);
        $parid = $this->gb->getparent($id);
        $r = $this->gb->copyFile($id, $did, $this->sessid);
        if(PEAR::isError($r)){
            $this->_retMsg($r->getMessage());
            $this->redirUrl  = UI_BROWSER."?id=$parid";
        }
        else $this->redirUrl = UI_BROWSER."?id=$did";
    }

    /**
     *  delete
     *
     *  Delete of stored file
     *
     *  @param id int, local id of deleted file or folder
     *  @param delOverride int, local id od folder which can deleted if not empty
     */
    function delete($id, $delOverride=FALSE)
    {
        $parid = $this->gb->getparent($id);

        ## add emtyness-test here ###
        if (!($delOverride==$id) && (count($this->gb->getObjType($id)=='Folder'?
                      $this->gb->listFolder($id, $this->sessid):NULL))) {
            $this->_retMsg("Folder is not empty. You can override this protection by clicking DEL again");
            $this->redirUrl = UI_BROWSER."?id=$parid&delOverride=$id";
            return;
        }
        #############################

        $r = $this->gb->deleteFile($id, $this->sessid);
        if(PEAR::isError($r)) $this->_retMsg($r->getMessage());
        $this->redirUrl = UI_BROWSER."?id=$parid";
    }


    /**
     *  getFile
     *
     *  Call access method and show access path.
     *  Example only - not really useable.
     *  TODO: resource should be released by release method call
     *
     *  @param id int, local id of accessed file
     */
    function getFile($id)
    {
        $r = $this->gb->access($id, $this->sessid);
        if(PEAR::isError($r)) $this->_retMsg($r->getMessage());
        else echo $r;
    }

    /**
     *  getMdata
     *
     *  Show file's metadata as XML
     *
     *  @param id int, local id of stored file
     */
    function getMdata($id)
    {
        header("Content-type: text/xml");
        $r = $this->gb->getMdata($id, $this->sessid);
        print_r($r);
    }

   // --- subjs ----
   /**
    *  addSubj
    *
    *  Create new user or group (empty pass => create group)
    *
    *  @param formdata array('login', 'pass')
    */
    function addSubj(&$formdata, &$mask)
    {
        $this->redirUrl = UI_BROWSER.'?act='.$formdata['act'];

        ## first validate the form data
        if ($this->_validateForm($formdata, $mask)) {
            if($this->gb->checkPerm($this->userid, 'subjects')){
                $res = $this->gb->addSubj($formdata['login'], ($formdata['pass']=='' ? NULL:$formdata['pass'] ));
                $this->_retMsg('Subject $1 added.', $formdata['login']);
            } else {
                $this->_retMsg('Access denied.');
                return;
            }
        }
        if(PEAR::isError($res)) $this->_retMsg($res->getMessage());
    }

    /**
     *  removeSubj
     *
     *  Remove existing user or group
     *
     *  @param login string, login name of removed user
     */
    function removeSubj($login)
    {
        $this->redirUrl = UI_BROWSER.'?act=subjects';

        if($this->gb->checkPerm($this->userid, 'subjects')){
            $res = $this->gb->removeSubj($login);
        }else{
            $this->_retMsg('Access denied.');
            return;
        }
        if(PEAR::isError($res)) $this->_retMsg($res->getMessage());
    }

    /**
     *  chgPasswd
     *
     *  Change password for specified user
     *
     *  @param uid int, local user id
     *  @param oldpass string, old user password
     *  @param pass string, new password
     *  @param pass2 string, retype of new password
     */
    function chgPasswd($uid, $oldpass, $pass, $pass2)
    {
        $this->redirUrl = UI_BROWSER.'?act=chgPasswd&uid='.$uid;
        $ulogin = $this->gb->getSubjName($uid);

        if($this->userid != $uid &&
            ! $this->gb->checkPerm($this->userid, 'subjects')){
            $this->_retMsg('Access denied.');
            return;
        }
        if(FALSE === $this->gb->authenticate($ulogin, $oldpass)){
            $this->_retMsg('Wrong old pasword.');
            return;
        }
        if($pass !== $pass2){
            $this->_retMsg("Passwords do not match.").
                "($pass/$pass2)";
            $this->redirUrl = UI_BROWSER.'?act=subjects';
            return;
        }
        $this->_retMsg('Password changed');
        $this->redirUrl = UI_BROWSER.'?act=subjects';
        $this->gb->passwd($ulogin, $oldpass, $pass);
    }

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
    function addPerm($subj, $permAction, $id, $allowDeny)
    {
        if($this->gb->checkPerm($this->userid, 'editPerms', $id)){
            $this->gb->addPerm($subj, $permAction,
                $id, $allowDeny);
        }else{
            $this->_retMsg('Access denied.');
        }
        $this->redirUrl = UI_BROWSER.'?id='.$id.'&act=permissions';
    }

    /**
     *  removePerm
     *
     *  Remove permission record
     *
     *  @param permid int, local id of permission record
     *  @param oid int, local id of object to handle
     */
    function removePerm($permid, $oid)
    {
        if($this->gb->checkPerm($this->userid, 'editPerms', $oid))
            $this->gb->removePerm($permid);
        else $this->_retMsg('Access denied.');
        $this->redirUrl = UI_BROWSER.'?act=permissions&id='.$oid;
    }


    /**
     *   addSubj2Group
     *
     *   Add {login} and direct/indirect members to {gname} and to groups,
     *   where {gname} is [in]direct member
     *
     *   @param login string
     *   @param gname string
     *   @param reid string, local id of managed group, just needed for redirect
     */
    function addSubj2Group(&$formdata)
    {
        if($this->gb->checkPerm($this->userid, 'subjects')){
            $res = $this->gb->addSubj2Gr($formdata['login'], $formdata['gname']);
        }else{
            $this->_retMsg('Access denied.');
            return;
        }
        if(PEAR::isError($res)) $this->_retMsg($res->getMessage());

        $this->redirUrl = UI_BROWSER.'?act=groupMembers&id='.$formdata['reid'];
    }

    /**
     *   removeGroupMember
     *
     *   Remove subject from group
     *
     *   @param login string
     *   @param gname string
     *   @param reid string, local id of managed group, just needed for redirect
     */
    function removeGroupMember(&$formdata)
    {
        if ($this->gb->checkPerm($this->userid, 'subjects')){
            $res = $this->gb->removeSubjFromGr($formdata['login'], $formdata['gname']);
        } else {
            $this->_retMsg('Access denied.');
            return;
        }
        if(PEAR::isError($res)) $this->_retMsg($res->getMessage());
        $this->redirUrl = UI_BROWSER.'?act=groupMembers&id='.$formdata['reid'];
    }


    function editMetaData($id, &$formdata, &$mask)
    {
        $this->redirUrl = UI_BROWSER.'?id='.$this->gb->getParent($id);
        ## first remove old entrys
        $this->gb->replaceMetaData($id, $this->_analyzeFile($id, 'xml'), 'string', $this->sessid);

        foreach ($mask['tabs']['group']['group'] as $key) {
            foreach ($mask['pages'][$key] as $k=>$v) {
                $formdata[$key.'__'.$v['element']] ? $mData[strtr($v['element'], '_', '.')] = $formdata[$key.'__'.$v['element']] : NULL;
            }
        }
        $data = $this->_dateArr2Str($mData);
        foreach ($data as $key=>$val) {
            $this->gb->setMDataValue($id, $key, $this->sessid, $val);
        }
        $this->_retMsg('Metadata saved');
    }


    function _validateForm(&$formdata, &$mask)
    {
        $form = new HTML_QuickForm('validation', UI_STANDARD_FORM_METHOD, UI_HANDLER);
        $this->_parseArr2Form($form, $mask, 'server');
        if (!$form->validate()) {
            $_SESSION['retransferFormData'] = $_REQUEST;
            return FALSE;
        }
        ## test for uploadet files bacause HTMLQuickForm::validate() ignores them ####
        if (is_array($form->_submitFiles)) {
            foreach ($form->_submitFiles as $key => $val) {
                if ($val['error']) {

                    switch ($val['error']) {
                        case 1: $was_error = TRUE; $this->_retMsg('Uploaded File $1 is greater than System setting.', $mask[$key]['label']); break;
                        case 2: $was_error = TRUE; $this->_retMsg('Uploaded File $1 is greater than LS setting.', $mask[$key]['label']); break;
                        case 3: $was_error = TRUE; $this->_retMsg('File $1 was uploadet partly.', $mask[$key]['label']); break;
                        case 4: if ($mask[$key]['required']) {$was_error = TRUE; $this->_retMsg('File $1 was not uploadet.', $mask[$key]['label']);} break;
                    }
                }
            }
            if ($was_error) {
                $_SESSION['retransferFormData'] = array_merge($_REQUEST, $_FILES);
                return FALSE;
            }
        }
        /*
        foreach($mask as $k) {
            if ($k['type']=='file' && $k['required']==TRUE) {
                if ($_FILES[$k['element']]['error']) {
                    $_SESSION['retransferFormData'] = array_merge($_REQUEST, $_FILES);
                    return FALSE;
                }
            }
        }  */
        return TRUE;
    }


    function storeSystemPrefs(&$formdata, &$mask)
    {
        $this->redirUrl = UI_BROWSER.'?act=systemPrefs';

        if ($this->_validateForm($formdata, $mask)) {
            foreach($mask as $key=>$val) {
                if ($val['isPref']) {
                    if (strlen($formdata[$val['element']]))
                        $this->gb->saveGroupPref($this->sessid, 'StationPrefs', $val['element'], $formdata[$val['element']]);
                    else
                        $this->gb->delGroupPref($this->sessid, 'StationPrefs', $val['element']);
                        $this->systemPrefs[$val['element']] = is_string($this->gb->loadGroupPref(NULL, 'StationPrefs', $val['element'])) ? $this->gb->loadGroupPref($this->sessid, 'StationPrefs', $val['element']) : NULL;
                }
                if ($val['type'] == 'file' && $formdata[$val['element']]['name']) {
                    if (FALSE === @move_uploaded_file($formdata[$val['element']]['tmp_name'], $this->gb->loadGroupPref($this->sessid, 'StationPrefs', 'stationLogoPath')))
                        $this->_retMsg('Error uploading Logo');
                        return;
                }
            }

            $this->_retMsg('Settings saved');
            return TRUE;
        } else {
            $this->_retMsg('Error saving Settings');
            return FALSE;
        }
    }

    /*
    function _isTextInput($input)
    {
        $test = array('text' =>0, 'textarea' =>0, 'select'=>0, 'radio'=>0, 'checkbox'=>0);
        if (array_key_exists($input, $test))
             return TRUE;

        return FALSE;
    }
    */
}

?>