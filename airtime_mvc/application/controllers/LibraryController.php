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
                    ->addActionContext('context-menu', 'json')
                    ->addActionContext('get-file-meta-data', 'html')
                    ->initContext();

        $this->pl_sess = new Zend_Session_Namespace(UI_PLAYLIST_SESSNAME);
        $this->search_sess = new Zend_Session_Namespace("search");
    }

    public function indexAction()
    {
        $request = $this->getRequest();
        $baseUrl = $request->getBaseUrl();

        $this->view->headScript()->appendFile($baseUrl.'/js/contextmenu/jjmenu.js','text/javascript');
        $this->view->headScript()->appendFile($baseUrl.'/js/jplayer/jquery.jplayer.min.js');
        $this->view->headScript()->appendFile($baseUrl.'/js/datatables/js/jquery.dataTables.js','text/javascript');
        $this->view->headScript()->appendFile($baseUrl.'/js/datatables/plugin/dataTables.pluginAPI.js','text/javascript');
        $this->view->headScript()->appendFile($baseUrl.'/js/datatables/plugin/dataTables.fnSetFilteringDelay.js','text/javascript');
        $this->view->headScript()->appendFile($baseUrl.'/js/airtime/library/library.js','text/javascript');
        $this->view->headScript()->appendFile($baseUrl.'/js/airtime/library/advancedsearch.js','text/javascript');

        $this->view->headLink()->appendStylesheet($baseUrl.'/css/media_library.css');
        $this->view->headLink()->appendStylesheet($baseUrl.'/css/contextmenu.css');

        $this->_helper->layout->setLayout('library');
        $this->_helper->viewRenderer->setResponseSegment('library');

        $form = new Application_Form_AdvancedSearch();
        $form->addGroup(1, 1);

        $this->search_sess->next_group = 2;
        $this->search_sess->next_row[1] = 2;
        $this->view->form = $form;
        $this->view->md = $this->search_sess->md;

        $this->_helper->actionStack('index', 'playlist');
    }

    public function contextMenuAction()
    {
    	global $CC_CONFIG;

        $id = $this->_getParam('id');
        $type = $this->_getParam('type');

        $params = '/format/json/id/#id#/type/#type#';

        $paramsPop = str_replace('#id#', $id, $params);
        $paramsPop = str_replace('#type#', $type, $paramsPop);

        $pl_sess = $this->pl_sess;

        if($type === "au") {

            if(isset($pl_sess->id)) {
                $menu[] = array('action' => array('type' => 'ajax', 'url' => '/Playlist/add-item'.$params, 'callback' =>                      'window["setSPLContent"]'),
                    'title' => 'Add to Playlist');
            }

            $menu[] = array('action' => array('type' => 'gourl', 'url' => '/Library/edit-file-md/id/#id#'),
                            'title' => 'Edit Metadata');

            // added for downlaod
	    	$id = $this->_getParam('id');

	    	$file_id = $this->_getParam('id', null);
	        $file = StoredFile::Recall($file_id);

	        $url = $file->getRelativeFileUrl().'/api_key/'.$CC_CONFIG["apiKey"][0].'/download/true';
            $menu[] = array('action' => array('type' => 'gourl', 'url' => $url),
            				'title' => 'Download');

            $menu[] = array('action' => array('type' => 'fn',
                    'callback' => "window['confirmDeleteAudioClip']('$paramsPop')"),
                    'title' => 'Delete');
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

            //$menu[] = array('action' => array('type' => 'ajax', 'url' => '/Playlist/metadata/format/json/id/#id#', 'callback' => 'window["createPlaylistMetaForm"]'), 'title' => 'Edit Metadata');

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

        if (!is_null($id)) {
            $file = StoredFile::Recall($id);

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
        }

        $this->view->id = $id;
    }

    public function contentsAction()
    {
        $post = $this->getRequest()->getPost();
        $datatables = StoredFile::searchFilesForPlaylistBuilder($post);

        die(json_encode($datatables));
    }

    public function editFileMdAction()
    {
        $request = $this->getRequest();
        $form = new Application_Form_EditAudioMD();

        $file_id = $this->_getParam('id', null);
        $file = StoredFile::Recall($file_id);

        if ($request->isPost()) {
            if ($form->isValid($request->getPost())) {

                $formdata = $form->getValues();
                $file->setDbColMetadata($formdata);

                $data = $file->getMetadata();

                RabbitMq::SendMessageToMediaMonitor("md_update", $data);

                $this->_helper->redirector('index');
            }
        }

        $form->populate($file->getDbColMetadata());
        $this->view->form = $form;
    }

    public function getFileMetaDataAction()
    {
        $id = $this->_getParam('id');
        $type = $this->_getParam('type');

        if($type == "au") {
            $file = StoredFile::Recall($id);
            $this->view->type = $type;
            $this->view->md = $file->getMetadata();
        }
        else if($type == "pl") {
            $file = Playlist::Recall($id);
            $this->view->type = $type;
            $this->view->md = $file->getAllPLMetaData();
            $this->view->contents = $file->getContents();
        }

    }


}



















