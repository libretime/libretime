<?php

class LibraryController extends Zend_Controller_Action
{
    public function init()
    {
        $ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext->addActionContext('contents-feed', 'json')
            ->addActionContext('delete', 'json')
            ->addActionContext('duplicate', 'json')
            ->addActionContext('duplicate-block', 'json')
            ->addActionContext('delete-group', 'json')
            ->addActionContext('context-menu', 'json')
            ->addActionContext('get-file-metadata', 'html')
            ->addActionContext('set-num-entries', 'json')
            ->addActionContext('edit-file-md', 'json')
            ->addActionContext('publish-dialog', 'html')
            ->initContext();
    }

    public function indexAction()
    {
        $this->_redirect('showbuilder');
    }

    protected function playlistNotFound($p_type)
    {
        $this->view->error = sprintf(_('%s not found'), $p_type);

        Logging::info("{$p_type} not found");
        Application_Model_Library::changePlaylist(null, $p_type);
        $this->createFullResponse(null);
    }

    protected function playlistUnknownError($e)
    {
        $this->view->error = _('Something went wrong.');
        Logging::info($e->getMessage());
    }

    protected function createFullResponse($obj = null, $isJson = false)
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
                }
                $this->view->html = $this->view->render($viewPath);
            } else {
                $this->view->obj = $obj;
                $this->view->id = $obj->getId();
                $this->view->html = $this->view->render($viewPath);
                unset($this->view->obj);
            }
        } else {
            $this->view->html = $this->view->render($viewPath);
        }
    }

    public function contextMenuAction()
    {
        $baseUrl = Config::getBasePath();
        $id = $this->_getParam('id');
        $type = $this->_getParam('type');
        // playlist||timeline
        $screen = $this->_getParam('screen');

        $menu = [];

        $userInfo = Zend_Auth::getInstance()->getStorage()->read();
        $user = new Application_Model_User($userInfo->id);

        // Open a jPlayer window and play the audio clip.
        $menu['play'] = ['name' => _('Preview'), 'icon' => 'play', 'disabled' => false];

        $isAdminOrPM = $user->isUserType([UTYPE_SUPERADMIN, UTYPE_ADMIN, UTYPE_PROGRAM_MANAGER]);

        $obj_sess = new Zend_Session_Namespace(UI_PLAYLISTCONTROLLER_OBJ_SESSNAME);

        if ($type === 'audioclip') {
            $file = Application_Model_StoredFile::RecallById($id);

            $menu['play']['mime'] = $file->getPropelOrm()->getDbMime();

            if (isset($obj_sess->id) && $screen == 'playlist') {
                // if the user is not admin or pm, check the creator and see if this person owns the playlist or Block
                if ($obj_sess->type == 'playlist') {
                    $obj = new Application_Model_Playlist($obj_sess->id);
                } elseif ($obj_sess->type == 'block') {
                    $obj = new Application_Model_Block($obj_sess->id);
                }
                if ($isAdminOrPM || $obj->getCreatorId() == $user->getId()) {
                    if ($obj_sess->type === 'playlist') {
                        $menu['pl_add'] = ['name' => _('Add to Playlist'), 'icon' => 'add-playlist', 'icon' => 'copy'];
                    } elseif ($obj_sess->type === 'block' && $obj->isStatic()) {
                        $menu['pl_add'] = ['name' => _('Add to Smart Block'), 'icon' => 'add-playlist', 'icon' => 'copy'];
                    }
                }
            }
            if ($isAdminOrPM || $file->getFileOwnerId() == $user->getId()) {
                $menu['del'] = ['name' => _('Delete'), 'icon' => 'delete', 'url' => $baseUrl . 'library/delete'];
                $menu['edit'] = ['name' => _('Edit...'), 'icon' => 'edit', 'url' => $baseUrl . "library/edit-file-md/id/{$id}"];
                // Disable My podcasts
                // See https://github.com/libretime/libretime/issues/1320
                // $menu["publish"] = array("name"=> _("Publish..."), "url" => $baseUrl."library/publish/id/{$id}");
            }

            $url = $baseUrl . "api/get-media/file/{$id}/download/true";
            $menu['download'] = ['name' => _('Download'), 'icon' => 'download', 'url' => $url];
        } elseif ($type === 'playlist' || $type === 'block') {
            if ($type === 'playlist') {
                $obj = new Application_Model_Playlist($id);
                $menu['duplicate'] = ['name' => _('Duplicate Playlist'), 'icon' => 'edit', 'url' => $baseUrl . 'library/duplicate'];
            } elseif ($type === 'block') {
                $obj = new Application_Model_Block($id);
                $menu['duplicate'] = ['name' => _('Duplicate Smartblock'), 'icon' => 'edit', 'url' => $baseUrl . 'library/duplicate-block'];
                if (!$obj->isStatic()) {
                    unset($menu['play']);
                }
                if (($isAdminOrPM || $obj->getCreatorId() == $user->getId()) && $screen == 'playlist') {
                    if ($obj_sess->type === 'playlist') {
                        $menu['pl_add'] = ['name' => _('Add to Playlist'), 'icon' => 'add-playlist', 'icon' => 'copy'];
                    }
                }
            }

            if ($obj_sess->id !== $id && $screen == 'playlist') {
                if ($isAdminOrPM || $obj->getCreatorId() == $user->getId()) {
                    $menu['edit'] = ['name' => _('Edit...'), 'icon' => 'edit'];
                }
            }

            if ($isAdminOrPM || $obj->getCreatorId() == $user->getId()) {
                $menu['del'] = ['name' => _('Delete'), 'icon' => 'delete', 'url' => $baseUrl . 'library/delete'];
            }
        } elseif ($type == 'stream') {
            $webstream = CcWebstreamQuery::create()->findPK($id);
            $obj = new Application_Model_Webstream($webstream);

            $menu['play']['mime'] = $webstream->getDbMime();

            if (isset($obj_sess->id) && $screen == 'playlist') {
                if ($isAdminOrPM || $obj->getCreatorId() == $user->getId()) {
                    if ($obj_sess->type === 'playlist') {
                        $menu['pl_add'] = ['name' => _('Add to Playlist'), 'icon' => 'add-playlist', 'icon' => 'copy'];
                    }
                }
            }
            if ($isAdminOrPM || $obj->getCreatorId() == $user->getId()) {
                if ($screen == 'playlist') {
                    $menu['edit'] = ['name' => _('Edit...'), 'icon' => 'edit', 'url' => $baseUrl . "library/edit-file-md/id/{$id}"];
                }
                $menu['del'] = ['name' => _('Delete'), 'icon' => 'delete', 'url' => $baseUrl . 'library/delete'];
            }
        }

        if (empty($menu)) {
            $menu['noaction'] = ['name' => _('No action available')];
        }

        $this->view->items = $menu;
    }

    public function deleteAction()
    {
        // array containing id and type of media to delete.
        $mediaItems = $this->_getParam('media', null);

        $user = Application_Model_User::getCurrentUser();
        // $isAdminOrPM = $user->isUserType(array(UTYPE_SUPERADMIN, UTYPE_ADMIN, UTYPE_PROGRAM_MANAGER));

        $files = [];
        $playlists = [];
        $blocks = [];
        $streams = [];

        $message = null;
        $noPermissionMsg = _("You don't have permission to delete selected items.");

        foreach ($mediaItems as $media) {
            if ($media['type'] === 'audioclip') {
                $files[] = intval($media['id']);
            } elseif ($media['type'] === 'playlist') {
                $playlists[] = intval($media['id']);
            } elseif ($media['type'] === 'block') {
                $blocks[] = intval($media['id']);
            } elseif ($media['type'] === 'stream') {
                $streams[] = intval($media['id']);
            }
        }

        try {
            Application_Model_Playlist::deletePlaylists($playlists, $user->getId());
        } catch (PlaylistNoPermissionException $e) {
            $message = $noPermissionMsg;
        }

        try {
            Application_Model_Block::deleteBlocks($blocks, $user->getId());
        } catch (BlockNoPermissionException $e) {
            $message = $noPermissionMsg;
        } catch (Exception $e) {
            // TODO: warn user that not all blocks could be deleted.
        }

        try {
            Application_Model_Webstream::deleteStreams($streams, $user->getId());
        } catch (WebstreamNoPermissionException $e) {
            $message = $noPermissionMsg;
        } catch (Exception $e) {
            // TODO: warn user that not all streams could be deleted.
            Logging::info($e);
        }

        foreach ($files as $id) {
            $file = Application_Model_StoredFile::RecallById($id);
            if (isset($file)) {
                try {
                    $res = $file->delete();
                } catch (FileNoPermissionException $e) {
                    $message = $noPermissionMsg;
                } catch (DeleteScheduledFileException $e) {
                    $message = _('Could not delete file because it is scheduled in the future.');
                } catch (Exception $e) {
                    // could throw a scheduled in future exception.
                    $message = _('Could not delete file(s).');
                    Logging::info($message . ': ' . $e->getMessage());
                }
            }
        }

        if (isset($message)) {
            $this->view->message = $message;
        }
    }

    // duplicate playlist
    public function duplicateAction()
    {
        $params = $this->getRequest()->getParams();
        $id = $params['id'];
        Logging::info($params);

        $originalPl = new Application_Model_Playlist($id);
        $newPl = new Application_Model_Playlist();

        $contents = $originalPl->getContents();
        foreach ($contents as &$c) {
            if ($c['type'] == '0') {
                $c[1] = 'audioclip';
            } elseif ($c['type'] == '2') {
                $c[1] = 'block';
            } elseif ($c['type'] == '1') {
                $c[1] = 'stream';
            }
            $c[0] = $c['item_id'];
        }

        $newPl->addAudioClips($contents, null, 'before');

        $newPl->setCreator(Application_Model_User::getCurrentUser()->getId());
        $newPl->setDescription($originalPl->getDescription());

        [$plFadeIn] = $originalPl->getFadeInfo(0);
        [, $plFadeOut] = $originalPl->getFadeInfo($originalPl->getSize() - 1);

        $newPl->setfades($plFadeIn, $plFadeOut);
        $newPl->setName(sprintf(_('Copy of %s'), $originalPl->getName()));
    }

    // duplicate smartblock
    public function duplicateBlockAction()
    {
        Logging::info('duplicate smartblock functionality not yet implemented');
        $params = $this->getRequest()->getParams();
        $id = $params['id'];
        Logging::info($params);

        $originalBl = new Application_Model_Block($id);
        $newBl = new Application_Model_Block();
        $newBl->setCreator(Application_Model_User::getCurrentUser()->getId());
        $newBl->setDescription($originalBl->getDescription());
        if ($originalBl->isStatic()) {
            $newBl->saveType('static');
        } else {
            $newBl->saveType('dynamic');
        }
        // the issue here is that the format that getCriteria provides is different from the format the saveCriteria
        // expects due to the useage of startForm. So we either need to write new code that simply copies the database
        // or figure out a way to instantiate a form inside of here and save it without modifying it.
        // $newBlForm = new Application_Form_SmartBlockCriteria;
        // $newBlForm->startForm($id);
        $criteria = CcBlockcriteriaQuery::create()->orderByDbCriteria()->findByDbBlockId($id);
        foreach ($criteria as &$c) {
            $row = new CcBlockcriteria();
            $row->setDbCriteria($c->getDbCriteria());
            $row->setDbModifier($c->getDbModifier());
            $row->setDbValue($c->getDbValue());
            $row->setDbExtra($c->getDbExtra());
            $row->setDbBlockId($newBl->getId());
            $row->save();
        }
        $newBl->setName(sprintf(_('Copy of %s'), $originalBl->getName()));
    }

    public function contentsFeedAction()
    {
        $params = $this->getRequest()->getParams();

        // terrible name for the method below. it does not only search files.
        $r = Application_Model_StoredFile::searchLibraryFiles($params);

        $this->view->sEcho = $r['sEcho'];
        $this->view->iTotalDisplayRecords = $r['iTotalDisplayRecords'];
        $this->view->iTotalRecords = $r['iTotalRecords'];
        $this->view->files = SecurityHelper::htmlescape_recursive($r['aaData']);
    }

    public function editFileMdAction()
    {
        $user = Application_Model_User::getCurrentUser();
        $isAdminOrPM = $user->isUserType([UTYPE_SUPERADMIN, UTYPE_ADMIN, UTYPE_PROGRAM_MANAGER]);
        $isAdmin = $user->isUserType([UTYPE_SUPERADMIN, UTYPE_ADMIN]);

        $request = $this->getRequest();

        $file_id = $this->_getParam('id', null);
        $file = Application_Model_StoredFile::RecallById($file_id);

        $form = new Application_Form_EditAudioMD();
        $form->startForm($file_id);
        $form->populate($file->getDbColMetadata());

        $this->view->permissionDenied = false;
        if (!$isAdminOrPM && $file->getFileOwnerId() != $user->getId()) {
            $form->makeReadOnly();
            $form->removeActionButtons();
            $this->view->permissionDenied = true;
        }
        // only admins should be able to edit the owner of a file
        if (!$isAdmin) {
            $form->removeOwnerEdit();
        }

        if ($request->isPost()) {
            $js = $this->_getParam('data');
            $serialized = [];
            // need to convert from serialized jQuery array.
            foreach ($js as $j) {
                // on edit, if no artwork is set and audiofile has image, automatically add it
                if ($j['name'] == 'artwork') {
                    if ($j['value'] == null || $j['value'] == '') {
                        $serialized['artwork'] = FileDataHelper::resetArtwork($file_id);
                    }
                } elseif ($j['name'] == 'set_artwork') {
                    if ($j['value'] != null || $j['value'] != '') {
                        $serialized['artwork'] = FileDataHelper::setArtwork($file_id, $j['value']);
                    }
                } elseif ($j['name'] == 'remove_artwork') {
                    if ($j['value'] == 1) {
                        $remove_artwork = true;
                        $serialized['artwork'] = FileDataHelper::removeArtwork($file_id);
                    }
                } else {
                    $serialized[$j['name']] = $j['value'];
                }
            }

            // Sanitize any wildly incorrect metadata before it goes to be validated.
            FileDataHelper::sanitizeData($serialized);

            if ($form->isValid($serialized)) {
                $file->setDbColMetadata($serialized);
                $this->view->status = true;
            } else {
                $this->view->status = false;
            }
        }

        $this->view->form = $form;
        $this->view->id = $file_id;
        $this->view->title = $file->getPropelOrm()->getDbTrackTitle();
        $this->view->artist_name = $file->getPropelOrm()->getDbArtistName();
        $this->view->file_path = $file->getPropelOrm()->getDbFilepath();
        $this->view->artwork = $file->getPropelOrm()->getDbArtwork();
        $this->view->replay_gain = $file->getPropelOrm()->getDbReplayGain();
        $this->view->cuein = $file->getPropelOrm()->getDbCuein();
        $this->view->cueout = $file->getPropelOrm()->getDbCueout();
        $this->view->format = $file->getPropelOrm()->getDbFormat();
        $this->view->bit_rate = $file->getPropelOrm()->getDbBitRate();
        $this->view->sample_rate = $file->getPropelOrm()->getDbSampleRate();
        // 1000 B in KB and 1000 KB in MB and 1000 MB in GB
        $size = $file->getPropelOrm()->getFileSize();
        if ($size < 1000) {
            // Use B up to 1 KB
            $this->view->file_size = $size . " B";
        } elseif ($size < (500 * 1000)) {
            // Use KB up to 500 KB
            $this->view->file_size = round($size / 1000, 1) . " KB";
        } elseif ($size < (1 * 1000 * 1000 * 1000)) {
            // Use MB up to 1 GB
            $this->view->file_size = round($size / 1000 / 1000, 1) . " MB";
        } else {
            $this->view->file_size = round($size / 1000 / 1000 / 1000, 1) . " GB";
        }
        $this->view->html = $this->view->render('library/edit-file-md.phtml');
    }

    public function getFileMetadataAction()
    {
        $id = $this->_getParam('id');
        $type = $this->_getParam('type');

        try {
            if ($type == 'audioclip') {
                $file = Application_Model_StoredFile::RecallById($id);
                $this->view->type = $type;
                $md = $file->getMetadata();
                $storagePath = Config::getStoragePath();

                foreach ($md as $key => $value) {
                    if ($key == 'MDATA_KEY_DIRECTORY' && !is_null($value)) {
                        $md['MDATA_KEY_FILEPATH'] = Application_Common_OsPath::join($storagePath, $md['MDATA_KEY_FILEPATH']);
                    }
                }

                $formatter = new SamplerateFormatter($md['MDATA_KEY_SAMPLERATE']);
                $md['MDATA_KEY_SAMPLERATE'] = $formatter->format();

                $formatter = new BitrateFormatter($md['MDATA_KEY_BITRATE']);
                $md['MDATA_KEY_BITRATE'] = $formatter->format();

                $formatter = new LengthFormatter($md['MDATA_KEY_DURATION']);
                $md['MDATA_KEY_DURATION'] = $formatter->format();

                $this->view->md = $md;
            } elseif ($type == 'playlist') {
                $file = new Application_Model_Playlist($id);
                $this->view->type = $type;
                $md = $file->getAllPLMetaData();

                $formatter = new LengthFormatter($md['dcterms:extent']);
                $md['dcterms:extent'] = $formatter->format();

                $this->view->md = $md;
                $this->view->contents = $file->getContents();
            } elseif ($type == 'block') {
                $block = new Application_Model_Block($id);
                $this->view->type = $type;
                $md = $block->getAllPLMetaData();

                $formatter = new LengthFormatter($md['dcterms:extent']);
                $md['dcterms:extent'] = $formatter->format();

                $this->view->md = $md;
                if ($block->isStatic()) {
                    $this->view->blType = 'Static';
                    $this->view->contents = $block->getContents();
                } else {
                    $this->view->blType = 'Dynamic';
                    $this->view->contents = $block->getCriteria();
                }
                $this->view->block = $block;
            } elseif ($type == 'stream') {
                $webstream = CcWebstreamQuery::create()->findPK($id);
                $ws = new Application_Model_Webstream($webstream);

                $md = $ws->getMetadata();

                $this->view->md = $md;
                $this->view->type = $type;
            }
        } catch (Exception $e) {
            Logging::info($e->getMessage());
        }
    }

    public function publishDialogAction()
    {
        $this->_helper->layout->disableLayout();
        // This just spits out publish-dialog.phtml!
    }
}
