<?php
class uiSubjects
{
    function uiSubjects(&$uiBase)
    {
        $this->Base       =& $uiBase;
        $this->reloadUrl  = UI_BROWSER.'?popup[]=_reload_parent&popup[]=_close';
        $this->redirUrl   = UI_BROWSER.'?act=SUBJECTS';
    }

    function setReload()
    {
         $this->Base->redirUrl = $this->reloadUrl;
    }

    function setRedir()
    {
         $this->Base->redirUrl = $this->redirUrl;
    }
    /**
     *  getAddSubjectForm
     *
     *  create a form to add GreenBox subjects (users/groups)
     *
     *  @return string (html)
     */
    function getAddSubjForm($type)
    {
        include dirname(__FILE__). '/formmask/subjects.inc.php';

        $form = new HTML_QuickForm('addSubject', UI_STANDARD_FORM_METHOD, UI_HANDLER);
        $this->Base->_parseArr2Form($form, $mask[$type]);
        $renderer =& new HTML_QuickForm_Renderer_Array(true, true);
        $form->accept($renderer);
        return $renderer->toArray();
    }


   /**
    *  addSubj
    *
    *  Create new user or group (empty pass => create group)
    *
    *  @param formdata array('login', 'pass')
    */
    function addSubj(&$request)
    {
        include dirname(__FILE__). '/formmask/subjects.inc.php';
        $this->setRedir();

        if ($this->Base->_validateForm($request, $mask[$request['passwd'] ? 'addUser' : 'addGroup']) !== TRUE) {
            return FALSE;
        }
        if ($this->Base->gb->checkPerm($this->Base->userid, 'subjects') !== TRUE) {
            $this->Base->_retMsg('Access denied.');
            return FALSE;
        }
        if ($this->Base->gb->getSubjId($request['login'])) {
            $this->Base->_retMsg('User or group "$1" already exists.', $request['login']);
            $this->Base->redirUrl = $_SERVER['HTTP_REFERER'];
            return FALSE;
        }

        if (PEAR::isError($res = $this->Base->gb->addSubj($request['login'], ($request['passwd']==='' ? NULL : $request['passwd'])))) {
            $this->Base->_retMsg($res->getMessage());
            return FALSE;
        }
        if (UI_VERBOSE) $this->Base->_retMsg('Subject $1 added.', $request['login']);

        return TRUE;
    }

    /**
     *  removeSubj
     *
     *  Remove existing user or group
     *
     *  @param login string, login name of removed user
     */
    function removeSubj(&$request)
    {
        $this->setReload();

        if ($this->Base->gb->checkPerm($this->Base->userid, 'subjects') !== TRUE) {
            $this->Base->_retMsg('Access denied.');
            return FALSE;
        }
        if (PEAR::isError($res = $this->Base->gb->removeSubj($request['login']))) {
            $this->Base->_retMsg($res->getMessage());
            return FALSE;
        }

        return TRUE;
    }


    /**
     *  getChgPasswdForm
     *
     *  create a form to change user-passwords in GreenBox
     *
     *  @return string (html)
     */
    function getChgPasswdForm($login, $su=FALSE)
    {
        include dirname(__FILE__). '/formmask/subjects.inc.php';

        $form = new HTML_QuickForm('chgPasswd', UI_STANDARD_FORM_METHOD, UI_HANDLER);
        if ($su === TRUE) {
            unset ($mask['chgPasswd']['oldpasswd']);
        }
        $this->Base->_parseArr2Form($form, $mask['chgPasswd']);
        $form->setConstants(array('login' => $login));
        $renderer =& new HTML_QuickForm_Renderer_Array(true, true);
        $form->accept($renderer);
        return $renderer->toArray();
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
    function chgPasswd(&$request)
    {
        $this->setRedir();

        if ($this->Base->userid != $uid &&
            ! $this->Base->gb->checkPerm($this->Base->userid, 'subjects')){
            $this->Base->_retMsg('Access denied.');
            return FALSE;
        }
        if (FALSE === $this->Base->gb->authenticate($request['login'], $request['oldpasswd'])) {
            $this->Base->_retMsg('Old password was incorrect.');
            $this->Base->redirUrl = $_SERVER['HTTP_REFERER'];
            return FASLE;
        }
        if ($request['passwd'] !== $request['passwd2']) {
            $this->Base->_retMsg("Passwords did not match.");
            $this->Base->redirUrl = $_SERVER['HTTP_REFERER'];
            return FALSE;
        }
        if (PEAR::isError($ret = $this->Base->gb->passwd($request['login'], $request['oldpass'], $request['pass'], $this->Base->sessid))) { 
            $this->Base->_retMsg($ret->getMessage());
            return FALSE;
        }
        if (UI_VERBOSE)
            $this->Base->_retMsg('Password changed.');
        return TRUE;
    }


    /**
     *  getSubjects
     *
     *  get all GreenBox subjects (users/groups)
     *
     *  @return array subj=>unique id of subject, loggedAs=>corresponding login name
     */
    function getSubjectsWCnt()
    {
        return $this->Base->gb->getSubjectsWCnt();
    }

    /**
     *  getGroupMember
     *
     *  get a list of groups where user is member of
     *
     *  @parm $id int local user ID
     *  @return array
     */
    function getGroupMember($id)
    {
        return $this->Base->gb->listGroup($id);
    }

    /**
     *  getNonGroupMember
     *
     *  get a list of groups where user is member of
     *
     *  @parm $id int local user ID
     *  @return array
     */
    function getNonGroupMember($id)
    {
        foreach($this->Base->gb->listGroup($id) as $val1)
            $members[$val1['id']] = TRUE;

        $all = $this->Base->gb->getSubjectsWCnt();
        foreach($all as $key2=>$val2)
            if($members[$val2['id']])
                unset($all[$key2]);

        return $all;
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
    function addSubj2Gr(&$request)
    {
        $this->setReload();

        if (!$request['login'] && !$request['id']) {
            $this->Base->_retMsg('Nothing selected.');
            return FALSE;
        }

        ## loop for multiple action
        if (is_array($request['id'])) {
            foreach ($request['id'] as $val) {
                $req = array('login' => $this->Base->gb->getSubjName($val), 'gname' => $request['gname']);
                $this->addSubj2Gr($req);
            }
            return TRUE;
        }

        if ($this->Base->gb->checkPerm($this->Base->userid, 'subjects') !== TRUE){
            $this->Base->_retMsg('Access denied.');
            return FALSE;
        }
        if (PEAR::isError($res = $this->Base->gb->addSubj2Gr($request['login'], $request['gname']))) {
            $this->Base->_retMsg($res->getMessage());
            return FALSE;
        }

        return TRUE;
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
    function removeSubjFromGr(&$request)
    {
        $this->setReload();

        if (!$request['login'] && !$request['id']) {
            $this->Base->_retMsg('Nothing selected.');
            return FALSE;
        }

        ## loop for multiple action
        if (is_array($request['id'])) {
            foreach ($request['id'] as $val) {
                $req = array('login' => $this->Base->gb->getSubjName($val), 'gname' => $request['gname']);
                $this->removeSubjFromGr($req);
            }
            return TRUE;
        }

        if ($this->Base->gb->checkPerm($this->Base->userid, 'subjects') !== TRUE){
            $this->Base->_retMsg('Access denied.');
            return FALSE;
        }
        if (PEAR::isError($res = $this->Base->gb->removeSubjFromGr($request['login'], $request['gname']))) {
            $this->Base->_retMsg($res->getMessage());
            return FALSE;
        }

        return TRUE;
    }
}


?>
