<?php
class uiBrowser extends uiBase {
    var $alertMsg;

    // --- class constructor ---
    /**
     *  uiBrowser
     *
     *  Initialize a new Browser Class including:
     *  - database  initialation
     *  - GreenBox initialation
     *
     *  @param $config array, configurartion data
     */
    function uiBrowser($config)
    {
        $dbc = DB::connect($config['dsn'], TRUE);
        if (DB::isError($dbc)) {
            die($dbc->getMessage());
        }
        $dbc->setFetchMode(DB_FETCHMODE_ASSOC);
        $this->gb =& new GreenBox(&$dbc, $config);
        $this->sessid = $_REQUEST[$config['authCookieName']];
        $this->userid = $this->gb->getSessUserId($this->sessid);
        $this->login  = $this->gb->getSessLogin($this->sessid);
        $this->id =  $_REQUEST['id'] ? $_REQUEST['id'] : $this->gb->getObjId($this->login, $this->gb->storId);
        $this->InputTextStandardAttrib = array('size'     =>UI_INPUT_STANDARD_SIZE,
                                               'maxlength'=>UI_INPUT_STANDARD_MAXLENGTH);



    }

    // --- error handling ---
    /**
     *  alertMsg
     *
     *  takes error message from session var
     *
     *  @return string
     */

    function alertMsg()
    {
        if ($_SESSION['alertMsg']) {
            $this->alertMsg = $_SESSION['alertMsg'];
            unset($_SESSION['alertMsg']);
            return $this->alertMsg;
        }
        return false;
    }


    function getStationInfo()
    {
        $a['frequency']   = $this->gb->loadGroupPref($this->sessid, 'StationPrefs', 'frequency');
        $a['stationName'] = $this->gb->loadGroupPref($this->sessid, 'StationPrefs', 'stationName');
        $a['stationLogoPath'] = $this->gb->loadGroupPref($this->sessid, 'StationPrefs', 'stationLogoPath');

        return $a;
    }

    // --- template feed ---
    /**
     *  loginform
     *
     *  create a login-form
     *
     *  @param string $faillogin login name of failed login process
     *  @return string (html)
     */
    function loginform(&$Smarty, &$mask)
    {
        $form = new HTML_QuickForm('loginbox', UI_STANDARD_FORM_METHOD, UI_HANDLER);
        $form->setRequiredNote(file_get_contents(UI_QFORM_REQUIREDNOTE));
        $this->_parseArr2Form($form, $mask['loginform']);
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
        $data=array_merge($data, array(
                'pathdata'  => $this->gb->getPath($id, $this->sessid),
                'listdata'  => ($this->gb->getObjType($id)=='Folder'?
                    $this->gb->listFolder($id, $this->sessid):array()
                ),
                'tree'  => ($_REQUEST['tree']=='Y'),
                'showPath'  => true,
                'showTree'  => true,
            ));
            if($_REQUEST['tree']=='Y'){
                $data['treedata'] = $this->gb->getSubTree($id, $this->sessid);
            }

        if(PEAR::isError($data['listdata'])){
            $data['msg'] = $data['listdata']->getMessage();
            $data['listdata'] = array();
        }

        return $data;
    }


    /**
     *  getNewFileForm
     *
     *  create a form for file-upload
     *
     *  @param int local $id of directory to store file in
     *
     *  @eturn string  (html)
     */
    function getNewFileForm($id, $mask)
    {
        $form = new HTML_QuickForm('newfile', UI_STANDARD_FORM_METHOD, UI_HANDLER);
        $form->setMaxFileSize($this->gb->loadGroupPref($this->sessid, 'StationPrefs', 'maxfilesize'));
        $form->setConstants(array('id' => $id));

        $this->_parseArr2Form($form, $mask);

        return $form->toHTML();
    }


