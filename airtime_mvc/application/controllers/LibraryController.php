<?php

class LibraryController extends Zend_Controller_Action
{

    protected $pl_sess = null;
    protected $search_sess = null;

    public function init()
    {
        $ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext->addActionContext('contents', 'json')
                    ->addActionContext('delete', 'json')
                    ->addActionContext('delete-group', 'json')
                    ->addActionContext('context-menu', 'json')
                    ->addActionContext('get-file-meta-data', 'html')
                    ->addActionContext('upload-file-soundcloud', 'json')
                    ->addActionContext('get-upload-to-soundcloud-status', 'json')
                    ->addActionContext('set-num-entries', 'json')
                    ->initContext();

        $this->pl_sess = new Zend_Session_Namespace(UI_PLAYLIST_SESSNAME);
        $this->search_sess = new Zend_Session_Namespace("search");
    }

    public function indexAction() {

        $this->_helper->layout->setLayout('library');

        $this->view->headScript()->appendFile($this->view->baseUrl('/js/airtime/library/events/library_playlistbuilder.js'),'text/javascript');

        $this->_helper->actionStack('library', 'library');
        $this->_helper->actionStack('index', 'playlist');
    }

    public function libraryAction()
    {
        $request = $this->getRequest();
        $baseUrl = $request->getBaseUrl();

        $this->view->headScript()->appendFile($baseUrl.'/js/contextmenu/jjmenu.js','text/javascript');
        $this->view->headScript()->appendFile($baseUrl.'/js/datatables/js/jquery.dataTables.js','text/javascript');
        $this->view->headScript()->appendFile($baseUrl.'/js/datatables/plugin/dataTables.pluginAPI.js','text/javascript');
        $this->view->headScript()->appendFile($baseUrl.'/js/datatables/plugin/dataTables.fnSetFilteringDelay.js','text/javascript');
        $this->view->headScript()->appendFile($baseUrl.'/js/datatables/plugin/dataTables.ColVis.js','text/javascript');
        $this->view->headScript()->appendFile($baseUrl.'/js/datatables/plugin/dataTables.ColReorderResize.js','text/javascript');
        $this->view->headScript()->appendFile($baseUrl.'/js/datatables/plugin/dataTables.FixedColumns.js','text/javascript');
        $this->view->headScript()->appendFile($baseUrl.'/js/airtime/library/library.js','text/javascript');
        $this->view->headScript()->appendFile($baseUrl.'/js/airtime/library/advancedsearch.js','text/javascript');

        $this->view->headLink()->appendStylesheet($baseUrl.'/css/media_library.css');
        $this->view->headLink()->appendStylesheet($baseUrl.'/css/contextmenu.css');
        $this->view->headLink()->appendStylesheet($baseUrl.'/css/datatables/css/ColVis.css');
        $this->view->headLink()->appendStylesheet($baseUrl.'/css/datatables/css/ColReorder.css');

        $this->_helper->viewRenderer->setResponseSegment('library');

        $form = new Application_Form_AdvancedSearch();
        $form->addGroup(1, 1);

        $this->search_sess->next_group = 2;
        $this->search_sess->next_row[1] = 2;
        $this->view->form = $form;
        $this->view->md = $this->search_sess->md;
    }

