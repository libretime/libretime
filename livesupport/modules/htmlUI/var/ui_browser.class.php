<?php
class uiBrowser extends uiBase {
    var $alertMsg;

    // --- class constructor ---
    /**
     *  uiBrowser
     *
     *  Initialize a new Browser Class
     *  Call uiBase constructor
     *
     *  @param $config array, configurartion data
     */
    function uiBrowser(&$config)
    {
        $this->uiBase($config);
    }

      /**
     *  login
     *
     *  Perform a frontend action
     *  map to a function called action_<actionName>.inc.php
     *
     *  @param actionName string, name of a action
     *  @param params  array[], request vars
     */
    function performAction( $actionName, $params )
    {
        $actionFunctionName = 'action_' . $actionName ;
        $actionFunctionFileName = ACTION_BASE . '/action_' . $actionName . '.inc.php' ;
        if ( file_exists( $actionFunctionFileName ) )
        {
            include ( $actionFunctionFileName ) ;
            if ( method_exists( $actionFunctionName ) )
            {
                $actionFunctionName( &$this, $params ) ;
            }
        }
    }

    // --- error handling ---
    /**
     *  getAlertMsg
     *
     *  extractes error message from session var
     *
     *  @return string
     */


    function getAlertMsg()
    {
        if ($_SESSION['alertMsg']) {
            $this->alertMsg = $_SESSION['alertMsg'];
            unset($_SESSION['alertMsg']);

            return $this->alertMsg;
        }
        return false;
    }


    // --- template feed ---
    /**
     *  login
     *
     *  create a login-form
     *
     *  @param string $faillogin login name of failed login process
     *  @return string (html)
     */
    function login(&$Smarty, &$mask)
    {
        $form = new HTML_QuickForm('login', UI_STANDARD_FORM_METHOD, UI_HANDLER);
        $form->setRequiredNote(file_get_contents(UI_QFORM_REQUIREDNOTE));
        $this->_parseArr2Form($form, $mask['login']);
        $this->_parseArr2Form($form, $mask['languages']);

        ## using Static Smarty Renderer
        $renderer =& new HTML_QuickForm_Renderer_ArraySmarty($Smarty, true);
        $renderer->setRequiredTemplate(file_get_contents(UI_QFORM_REQUIRED));
        #$renderer->setErrorTemplate(file_get_contents(UI_QFORM_ERROR));

        $form->accept($renderer);

        return $renderer->toArray();
    }




    /**
     *  getUserInfo
     *
     *  get info about logged in user
     *
     *  @return array uname=>user Name, uid=>user ID
     */
    function getUserInfo()
    {
        return array('uname'=>$this->gb->getSessLogin($this->sessid),
                     'uid'  =>$this->gb->getSessUserId($this->sessid));
    }

    /**
     *  getStructure
     *
     *  get directory-structure
     *
     *  @param int local ID of start-directory
     *  @param boolean $homedir TRUE: get homedir of current user

     *  @eturn array tree of directory with subs
     */
    function getStructure($id)
    {
        $data = array(
                    'pathdata'  => $this->gb->getPath($id, $this->sessid),
                    'listdata'  => ($this->gb->getObjType($id)=='Folder'?
                                    $this->gb->listFolder($id, $this->sessid) : array()),
                    #'tree'      => ($_REQUEST['tree']=='Y'),
                    'showPath'  => true,
                    'showTree'  => true,
                );
            if($_REQUEST['tree']=='Y'){
                $data['treedata'] = $this->gb->getSubTree($id, $this->sessid);
            }

        if(PEAR::isError($data['listdata'])){
            $data['msg'] = $data['listdata']->getMessage();
            $data['listdata'] = array();
        } else {
            foreach ($data['listdata'] as $key=>$val) {
                if ($val['type'] != 'Folder')
                    $data['listdata'][$key]['title'] = $this->_getMDataValue($val['id'], 'title');
                else
                    $data['listdata'][$key]['title'] = $val['name'];
            }
        }

        return $data;
    }


