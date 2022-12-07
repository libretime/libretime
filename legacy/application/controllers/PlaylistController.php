<?php

declare(strict_types=1);

class PlaylistController extends Zend_Controller_Action
{
    public function init()
    {
        $ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext->addActionContext('add-items', 'json')
            ->addActionContext('move-items', 'json')
            ->addActionContext('delete-items', 'json')
            ->addActionContext('set-fade', 'json')
            ->addActionContext('set-crossfade', 'json')
            ->addActionContext('set-cue', 'json')
            ->addActionContext('new', 'json')
            ->addActionContext('edit', 'json')
            ->addActionContext('delete', 'json')
            ->addActionContext('close-playlist', 'json')
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
            ->addActionContext('shuffle', 'json')
            ->addActionContext('empty-content', 'json')
            ->addActionContext('change-playlist', 'json')
            ->initContext();

        // This controller writes to the session all over the place, so we're going to reopen it for writing here.
        SessionHelper::reopenSessionForWriting();
    }

    private function getPlaylist($p_type)
    {
        $obj = null;
        $objInfo = Application_Model_Library::getObjInfo($p_type);

        $obj_sess = new Zend_Session_Namespace(UI_PLAYLISTCONTROLLER_OBJ_SESSNAME);

        if (isset($obj_sess->id)) {
            $obj = new $objInfo['className']($obj_sess->id);

            $modified = $this->_getParam('modified', null);
            if ($obj->getLastModified('U') !== $modified) {
                $this->createFullResponse($obj);

                throw new PlaylistOutDatedException(sprintf(_('You are viewing an older version of %s'), $obj->getName()));
            }
        }

        return $obj;
    }

    private function createUpdateResponse($obj, $formIsValid = false)
    {
        $formatter = new LengthFormatter($obj->getLength());
        $this->view->length = $formatter->format();

        $this->view->obj = $obj;
        $this->view->contents = $obj->getContents();
        if ($formIsValid && $obj instanceof Application_Model_Block) {
            $this->view->poolCount = $obj->getListofFilesMeetCriteria()['count'];
        }
        $this->view->showPoolCount = true;
        $this->view->html = $this->view->render('playlist/update.phtml');
        $this->view->name = $obj->getName();
        $this->view->description = $obj->getDescription();
        $this->view->modified = $obj->getLastModified('U');
        unset($this->view->obj);
    }