    public function contextMenuAction()
    {
    	global $CC_CONFIG;

        $id = $this->_getParam('id');
        $type = $this->_getParam('type');
        $request = $this->getRequest();
        $baseUrl = $request->getBaseUrl();

        $params = '/format/json/id/#id#/type/#type#';

        $paramsPop = str_replace('#id#', $id, $params);
        $paramsPop = str_replace('#type#', $type, $paramsPop);

        $userInfo = Zend_Auth::getInstance()->getStorage()->read();
        $user = new Application_Model_User($userInfo->id);

        $pl_sess = $this->pl_sess;

        if($type === "au") {

            if(isset($pl_sess->id)) {
                $menu[] = array('action' => array('type' => 'ajax', 'url' => '/Playlist/add-item'.$params, 'callback' => 'window["setSPLContent"]'),
                    'title' => 'Add to Playlist');
            }

            $menu[] = array('action' => array('type' => 'gourl', 'url' => '/Library/edit-file-md/id/#id#'),
                            'title' => 'Edit Metadata');

            // added for downlaod
	    	$id = $this->_getParam('id');

	    	$file_id = $this->_getParam('id', null);
	        $file = Application_Model_StoredFile::Recall($file_id);

	        $url = $file->getRelativeFileUrl($baseUrl).'/download/true';
            $menu[] = array('action' => array('type' => 'gourl', 'url' => $url),
            				'title' => 'Download');

            if (Application_Model_Preference::GetUploadToSoundcloudOption()) {
                $text = "Upload to SoundCloud";
                if(!is_null($file->getSoundCloudId())){
                    $text = "Re-upload to SoundCloud";
                }
                $menu[] = array('action' => array('type' => 'ajax', 'url' => '/Library/upload-file-soundcloud/id/#id#',
                                'callback'=>"window['addProgressIcon']('$file_id')"),'title' => $text);

                $scid = $file->getSoundCloudId();

                if($scid > 0){
                    $link_to_file = $file->getSoundCloudLinkToFile();
                    $menu[] = array('action' => array('type' => 'fn',
                    	    'callback' => "window['openFileOnSoundCloud']('$link_to_file')"),
                                				'title' => 'View on SoundCloud');
                }
            }

            if ($user->isAdmin()) {
                $menu[] = array('action' => array('type' => 'fn',
                        'callback' => "window['confirmDeleteAudioClip']('$paramsPop')"),
                        'title' => 'Delete');
            }
        }
        else if($type === "pl") {

            if(!isset($pl_sess->id) || $pl_sess->id !== $id) {
                $menu[] = array('action' =>
                                    array('type' => 'ajax',
                                    'url' => '/Playlist/edit'.$params,
                                    'callback' => 'window["openDiffSPL"]'),
                                'title' => 'Edit');
            }
            else if(isset($pl_sess->id) && $pl_sess->id === $id) {
                $menu[] = array('action' =>
                                    array('type' => 'ajax',
                                    'url' => '/Playlist/close'.$params,
                                    'callback' => 'window["noOpenPL"]'),
                                'title' => 'Close');
            }

            $menu[] = array('action' => array('type' => 'fn',
                    'callback' => "window['confirmDeletePlaylist']('$paramsPop')"),
                    'title' => 'Delete');

        }

        //returns format jjmenu is looking for.
        die(json_encode($menu));
    }

    public function deleteAction()
    {
        $id = $this->_getParam('id');
        $userInfo = Zend_Auth::getInstance()->getStorage()->read();
        $user = new Application_Model_User($userInfo->id);

        if ($user->isAdmin()) {

            if (!is_null($id)) {
                $file = Application_Model_StoredFile::Recall($id);

                if (PEAR::isError($file)) {
                    $this->view->message = $file->getMessage();
                    return;
                }
                else if(is_null($file)) {
                    $this->view->message = "file doesn't exist";
                    return;
                }

                $res = $file->delete();

                if (PEAR::isError($res)) {
                    $this->view->message = $res->getMessage();
                    return;
                }
                else {
                    $res = settype($res, "integer");
                    $data = array("filepath" => $file->getFilePath(), "delete" => $res);
                    Application_Model_RabbitMq::SendMessageToMediaMonitor("file_delete", $data);
                }
            }

            $this->view->id = $id;
        }
    }

    public function deleteGroupAction()
    {
        $ids = $this->_getParam('ids');
        $userInfo = Zend_Auth::getInstance()->getStorage()->read();
        $user = new Application_Model_User($userInfo->id);

        if ($user->isAdmin()) {

            if (!is_null($ids)) {
                foreach ($ids as $key => $id) {
                    $file = Application_Model_StoredFile::Recall($id);

                    if (PEAR::isError($file)) {
                        $this->view->message = $file->getMessage();
                        return;
                    }
                    else if(is_null($file)) {
                        $this->view->message = "file doesn't exist";
                        return;
                    }

                    $res = $file->delete();

                    if (PEAR::isError($res)) {
                        $this->view->message = $res->getMessage();
                        return;
                    }
                    else {
                        $res = settype($res, "integer");
                        $data = array("filepath" => $file->getFilePath(), "delete" => $res);
                        Application_Model_RabbitMq::SendMessageToMediaMonitor("file_delete", $data);
                    }
                }

                $this->view->ids = $ids;
            }
        }
    }