    /**
     *  uploadFileM
     *
     *  create a form for file-upload
     *
     *  @param int local $id of directory to store file in
     *
     *  @eturn string  (html)
     */
    function uploadFileM($id, $mask)
    {
        $form = new HTML_QuickForm('uploadFileM', UI_STANDARD_FORM_METHOD, UI_HANDLER);
        $form->setMaxFileSize(!PEAR::isError($this->gb->loadGroupPref($this->sessid, 'StationPrefs', 'maxfilesize')) ?
                                                $this->gb->loadGroupPref($this->sessid, 'StationPrefs', 'maxfilesize')
                                                : ini_get('upload_max_filesize'));
        $form->setConstants(array('id' => $id));

        $this->_parseArr2Form($form, $mask);

        return $form->toHTML();
    }


    /**
     *  uploadFile
     *
     *  create a form for file-upload
     *
     *  @param int local $id of directory to store file
     *
     *  @eturn string  (html)
     */
    function uploadFile($id, $mask)
    {
        $form = new HTML_QuickForm('uploadFile', UI_STANDARD_FORM_METHOD, UI_HANDLER);
        $form->setMaxFileSize(!PEAR::isError($this->gb->loadGroupPref($this->sessid, 'StationPrefs', 'maxfilesize')) ?
                                                $this->gb->loadGroupPref($this->sessid, 'StationPrefs', 'maxfilesize')
                                                : ini_get('upload_max_filesize'));
        $form->setConstants(array('id' => $id));

        $this->_parseArr2Form($form, $mask);

        return $form->toHTML();
    }


    /**
     *  addWebstream
     *
     *  create a form to add Webstream
     *
     *  @param int local $id of directory to store stream
     *
     *  @eturn string  (html)
     */
    function addWebstream($id, $mask)
    {
        $form = new HTML_QuickForm('addWebstream', UI_STANDARD_FORM_METHOD, UI_HANDLER);
        $form->setMaxFileSize(!PEAR::isError($this->gb->loadGroupPref($this->sessid, 'StationPrefs', 'maxfilesize')) ?
                                                $this->gb->loadGroupPref($this->sessid, 'StationPrefs', 'maxfilesize')
                                                : ini_get('upload_max_filesize'));
        $form->setConstants(array('id' => $id));

        $this->_parseArr2Form($form, $mask);

        return $form->toHTML();
    }


    /**
     *  getSubjects
     *
     *  get all GreenBox subjects (users/groups)
     *
     *  @return array subj=>unique id of subject, loggedAs=>corresponding login name
     */
    function getSubjects()
    {
        return array('subj'       => $this->gb->getSubjectsWCnt(),
                     'loggedAs'   => $this->login
                    );
    }


    /**
     *  addSubjectForm
     *
     *  create a form to add GreenBox subjects (users/groups)
     *
     *  @return string (html)
     */
    function getAddSubjectForm($mask)
    {
        $form = new HTML_QuickForm('addSubject', UI_STANDARD_FORM_METHOD, UI_HANDLER);
        $this->_parseArr2Form($form, $mask);
        return $form->toHTML();
    }

    /**
     *  chgPasswd
     *
     *  create a form to change user-passwords in GreenBox
     *
     *  @return string (html)
     */
    function chgPasswd($uid, &$mask)
    {
        $form = new HTML_QuickForm('chgPasswd', UI_STANDARD_FORM_METHOD, UI_HANDLER);
        $form->setConstants(array('uid'=>$uid));
        $this->_parseArr2Form($form, $mask);
        return $form->toHTML();
    }

    /**
     *  getGroups
     *
     *  get a list of groups where user is member of
     *
     *  @parm $id int local user ID
     *  @return array
     */
    function groupMembers($id)
    {
        return array(
            'rows'      => $this->gb->listGroup($id),
            'id'        => $id,
            'loggedAs'  => $this->login,
            'gname'     => $this->gb->getSubjName($id),
            'subj'      => $this->gb->getSubjects()
        );
    }


