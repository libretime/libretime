<?php

require_once 'formatters/LengthFormatter.php';
require_once 'formatters/SamplerateFormatter.php';
require_once 'formatters/BitrateFormatter.php';

class LibraryController extends Zend_Controller_Action
{

    public function init()
    {
        $ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext->addActionContext('contents-feed', 'json')
                    ->addActionContext('delete', 'json')
                    ->addActionContext('duplicate', 'json')
                    ->addActionContext('delete-group', 'json')
                    ->addActionContext('context-menu', 'json')
                    ->addActionContext('get-file-metadata', 'html')
                    ->addActionContext('set-num-entries', 'json')
                    ->addActionContext('edit-file-md', 'json')
                    ->initContext();
    }

    public function indexAction()
    {
        $CC_CONFIG = Config::getConfig();

        $request = $this->getRequest();
        $baseUrl = Application_Common_OsPath::getBaseDir();

        $this->view->headScript()->appendFile($baseUrl.'js/blockui/jquery.blockUI.js?'.$CC_CONFIG['airtime_version'], 'text/javascript');
        $this->view->headScript()->appendFile($baseUrl.'js/contextmenu/jquery.contextMenu.js?'.$CC_CONFIG['airtime_version'], 'text/javascript');
        $this->view->headScript()->appendFile($baseUrl.'js/datatables/js/jquery.dataTables.js?'.$CC_CONFIG['airtime_version'], 'text/javascript');
        $this->view->headScript()->appendFile($baseUrl.'js/datatables/plugin/dataTables.pluginAPI.js?'.$CC_CONFIG['airtime_version'], 'text/javascript');
        $this->view->headScript()->appendFile($baseUrl.'js/datatables/plugin/dataTables.fnSetFilteringDelay.js?'.$CC_CONFIG['airtime_version'],'text/javascript');
        $this->view->headScript()->appendFile($baseUrl.'js/datatables/plugin/dataTables.ColVis.js?'.$CC_CONFIG['airtime_version'], 'text/javascript');
        $this->view->headScript()->appendFile($baseUrl.'js/datatables/plugin/dataTables.ColReorder.js?'.$CC_CONFIG['airtime_version'], 'text/javascript');
        $this->view->headScript()->appendFile($baseUrl.'js/datatables/plugin/dataTables.FixedColumns.js?'.$CC_CONFIG['airtime_version'], 'text/javascript');
        $this->view->headScript()->appendFile($baseUrl.'js/datatables/plugin/dataTables.columnFilter.js?'.$CC_CONFIG['airtime_version'], 'text/javascript');

        $this->view->headScript()->appendFile($baseUrl.'js/airtime/buttons/buttons.js?'.$CC_CONFIG['airtime_version'], 'text/javascript');
        $this->view->headScript()->appendFile($baseUrl.'js/airtime/utilities/utilities.js?'.$CC_CONFIG['airtime_version'], 'text/javascript');
        $this->view->headScript()->appendFile($baseUrl.'js/airtime/library/library.js?'.$CC_CONFIG['airtime_version'], 'text/javascript');
        $this->view->headScript()->appendFile($baseUrl.'js/airtime/library/events/library_playlistbuilder.js?'.$CC_CONFIG['airtime_version'], 'text/javascript');

        $this->view->headLink()->appendStylesheet($baseUrl.'css/media_library.css?'.$CC_CONFIG['airtime_version']);
        $this->view->headLink()->appendStylesheet($baseUrl.'css/jquery.contextMenu.css?'.$CC_CONFIG['airtime_version']);
        $this->view->headLink()->appendStylesheet($baseUrl.'css/datatables/css/ColVis.css?'.$CC_CONFIG['airtime_version']);
        $this->view->headLink()->appendStylesheet($baseUrl.'css/datatables/css/ColReorder.css?'.$CC_CONFIG['airtime_version']);
        $this->view->headLink()->appendStylesheet($baseUrl.'css/waveform.css?'.$CC_CONFIG['airtime_version']);

        $this->view->headScript()->appendFile($baseUrl.'js/airtime/library/spl.js?'.$CC_CONFIG['airtime_version'], 'text/javascript');
        $this->view->headScript()->appendFile($baseUrl.'js/airtime/playlist/smart_blockbuilder.js?'.$CC_CONFIG['airtime_version'], 'text/javascript');

        $this->view->headScript()->appendFile($baseUrl.'js/waveformplaylist/observer/observer.js?'.$CC_CONFIG['airtime_version'], 'text/javascript');
        $this->view->headScript()->appendFile($baseUrl.'js/waveformplaylist/config.js?'.$CC_CONFIG['airtime_version'], 'text/javascript');
        $this->view->headScript()->appendFile($baseUrl.'js/waveformplaylist/curves.js?'.$CC_CONFIG['airtime_version'], 'text/javascript');
        $this->view->headScript()->appendFile($baseUrl.'js/waveformplaylist/fades.js?'.$CC_CONFIG['airtime_version'], 'text/javascript');
        $this->view->headScript()->appendFile($baseUrl.'js/waveformplaylist/local_storage.js?'.$CC_CONFIG['airtime_version'], 'text/javascript');
        $this->view->headScript()->appendFile($baseUrl.'js/waveformplaylist/controls.js?'.$CC_CONFIG['airtime_version'], 'text/javascript');
        $this->view->headScript()->appendFile($baseUrl.'js/waveformplaylist/playout.js?'.$CC_CONFIG['airtime_version'], 'text/javascript');
        $this->view->headScript()->appendFile($baseUrl.'js/waveformplaylist/track_render.js?'.$CC_CONFIG['airtime_version'], 'text/javascript');
        $this->view->headScript()->appendFile($baseUrl.'js/waveformplaylist/track.js?'.$CC_CONFIG['airtime_version'], 'text/javascript');
        $this->view->headScript()->appendFile($baseUrl.'js/waveformplaylist/time_scale.js?'.$CC_CONFIG['airtime_version'], 'text/javascript');
        $this->view->headScript()->appendFile($baseUrl.'js/waveformplaylist/playlist.js?'.$CC_CONFIG['airtime_version'], 'text/javascript');

        //arbitrary attributes need to be allowed to set an id for the templates.
        $this->view->headScript()->setAllowArbitraryAttributes(true);
        //$this->view->headScript()->appendScript(file_get_contents(APPLICATION_PATH.'/../public/js/waveformplaylist/templates/bottombar.tpl'),
        //		'text/template', array('id' => 'tpl_playlist_cues', 'noescape' => true));

        $this->view->headLink()->appendStylesheet($baseUrl.'css/playlist_builder.css?'.$CC_CONFIG['airtime_version']);

        try {

            $obj_sess = new Zend_Session_Namespace(UI_PLAYLISTCONTROLLER_OBJ_SESSNAME);
            if (isset($obj_sess->id)) {
                $objInfo     = Application_Model_Library::getObjInfo($obj_sess->type);
                $obj         = new $objInfo['className']($obj_sess->id);
                $userInfo    = Zend_Auth::getInstance()->getStorage()->read();
                $user        = new Application_Model_User($userInfo->id);
                $isAdminOrPM = $user->isUserType(array(UTYPE_SUPERADMIN, UTYPE_ADMIN, UTYPE_PROGRAM_MANAGER));

                if ($isAdminOrPM || $obj->getCreatorId() == $userInfo->id) {
                    $this->view->obj = $obj;
                    if ($obj_sess->type == "block") {
                        $form = new Application_Form_SmartBlockCriteria();
                        $form->startForm($obj_sess->id);
                        $this->view->form = $form;
                    }
                }

                $formatter = new LengthFormatter($obj->getLength());
                $this->view->length = $formatter->format();
                $this->view->type = $obj_sess->type;
            }

            //get user settings and determine if we need to hide
            // or show the playlist editor
            $showPlaylist = false;
            $data = Application_Model_Preference::getLibraryScreenSettings();
            if (!is_null($data)) {
                if ($data["playlist"] == "true") {
                    $showPlaylist = true;
                }
            }
            $this->view->showPlaylist = $showPlaylist;
        } catch (PlaylistNotFoundException $e) {
            $this->playlistNotFound($obj_sess->type);
        } catch (Exception $e) {
            $this->playlistNotFound($obj_sess->type);
            Logging::info($e->getMessage());
            //$this->playlistUnknownError($e);
        }
    }