    public function contentsAction()
    {
        $params = $this->getRequest()->getParams();
        $datatables = Application_Model_StoredFile::searchFilesForPlaylistBuilder($params);

        //format clip lengh to 1 decimal
        foreach($datatables["aaData"] as &$data){
            if($data['ftype'] == 'audioclip'){
                $file = Application_Model_StoredFile::Recall($data['id']);
                $scid = $file->getSoundCloudId();
                if($scid == "-2"){
                    $data['track_title'] .= '<span id="'.$data['id'].'" class="small-icon progress"></span>';
                }else if($scid == "-3"){
                    $data['track_title'] .= '<span id="'.$data['id'].'" class="small-icon sc-error"></span>';
                }else if(!is_null($scid)){
                    $data['track_title'] .= '<span id="'.$data['id'].'" class="small-icon soundcloud"></span>';
                }
            }
            $sec = Application_Model_Playlist::playlistTimeToSeconds($data['length']);
            $data['length'] = Application_Model_Playlist::secondsToPlaylistTime($sec);
        }

        die(json_encode($datatables));
    }

    public function editFileMdAction()
    {
        $request = $this->getRequest();
        $form = new Application_Form_EditAudioMD();

        $file_id = $this->_getParam('id', null);
        $file = Application_Model_StoredFile::Recall($file_id);
        $form->populate($file->getDbColMetadata());

        if ($request->isPost()) {
            if ($form->isValid($request->getPost())) {

                $formdata = $form->getValues();
                $file->setDbColMetadata($formdata);

                $data = $file->getMetadata();

                // set MDATA_KEY_FILEPATH
                $data['MDATA_KEY_FILEPATH'] = $file->getFilePath();
                Logging::log($data['MDATA_KEY_FILEPATH']);
                Application_Model_RabbitMq::SendMessageToMediaMonitor("md_update", $data);


                $this->_helper->redirector('index');
            }
        }

        $this->view->form = $form;
    }

    public function getFileMetaDataAction()
    {
        $id = $this->_getParam('id');
        $type = $this->_getParam('type');

        if($type == "au") {
            $file = Application_Model_StoredFile::Recall($id);
            $this->view->type = $type;
            $this->view->md = $file->getMetadata();
        }
        else if($type == "pl") {
            $file = Application_Model_Playlist::Recall($id);
            $this->view->type = $type;
            $this->view->md = $file->getAllPLMetaData();
            $this->view->contents = $file->getContents();
        }

    }

    public function uploadFileSoundcloudAction(){
        $id = $this->_getParam('id');
        $res = exec("/usr/lib/airtime/utils/soundcloud-uploader $id > /dev/null &");
        // we should die with ui info
        die();
    }

    public function getUploadToSoundcloudStatusAction(){
        $id = $this->_getParam('id');
        $type = $this->_getParam('type');
        if($type == "show"){
            $show_instance = new Application_Model_ShowInstance($id);
            $this->view->sc_id = $show_instance->getSoundCloudFileId();
            $file = $show_instance->getRecordedFile();
            $this->view->error_code = $file->getSoundCloudErrorCode();
            $this->view->error_msg = $file->getSoundCloudErrorMsg();
        }else{
            $file = Application_Model_StoredFile::Recall($id);
            $this->view->sc_id = $file->getSoundCloudId();
            $this->view->error_code = $file->getSoundCloudErrorCode();
            $this->view->error_msg = $file->getSoundCloudErrorMsg();
        }
    }

    /**
     * Stores the number of entries user chose to show in the Library
     * to the pref db
     */
    public function setNumEntriesAction() {
    	$request = $this->getRequest();
    	$numEntries = $request->getParam('numEntries');
    	Application_Model_Preference::SetLibraryNumEntries($numEntries);
    }
}
