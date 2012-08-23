<?php

class PlaylistController extends Zend_Controller_Action
{
    /*protected $pl_sess = null;
    protected $bl_sess = null;*/
    protected $obj_sess = null;

    public function init()
    {
        $ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext->addActionContext('add-items', 'json')
                    ->addActionContext('move-items', 'json')
                    ->addActionContext('delete-items', 'json')
                    ->addActionContext('set-fade', 'json')
                    ->addActionContext('set-cue', 'json')
                    ->addActionContext('new', 'json')
                    ->addActionContext('edit', 'json')
                    ->addActionContext('delete', 'json')
                    ->addActionContext('play', 'json')
                    ->addActionContext('set-playlist-fades', 'json')
                    ->addActionContext('get-playlist-fades', 'json')
                    ->addActionContext('set-playlist-name', 'json')
                    ->addActionContext('set-playlist-description', 'json')
                    ->addActionContext('playlist-preview', 'json')
                    ->addActionContext('get-playlist', 'json')
                    ->addActionContext('save', 'json')
                    ->addActionContext('smart-block-generate', 'json')
                    ->addActionContext('smart-block-shuffle', 'json')
                    ->addActionContext('get-block-info', 'json')
                    ->initContext();

        /*$this->pl_sess = new Zend_Session_Namespace(UI_PLAYLIST_SESSNAME);
        $this->bl_sess = new Zend_Session_Namespace(UI_BLOCK_SESSNAME);*/
        $this->obj_sess = new Zend_Session_Namespace(UI_PLAYLISTCONTROLLER_OBJ_SESSNAME);
    }

    private function getPlaylist($p_type)
    {
        $obj = null;
        
        $objInfo = $this->getObjInfo($p_type);

        if (isset($this->obj_sess->id)) {
            $obj = new $objInfo['className']($this->obj_sess->id);

            $modified = $this->_getParam('modified', null);
            if ($obj->getLastModified("U") !== $modified) {
                $this->createFullResponse($obj);
                throw new PlaylistOutDatedException("You are viewing an older version of {$obj->getName()}");
            }
        }

        return $obj;
    }

    private function changePlaylist($p_id, $p_type)
    {
        if (is_null($p_id) || is_null($p_type)) {
            unset($this->obj_sess->id);
            unset($this->obj_sess->type);
        } else {
            $this->obj_sess->id = intval($p_id);
            $this->obj_sess->type = $p_type;
        }
    }

    private function createUpdateResponse($obj)
    {
        $formatter = new LengthFormatter($obj->getLength());
        $this->view->length = $formatter->format();

        $this->view->obj = $obj;
        $this->view->contents = $obj->getContents();
        $this->view->html = $this->view->render('playlist/update.phtml');
        $this->view->name = $obj->getName();
        $this->view->description = $obj->getDescription();
        $this->view->modified = $obj->getLastModified("U");

        unset($this->view->obj);
    }

    private function createFullResponse($obj = null, $isJson = false)
    {
        $isBlock = false;
        $viewPath = 'playlist/playlist.phtml';
        if ($obj instanceof Application_Model_Block) {
            $isBlock = true;
            $viewPath = 'playlist/smart-block.phtml';
        }
        
        if (isset($obj)) {
            $formatter = new LengthFormatter($obj->getLength());
            $this->view->length = $formatter->format();
            
            if ($isBlock) {
                $form = new Application_Form_SmartBlockCriteria();
                $form->removeDecorator('DtDdWrapper');
                $form->startForm($obj->getId());
                
                $this->view->form = $form;
                $this->view->obj = $obj;
                $this->view->id = $obj->getId();
                if ($isJson) {
                    return $this->view->render($viewPath);
                } else {
                    $this->view->html = $this->view->render($viewPath);
                }
            }else{
                $this->view->obj = $obj;
                $this->view->id = $obj->getId();
                $this->view->html = $this->view->render($viewPath);
                unset($this->view->obj);
            }
        }
        else {
            $this->view->html = $this->view->render($viewPath);
        }
    }

    private function playlistOutdated($e)
    {
        $this->view->error = $e->getMessage();
    }

    private function blockDynamic($obj)
    {
        $this->view->error = "You cannot add tracks to dynamic blocks.";
        $this->createFullResponse($obj);
    }
    
    private function playlistNotFound($p_type)
    {
        $this->view->error = "{$p_type} not found";

        Logging::log("{$p_type} not found");
        $this->changePlaylist(null, $p_type);
        $this->createFullResponse(null);
    }
    