    /**
     *  getSubj2GroupForm
     *
     *  creates a form to assign groups to a user
     *
     *  @param $id int local user ID
     *
     *  @return string (html)
     */
    function addGroupMember($id)
    {
        $g = $this->groupMembers($id);
        foreach($g['subj'] as $s) {
            $this->logins[($s['login'])]=$s['login'];
        }

        $form = new HTML_QuickForm('addGroupMember', UI_STANDARD_FORM_METHOD, UI_HANDLER);
        $form->setConstants(array('act'=>'addGroupMember',
                                  'reid'=>$g['id'],
                                  'gname'=>$g['gname']));
        $form->addElement('hidden', 'act');
        $form->addElement('hidden', 'reid');
        $form->addElement('hidden', 'gname');
        $s =& $form->createElement('select', 'login', 'Add Group: ');
        $s->loadArray($this->logins, NULL);
        $form->addElement($s);
        $form->addElement('submit', NULL, tra('Do'));

        return $form->toHTML();
    }

    /**
     *  getPermissions
     *
     *  get permissions for local object ID
     *
     *  @param $id int local ID (file/folder)
     *
     *  @return array
     */
    function permissions($id)
    {
        return array('pathdata'  => $this->gb->getPath($id),
                     'perms'     => $this->gb->getObjPerms($id),
                     'actions'   => $this->gb->getAllowedActions($this->gb->getObjType($id)),
                     'subjects'  => $this->gb->getSubjects(),
                     'id'        => $id,
                     'loggedAs'  => $this->login
                     );
    }


    /**
     *  getSearchForm
     *
     *  create a form for searching in StorageServer
     *
     *  @param int local ID of start-directory
     *
     *  @return string (html)
     */
    function getSearchForm($id, &$formdata, &$mask)
    {
        $form = new HTML_QuickForm('search', 'get', UI_BROWSER);
        $form->setConstants(array('id'=>$id, 'counter'=>($formdata['counter'] ? $formdata['counter'] : UI_SEARCH_MIN_ROWS)));

        foreach ($mask['mData']['tabs']['group']['group'] as $k=>$v) {
            foreach ($mask['mData']['pages'][$v] as $val){
                $col1[$val['element']] = $val['element'];
                if (isset($val['relation']))
                    $col2[$val['element']] = $mask['relations'][$val['relation']];
                else
                    $col2[$val['element']] = $mask['relations']['standard'];
            };
        };

        for($n=1; $n<=UI_SEARCH_MAX_ROWS; $n++) {
            unset ($group);

            $form->addElement('static', 's1', NULL, "<div id='searchRow_$n'>");

            if ($n>($formdata['counter'] ? $formdata['counter'] : UI_SEARCH_MIN_ROWS)) $form->addElement('static', 's1_style', NULL, "<style type='text/css'>#searchRow_$n {visibility : hidden; height : 0px;}</style>");
            $sel = &$form->createElement('hierselect', "row_$n", NULL);
            $sel->setOptions(array($col1, $col2));
            $group[] = &$sel;
            $group[] = &$form->createElement('text', 'row_'.$n.'[2]', NULL);
            $group[] = &$form->createElement('button', "dropRow_$n", 'Drop', array('onClick' => "dropRow('$n')"));
            $form->addGroup($group);

            $form->addElement('static', 's2', NULL, "</div id='searchRow_$n'>");
        }



        $this->_parseArr2Form($form, $mask['searchform']);
        $form->validate();

        $renderer =& new HTML_QuickForm_Renderer_Array(true, true);
        $form->accept($renderer);
        $output['dynform'] = $renderer->toArray();
              #print_r($output);
        return $output;
    }