    protected function playlistNotFound($p_type)
    {
        $this->view->error = sprintf(_("%s not found"), $p_type);

        Logging::info("$p_type not found");
        Application_Model_Library::changePlaylist(null, $p_type);
        $this->createFullResponse(null);
    }

    protected function playlistUnknownError($e)
    {
        $this->view->error = _("Something went wrong.");
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
                } else {
                    $this->view->html = $this->view->render($viewPath);
                }
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
        $baseUrl = Application_Common_OsPath::getBaseDir();
        $id = $this->_getParam('id');
        $type = $this->_getParam('type');
        //playlist||timeline
        $screen = $this->_getParam('screen');

        $menu = array();

        $userInfo = Zend_Auth::getInstance()->getStorage()->read();
        $user = new Application_Model_User($userInfo->id);

        //Open a jPlayer window and play the audio clip.
        $menu["play"] = array("name"=> _("Preview"), "icon" => "play", "disabled" => false);

        $isAdminOrPM = $user->isUserType(array(UTYPE_SUPERADMIN, UTYPE_ADMIN, UTYPE_PROGRAM_MANAGER));

        $obj_sess = new Zend_Session_Namespace(UI_PLAYLISTCONTROLLER_OBJ_SESSNAME);

        if ($type === "audioclip") {
            $file = Application_Model_StoredFile::RecallById($id);

            $menu["play"]["mime"] = $file->getPropelOrm()->getDbMime();

            if (isset($obj_sess->id) && $screen == "playlist") {
                // if the user is not admin or pm, check the creator and see if this person owns the playlist or Block
                if ($obj_sess->type == 'playlist') {
                    $obj = new Application_Model_Playlist($obj_sess->id);
                } elseif ($obj_sess->type == 'block') {
                    $obj = new Application_Model_Block($obj_sess->id);
                }
                if ($isAdminOrPM || $obj->getCreatorId() == $user->getId()) {
                    if ($obj_sess->type === "playlist") {
                        $menu["pl_add"] = array("name"=> _("Add to Playlist"), "icon" => "add-playlist", "icon" => "copy");
                    } elseif ($obj_sess->type === "block" && $obj->isStatic()) {
                        $menu["pl_add"] = array("name"=> _("Add to Smart Block"), "icon" => "add-playlist", "icon" => "copy");
                    }
                }
            }
            if ($isAdminOrPM || $file->getFileOwnerId() == $user->getId()) {
                $menu["del"] = array("name"=> _("Delete"), "icon" => "delete", "url" => $baseUrl."library/delete");
                $menu["edit"] = array("name"=> _("Edit Metadata"), "icon" => "edit", "url" => $baseUrl."library/edit-file-md/id/{$id}");
            }

            // It's important that we always return the parent id (cc_files id)
            // and not the cloud_file id (if applicable) for track download.
            // Our application logic (StoredFile.php) will determine if the track
            // is a cloud_file and handle it appropriately.
            $url = $baseUrl."api/get-media/file/$id/download/true";
            $menu["download"] = array("name" => _("Download"), "icon" => "download", "url" => $url);
        } elseif ($type === "playlist" || $type === "block") {
            if ($type === 'playlist') {
                $obj = new Application_Model_Playlist($id);
                $menu["duplicate"] = array("name" => _("Duplicate Playlist"), "icon" => "edit", "url" => $baseUrl."library/duplicate");
            } elseif ($type === 'block') {
                $obj = new Application_Model_Block($id);
                if (!$obj->isStatic()) {
                    unset($menu["play"]);
                }
                if (($isAdminOrPM || $obj->getCreatorId() == $user->getId()) && $screen == "playlist") {
                    if ($obj_sess->type === "playlist") {
                        $menu["pl_add"] = array("name"=> _("Add to Playlist"), "icon" => "add-playlist", "icon" => "copy");
                    }
                }
            }

            if ($obj_sess->id !== $id && $screen == "playlist") {
                if ($isAdminOrPM || $obj->getCreatorId() == $user->getId()) {
                    $menu["edit"] = array("name"=> _("Edit"), "icon" => "edit");
                }
            }

            if ($isAdminOrPM || $obj->getCreatorId() == $user->getId()) {
                $menu["del"] = array("name"=> _("Delete"), "icon" => "delete", "url" => $baseUrl."library/delete");
            }
        } elseif ($type == "stream") {
            $webstream = CcWebstreamQuery::create()->findPK($id);
            $obj = new Application_Model_Webstream($webstream);

            $menu["play"]["mime"] = $webstream->getDbMime();

            if (isset($obj_sess->id) && $screen == "playlist") {
                if ($isAdminOrPM || $obj->getCreatorId() == $user->getId()) {
                    if ($obj_sess->type === "playlist") {
                        $menu["pl_add"] = array("name"=> _("Add to Playlist"), "icon" => "add-playlist", "icon" => "copy");
                    }
                }
            }
            if ($isAdminOrPM || $obj->getCreatorId() == $user->getId()) {
                if ($screen == "playlist") {
                    $menu["edit"] = array("name"=> _("Edit"), "icon" => "edit", "url" => $baseUrl."library/edit-file-md/id/{$id}");
                }
                $menu["del"] = array("name"=> _("Delete"), "icon" => "delete", "url" => $baseUrl."library/delete");
            }
        }