    private function createFullResponse(
        $obj = null,
        $isJson = false,
        $formIsValid = false
    ) {
        $user = Application_Model_User::getCurrentUser();
        $isAdminOrPM = $user->isUserType([UTYPE_SUPERADMIN, UTYPE_ADMIN, UTYPE_PROGRAM_MANAGER]);

        if (!$isAdminOrPM && $obj->getCreatorId() != $user->getId()) {
            $this->view->objType = $obj instanceof Application_Model_Block ? 'block' : 'playlist';
            $this->view->obj = $obj;
            $this->view->html = $this->view->render('playlist/permission-denied.phtml');

            return;
        }

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
                $form->startForm($obj->getId(), $formIsValid);
                $this->view->form = $form;
                $this->view->obj = $obj;
                // $this->view->type = "sb";
                $this->view->id = $obj->getId();

                if ($isJson) {
                    return $this->view->render($viewPath);
                }
                $this->view->html = $this->view->render($viewPath);
            } else {
                $this->view->obj = $obj;
                // $this->view->type = "pl";
                $this->view->id = $obj->getId();
                if ($isJson) {
                    return $this->view->html = $this->view->render($viewPath);
                }
                $this->view->html = $this->view->render($viewPath);

                unset($this->view->obj);
            }
        } else {
            if ($isJson) {
                return $this->view->render($viewPath);
            }
            $this->view->html = $this->view->render($viewPath);
        }
    }

    private function playlistOutdated($e)
    {
        $this->view->error = $e->getMessage();
    }

    private function blockDynamic($obj)
    {
        $this->view->error = _('You cannot add tracks to dynamic blocks.');
        $this->createFullResponse($obj);
    }

    private function playlistNotFound($p_type, $p_isJson = false)
    {
        $p_type = ucfirst($p_type);
        $this->view->error = sprintf(_('%s not found'), $p_type);

        Logging::info("{$p_type} not found");
        Application_Model_Library::changePlaylist(null, $p_type);

        if (!$p_isJson) {
            $this->createFullResponse(null);
        } else {
            $this->_helper->json->sendJson(['error' => $this->view->error, 'result' => 1, 'html' => $this->createFullResponse(null, $p_isJson)]);
        }
    }

    private function playlistNoPermission($p_type)
    {
        $this->view->error = sprintf(_("You don't have permission to delete selected %s(s)."), $p_type);
        $this->changePlaylist(null, $p_type);
        $this->createFullResponse(null);
    }

    private function playlistUnknownError($e)
    {
        $this->view->error = _('Something went wrong.');
        Logging::info($e->getMessage());
    }

    private function wrongTypeToBlock($obj)
    {
        $this->view->error = _('You can only add tracks to smart block.');
        $this->createFullResponse($obj);
    }

    private function wrongTypeToPlaylist($obj)
    {
        $this->view->error = _('You can only add tracks, smart blocks, and webstreams to playlists.');
        $this->createFullResponse($obj);
    }

    public function newAction()
    {
        // $pl_sess = $this->pl_sess;
        $userInfo = Zend_Auth::getInstance()->getStorage()->read();
        $type = $this->_getParam('type');

        $objInfo = Application_Model_Library::getObjInfo($type);

        $name = _('Untitled Playlist');
        if ($type == 'block') {
            $name = _('Untitled Smart Block');
        }

        $obj = new $objInfo['className']();
        $obj->setName($name);
        $obj->setMetadata('dc:creator', $userInfo->id);

        Application_Model_Library::changePlaylist($obj->getId(), $type);
        $this->createFullResponse($obj);
    }

    public function changePlaylistAction()
    {
        $this->view->layout()->disableLayout();  // Don't inject the standard Now Playing header.
        $this->_helper->viewRenderer->setNoRender(true);  // Don't use (phtml) templates

        $id = $this->_getParam('id', null);
        $type = $this->_getParam('type');

        Application_Model_Library::changePlaylist($id, $type);
    }

    public function editAction()
    {
        $id = $this->_getParam('id', null);
        $type = $this->_getParam('type');
        $objInfo = Application_Model_Library::getObjInfo($type);

        //        if (!is_null($id)) {
        Application_Model_Library::changePlaylist($id, $type);
        //        }

        try {
            $obj = new $objInfo['className']($id);
            $this->createFullResponse($obj);
        } catch (PlaylistNotFoundException $e) {
            $this->playlistNotFound($type);
        }
    }

    public function deleteAction()
    {
        $ids = $this->_getParam('ids');
        $ids = (!is_array($ids)) ? [$ids] : $ids;
        $type = $this->_getParam('type');

        $obj = null;

        $objInfo = Application_Model_Library::getObjInfo($type);

        $userInfo = Zend_Auth::getInstance()->getStorage()->read();

        $obj_sess = new Zend_Session_Namespace(
            UI_PLAYLISTCONTROLLER_OBJ_SESSNAME
        );

        try {
            Logging::info("Currently active {$type} {$obj_sess->id}");
            if (in_array($obj_sess->id, $ids)) {
                Logging::info("Deleting currently active {$type}");
            // Application_Model_Library::changePlaylist(null, $type);
            } else {
                Logging::info("Not deleting currently active {$type}");
                $obj = new $objInfo['className']($obj_sess->id);
            }

            if (strcmp($objInfo['className'], 'Application_Model_Playlist') == 0) {
                Application_Model_Playlist::deletePlaylists($ids, $userInfo->id);
            } else {
                Application_Model_Block::deleteBlocks($ids, $userInfo->id);
            }
            $this->createFullResponse($obj);
        } catch (PlaylistNoPermissionException $e) {
            $this->playlistNoPermission($type);
        } catch (BlockNoPermissionException $e) {
            $this->playlistNoPermission($type);
        } catch (PlaylistNotFoundException $e) {
            $this->playlistNotFound($type);
        } catch (Exception $e) {
            $this->playlistUnknownError($e);
        }
    }

    public function closePlaylistAction()
    {
        $type = $this->_getParam('type');
        $obj = null;
        Application_Model_Library::changePlaylist($obj, $type);
        $this->createFullResponse($obj);
    }

    public function addItemsAction()
    {
        $ids = $this->_getParam('aItems', []);
        $ids = (!is_array($ids)) ? [$ids] : $ids;
        $afterItem = $this->_getParam('afterItem', null);
        $addType = $this->_getParam('type', 'after');
        // this is the obj type of destination
        $obj_type = $this->_getParam('obj_type');

        try {
            $obj = $this->getPlaylist($obj_type);
            if ($obj_type == 'playlist') {
                foreach ($ids as $id) {
                    if (is_array($id) && isset($id[1])) {
                        if ($id[1] == 'playlist') {
                            throw new WrongTypeToPlaylistException();
                        }
                    }
                }
                $obj->addAudioClips($ids, $afterItem, $addType);
            } elseif ($obj->isStatic()) {
                // if the dest is a block object
                // check if any items are playlists
                foreach ($ids as $id) {
                    if (is_array($id) && isset($id[1])) {
                        if ($id[1] != 'audioclip') {
                            throw new WrongTypeToBlockException();
                        }
                    }
                }
                $obj->addAudioClips($ids, $afterItem, $addType);
            } else {
                throw new BlockDynamicException();
            }
            $this->createUpdateResponse($obj);
        } catch (PlaylistOutDatedException $e) {
            $this->playlistOutdated($e);
        } catch (PlaylistNotFoundException $e) {
            $this->playlistNotFound($obj_type);
        } catch (WrongTypeToBlockException $e) {
            $this->wrongTypeToBlock($obj);
        } catch (WrongTypeToPlaylistException $e) {
            $this->wrongTypeToPlaylist($obj);
        } catch (BlockDynamicException $e) {
            $this->blockDynamic($obj);
        } catch (BlockNotFoundException $e) {
            $this->playlistNotFound($obj_type);
        } catch (Exception $e) {
            $this->playlistUnknownError($e);
        }
    }

    public function moveItemsAction()
    {
        $ids = $this->_getParam('ids');
        $ids = (!is_array($ids)) ? [$ids] : $ids;
        $afterItem = $this->_getParam('afterItem', null);
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
        $ids = (!is_array($ids)) ? [$ids] : $ids;
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

    public function emptyContentAction()
    {
        $type = $this->_getParam('obj_type');

        try {
            $obj = $this->getPlaylist($type);
            if ($type == 'playlist') {
                $obj->deleteAllFilesFromPlaylist();
            } else {
                $obj->deleteAllFilesFromBlock();
            }
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

            if (!isset($response['error'])) {
                $this->view->response = $response;
                $this->createUpdateResponse($obj);
            } else {
                $this->view->cue_error = $response['error'];
                $this->view->code = $response['type'];
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

            if (!isset($response['error'])) {
                $this->createUpdateResponse($obj);
                $this->view->response = $response;
            } else {
                $this->view->fade_error = $response['error'];
            }
        } catch (PlaylistOutDatedException $e) {
            $this->playlistOutdated($e);
        } catch (PlaylistNotFoundException $e) {
            $this->playlistNotFound($type);
        } catch (Exception $e) {
            $this->playlistUnknownError($e);
        }
    }

    public function setCrossfadeAction()
    {
        $id1 = $this->_getParam('id1', null);
        $id2 = $this->_getParam('id2', null);
        $type = $this->_getParam('type');
        $fadeIn = $this->_getParam('fadeIn', 0);
        $fadeOut = $this->_getParam('fadeOut', 0);
        $offset = $this->_getParam('offset', 0);

        try {
            $obj = $this->getPlaylist($type);
            $response = $obj->createCrossfade($id1, $fadeOut, $id2, $fadeIn, $offset);

            if (!isset($response['error'])) {
                $this->createUpdateResponse($obj);
            } else {
                $this->view->error = $response['error'];
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

            $fades = $obj->getFadeInfo($obj->getSize() - 1);
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
     */
    public function setPlaylistFadesAction()
    {
        $fadeIn = $this->_getParam('fadeIn', null);
        $fadeOut = $this->_getParam('fadeOut', null);
        $type = $this->_getParam('type');

        try {
            $obj = $this->getPlaylist($type);
            $obj->setfades($fadeIn, $fadeOut);
            $this->view->modified = $obj->getLastModified('U');
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
        $name = $this->_getParam('name', _('Unknown Playlist'));
        $description = $this->_getParam('description', '');
        $type = $this->_getParam('type');

        try {
            $obj = $this->getPlaylist($type);
            $obj->setName(trim($name));
            $obj->setDescription($description);
            $this->view->description = $description;
            $this->view->playlistName = $name;
            $this->view->modified = $obj->getLastModified('U');
        } catch (PlaylistOutDatedException $e) {
            $this->playlistOutdated($e);
        } catch (PlaylistNotFoundException $e) {
            $this->playlistNotFound($type, true);
        } catch (Exception $e) {
            $this->playlistUnknownError($e);
        }
    }

    public function saveAction()
    {
        $request = $this->getRequest();
        $params = $request->getPost();
        $result = [];

        if ($params['type'] == 'block') {
            try {
                $bl = new Application_Model_Block($params['obj_id']);
            } catch (BlockNotFoundException $e) {
                $this->playlistNotFound('block', true);
            }
            $form = new Application_Form_SmartBlockCriteria();
            $form->startForm($params['obj_id']);
            if ($form->isValid($params)) {
                $this->setPlaylistNameDescAction();
                $bl->saveSmartBlockCriteria($params['data']);
                $this->createUpdateResponse($bl, true);
                $this->view->result = 0;
            // $result['html'] = $this->createFullResponse($bl, true, true);
            } else {
                $this->view->form = $form;
                $this->view->unsavedName = $params['name'];
                $this->view->unsavedDesc = $params['description'];
                $viewPath = 'playlist/smart-block.phtml';
                $this->view->obj = $bl;
                $this->view->id = $bl->getId();
                $this->view->html = $this->view->render($viewPath);
                $this->view->result = 1;
            }
            $this->view->name = $bl->getName();
            // $this->view->type = "sb";
            $this->view->id = $bl->getId();
            $this->view->modified = $bl->getLastModified('U');
        } elseif ($params['type'] == 'playlist') {
            $this->setPlaylistNameDescAction();
            $this->view->modified = $this->view->modified;
            $this->view->name = $params['name'];
        }

        // $this->_helper->json->sendJson($result);
    }

    public function smartBlockGenerateAction()
    {
        $request = $this->getRequest();
        $params = $request->getPost();

        // make sure block exists
        try {
            $bl = new Application_Model_Block($params['obj_id']);

            $form = new Application_Form_SmartBlockCriteria();
            $form->startForm($params['obj_id']);
            if ($form->isValid($params)) {
                $result = $bl->generateSmartBlock($params['data']);
                $this->view->result = $result['result'];
                $this->createUpdateResponse($bl, true);
            // $this->_helper->json->sendJson(array("result"=>0, "html"=>$this->createFullResponse($bl, true, true)));
            } else {
                $this->view->obj = $bl;
                $this->view->id = $bl->getId();
                $this->view->form = $form;
                $this->createFullResponse($bl, false, true);
            }
        } catch (BlockNotFoundException $e) {
            $this->playlistNotFound('block', true);
        } catch (Exception $e) {
            Logging::info($e);
            $this->playlistUnknownError($e);
        }
    }

    public function smartBlockShuffleAction()
    {
        $request = $this->getRequest();
        $params = $request->getPost();

        try {
            $bl = new Application_Model_Block($params['obj_id']);
            $result = $bl->shuffleSmartBlock();

            $this->view->result = $result['result'];
            $this->createUpdateResponse($bl, true);

            /*
            if ($result['result'] == 0) {
                $this->_helper->json->sendJson(array(
                    "result"=>0,
                    "contents" => $bl->getContents());
                    ///"html"=>$this->viwe));

            } else {
                $this->_helper->json->sendJson($result);
            }*/
        } catch (BlockNotFoundException $e) {
            $this->playlistNotFound('block', true);
        } catch (Exception $e) {
            $this->playlistUnknownError($e);
        }
    }

    public function shuffleAction()
    {
        $request = $this->getRequest();
        $params = $request->getPost();

        try {
            $pl = new Application_Model_Playlist($params['obj_id']);
            $result = $pl->shuffle();

            $this->view->result = $result['result'];
            $this->createUpdateResponse($pl, true);
            /*
            if ($result['result'] == 0) {
                $this->_helper->json->sendJson(array(
                    "result"=>0,
                    "contents" => $pl->getContents(),
                    "html"=>$this->createUpdateResponse($pl, true)));
            } else {
                $this->_helper->json->sendJson($result);
            }*/
        } catch (PlaylistNotFoundException $e) {
            $this->playlistNotFound('block', true);
        } catch (Exception $e) {
            $this->playlistUnknownError($e);
        }
    }

    public function getBlockInfoAction()
    {
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
        $this->_helper->json->sendJson($out);
    }
}
class WrongTypeToBlockException extends Exception
{
}
class WrongTypeToPlaylistException extends Exception
{
}
class BlockDynamicException extends Exception
{
}
