<?php

require_once 'formatters/LengthFormatter.php';
require_once 'formatters/SamplerateFormatter.php';
require_once 'formatters/BitrateFormatter.php';

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
        global $CC_CONFIG;

        $this->_helper->viewRenderer->setResponseSegment('library');

        $request = $this->getRequest();
        $baseUrl = $request->getBaseUrl();

        $this->view->headScript()->appendFile($baseUrl.'/js/contextmenu/jquery.contextMenu.js?'.$CC_CONFIG['airtime_version'],'text/javascript');
        $this->view->headScript()->appendFile($baseUrl.'/js/datatables/js/jquery.dataTables.js?'.$CC_CONFIG['airtime_version'],'text/javascript');
        $this->view->headScript()->appendFile($baseUrl.'/js/datatables/plugin/dataTables.pluginAPI.js?'.$CC_CONFIG['airtime_version'],'text/javascript');
        $this->view->headScript()->appendFile($baseUrl.'/js/datatables/plugin/dataTables.fnSetFilteringDelay.js?'.$CC_CONFIG['airtime_version'],'text/javascript');
        $this->view->headScript()->appendFile($baseUrl.'/js/datatables/plugin/dataTables.ColVis.js?'.$CC_CONFIG['airtime_version'],'text/javascript');
        $this->view->headScript()->appendFile($baseUrl.'/js/datatables/plugin/dataTables.ColReorder.js?'.$CC_CONFIG['airtime_version'],'text/javascript');
        $this->view->headScript()->appendFile($baseUrl.'/js/datatables/plugin/dataTables.FixedColumns.js?'.$CC_CONFIG['airtime_version'],'text/javascript');
        $this->view->headScript()->appendFile($baseUrl.'/js/datatables/plugin/dataTables.TableTools.js?'.$CC_CONFIG['airtime_version'],'text/javascript');

        $this->view->headScript()->appendFile($baseUrl.'/js/airtime/buttons/buttons.js?'.$CC_CONFIG['airtime_version'],'text/javascript');
        $this->view->headScript()->appendFile($baseUrl.'/js/airtime/library/library.js?'.$CC_CONFIG['airtime_version'],'text/javascript');
        $this->view->headScript()->appendFile($baseUrl.'/js/airtime/library/main_library.js?'.$CC_CONFIG['airtime_version'],'text/javascript');

        $this->view->headLink()->appendStylesheet($baseUrl.'/css/media_library.css?'.$CC_CONFIG['airtime_version']);
        $this->view->headLink()->appendStylesheet($baseUrl.'/css/jquery.contextMenu.css?'.$CC_CONFIG['airtime_version']);
        $this->view->headLink()->appendStylesheet($baseUrl.'/css/datatables/css/ColVis.css?'.$CC_CONFIG['airtime_version']);
        $this->view->headLink()->appendStylesheet($baseUrl.'/css/datatables/css/ColReorder.css?'.$CC_CONFIG['airtime_version']);
        $this->view->headLink()->appendStylesheet($baseUrl.'/css/TableTools.css?'.$CC_CONFIG['airtime_version']);
    }

    public function contextMenuAction()
    {
    	global $CC_CONFIG;

        $id = $this->_getParam('id');
        $type = $this->_getParam('type');
        //playlist||timeline
        $screen = $this->_getParam('screen');
        $request = $this->getRequest();
        $baseUrl = $request->getBaseUrl();
        $menu = array();

        $userInfo = Zend_Auth::getInstance()->getStorage()->read();
        $user = new Application_Model_User($userInfo->id);

        if ($type === "audioclip") {

            $file = Application_Model_StoredFile::Recall($id);

            if (isset($this->pl_sess->id) && $screen == "playlist") {
                $menu["pl_add"] = array("name"=> "Add to Playlist", "icon" => "copy");
            }

            $menu["edit"] = array("name"=> "Edit Metadata", "icon" => "edit", "url" => "/library/edit-file-md/id/{$id}");
	    
	    if ($user->isAdmin()) {
                $menu["del"] = array("name"=> "Delete", "icon" => "delete", "url" => "/library/delete");
            }

	        $url = $file->getRelativeFileUrl($baseUrl).'/download/true';
	        $menu["download"] = array("name" => "Download", "url" => $url);

            if (Application_Model_Preference::GetUploadToSoundcloudOption()) {

                //create a menu separator
                $menu["sep1"] = "-----------";

                //create a sub menu for Soundcloud actions.
                $menu["soundcloud"] = array("name" => "Soundcloud", "icon" => "soundcloud", "items" => array());

                $scid = $file->getSoundCloudId();

                if (!is_null($scid)){
                    $text = "Re-upload to SoundCloud";
                }
                else {
                    $text = "Upload to SoundCloud";
                }

                $menu["soundcloud"]["items"]["upload"] = array("name" => $text, "url" => "/library/upload-file-soundcloud/id/{$id}");

                if ($scid > 0){
                    $url = $file->getSoundCloudLinkToFile();
                    $menu["soundcloud"]["items"]["view"] = array("name" => "View on Soundcloud", "url" => $url);
                }
            }
        }
        else if ($type === "playlist") {

            if ($this->pl_sess->id !== $id && $screen == "playlist") {
                $menu["edit"] = array("name"=> "Edit", "icon" => "edit");
            }

            $menu["del"] = array("name"=> "Delete", "icon" => "delete", "url" => "/library/delete");
        }

        $this->view->items = $menu;
    }

    public function deleteAction()
    {
        //array containing id and type of media to delete.
        $mediaItems = $this->_getParam('media', null);

        $user = Application_Model_User::GetCurrentUser();

        $files = array();
        $playlists = array();

        $message = null;

        foreach ($mediaItems as $media) {

            if ($media["type"] === "audioclip") {
                $files[] = intval($media["id"]);
            }
            else if ($media["type"] === "playlist") {
                $playlists[] = intval($media["id"]);
            }
        }

        if (count($playlists)) {
            Application_Model_Playlist::DeletePlaylists($playlists);
        }

        if (!$user->isAdmin()) {
            return;
        }

        foreach ($files as $id) {
            Logging::log("deleting file {$id}");

            $file = Application_Model_StoredFile::Recall($id);

            if (isset($file)) {
                try {
                    $res = $file->delete(true);
                }
                //could throw a scheduled in future exception.
                catch (Exception $e) {
                    $message = "Could not delete some scheduled files.";
                }
            }
        }

        if (isset($message)) {
            $this->view->message = $message;
        }
    }

    public function contentsAction()
    {
        $params = $this->getRequest()->getParams();
        $datatables = Application_Model_StoredFile::searchFilesForPlaylistBuilder($params);

        //TODO move this to the datatables row callback.
        foreach ($datatables["aaData"] as &$data) {

            if ($data['ftype'] == 'audioclip'){
                $file = Application_Model_StoredFile::Recall($data['id']);
                $scid = $file->getSoundCloudId();

                if ($scid == "-2"){
                    $data['track_title'] .= '<span class="small-icon progress"/>';
                }
                else if ($scid == "-3"){
                    $data['track_title'] .= '<span class="small-icon sc-error"/>';
                }
                else if (!is_null($scid)){
                    $data['track_title'] .= '<span class="small-icon soundcloud"/>';
                }
            }
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

        try {
            if ($type == "audioclip") {
                $file = Application_Model_StoredFile::Recall($id);
                $this->view->type = $type;
                $md = $file->getMetadata();

                $formatter = new SamplerateFormatter($md["MDATA_KEY_SAMPLERATE"]);
                $md["MDATA_KEY_SAMPLERATE"] = $formatter->format();

                $formatter = new BitrateFormatter($md["MDATA_KEY_BITRATE"]);
                $md["MDATA_KEY_BITRATE"] = $formatter->format();

                $formatter = new LengthFormatter($md["MDATA_KEY_DURATION"]);
                $md["MDATA_KEY_DURATION"] = $formatter->format();

                $this->view->md = $md;

            }
            else if ($type == "playlist") {

                $file = new Application_Model_Playlist($id);
                $this->view->type = $type;
                $md = $file->getAllPLMetaData();

                $formatter = new LengthFormatter($md["dcterms:extent"]);
                $md["dcterms:extent"] = $formatter->format();

                $this->view->md = $md;
                $this->view->contents = $file->getContents();
            }
        }
        catch (Exception $e) {
            Logging::log($e->getMessage());
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

        if ($type == "show") {
            $show_instance = new Application_Model_ShowInstance($id);
            $this->view->sc_id = $show_instance->getSoundCloudFileId();
            $file = $show_instance->getRecordedFile();
            $this->view->error_code = $file->getSoundCloudErrorCode();
            $this->view->error_msg = $file->getSoundCloudErrorMsg();
        }
        else if ($type == "file") {
            $file = Application_Model_StoredFile::Recall($id);
            $this->view->sc_id = $file->getSoundCloudId();
            $this->view->error_code = $file->getSoundCloudErrorCode();
            $this->view->error_msg = $file->getSoundCloudErrorMsg();
        }
    }
}
