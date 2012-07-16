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
        $ajaxContext->addActionContext('contents-feed', 'json')
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

    public function contextMenuAction()
    {
        $id = $this->_getParam('id');
        $type = $this->_getParam('type');
        //playlist||timeline
        $screen = $this->_getParam('screen');
        $request = $this->getRequest();
        $baseUrl = $request->getBaseUrl();
        $menu = array();

        $userInfo = Zend_Auth::getInstance()->getStorage()->read();
        $user = new Application_Model_User($userInfo->id);

        //Open a jPlayer window and play the audio clip.
        $menu["play"] = array("name"=> "Preview", "icon" => "play");

        $isAdminOrPM = $user->isUserType(array(UTYPE_ADMIN, UTYPE_PROGRAM_MANAGER));

        if ($type === "audioclip") {

            $file = Application_Model_StoredFile::Recall($id);

            if (isset($this->pl_sess->id) && $screen == "playlist") {
                // if the user is not admin or pm, check the creator and see if this person owns the playlist
                $playlist = new Application_Model_Playlist($this->pl_sess->id);
                if ($isAdminOrPM || $playlist->getCreatorId() == $user->getId()) {
                    $menu["pl_add"] = array("name"=> "Add to Playlist", "icon" => "add-playlist", "icon" => "copy");
                }
            }
            if ($isAdminOrPM) {
                $menu["del"] = array("name"=> "Delete", "icon" => "delete", "url" => "/library/delete");
                $menu["edit"] = array("name"=> "Edit Metadata", "icon" => "edit", "url" => "/library/edit-file-md/id/{$id}");
            }

            $url = $file->getRelativeFileUrl($baseUrl).'/download/true';
            $menu["download"] = array("name" => "Download", "icon" => "download", "url" => $url);
        } elseif ($type === "playlist") {
            $playlist = new Application_Model_Playlist($id);
            if ($this->pl_sess->id !== $id && $screen == "playlist") {
                if ($isAdminOrPM || $playlist->getCreatorId() == $user->getId()) {
                    $menu["edit"] = array("name"=> "Edit", "icon" => "edit");
                }
            }
            if ($isAdminOrPM || $playlist->getCreatorId() == $user->getId()) {
                $menu["del"] = array("name"=> "Delete", "icon" => "delete", "url" => "/library/delete");
            }
        }

        //SOUNDCLOUD MENU OPTIONS
        if ($type === "audioclip" && Application_Model_Preference::GetUploadToSoundcloudOption()) {

            //create a menu separator
            $menu["sep1"] = "-----------";

            //create a sub menu for Soundcloud actions.
            $menu["soundcloud"] = array("name" => "Soundcloud", "icon" => "soundcloud", "items" => array());

            $scid = $file->getSoundCloudId();

            if ($scid > 0) {
                $url = $file->getSoundCloudLinkToFile();
                $menu["soundcloud"]["items"]["view"] = array("name" => "View on Soundcloud", "icon" => "soundcloud", "url" => $url);
            }

            if (!is_null($scid)) {
                $text = "Re-upload to SoundCloud";
            } else {
                $text = "Upload to SoundCloud";
            }

            $menu["soundcloud"]["items"]["upload"] = array("name" => $text, "icon" => "soundcloud", "url" => "/library/upload-file-soundcloud/id/{$id}");
        }

        $this->view->items = $menu;
    }

    public function deleteAction()
    {
        //array containing id and type of media to delete.
        $mediaItems = $this->_getParam('media', null);

        $user = Application_Model_User::getCurrentUser();
        $isAdminOrPM = $user->isUserType(array(UTYPE_ADMIN, UTYPE_PROGRAM_MANAGER));

        $files = array();
        $playlists = array();

        $message = null;

        foreach ($mediaItems as $media) {

            if ($media["type"] === "audioclip") {
                $files[] = intval($media["id"]);
            } elseif ($media["type"] === "playlist") {
                $playlists[] = intval($media["id"]);
            }
        }

        $hasPermission = true;
        if (count($playlists)) {
            // make sure use has permission to delete all playslists in the list
            if (!$isAdminOrPM) {
                foreach ($playlists as $pid) {
                    $pl = new Application_Model_Playlist($pid);
                    if ($pl->getCreatorId() != $user->getId()) {
                        $hasPermission = false;
                    }
                }
            }
        }

        if (!$isAdminOrPM && count($files)) {
            $hasPermission = false;
        }
        if (!$hasPermission) {
            $this->view->message = "You don't have a permission to delete all playlists/files that are selected.";

            return;
        } else {
            Application_Model_Playlist::DeletePlaylists($playlists);
        }

        foreach ($files as $id) {

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

    public function contentsFeedAction()
    {
        $params = $this->getRequest()->getParams();
        $r = Application_Model_StoredFile::searchLibraryFiles($params);

        //TODO move this to the datatables row callback.
        foreach ($r["aaData"] as &$data) {

            if ($data['ftype'] == 'audioclip') {
                $file = Application_Model_StoredFile::Recall($data['id']);
                $scid = $file->getSoundCloudId();

                if ($scid == "-2") {
                    $data['track_title'] .= '<span class="small-icon progress"/>';
                } elseif ($scid == "-3") {
                    $data['track_title'] .= '<span class="small-icon sc-error"/>';
                } elseif (!is_null($scid)) {
                    $data['track_title'] .= '<span class="small-icon soundcloud"/>';
                }
            }
        }

        $this->view->sEcho = $r["sEcho"];
        $this->view->iTotalDisplayRecords = $r["iTotalDisplayRecords"];
        $this->view->iTotalRecords = $r["iTotalRecords"];
        $this->view->files = $r["aaData"];
    }

    public function editFileMdAction()
    {
        $user = Application_Model_User::getCurrentUser();
        $isAdminOrPM = $user->isUserType(array(UTYPE_ADMIN, UTYPE_PROGRAM_MANAGER));
        if (!$isAdminOrPM) {
            return;
        }

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

                $this->_redirect('playlist/index');
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

                foreach ($md as $key => $value) {
                    if ($key == 'MDATA_KEY_DIRECTORY') {
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
            }
        } catch (Exception $e) {
            Logging::log($e->getMessage());
        }
    }

    public function uploadFileSoundcloudAction()
    {
        $id = $this->_getParam('id');
        $res = exec("/usr/lib/airtime/utils/soundcloud-uploader $id > /dev/null &");
        // we should die with ui info
        die();
    }

    public function getUploadToSoundcloudStatusAction()
    {
        $id = $this->_getParam('id');
        $type = $this->_getParam('type');

        if ($type == "show") {
            $show_instance = new Application_Model_ShowInstance($id);
            $this->view->sc_id = $show_instance->getSoundCloudFileId();
            $file = $show_instance->getRecordedFile();
            $this->view->error_code = $file->getSoundCloudErrorCode();
            $this->view->error_msg = $file->getSoundCloudErrorMsg();
        } elseif ($type == "file") {
            $file = Application_Model_StoredFile::Recall($id);
            $this->view->sc_id = $file->getSoundCloudId();
            $this->view->error_code = $file->getSoundCloudErrorCode();
            $this->view->error_msg = $file->getSoundCloudErrorMsg();
        }
    }
}
