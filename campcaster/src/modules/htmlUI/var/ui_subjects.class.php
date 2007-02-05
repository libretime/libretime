<?php
/**
 * @package Campcaster
 * @subpackage htmlUI
 * @version $Revision$
 */
class uiSubjects
{
	public $Base;
	private $reloadUrl;
	private $suRedirUrl;
	private $redirUrl;

    public function __construct(&$uiBase)
    {
        $this->Base =& $uiBase;
        $this->reloadUrl = UI_BROWSER.'?popup[]=_reload_parent&popup[]=_close';
        $this->suRedirUrl = UI_BROWSER.'?act=SUBJECTS';
        $this->redirUrl = UI_BROWSER.'?act=SUBJECTS';
    }


    public function setReload()
    {
         $this->Base->redirUrl = $this->reloadUrl;
    }


    public function setSuRedir()
    {
         $this->Base->redirUrl = $this->suRedirUrl;
    }


    public function setRedir()
    {
         $this->Base->redirUrl = $this->redirUrl;
    }


    public function getSubjectName($p_name)
    {
        return Subjects::GetSubjName($p_name);
    }


    /**
     * Create a form to add GreenBox subjects (users/groups).
     *
     * @param unknown $type
     * @return string (html)
     */
    public function getAddSubjForm($type)
    {
        include(dirname(__FILE__). '/formmask/subjects.inc.php');

        $form = new HTML_QuickForm('addSubject', UI_STANDARD_FORM_METHOD, UI_HANDLER);
        uiBase::parseArrayToForm($form, $mask[$type]);
        $renderer = new HTML_QuickForm_Renderer_Array(true, true);
        $form->accept($renderer);
        return $renderer->toArray();
    }


   /**
    * Create a new user or group (empty password => create group).
    *
    * @param array $request
    * 		Must have keys -> value:
    * 		login - string
    * 		passwd - string
    * @return string
    */
    public function addSubj($request)
    {
        include(dirname(__FILE__). '/formmask/subjects.inc.php');
        $this->setRedir();

        if ($this->Base->_validateForm($request, $mask[$request['passwd'] ? 'addUser' : 'addGroup']) !== TRUE) {
            return FALSE;
        }
        if (Alib::CheckPerm($this->Base->userid, 'subjects') !== TRUE) {
            $this->Base->_retMsg('Access denied.');
            return FALSE;
        }
        if (Subjects::GetSubjId($request['login'])) {
            $this->Base->_retMsg('User or group "$1" already exists.', $request['login']);
            $this->Base->redirUrl = $_SERVER['HTTP_REFERER'];
            return FALSE;
        }

        $tmpPassword = $request['passwd']==='' ? NULL : $request['passwd'];
        $res = $this->Base->gb->addSubj($request['login'], $tmpPassword);
        if (PEAR::isError($res)) {
            $this->Base->_retMsg($res->getMessage());
            return FALSE;
        }
        if (UI_VERBOSE) {
            if ($request['passwd']) {
                $this->Base->_retMsg('User "$1" added.', $request['login']);
            } else {
                $this->Base->_retMsg('Group "$1" added.', $request['login']);
            }
        }
        return TRUE;
    }


