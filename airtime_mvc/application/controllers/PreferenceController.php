<?php

class PreferenceController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    	$ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext->addActionContext('register', 'json')
        			->addActionContext('remindme', 'json')
                    ->addActionContext('server-browse', 'json')
                    ->addActionContext('change-stor-directory', 'json')
                    ->addActionContext('reload-watch-directory', 'json')
                    ->addActionContext('remove-watch-directory', 'json')
                    ->initContext();
    }

    public function indexAction()
    {
        $request = $this->getRequest();
        $baseUrl = $request->getBaseUrl();

        $this->view->headScript()->appendFile($baseUrl.'/js/airtime/preferences/preferences.js','text/javascript');
        $this->view->statusMsg = "";

        $form = new Application_Form_Preferences();

        if ($request->isPost()) {

            if ($form->isValid($request->getPost())) {

                $values = $form->getValues();

                Application_Model_Preference::SetHeadTitle($values["preferences_general"]["stationName"], $this->view);
                Application_Model_Preference::SetDefaultFade($values["preferences_general"]["stationDefaultFade"]);
                Application_Model_Preference::SetStreamLabelFormat($values["preferences_general"]["streamFormat"]);
                Application_Model_Preference::SetAllow3rdPartyApi($values["preferences_general"]["thirdPartyApi"]);

                Application_Model_Preference::SetDoSoundCloudUpload($values["preferences_soundcloud"]["UseSoundCloud"]);
                Application_Model_Preference::SetSoundCloudUser($values["preferences_soundcloud"]["SoundCloudUser"]);
                Application_Model_Preference::SetSoundCloudPassword($values["preferences_soundcloud"]["SoundCloudPassword"]);
                Application_Model_Preference::SetSoundCloudTags($values["preferences_soundcloud"]["SoundCloudTags"]);
                Application_Model_Preference::SetSoundCloudGenre($values["preferences_soundcloud"]["SoundCloudGenre"]);
                Application_Model_Preference::SetSoundCloudTrackType($values["preferences_soundcloud"]["SoundCloudTrackType"]);

                Application_Model_Preference::SetSoundCloudLicense($values["preferences_soundcloud"]["SoundCloudLicense"]);

                Application_Model_Preference::SetPhone($values["preferences_support"]["Phone"]);
                Application_Model_Preference::SetEmail($values["preferences_support"]["Email"]);
                Application_Model_Preference::SetStationWebSite($values["preferences_support"]["StationWebSite"]);
                Application_Model_Preference::SetSupportFeedback($values["preferences_support"]["SupportFeedback"]);
                Application_Model_Preference::SetPublicise($values["preferences_support"]["Publicise"]);

                $imagePath = $form->getSubForm('preferences_support')->Logo->getFileName();

                Application_Model_Preference::SetStationCountry($values["preferences_support"]["Country"]);
                Application_Model_Preference::SetStationCity($values["preferences_support"]["City"]);
                Application_Model_Preference::SetStationDescription($values["preferences_support"]["Description"]);
                Application_Model_Preference::SetStationLogo($imagePath);

                $this->view->statusMsg = "<div class='success'>Preferences updated.</div>";

            }
        }
        $this->view->supportFeedback = Application_Model_Preference::GetSupportFeedback();
    	$logo = Application_Model_Preference::GetStationLogo();
		if($logo){
			$this->view->logoImg = $logo;
		}
        $this->view->form = $form;
    }

    public function registerAction(){
    	$request = $this->getRequest();
    	$baseUrl = $request->getBaseUrl();

    	$this->view->headScript()->appendFile($baseUrl.'/js/airtime/preferences/preferences.js','text/javascript');

        $form = new Application_Form_RegisterAirtime();

        if ($request->isPost()) {
            $values = $request->getPost();
            if ($values["Publicise"] == '1' && $form->isValid($values)) {
                Application_Model_Preference::SetHeadTitle($values["stnName"], $this->view);
                Application_Model_Preference::SetPhone($values["Phone"]);
                Application_Model_Preference::SetEmail($values["Email"]);
                Application_Model_Preference::SetStationWebSite($values["StationWebSite"]);
                Application_Model_Preference::SetPublicise($values["Publicise"]);

                $imagePath = $form->Logo->getFileName();

                Application_Model_Preference::SetStationCountry($values["Country"]);
                Application_Model_Preference::SetStationCity($values["City"]);
                Application_Model_Preference::SetStationDescription($values["Description"]);
                Application_Model_Preference::SetStationLogo($imagePath);
            }
            Application_Model_Preference::SetSupportFeedback($values["SupportFeedback"]);
            // unset session
            Zend_Session::namespaceUnset('referrer');
            $this->_redirect('Nowplaying');
        }else{
            $logo = Application_Model_Preference::GetStationLogo();
    		if($logo){
    			$this->view->logoImg = $logo;
    		}

            $this->view->dialog = $form->render($this->view);
        }
    }

    public function remindmeAction()
    {
        // unset session
        Zend_Session::namespaceUnset('referrer');
    	$now = date("Y-m-d H:i:s");
    	Application_Model_Preference::SetRemindMeDate($now);
    	die();
    }

    public function directoryConfigAction()
    {
        $request = $this->getRequest();
        $baseUrl = $request->getBaseUrl();

        $this->view->headScript()->appendFile($baseUrl.'/js/serverbrowse/serverbrowser.js','text/javascript');
        $this->view->headScript()->appendFile($baseUrl.'/js/airtime/preferences/musicdirs.js','text/javascript');

        $watched_dirs_pref = new Application_Form_WatchedDirPreferences();
        $watched_dirs_pref->setWatchedDirs();

        $this->view->form = $watched_dirs_pref;
    }

    public function serverBrowseAction()
    {
        $request = $this->getRequest();
        $path = $request->getParam("path", null);

        $result = array();

        if(is_null($path))
        {
            $element = array();
            $element["name"] = "path should be specified";
            $element["isFolder"] = false;
            $element["isError"] = true;
            $result[$path] = $element;
        }
        else
        {
            $path = $path.'/';
            $handle =  opendir($path);
            while (false !== ($file = readdir($handle))) {
                if ($file != "." && $file != "..") {
                    //only show directories that aren't private.
                    if (is_dir($path.$file) && substr($file, 0, 1) != ".") {
                        $element = array();
                        $element["name"] = $file;
                        $element["isFolder"] = true;
                        $element["isError"] = false;
                        $result[$file] = $element;
                    }
                }
            }
        }
        ksort($result);
        //returns format serverBrowse is looking for.
        die(json_encode($result));
    }

    public function changeStorDirectoryAction()
    {
        $chosen = $this->getRequest()->getParam("dir");
        $element = $this->getRequest()->getParam("element");
        $watched_dirs_form = new Application_Form_WatchedDirPreferences();
        $watched_dirs_form->populate(array('storageFolder' => $chosen));
        $bool = $watched_dirs_form->verifyChosenFolder($element);

        if ($bool === true) {
            MusicDir::setStorDir($chosen);
            $data = array();
            $data["directory"] = $chosen;
            RabbitMq::SendMessageToMediaMonitor("change_stor", $data);
        }

        $watched_dirs_form->setWatchedDirs();

        $this->view->subform = $watched_dirs_form->render();
    }

    public function reloadWatchDirectoryAction()
    {
        $chosen = $this->getRequest()->getParam("dir");
        $element = $this->getRequest()->getParam("element");
        $watched_dirs_form = new Application_Form_WatchedDirPreferences();
        $watched_dirs_form->populate(array('watchedFolder' => $chosen));
        $bool = $watched_dirs_form->verifyChosenFolder($element);

        if ($bool === true) {
            MusicDir::addWatchedDir($chosen);
            $data = array();
            $data["directory"] = $chosen;
            RabbitMq::SendMessageToMediaMonitor("new_watch", $data);
        }

        $watched_dirs_form->setWatchedDirs();

        $this->view->subform = $watched_dirs_form->render();
    }

    public function removeWatchDirectoryAction()
    {
        $chosen = $this->getRequest()->getParam("dir");

        $dir = MusicDir::getDirByPath($chosen);
        $dir->remove();

        $data = array();
        $data["directory"] = $chosen;
        RabbitMq::SendMessageToMediaMonitor("remove_watch", $data);
    }
}