    private function playlistNoPermission($p_type){
        $this->view->error = "You don't have permission to delete selected {$p_type}(s).";
    }

    private function playlistUnknownError($e)
    {
        $this->view->error = "Something went wrong.";

        Logging::log("{$e->getFile()}");
        Logging::log("{$e->getLine()}");
        Logging::log("{$e->getMessage()}");
    }
    
    private function wrongTypeToBlock($obj) {
        $this->view->error = "You can only add tracks to smart block.";
        $this->createFullResponse($obj);
    }
    
    private function wrongTypeToPlaylist($obj) {
        $this->view->error = "You can only add tracks and smart blocks to playlists.";
        $this->createFullResponse($obj);
    }

    public function indexAction()
    {
        global $CC_CONFIG;

        $request = $this->getRequest();
        $baseUrl = $request->getBaseUrl();

        $this->view->headScript()->appendFile($baseUrl.'/js/blockui/jquery.blockUI.js?'.$CC_CONFIG['airtime_version'],'text/javascript');
        $this->view->headScript()->appendFile($baseUrl.'/js/contextmenu/jquery.contextMenu.js?'.$CC_CONFIG['airtime_version'],'text/javascript');
        $this->view->headScript()->appendFile($baseUrl.'/js/datatables/js/jquery.dataTables.js?'.$CC_CONFIG['airtime_version'],'text/javascript');
        $this->view->headScript()->appendFile($baseUrl.'/js/datatables/plugin/dataTables.pluginAPI.js?'.$CC_CONFIG['airtime_version'],'text/javascript');
        $this->view->headScript()->appendFile($baseUrl.'/js/datatables/plugin/dataTables.fnSetFilteringDelay.js?'.$CC_CONFIG['airtime_version'],'text/javascript');
        $this->view->headScript()->appendFile($baseUrl.'/js/datatables/plugin/dataTables.ColVis.js?'.$CC_CONFIG['airtime_version'],'text/javascript');
        $this->view->headScript()->appendFile($baseUrl.'/js/datatables/plugin/dataTables.ColReorder.js?'.$CC_CONFIG['airtime_version'],'text/javascript');
        $this->view->headScript()->appendFile($baseUrl.'/js/datatables/plugin/dataTables.FixedColumns.js?'.$CC_CONFIG['airtime_version'],'text/javascript');
        $this->view->headScript()->appendFile($baseUrl.'/js/datatables/plugin/dataTables.columnFilter.js?'.$CC_CONFIG['airtime_version'],'text/javascript');

        $this->view->headScript()->appendFile($baseUrl.'/js/airtime/buttons/buttons.js?'.$CC_CONFIG['airtime_version'],'text/javascript');
        $this->view->headScript()->appendFile($baseUrl.'/js/airtime/utilities/utilities.js?'.$CC_CONFIG['airtime_version'],'text/javascript');
        $this->view->headScript()->appendFile($baseUrl.'/js/airtime/library/library.js?'.$CC_CONFIG['airtime_version'],'text/javascript');
        $this->view->headScript()->appendFile($this->view->baseUrl('/js/airtime/library/events/library_playlistbuilder.js'),'text/javascript');

        $this->view->headLink()->appendStylesheet($baseUrl.'/css/media_library.css?'.$CC_CONFIG['airtime_version']);
        $this->view->headLink()->appendStylesheet($baseUrl.'/css/jquery.contextMenu.css?'.$CC_CONFIG['airtime_version']);
        $this->view->headLink()->appendStylesheet($baseUrl.'/css/datatables/css/ColVis.css?'.$CC_CONFIG['airtime_version']);
        $this->view->headLink()->appendStylesheet($baseUrl.'/css/datatables/css/ColReorder.css?'.$CC_CONFIG['airtime_version']);

        $this->view->headScript()->appendFile($baseUrl.'/js/airtime/library/spl.js?'.$CC_CONFIG['airtime_version'],'text/javascript');
        $this->view->headScript()->appendFile($baseUrl.'/js/airtime/playlist/smart_blockbuilder.js?'.$CC_CONFIG['airtime_version'],'text/javascript');
        $this->view->headLink()->appendStylesheet($baseUrl.'/css/playlist_builder.css?'.$CC_CONFIG['airtime_version']);

        try {
            if (isset($this->obj_sess->id)) {
                $objInfo = $this->getObjInfo($this->obj_sess->type);
                $obj = new $objInfo['className']($this->obj_sess->id);
                $userInfo = Zend_Auth::getInstance()->getStorage()->read();
                $user = new Application_Model_User($userInfo->id);
                $isAdminOrPM = $user->isUserType(array(UTYPE_ADMIN, UTYPE_PROGRAM_MANAGER));
                
                if($isAdminOrPM || $obj->getCreatorId() == $userInfo->id){
                    $this->view->obj = $obj;
                    if($this->obj_sess->type == "block"){
                        $form = new Application_Form_SmartBlockCriteria();
                        $form->startForm($this->obj_sess->id);
                        $this->view->form = $form;
                    }
                }
                
                $formatter = new LengthFormatter($obj->getLength());
                $this->view->length = $formatter->format();
                $this->view->type = $this->obj_sess->type;
            }
        } catch (PlaylistNotFoundException $e) {
            $this->playlistNotFound($this->obj_sess->type);
        } catch (Exception $e) {
            $this->playlistUnknownError($e);
        }
    }