    /**
     *  getUploadFileForm
     *
     *  create a form for file-upload
     *
     *  @param int local $id of directory to store file in
     *
     *  @eturn string  (html)
     */
    function getUploadFileForm($id, $mask)
    {
        $form = new HTML_QuickForm('upload', UI_STANDARD_FORM_METHOD, UI_HANDLER);
        $form->setMaxFileSize($this->gb->loadGroupPref($this->sessid, 'StationPrefs', 'maxfilesize'));
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
     *  getChangePasswdForm
     *
     *  create a form to change user-passwords in GreenBox
     *
     *  @return string (html)
     */
    function getChangePasswdForm($uid, &$mask)
    {
        $form = new HTML_QuickForm('changePasswd', UI_STANDARD_FORM_METHOD, UI_HANDLER);
        $form->setConstants(array('act'=>'changePasswd',
                                  'uid'=>$uid));
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
    function getGroups($id)
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
    function getSubj2GroupForm($id)
    {
        $g = $this->getGroups($id);
        foreach($g['subj'] as $s) {
            $this->logins[($s['login'])]=$s['login'];
        }

        $form = new HTML_QuickForm('addSubj2Group', UI_STANDARD_FORM_METHOD, UI_HANDLER);
        $form->setConstants(array('act'=>'addSubj2Group',
                                  'reid'=>$g['id'],
                                  'gname'=>$g['gname']));
        $form->addElement('hidden', 'act');
        $form->addElement('hidden', 'reid');
        $form->addElement('hidden', 'gname');
        $s =& $form->createElement('select', 'login', 'Add Group: ');
        $s->loadArray($this->logins, NULL);
        $form->addElement($s);
        $form->addElement('submit', NULL, $this->tra('Do'));

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
    function getPermissions($id)
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
        $rowsBegin = ($formdata['counter'] ? $formdata['counter']-1 : UI_SEARCH_MIN_ROWS);
        $form = new HTML_QuickForm('search', UI_STANDARD_FORM_METHOD, UI_BROWSER);
        $form->setConstants(array('id'=>$id, 'counter'=>$rowsBegin+1));

        foreach ($mask['mData']['tabs']['group']['group'] as $k=>$v) {
            foreach ($mask['mData']['pages'][$v] as $val){
                $options[$val['element']] = $val['element'];
            };
        };

        for($n=1; $n<=UI_SEARCH_MAX_ROWS; $n++) {
            $advRow = array(
                array(
                    'element'   => 's1',
                    'type'      => 'static',
                    'text'      => "<div id='searchRow_$n'>"
                ),
                $n>$rowsBegin ? array(
                    'element'   => 's1_style',
                    'type'      => 'static',
                    'text'      => "<style type='text/css'>#searchRow_$n {visibility : hidden; height : 0px;}</style>"
                ) : NULL,
                array(
                    'element'   => 'searchBy['.$n.']',
                    'type'      => 'select',
                    'label'     => 'Search by',
                    'groupit'   => TRUE,
                    'options'   =>  $options,
                ),
                array(
                    'element'   => 'relation['.$n.']',
                    'type'      => 'select',
                    'groupit'   => TRUE,
                    'options'   => $mask['searchform.relations'],
                    'selected'  => '=',
                ),
                array(
                    'element'   => 'criteria['.$n.']',
                    'type'      => 'text',
                    'groupit'   => TRUE
                ),
                array(
                    'group'     => array('searchBy['.$n.']', 'relation['.$n.']', 'criteria['.$n.']'),
                    'name'      => NULL,
                    'label'     => NULL,
                    'seperator' => '&nbsp;&nbsp;',
                    'appendName'=> NULL
                ),
                $n>$rowsBegin ? array(
                    'element'   => 's2',
                    'type'      => 'static',
                    'text'      => "</div id='searchRow_$n'>"
                ) : NULL,
            );
            $this->_parseArr2Form($form, $advRow);
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
        foreach ($formdata['criteria'] as $key=>$val) {
            if (strlen($val)) {
                $critArr[] = array('cat' => $formdata['searchBy'][$key],
                                   'op'  => $formdata['relation'][$key],
                                   'val' => $val
                             );
            }
        }
        $searchCriteria = array('filetype'  => 'audioclip',
                                'operator'  => 'or',
                                'conditions'=> $critArr
                          );

        $results = $this->gb->localSearch($searchCriteria, $this->sessid);
        foreach ($results['results'] as $rec) {
                $res[] = array('gunid'  => $rec,
                               'par_id' => $this->gb->_idFromGunid($rec));
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
     *  Show file's metadata as XML
     *
     *  @param id int, local id of stored file
     *  @return array
     */
    function getMdata($id)
    {
        return($this->gb->getMdata($id, $this->sessid));
    }


    /**
     *  getMdataValue
     *
     *  Get Files Metadata Record
     *
     *  @param id int, local id of stored file
     *  @param cetagory string, metadata element name
     *  @return array
     */
    function _getMdataValue($id, $category)
    {
        return($this->gb->getMdataValue($id, $category, $this->sessid));
    }


    /**
     *  getMetaDataForm
     *
     *  create a form to edit Metadata
     *
     *  @param id int
     *  @return string (html)
     */
    function getMetadataForm($id, &$mask, $get=FALSE, $data=NULL)
    {
        $form = new HTML_QuickForm('tabs', UI_STANDARD_FORM_METHOD, UI_BROWSER);
        $this->_parseArr2Form($form, $mask['tabs']);
        $output['tabs'] = $form->toHTML();
        $form = new HTML_QuickForm('langswitch', UI_STANDARD_FORM_METHOD, UI_BROWSER);
        $this->_parseArr2Form($form, $mask['langswitch']);
        $output['langswitch'] = $form->toHTML();
        /*
        ## multiple forms
        $parts = array ('Main', 'Music_Basic', 'Music_Advanced', 'Talk_Basic', 'Talk_Advanced');
        foreach ($parts as $key) {
            unset ($form);
            $form = new HTML_QuickForm($key, UI_STANDARD_FORM_METHOD, UI_BROWSER);
            $this->_parseArr2Form($form, $mask['pages'][$key]);
            $this->_parseArr2Form($form, $mask['basics']);
            $output['pages'][$key] = $form->toHTML();
        }  */

        ## single form
        $form = new HTML_QuickForm('metadata', UI_STANDARD_FORM_METHOD, UI_HANDLER);
        $this->_parseArr2Form($form, $mask['basics']);
        $form->setConstants( array('id'     => $id,
                                  #!!!!!'langid' => array_pop($this->gb->getMDataValue($id, 'langid', $this->sessid))
                                  'langid'  => 'en'
                             )
                           );

        ## convert element names to be unique over different forms-parts, add javascript to spread values over parts, add existing values from database
        foreach ($mask['tabs']['group']['group'] as $key) {
            foreach ($mask['pages'][$key] as $k=>$v) {
                $mask['pages'][$key][$k]['element']    = $key.'-'.$v['element'];
                $mask['pages'][$key][$k]['attributes'] = array ('onChange' => "spread(this, '".$v['element']."')");

                ## recive data from GreenBox
                if ($get) {
                    $mask['pages'][$key][$k]['default'] = array_pop($this->gb->getMDataValue($id, strtr($v['element'], '_', '.'), $this->sessid));
                }

                ## get data from parameter
                if (is_array($data)) {
                    $mask['pages'][$key][$k]['default'] = $data[strtr($v['element'], '_', '.')];
                }
            }
            #$form->addElement('html', "<div id='div_$key'>");
            $form->addElement('static', NULL, NULL, "<div id='div_$key'>");
            $this->_parseArr2Form($form, $mask['pages'][$key]);
            $this->_parseArr2Form($form, $mask['buttons']);
            #$form->addElement('html', "</div id='div_$key'>");
            $form->addElement('static', NULL, NULL, "</div id='div_$key'>");
        }
        /*
        $renderer = new HTML_QuickForm_Renderer_Default;
        $renderer->setFormTemplate("\n<form{attributes}>\n<div>\n{content}\n</div>\n</form>");
        #$renderer->setElementTemplate("\n\t<div class='mdataelement'><div class='mdataelementtext'><!-- BEGIN required --><span style=\"color: #ff0000\">*</span><!-- END required --><b>{label}</b><!-- BEGIN error --><span style=\"color: #ff0000\">{error}</span><br /><!-- END error -->\t</div><div class='mdataelementelement'>{element}</div></div>\n\t");
        $renderer->setElementTemplate("\n\t<div class='mdataelement'><!-- BEGIN required --><span style=\"color: #ff0000\">*</span><!-- END required --><b>{label}</b><!-- BEGIN error --><span style=\"color: #ff0000\">{error}</span><br /><!-- END error -->\t<br>{element}</div>\n\t");
        $form->accept($renderer);
        $output['pages'][] = $renderer->toHTML();
        */

        ## using Dynamic Smarty Renderer
        $renderer =& new HTML_QuickForm_Renderer_Array(true, true);
        $form->accept($renderer);
        $output['pages'][] = $renderer->toArray();
        #print_r($output);

        return $output;
    }



    function systemPrefs(&$mask)
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