    /**
     * Remove an existing user or group.
     *
     * @todo Renamed this function to "removeSubject".
     * @param array $request
     * 		must contain the "login" key,
     * 		a string, the login name of removed user
     * @return boolean
     */
    public function removeSubj($request)
    {
        $this->setReload();

        if (Alib::CheckPerm($this->Base->userid, 'subjects') !== TRUE) {
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
     * Create a form to change user-passwords in GreenBox.
     *
     * @param string $login
     * @param boolean $su
     * 		this parameter isnt used
     * @return string
     * 		HTML string
     */
    public function getChgPasswdForm($login, $su = FALSE)
    {
        include(dirname(__FILE__). '/formmask/subjects.inc.php');

        $form = new HTML_QuickForm('chgPasswd', UI_STANDARD_FORM_METHOD, UI_HANDLER);
        if (Alib::CheckPerm($this->Base->userid, 'subjects') === TRUE) {
            $mask['chgPasswd']['cancel']['attributes'] = array('onClick' => 'location.href="'.UI_BROWSER.'?act=SUBJECTS"');
            unset ($mask['chgPasswd']['oldpasswd']);
        } else {
            $mask['chgPasswd']['cancel']['attributes'] = array('onClick' => 'location.href="'.UI_BROWSER.'"');
        }
        uiBase::parseArrayToForm($form, $mask['chgPasswd']);
        $form->setConstants(array('login' => $login));
        $renderer = new HTML_QuickForm_Renderer_Array(true, true);
        $form->accept($renderer);
        return $renderer->toArray();
    }


    /**
     * Change password for specified user.
     *
     * @todo Rename this function to "changePassword".
     * @param array $request
     * 		Required array keys: passwd, passwd2, login, oldpasswd
     * @return boolean
     */
    public function chgPasswd($request)
    {
        if ($request['passwd'] !== $request['passwd2']) {
            $this->Base->_retMsg("Passwords did not match.");
            $this->Base->redirUrl = $_SERVER['HTTP_REFERER'];
            return FALSE;
        }

        if (Alib::CheckPerm($this->Base->userid, 'subjects')) {
            $this->setSuRedir();
        } else {
            $this->setRedir();

            if ($this->Base->login !== $request['login']) {
                $this->Base->_retMsg('Access denied.');
                return FALSE;
            }
            if (Subjects::Authenticate($request['login'], $request['oldpasswd']) === FALSE) {
                $this->Base->_retMsg('Old password was incorrect.');
                $this->Base->redirUrl = $_SERVER['HTTP_REFERER'];
                return FASLE;
            }
        }

        if (PEAR::isError($ret = $this->Base->gb->passwd($request['login'], $request['oldpasswd'], $request['passwd'], $this->Base->sessid))) {
            $this->Base->_retMsg($ret->getMessage());
            return FALSE;
        }
        if (UI_VERBOSE) {
            $this->Base->_retMsg('Password changed.');
        }

        return TRUE;
    }


    /**
     * Get all GreenBox subjects (users/groups)
     *
     * @todo Rename this function.
     * @return array
     * 		subj=>unique id of subject
     * 		loggedAs=>corresponding login name
     */
    public function getSubjectsWCnt()
    {
        return Subjects::GetSubjectsWCnt();
    }


    /**
     * Get a list of groups that the user belongs to.
     *
     * @todo Rename this function to "getGroupMembers"
     * @param int $id
     * 		local user ID
     * @return array
     */
    public function getGroupMember($id)
    {
        return Subjects::ListGroup($id);
    } // fn getGroupMember


    /**
     * Get a list of groups that the user does not belong to.
     *
     * @param int $id
     * 		Local user ID
     * @return array
     */
    public function getNonGroupMember($id)
    {
        foreach (Subjects::ListGroup($id) as $val1) {
            $members[$val1['id']] = TRUE;
        }

        $all = Subjects::GetSubjectsWCnt();
        foreach ($all as $key2=>$val2) {
            if ($members[$val2['id']]) {
                unset($all[$key2]);
            }
        }

        return $all;
    } // fn getNonGroupMember


    /**
     * Add a subject to a group.
     *
     * @todo Rename this function to "addSubjectToGroup"
     * @param array $request
     * 		Required array keys: login, id, gname
     * @return boolean
     */
    public function addSubj2Gr(&$request)
    {
        $this->setReload();

        if (!$request['login'] && !$request['id']) {
            $this->Base->_retMsg('Nothing selected.');
            return FALSE;
        }

        // loop for multiple action
        if (is_array($request['id'])) {
            foreach ($request['id'] as $val) {
                $req = array('login' => Subjects::GetSubjName($val), 'gname' => $request['gname']);
                $this->addSubj2Gr($req);
            }
            return TRUE;
        }

        if (Alib::CheckPerm($this->Base->userid, 'subjects') !== TRUE){
            $this->Base->_retMsg('Access denied.');
            return FALSE;
        }
        if (PEAR::isError($res = Subjects::AddSubjectToGroup($request['login'], $request['gname']))) {
            $this->Base->_retMsg($res->getMessage());
            return FALSE;
        }

        return TRUE;
    }


    /**
     * Remove a subject from a group.
     *
     * @param array $request
     * 		Required keys: login, id, gname
     * @return boolean
     */
    public function removeSubjFromGr(&$request)
    {
        $this->setReload();

        if (!$request['login'] && !$request['id']) {
            $this->Base->_retMsg('Nothing selected.');
            return FALSE;
        }

        // loop for multiple action
        if (is_array($request['id'])) {
            foreach ($request['id'] as $val) {
                $req = array('login' => Subjects::GetSubjName($val), 'gname' => $request['gname']);
                $this->removeSubjFromGr($req);
            }
            return TRUE;
        }

        if (Alib::CheckPerm($this->Base->userid, 'subjects') !== TRUE){
            $this->Base->_retMsg('Access denied.');
            return FALSE;
        }
        if (PEAR::isError($res = Subjects::RemoveSubjectFromGroup($request['login'], $request['gname']))) {
            $this->Base->_retMsg($res->getMessage());
            return FALSE;
        }

        return TRUE;
    }


    /**
     * Return true if the subject is a member of the given group.
     *
     * @param string $groupname
     * @return boolean
     */
    public function isMemberOf($groupname)
    {
        if ($gid = Subjects::GetSubjId($groupname)) {
            $members = $this->getGroupMember($gid);
            if (is_array($members)) {
                foreach($members as $member) {
                    if ($member['id'] === $this->Base->userid) {
                        return true;
                    }
                }
            }
        }
        return false;
    } // fn isMemberOf

} // class uiSubjects

?>