        // SOUNDCLOUD MENU OPTION
        $ownerId = empty($obj) ? $file->getFileOwnerId() : $obj->getCreatorId();
        if ($isAdminOrPM || $ownerId == $user->getId()) {
            $soundcloudService = new SoundcloudService();
            if ($type === "audioclip" && $soundcloudService->hasAccessToken()) {

                //create a menu separator
                $menu["sep1"] = "-----------";

                //create a sub menu for Soundcloud actions.
                $menu["soundcloud"] = array("name" => _(SOUNDCLOUD), "icon" => "soundcloud", "items" => array());

                $serviceId = $soundcloudService->getServiceId($id);
                if (!is_null($file) && $serviceId != 0) {
                    $menu["soundcloud"]["items"]["view"] = array("name" => _("View track"), "icon" => "soundcloud", "url" => $baseUrl . "soundcloud/view-on-sound-cloud/id/{$id}");
                    $menu["soundcloud"]["items"]["remove"] = array("name" => _("Remove track"), "icon" => "soundcloud", "url" => $baseUrl . "soundcloud/delete/id/{$id}");
                } else {
                    // If a reference exists for this file ID, that means the user has uploaded the track
                    // but we haven't yet gotten a response from Celery, so disable the menu item
                    if ($soundcloudService->referenceExists($id)) {
                        $menu["soundcloud"]["items"]["upload"] = array(
                            "name" => _("Upload track"), "icon" => "soundcloud",
                            "url" => $baseUrl . "soundcloud/upload/id/{$id}", "disabled" => true
                        );
                    } else {
                        $menu["soundcloud"]["items"]["upload"] = array(
                            "name" => _("Upload track"), "icon" => "soundcloud",
                            "url" => $baseUrl . "soundcloud/upload/id/{$id}"
                        );
                    }
                }
            }
        }