    /**
     *  getSearchRes
     *
     *  get Search Result
     *
     *  @param $id int local ID (file/folder) to search in
     *  @param $serach string
     *  @return array
     */
    function getSearchRes($id, &$formdata)
    {
        foreach ($formdata as $key=>$val) {
            if (is_array($val) && strlen($val[2])) {
                $critArr[] = array('cat' => $val[0],
                                   'op'  => $val[1],
                                   'val' => $val[2]
                             );
            }
        }
        $searchCriteria = array('filetype'  => $formdata['filetype'],
                                'operator'  => $formdata['operator'],
                                'conditions'=> $critArr
                          );

        $results = $this->gb->localSearch($searchCriteria, $this->sessid);
        foreach ($results['results'] as $rec) {
                $res[] = $this->_getMetaInfo($this->gb->_idFromGunid($rec));
            }

        return array('search'   => $res,
                     'id'       => $id
               );

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
        if(PEAR::isError($r)) $_SESSION['alertMsg'] = $r->getMessage();
        else print_r($r);
    }

    /**
     *  getMdata
     *
     *  Get file's metadata as XML
     *
     *  @param id int, local id of stored file
     *  @return array
     */
    function getMdata($id)
    {
        return($this->gb->getMdata($id, $this->sessid));
    }


    /**
     *  metaDataForm
     *
     *  create a form to edit Metadata
     *
     *  @param id int
     *  @return string (html)
     */
    function editMetaData($id, $mask, $get=FALSE, $data=NULL)
    {
        $form = new HTML_QuickForm('tabs', UI_STANDARD_FORM_METHOD, UI_BROWSER);
        $this->_parseArr2Form($form, $mask['tabs']);
        $output['tabs'] = $form->toHTML();
        $form = new HTML_QuickForm('langswitch', UI_STANDARD_FORM_METHOD, UI_BROWSER);
        $this->_parseArr2Form($form, $mask['langswitch']);
        $output['langswitch'] = $form->toHTML();

        $form = new HTML_QuickForm('editMetaData', UI_STANDARD_FORM_METHOD, UI_HANDLER);
        $this->_parseArr2Form($form, $mask['basics']);
        $form->setConstants( array('id'     => $id,
                                   #!!!!!'langid' => array_pop($this->gb->getMDataValue($id, 'langid', $this->sessid))
                                   'langid'  => 'en'
                             )
                           );

        ## convert element names to be unique over different forms-parts, add javascript to spread values over parts, add existing values from database
        foreach ($mask['tabs']['group']['group'] as $key) {
            foreach ($mask['pages'][$key] as $k=>$v) {
                $mask['pages'][$key][$k]['element']    = $key.'__'.$v['element'];
                $mask['pages'][$key][$k]['attributes'] = array_merge($mask['pages'][$key][$k]['attributes'], array('onChange' => "spread(this, '".$v['element']."')"));

                ## recive data from GreenBox
                if ($get) {
                    $mask['pages'][$key][$k]['default'] = $this->_getMDataValue($id, strtr($v['element'], '_', '.'));
                }

                ## get data from parameter
                if (is_array($data)) {
                    $mask['pages'][$key][$k]['default'] = $data[strtr($v['element'], '_', '.')];
                }
            }
            $form->addElement('static', NULL, NULL, "<div id='div_$key'>");
            $this->_parseArr2Form($form, $mask['pages'][$key]);
            $this->_parseArr2Form($form, $mask['buttons']);
            $form->addElement('static', NULL, NULL, "</div id='div_$key'>");
        }

        ## using Dynamic Smarty Renderer
        $renderer =& new HTML_QuickForm_Renderer_Array(true, true);
        $form->accept($renderer);
        $output['pages'][] = $renderer->toArray();
        #print_r($output);

        return $output;
    }



    function systemPrefsForm(&$mask)
    {
        $form = new HTML_QuickForm('systemPrefs', UI_STANDARD_FORM_METHOD, UI_HANDLER);

        foreach($mask as $key=>$val) {
            $p = $this->gb->loadGroupPref($this->sessid, 'StationPrefs', $val['element']);
            if (is_string($p)) $mask[$key]['default'] = $p;
        };

        $this->_parseArr2Form($form, $mask);

        ## using Dynamic Smarty Renderer
        $renderer =& new HTML_QuickForm_Renderer_Array(true, true);
        $form->accept($renderer);

        return $renderer->toArray();
    }
}
?>