    public function newAction()
    {
        //$pl_sess = $this->pl_sess;
        $userInfo = Zend_Auth::getInstance()->getStorage()->read();
        $type = $this->_getParam('type');
        
        $objInfo = $this->getObjInfo($type);
        
        $name = 'Untitled Playlist';
        if ($type == 'block') {
            $name = 'Untitled Smart Block';
        }

        $obj = new $objInfo['className']();
        $obj->setName($name);
        $obj->setMetaData('dc:creator', $userInfo->id);

        $this->changePlaylist($obj->getId(), $type);
        $this->createFullResponse($obj);
    }
    
    public function editAction()
    {
        $id = $this->_getParam('id', null);
        $type = $this->_getParam('type');
        $objInfo = $this->getObjInfo($type);
        Logging::log("editing {$type} {$id}");

        if (!is_null($id)) {
            $this->changePlaylist($id, $type);
        }

        try {
            $obj = new $objInfo['className']($id);
            $this->createFullResponse($obj);
        } catch (PlaylistNotFoundException $e) {
            $this->playlistNotFound();
        } catch (Exception $e) {
            $this->playlistUnknownError($e);
        }
    }

    public function deleteAction()
    {
        $ids = $this->_getParam('ids');
        $ids = (!is_array($ids)) ? array($ids) : $ids;
        $type = $this->_getParam('type');
        
        $obj = null;
     
        $objInfo = $this->getObjInfo($type);
        
        $userInfo = Zend_Auth::getInstance()->getStorage()->read();
        $user = new Application_Model_User($userInfo->id);

        try {

            Logging::log("Currently active {$type} {$this->obj_sess->id}");
            if (in_array($this->obj_sess->id, $ids)) {
                Logging::log("Deleting currently active {$type}");
                $this->changePlaylist(null, $type);
            } else {
                Logging::log("Not deleting currently active {$type}");
                $obj = new $objInfo['className']($this->obj_sess->id);
            }
            if (strcmp($objInfo['className'], 'Application_Model_Playlist')==0) {
                Application_Model_Playlist::deletePlaylists($ids, $userInfo->id);
            } else {
                Application_Model_Block::deleteBlocks($ids, $userInfo->id);
            }
            $this->createFullResponse($obj);
        }
        catch (PlaylistNoPermissionException $e) {
            $this->playlistNoPermission($type);
        }
        catch (PlaylistNotFoundException $e) {
            $this->playlistNotFound($type);
        } catch (Exception $e) {
            $this->playlistUnknownError($e);
        }
    }

    public function addItemsAction()
    {
        $ids = $this->_getParam('aItems', array());
        $ids = (!is_array($ids)) ? array($ids) : $ids;
        $afterItem = $this->_getParam('afterItem', null);
        $addType = $this->_getParam('type', 'after');
        // this is the obj type of destination
        $obj_type = $this->_getParam('obj_type');
        
        try {
            $obj = $this->getPlaylist($obj_type);
            if ($obj_type == 'playlist') {
                foreach($ids as $id) {
                    if (is_array($id) && isset($id[1])) {
                        if ($id[1] == 'playlist') {
                            throw new WrongTypeToPlaylistException;
                        }
                    }
                }
                $obj->addAudioClips($ids, $afterItem, $addType);
            } else if ($obj->isStatic()) {
                // if the dest is a block object
                //check if any items are playlists
                foreach($ids as $id) {
                    if (is_array($id) && isset($id[1])) {
                        if ($id[1] != 'audioclip') {
                            throw new WrongTypeToBlockException;
                        }
                    }
                }
                $obj->addAudioClips($ids, $afterItem, $addType);
            } else {
                throw new BlockDynamicException;
            }
            $this->createUpdateResponse($obj);
        }
        catch (PlaylistOutDatedException $e) {
            $this->playlistOutdated($e);
        }
        catch (PlaylistNotFoundException $e) {
            $this->playlistNotFound($obj_type);
        }
        catch (WrongTypeToBlockException $e) {
            $this->wrongTypeToBlock($obj);
        }
        catch (WrongTypeToPlaylistException $e) {
            $this->wrongTypeToPlaylist($obj);
        }
        catch (BlockDynamicException $e) {
            $this->blockDynamic($obj);
        }
        catch (Exception $e) {
            $this->playlistUnknownError($e);
        }
    }