        if (empty($menu)) {
            $menu["noaction"] = array("name"=>_("No action available"));
        }

        $this->view->items = $menu;
    }

    public function deleteAction()
    {
        //array containing id and type of media to delete.
        $mediaItems = $this->_getParam('media', null);

        $user = Application_Model_User::getCurrentUser();
        //$isAdminOrPM = $user->isUserType(array(UTYPE_SUPERADMIN, UTYPE_ADMIN, UTYPE_PROGRAM_MANAGER));

        $files     = array();
        $playlists = array();
        $blocks    = array();
        $streams   = array();

        $message = null;
        $noPermissionMsg = _("You don't have permission to delete selected items.");

        foreach ($mediaItems as $media) {

            if ($media["type"] === "audioclip") {
                $files[] = intval($media["id"]);
            } elseif ($media["type"] === "playlist") {
                $playlists[] = intval($media["id"]);
            } elseif ($media["type"] === "block") {
                $blocks[] = intval($media["id"]);
            } elseif ($media["type"] === "stream") {
                $streams[] = intval($media["id"]);
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
            //TODO: warn user that not all blocks could be deleted.
        }

        try {
            Application_Model_Webstream::deleteStreams($streams, $user->getId());
        } catch (WebstreamNoPermissionException $e) {
            $message = $noPermissionMsg;
        } catch (Exception $e) {
            //TODO: warn user that not all streams could be deleted.
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
                    $message = _("Could not delete file because it is scheduled in the future.");
                } catch (Exception $e) {
                    //could throw a scheduled in future exception.
                    $message = _("Could not delete file(s).");
                    Logging::info($message.": ".$e->getMessage());
                }
            }
        }

        if (isset($message)) {
            $this->view->message = $message;
        }
    }

    // duplicate playlist
    public function duplicateAction(){
        $params = $this->getRequest()->getParams();
        $id = $params['id'];

        $originalPl = new Application_Model_Playlist($id);
        $newPl = new Application_Model_Playlist();

        $contents = $originalPl->getContents();
        foreach ($contents as &$c) {
            if ($c['type'] == '0') {
                $c[1] = 'audioclip';
            } else if ($c['type'] == '2') {
                $c[1] = 'block';
            } else if ($c['type'] == '1') {
                $c[1] = 'stream';
            }
            $c[0] = $c['item_id'];
        }

        $newPl->addAudioClips($contents, null, 'before');

        $newPl->setCreator(Application_Model_User::getCurrentUser()->getId());
        $newPl->setDescription($originalPl->getDescription());

        list($plFadeIn, ) = $originalPl->getFadeInfo(0);
        list(, $plFadeOut) = $originalPl->getFadeInfo($originalPl->getSize()-1);

        $newPl->setfades($plFadeIn, $plFadeOut);
        $newPl->setName(sprintf(_("Copy of %s"), $originalPl->getName()));
    }

    public function contentsFeedAction()
    {
        $params = $this->getRequest()->getParams();

        # terrible name for the method below. it does not only search files.
        $r = Application_Model_StoredFile::searchLibraryFiles($params);

        $this->view->sEcho = $r["sEcho"];
        $this->view->iTotalDisplayRecords = $r["iTotalDisplayRecords"];
        $this->view->iTotalRecords = $r["iTotalRecords"];
        $this->view->files = SecurityHelper::htmlescape_recursive($r["aaData"]);
    }

    public function editFileMdAction()
    {
        $user = Application_Model_User::getCurrentUser();
        $isAdminOrPM = $user->isUserType(array(UTYPE_SUPERADMIN, UTYPE_ADMIN, UTYPE_PROGRAM_MANAGER));

        $request = $this->getRequest();

        $file_id = $this->_getParam('id', null);
        $file = Application_Model_StoredFile::RecallById($file_id);

        if (!$isAdminOrPM && $file->getFileOwnerId() != $user->getId()) {
            return;
        }

        $form = new Application_Form_EditAudioMD();
        $form->startForm($file_id);
        $form->populate($file->getDbColMetadata());

        if ($request->isPost()) {

            $js = $this->_getParam('data');
            $serialized = array();
            //need to convert from serialized jQuery array.
            foreach ($js as $j) {
                $serialized[$j["name"]] = $j["value"];
            }

            // Sanitize any wildly incorrect metadata before it goes to be validated.
            FileDataHelper::sanitizeData($serialized);

            if ($form->isValid($serialized)) {
                $file->setDbColMetadata($serialized);
            }
        }

        $this->view->form = $form;
        $this->view->dialog = $this->view->render('library/edit-file-md.phtml');
    }

    public function getFileMetadataAction()
    {
        $id = $this->_getParam('id');
        $type = $this->_getParam('type');

        try {
            if ($type == "audioclip") {
                $file = Application_Model_StoredFile::RecallById($id);
                $this->view->type = $type;
                $md = $file->getMetadata();

                foreach ($md as $key => $value) {
                    if ($key == 'MDATA_KEY_DIRECTORY' && !is_null($value)) {
                        $musicDir = Application_Model_MusicDir::getDirByPK($value);
                        $md['MDATA_KEY_FILEPATH'] = Application_Common_OsPath::join($musicDir->getDirectory(), $md['MDATA_KEY_FILEPATH']);
                    }
                }

                $formatter = new SamplerateFormatter($md["MDATA_KEY_SAMPLERATE"]);
                $md["MDATA_KEY_SAMPLERATE"] = $formatter->format();

                $formatter = new BitrateFormatter($md["MDATA_KEY_BITRATE"]);
                $md["MDATA_KEY_BITRATE"] = $formatter->format();

                $formatter = new LengthFormatter($md["MDATA_KEY_DURATION"]);
                $md["MDATA_KEY_DURATION"] = $formatter->format();

                $this->view->md = $md;

            } elseif ($type == "playlist") {

                $file = new Application_Model_Playlist($id);
                $this->view->type = $type;
                $md = $file->getAllPLMetaData();

                $formatter = new LengthFormatter($md["dcterms:extent"]);
                $md["dcterms:extent"] = $formatter->format();

                $this->view->md = $md;
                $this->view->contents = $file->getContents();
            } elseif ($type == "block") {
                $block = new Application_Model_Block($id);
                $this->view->type = $type;
                $md = $block->getAllPLMetaData();

                $formatter = new LengthFormatter($md["dcterms:extent"]);
                $md["dcterms:extent"] = $formatter->format();

                $this->view->md = $md;
                if ($block->isStatic()) {
                    $this->view->blType = 'Static';
                    $this->view->contents = $block->getContents();
                } else {
                    $this->view->blType = 'Dynamic';
                    $this->view->contents = $block->getCriteria();
                }
                $this->view->block = $block;
            } elseif ($type == "stream") {
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
}
