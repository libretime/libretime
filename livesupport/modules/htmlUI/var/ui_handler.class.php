<?php
/**
 *  uiHandler class
 *
 *  LiveSupport HTML User Interface module
 */
class uiHandler extends uiBase {
    var $redirUrl;
    var $alertMsg;

    function uiHandler($config)
    {
        $dbc = DB::connect($config['dsn'], TRUE);
        $dbc->setFetchMode(DB_FETCHMODE_ASSOC);
        $this->gb =& new GreenBox(&$dbc, $config);
        $this->id = (!$_REQUEST['id'] ? $this->gb->storId : $_REQUEST['id']);
        $this->sessid = $_REQUEST[$config['authCookieName']];
        $this->userid = $this->gb->getSessUserId($this->sessid);
        $this->login  = $this->gb->getSessLogin ($this->sessid);
        $this->config = $config;
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
                $this->alertMsg = 'Login failed.';
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

        if ($trigger_login)
             $this->redirUrl = UI_BROWSER.'?popup[]=_reload_parent&popup[]=login';
        else $this->redirUrl = UI_BROWSER.'?popup[]=_reload_parent&popup[]=_close';
    }

    // --- files ---
    /**
     *  upload
     *
     *  Provides file upload and store it to the storage
     *
     *  @param filename string, name for the uploaded file
     *  @param mediafile file uploded by HTTP, raw binary media file
     *  @param mdatafile file uploded by HTTP, metadata XML file
     *  @param id int, destination folder id
     */
    function upload(&$formdata, $id, &$mask)
    {
        if ($this->_validateForm($formdata, $mask)) {
            $tmpgunid = md5(
                microtime().$_SERVER['SERVER_ADDR'].rand()."org.mdlf.livesupport"
            );
            $ntmp = $this->gb->bufferDir.'/'.$tmpgunid;
            #        $ntmp = tempnam(""{$gb->bufferDir}", 'gbTmp_');
            $mdtmp = "";
            move_uploaded_file($formdata['mediafile']['tmp_name'], $ntmp);
            chmod($ntmp, 0664);
            if($formdata['mdatafile']['tmp_name']){
                $mdtmp = "$ntmp.xml";
                if(move_uploaded_file($formdata['mdatafile']['tmp_name'], $mdtmp)){
                    chmod($mdtmp, 0664);
                }
            }
            $r = $this->gb->putFile($id, $formdata['new_filename'] ? $formdata['new_filename'] : $formdata['mediafile']['name'], $ntmp, $mdtmp, $this->sessid);
            if(PEAR::isError($r)) $this->alertMsg = $r->getMessage();
            else{
            #            $gb->updateMetadataDB($gb->_pathFromId($r), $mdata, $sessid);
                @unlink($ntmp);
                @unlink($mdtmp);
            }
            $this->redirUrl = UI_BROWSER."?id=".$id;
         } else {
            $this->redirUrl = UI_BROWSER."?act=newfile&id=".$id;
         }
    }