    public function moveItemsAction()
    {
        $ids = $this->_getParam('ids');
        $ids = (!is_array($ids)) ? array($ids) : $ids;
        $afterItem = $this->_getParam('afterItem', null);
        $modified = $this->_getParam('modified');
        $type = $this->_getParam('obj_type');

        try {
            $obj = $this->getPlaylist($type);
            $obj->moveAudioClips($ids, $afterItem);
            $this->createUpdateResponse($obj);
        } catch (PlaylistOutDatedException $e) {
            $this->playlistOutdated($e);
        } catch (PlaylistNotFoundException $e) {
            $this->playlistNotFound($type);
        } catch (Exception $e) {
            $this->playlistUnknownError($e);
        }
    }

    public function deleteItemsAction()
    {
        $ids = $this->_getParam('ids');
        $ids = (!is_array($ids)) ? array($ids) : $ids;
        $modified = $this->_getParam('modified');
        $type = $this->_getParam('obj_type');

        try {
            $obj = $this->getPlaylist($type);
            $obj->delAudioClips($ids);
            $this->createUpdateResponse($obj);
        } catch (PlaylistOutDatedException $e) {
            $this->playlistOutdated($e);
        } catch (PlaylistNotFoundException $e) {
            $this->playlistNotFound($type);
        } catch (Exception $e) {
            $this->playlistUnknownError($e);
        }
    }

    public function setCueAction()
    {
        $id = $this->_getParam('id');
        $cueIn = $this->_getParam('cueIn', null);
        $cueOut = $this->_getParam('cueOut', null);
        $type = $this->_getParam('type');

        try {
            $obj = $this->getPlaylist($type);
            $response = $obj->changeClipLength($id, $cueIn, $cueOut);

            if (!isset($response["error"])) {
                $this->view->response = $response;
                $this->createUpdateResponse($obj);
            } else {
                $this->view->cue_error = $response["error"];
            }
        } catch (PlaylistOutDatedException $e) {
            $this->playlistOutdated($e);
        } catch (PlaylistNotFoundException $e) {
            $this->playlistNotFound($type);
        } catch (Exception $e) {
            $this->playlistUnknownError($e);
        }
    }

    public function setFadeAction()
    {
        $id = $this->_getParam('id');
        $fadeIn = $this->_getParam('fadeIn', null);
        $fadeOut = $this->_getParam('fadeOut', null);
        $type = $this->_getParam('type');

        try {
            $obj = $this->getPlaylist($type);
            $response = $obj->changeFadeInfo($id, $fadeIn, $fadeOut);

            if (!isset($response["error"])) {
                $this->createUpdateResponse($obj);
                $this->view->response = $response;
            } else {
                $this->view->fade_error = $response["error"];
            }
        } catch (PlaylistOutDatedException $e) {
            $this->playlistOutdated($e);
        } catch (PlaylistNotFoundException $e) {
            $this->playlistNotFound($type);
        } catch (Exception $e) {
            $this->playlistUnknownError($e);
        }
    }

    public function getPlaylistFadesAction()
    {
        $type = $this->_getParam('type');
        try {
            $obj = $this->getPlaylist($type);
            $fades = $obj->getFadeInfo(0);
            $this->view->fadeIn = $fades[0];

            $fades = $obj->getFadeInfo($obj->getSize()-1);
            $this->view->fadeOut = $fades[1];
        } catch (PlaylistOutDatedException $e) {
            $this->playlistOutdated($e);
        } catch (PlaylistNotFoundException $e) {
            $this->playlistNotFound($type);
        } catch (Exception $e) {
            $this->playlistUnknownError($e);
        }
    }