    /**
     *  upload_1
     *
     *  Provides file upload and store it to the storage
     *
     *  @param formdata array, submitted text and file
     *  @param id int, destination folder id
     */
    function upload_1(&$formdata, $id, &$mask)
    {
        if ($this->_validateForm($formdata, $mask)) {
            $tmpgunid = md5(
                microtime().$_SERVER['SERVER_ADDR'].rand()."org.mdlf.livesupport"
            );
            $ntmp = $this->gb->bufferDir.'/'.$tmpgunid;
            #        $ntmp = tempnam(""{$gb->bufferDir}", 'gbTmp_');
            $mdtmp = "";
            move_uploaded_file($formdata['mediafile']['tmp_name'], $ntmp);
            chmod($ntmp, 0664);

            $r = $this->gb->putFile($id, $formdata['new_filename'] ? $formdata['new_filename'] : $formdata['mediafile']['name'], $ntmp, NULL, $this->sessid);
            if(PEAR::isError($r)) $this->alertMsg = $r->getMessage();
            else{
            #            $gb->updateMetadataDB($gb->_pathFromId($r), $mdata, $sessid);
                @unlink($ntmp);
                @unlink($mdtmp);
            }

            ## extract some metadata with getID3
            $this->gb->replaceMetadata($r, $this->getInfo($r, 'xml'), $mdataLoc = 'string', $this->sessid);


            $this->redirUrl = UI_BROWSER."?act=editMetaDataValues&id=$r";
        } else {
            $this->redirUrl = UI_BROWSER."?act=upload_1&id=$id";
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
        $r = $this->gb->createFolder($id, $newname, $this->sessid);
        if(PEAR::isError($r)) $this->alertMsg = $r->getMessage();
        $this->redirUrl = UI_BROWSER.'?id='.$id;
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
        if(PEAR::isError($r)) $this->alertMsg = $r->getMessage();
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
            $this->alertMsg = $r->getMessage();
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
            $this->alertMsg = $r->getMessage();
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
            $this->alertMsg = $this->tra("Folder is not empty. You can override this protection by clicking DEL again");
            $this->redirUrl = UI_BROWSER."?id=$parid&delOverride=$id";
            return;
        }
        #############################

        $r = $this->gb->deleteFile($id, $this->sessid);
        if(PEAR::isError($r)) $this->alertMsg = $r->getMessage();
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
        if(PEAR::isError($r)) $this->alertMsg = $r->getMessage();
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
        $this->redirUrl = UI_BROWSER.'?act='.$_REQUEST['act'];

        ## first validate the form data
        if ($this->_validateForm($formdata, $mask)) {
            if($this->gb->checkPerm($this->userid, 'subjects')){
                $res = $this->gb->addSubj($formdata['login'], ($formdata['pass']=='' ? NULL:$formdata['pass'] ));
                $this->alertMsg = $this->tra('Subject "'.$formdata['login'].'" added.');
            } else {
                $this->alertMsg = $this->tra('Access denied.');
                return;
            }
        }
        if(PEAR::isError($res)) $this->alertMsg = $res->getMessage();
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
            $this->alertMsg='Access denied.';
            return;
        }
        if(PEAR::isError($res)) $this->alertMsg = $res->getMessage();
    }

    /**
     *  passwd
     *
     *  Change password for specified user
     *
     *  @param uid int, local user id
     *  @param oldpass string, old user password
     *  @param pass string, new password
     *  @param pass2 string, retype of new password
     */
    function passwd($uid, $oldpass, $pass, $pass2)
    {
        $this->redirUrl = UI_BROWSER.'?act=subjects';
        $ulogin = $this->gb->getSubjName($uid);

        if($this->userid != $uid &&
            ! $this->gb->checkPerm($this->userid, 'subjects')){
            $this->alertMsg='Access denied..';
            return;
        }
        if(FALSE === $this->gb->authenticate($ulogin, $oldpass)){
            $this->alertMsg='Wrong old pasword.';
            return;
        }
        if($pass !== $pass2){
            $this->alertMsg = "Passwords do not match. ".
                "($pass/$pass2)";
            $this->redirUrl = UI_BROWSER.'?act=subjects';
            return;
        }
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
            $this->alertMsg='Access denied.';
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
        else $this->alertMsg='Access denied.';
        $this->redirUrl = UI_BROWSER.'?act=permissions&id='.$oid;
    }


    /**
     *  addSubj2Group
     *
     *   Add {login} and direct/indirect members to {gname} and to groups,
     *   where {gname} is [in]direct member
     *
     *   @param login string
     *   @param gname string
     *   @param reid string, local id of managed group, just needed for redirect
     */
    function addSubj2Group($login, $gname, $reid)
    {
        if($this->gb->checkPerm($this->userid, 'subjects')){
            $res = $this->gb->addSubj2Gr($login, $gname);
        }else{
            $this->alertMsg='Access denied.';
            return;
        }
        if(PEAR::isError($res)) $this->alertMsg = $res->getMessage();

        $this->redirUrl = UI_BROWSER.'?act=groups&id='.$reid;
    }

    /**
     *   Remove subject from group
     *
     *   @param login string
     *   @param gname string
     *   @param reid string, local id of managed group, just needed for redirect
     */
    function removeSubjFromGr($login, $gname, $reid)
    {
        if($this->gb->checkPerm($this->userid, 'subjects')){
            $res = $this->gb->removeSubjFromGr($login, $gname);
        }else{
            $this->alertMsg='Access denied.';
            return;
        }
        if(PEAR::isError($res)) $this->alertMsg = $res->getMessage();

        $this->redirUrl = UI_BROWSER.'?act=groups&id='.$reid;
    }


    function storeMetaData(&$formdata, &$mask)
    {
        $this->redirUrl = UI_BROWSER.'?act=editMetaDataValues&id='.$formdata['id'];
        foreach ($mask['tabs']['group']['group'] as $key) {
            foreach ($mask['pages'][$key] as $k=>$v) {
                $formdata[$key.'__'.$v['element']] ? $mData[strtr($v['element'], '_', '.')] = $formdata[$key.'__'.$v['element']] : NULL;
            }
        }

        $this->_dateArr2Str(&$mData);

        foreach ($mData as $key=>$val) {
            #$this->gb->setMDataValue($formdata['id'], $key, $val, $this->sessid)
        }

        $this->alertMsg = $this->tra('Metadata saved');
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
        foreach($mask as $k) {
            if ($k['type']=='file' && $k['required']==TRUE) {
                if ($_FILES[$k['element']]['error']) {
                    $_SESSION['retransferFormData'] = array_merge($_REQUEST, $_FILES);
                    return FALSE;
                }
            }
        }

        reset($mask);
        return TRUE;
    }


    function storeSystemPrefs(&$formdata, &$mask)
    {
        $this->redirUrl = UI_BROWSER.'?act=systemPrefs';

        ## first validate the form data
        if ($this->_validateForm($formdata, $mask)) {

            foreach($mask as $key=>$val) {
                if ($this->_isTextInput ($val['type'], $mask)) {
                    if (strlen($formdata[$val['element']]))
                        $this->gb->saveGroupPref($this->sessid, 'StationPrefs', $val['element'], $formdata[$val['element']]);
                    else
                        $this->gb->delGroupPref($this->sessid, 'StationPrefs', $val['element']);
                }
                if ($val['type'] == 'file' && $formdata[$val['element']]['name']) {
                    if (FALSE === @move_uploaded_file($formdata[$val['element']]['tmp_name'], $this->gb->loadGroupPref($this->sessid, 'StationPrefs', 'stationLogoPath')))
                        $this->alertMsg = $this->tra('Error uploading Logo');
                        return;
                }
            }

            $this->alertMsg = $this->tra('Settings saved');
            return;
        }
        $this->alertMsg = $this->tra('Error saving Settings');
    }


    function _isTextInput($input)
    {
        $test = array('text' =>0, 'textarea' =>0, 'select'=>0, 'radio'=>0, 'checkbox'=>0);
        if (array_key_exists($input, $test))
             return TRUE;

        return FALSE;
    }

}

?>