    /**
     * The playlist fades are stored in the elements themselves.
     * The fade in is set to the first elements fade in and
     * the fade out is set to the last elements fade out.
     **/
    public function setPlaylistFadesAction()
    {
        $fadeIn = $this->_getParam('fadeIn', null);
        $fadeOut = $this->_getParam('fadeOut', null);
        $type = $this->_getParam('type');

        try {
            $obj = $this->getPlaylist($type);
            $obj->setfades($fadeIn, $fadeOut);
            $this->view->modified = $obj->getLastModified("U");
        } catch (PlaylistOutDatedException $e) {
            $this->playlistOutdated($e);
        } catch (PlaylistNotFoundException $e) {
            $this->playlistNotFound($type);
        } catch (Exception $e) {
            $this->playlistUnknownError($e);
        }
    }

    public function setPlaylistNameDescAction()
    {
        $name = $this->_getParam('name', 'Unknown Playlist');
        $description = $this->_getParam('description', "");
        $type = $this->_getParam('type');
        
        try {
            $obj = $this->getPlaylist($type);
            $obj->setName($name);
            $obj->setDescription($description);
            $this->view->description = $description;
            $this->view->playlistName = $name;
            $this->view->modified = $obj->getLastModified("U");
        } catch (PlaylistOutDatedException $e) {
            $this->playlistOutdated($e);
        } catch (PlaylistNotFoundException $e) {
            $this->playlistNotFound($type);
        } catch (Exception $e) {
            $this->playlistUnknownError($e);
        }
    }
    
    public function saveAction()
    {
        $request = $this->getRequest();
        $params = $request->getPost();
        $result = array();
        
        $this->setPlaylistNameDescAction();
        
        if ($params['type'] == 'block') {
            $form = new Application_Form_SmartBlockCriteria();
            $form->startForm($params['obj_id']);
            $bl = new Application_Model_Block($params['obj_id']);
            if ($form->isValid($params)) {
                $bl->saveSmartBlockCriteria($params['data']);
                $result['html'] = $this->createFullResponse($bl, true);
                $result['result'] = 0;
            } else {
                $this->view->obj = $bl;
                $this->view->id = $bl->getId();
                $this->view->form = $form;
                $viewPath = 'playlist/smart-block.phtml';
                $result['html'] = $this->view->render($viewPath);
                $result['result'] = 1;
            }
        }
        
        $result["modified"] = $this->view->modified;
        die(json_encode($result));
    }
    
    public function smartBlockGenerateAction()
    {
        $request = $this->getRequest();
        $params = $request->getPost();
        $form = new Application_Form_SmartBlockCriteria();
        $form->startForm($params['obj_id']);
        $bl = new Application_Model_Block($params['obj_id']);
        if ($form->isValid($params)) {
            $result = $bl->generateSmartBlock($params['data']);
            try {
                die(json_encode(array("result"=>0, "html"=>$this->createFullResponse($bl, true))));
            }
            catch (PlaylistNotFoundException $e) {
                $this->playlistNotFound('block');
            }
            catch (Exception $e) {
                $this->playlistUnknownError($e);
            }
        }else{
            $this->view->obj = $bl;
            $this->view->id = $bl->getId();
            $this->view->form = $form;
            $viewPath = 'playlist/smart-block.phtml';
            $result['html'] = $this->view->render($viewPath);
            $result['result'] = 1;
            die(json_encode($result));
        }
    }
    
    public function smartBlockShuffleAction()
    {
        $request = $this->getRequest();
        $params = $request->getPost();
        $bl = new Application_Model_Block($params['obj_id']);
        $result = $bl->shuffleSmartBlock();
        
        if ($result['result'] == 0) {
            try {
                die(json_encode(array("result"=>0, "html"=>$this->createFullResponse($bl, true))));
            }
            catch (PlaylistNotFoundException $e) {
                $this->playlistNotFound('block');
            }
            catch (Exception $e) {
                $this->playlistUnknownError($e);
            }
        }else{
            die(json_encode($result));
        }
    }
    
    public function getObjInfo($p_type)
    {
        $info = array();
        
        if (strcmp($p_type, 'playlist')==0) {
            $info['className'] = 'Application_Model_Playlist';
        } else {
            $info['className'] = 'Application_Model_Block';
        }
        
        return $info;
    }
    
    public function getBlockInfoAction(){
        $request = $this->getRequest();
        $params = $request->getPost();
        $bl = new Application_Model_Block($params['id']);
        if ($bl->isStatic()) {
            $out = $bl->getContents();
            $out['isStatic'] = true;
        } else {
            $out = $bl->getCriteria();
            $out['isStatic'] = false;
        }
        die(json_encode($out));
    }
}
class WrongTypeToBlockException extends Exception {}
class WrongTypeToPlaylistException extends Exception {}
class BlockDynamicException extends Exception {}
