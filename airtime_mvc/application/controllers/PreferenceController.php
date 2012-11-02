<?php

class PreferenceController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
        $ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext->addActionContext('server-browse', 'json')
                    ->addActionContext('change-stor-directory', 'json')
                    ->addActionContext('reload-watch-directory', 'json')
                    ->addActionContext('remove-watch-directory', 'json')
                    ->addActionContext('is-import-in-progress', 'json')
                    ->addActionContext('change-stream-setting', 'json')
                    ->addActionContext('get-liquidsoap-status', 'json')
                    ->addActionContext('set-source-connection-url', 'json')
                    ->initContext();
    }

    public function indexAction()
    {
        global $CC_CONFIG;

        $isSaas = Application_Model_Preference::GetPlanLevel() == 'disabled'?false:true;

        $request = $this->getRequest();
        $baseUrl = $request->getBaseUrl();

        $this->view->headScript()->appendFile($baseUrl.'/js/airtime/preferences/preferences.js?'.$CC_CONFIG['airtime_version'],'text/javascript');
        $this->view->statusMsg = "";

        $form = new Application_Form_Preferences();

        if ($request->isPost()) {
            if ($form->isValid($request->getPost())) {
                $values = $form->getValues();

                Application_Model_Preference::SetHeadTitle($values["preferences_general"]["stationName"], $this->view);
                Application_Model_Preference::SetDefaultFade($values["preferences_general"]["stationDefaultFade"]);
                Application_Model_Preference::SetAllow3rdPartyApi($values["preferences_general"]["thirdPartyApi"]);
                Application_Model_Preference::SetTimezone($values["preferences_general"]["timezone"]);
                Application_Model_Preference::SetWeekStartDay($values["preferences_general"]["weekStartDay"]);

                if (!$isSaas) {
                    Application_Model_Preference::SetEnableSystemEmail($values["preferences_email_server"]["enableSystemEmail"]);
                    Application_Model_Preference::SetSystemEmail($values["preferences_email_server"]["systemEmail"]);
                    Application_Model_Preference::SetMailServerConfigured($values["preferences_email_server"]["configureMailServer"]);
                    Application_Model_Preference::SetMailServer($values["preferences_email_server"]["mailServer"]);
                    Application_Model_Preference::SetMailServerEmailAddress($values["preferences_email_server"]["email"]);
                    Application_Model_Preference::SetMailServerPassword($values["preferences_email_server"]["ms_password"]);
                    Application_Model_Preference::SetMailServerPort($values["preferences_email_server"]["port"]);
                    Application_Model_Preference::SetMailServerRequiresAuth($values["preferences_email_server"]["msRequiresAuth"]);
                }

                Application_Model_Preference::SetAutoUploadRecordedShowToSoundcloud($values["preferences_soundcloud"]["UseSoundCloud"]);
                Application_Model_Preference::SetUploadToSoundcloudOption($values["preferences_soundcloud"]["UploadToSoundcloudOption"]);
                Application_Model_Preference::SetSoundCloudDownloadbleOption($values["preferences_soundcloud"]["SoundCloudDownloadbleOption"]);
                Application_Model_Preference::SetSoundCloudUser($values["preferences_soundcloud"]["SoundCloudUser"]);
                Application_Model_Preference::SetSoundCloudPassword($values["preferences_soundcloud"]["SoundCloudPassword"]);
                Application_Model_Preference::SetSoundCloudTags($values["preferences_soundcloud"]["SoundCloudTags"]);
                Application_Model_Preference::SetSoundCloudGenre($values["preferences_soundcloud"]["SoundCloudGenre"]);
                Application_Model_Preference::SetSoundCloudTrackType($values["preferences_soundcloud"]["SoundCloudTrackType"]);
                Application_Model_Preference::SetSoundCloudLicense($values["preferences_soundcloud"]["SoundCloudLicense"]);

                $this->view->statusMsg = "<div class='success'>Preferences updated.</div>";
            }
        }
        $this->view->form = $form;
    }

    public function supportSettingAction()
    {
        global $CC_CONFIG;

        $request = $this->getRequest();
        $baseUrl = $request->getBaseUrl();

        $this->view->headScript()->appendFile($baseUrl.'/js/airtime/preferences/support-setting.js?'.$CC_CONFIG['airtime_version'],'text/javascript');
        $this->view->statusMsg = "";

        $isSass = Application_Model_Preference::GetPlanLevel() == 'disabled'?false:true;

        $form = new Application_Form_SupportSettings();
        if ($request->isPost()) {
            $values = $request->getPost();
            if ($form->isValid($values)) {
                if (!$isSass && $values["Publicise"] != 1) {
                    Application_Model_Preference::SetSupportFeedback($values["SupportFeedback"]);
                    Application_Model_Preference::SetPublicise($values["Publicise"]);
                    if (isset($values["Privacy"])) {
                        Application_Model_Preference::SetPrivacyPolicyCheck($values["Privacy"]);
                    }
                } else {
                    Application_Model_Preference::SetHeadTitle($values["stationName"], $this->view);
                    Application_Model_Preference::SetPhone($values["Phone"]);
                    Application_Model_Preference::SetEmail($values["Email"]);
                    Application_Model_Preference::SetStationWebSite($values["StationWebSite"]);
                    if (!$isSass) {
                        Application_Model_Preference::SetSupportFeedback($values["SupportFeedback"]);
                        Application_Model_Preference::SetPublicise($values["Publicise"]);
                    }

                    $form->Logo->receive();
                    $imagePath = $form->Logo->getFileName();

                    Application_Model_Preference::SetStationCountry($values["Country"]);
                    Application_Model_Preference::SetStationCity($values["City"]);
                    Application_Model_Preference::SetStationDescription($values["Description"]);
                    Application_Model_Preference::SetStationLogo($imagePath);
                    if (!$isSass && isset($values["Privacy"])) {
                        Application_Model_Preference::SetPrivacyPolicyCheck($values["Privacy"]);
                    }
                }
                $this->view->statusMsg = "<div class='success'>Support setting updated.</div>";
            }
        }
        $logo = Application_Model_Preference::GetStationLogo();
        if ($logo) {
            $this->view->logoImg = $logo;
        }
        $privacyChecked = false;
        if (Application_Model_Preference::GetPrivacyPolicyCheck() == 1) {
            $privacyChecked = true;
        }
        $this->view->privacyChecked = $privacyChecked;
        $this->view->section_title = 'Support Feedback';
        $this->view->form = $form;
        //$form->render($this->view);
    }

    public function directoryConfigAction()
    {
        global $CC_CONFIG;

        if (Application_Model_Preference::GetPlanLevel() == 'disabled') {
            $request = $this->getRequest();
            $baseUrl = $request->getBaseUrl();

            $this->view->headScript()->appendFile($baseUrl.'/js/serverbrowse/serverbrowser.js?'.$CC_CONFIG['airtime_version'],'text/javascript');
            $this->view->headScript()->appendFile($baseUrl.'/js/airtime/preferences/musicdirs.js?'.$CC_CONFIG['airtime_version'],'text/javascript');

            $watched_dirs_pref = new Application_Form_WatchedDirPreferences();

            $this->view->form = $watched_dirs_pref;
        }
    }

    public function streamSettingAction()
    {
        global $CC_CONFIG;

        $request = $this->getRequest();
        $baseUrl = $request->getBaseUrl();

        $this->view->headScript()->appendFile($baseUrl.'/js/airtime/preferences/streamsetting.js?'.$CC_CONFIG['airtime_version'],'text/javascript');

        $isSaas = Application_Model_Preference::GetPlanLevel() == 'disabled'?false:true;

        // get current settings
        $temp = Application_Model_StreamSetting::getStreamSetting();
        $setting = array();
        foreach ($temp as $t) {
            $setting[$t['keyname']] = $t['value'];
        }
        // get predefined type and bitrate from pref table
        $temp_types = Application_Model_Preference::GetStreamType();
        $stream_types = array();
        foreach ($temp_types as $type) {
            if (trim($type) == "ogg") {
                $temp = "OGG/VORBIS";
            } else {
                $temp = strtoupper(trim($type));
            }
            $stream_types[trim($type)] = $temp;
        }

        $temp_bitrate = Application_Model_Preference::GetStreamBitrate();
        $max_bitrate = intval(Application_Model_Preference::GetMaxBitrate());
        $stream_bitrates = array();
        foreach ($temp_bitrate as $type) {
            if (intval($type) <= $max_bitrate) {
                $stream_bitrates[trim($type)] = strtoupper(trim($type))." Kbit/s";
            }
        }

        $num_of_stream = intval(Application_Model_Preference::GetNumOfStreams());
        $form = new Application_Form_StreamSetting();

        $form->setSetting($setting);
        $form->startFrom();

        $live_stream_subform = new Application_Form_LiveStreamingPreferences();
        $form->addSubForm($live_stream_subform, "live_stream_subform");

        for ($i=1; $i<=$num_of_stream; $i++) {
            $subform = new Application_Form_StreamSettingSubForm();
            $subform->setPrefix($i);
            $subform->setSetting($setting);
            $subform->setStreamTypes($stream_types);
            $subform->setStreamBitrates($stream_bitrates);
            $subform->startForm();
            $form->addSubForm($subform, "s".$i."_subform");
        }
        if ($request->isPost()) {
            $values = $request->getPost();

            $error = false;

            if ($form->isValid($values)) {
                if (!$isSaas) {
                    $values['output_sound_device'] = $form->getValue('output_sound_device');
                    $values['output_sound_device_type'] = $form->getValue('output_sound_device_type');
                }

                $values['icecast_vorbis_metadata'] = $form->getValue('icecast_vorbis_metadata');
                $values['streamFormat'] = $form->getValue('streamFormat');

                Application_Model_StreamSetting::setStreamSetting($values);

                // this goes into cc_pref table
                Application_Model_Preference::SetStreamLabelFormat($values['streamFormat']);
                Application_Model_Preference::SetLiveStreamMasterUsername($values["master_username"]);
                Application_Model_Preference::SetLiveStreamMasterPassword($values["master_password"]);
                Application_Model_Preference::SetDefaultTransitionFade($values["transition_fade"]);
                Application_Model_Preference::SetAutoTransition($values["auto_transition"]);
                Application_Model_Preference::SetAutoSwitch($values["auto_switch"]);

                if (!$isSaas) {
                    if (!Application_Model_Preference::GetMasterDjConnectionUrlOverride()) {
                        $master_connection_url = "http://".$_SERVER['SERVER_NAME'].":".$values["master_harbor_input_port"]."/".$values["master_harbor_input_mount_point"];
                        if (empty($values["master_harbor_input_port"]) || empty($values["master_harbor_input_mount_point"])) {
                            Application_Model_Preference::SetMasterDJSourceConnectionURL('N/A');
                        } else {
                            Application_Model_Preference::SetMasterDJSourceConnectionURL($master_connection_url);
                        }
                    } else {
                        Application_Model_Preference::SetMasterDJSourceConnectionURL($values["master_dj_connection_url"]);
                    }

                    if (!Application_Model_Preference::GetLiveDjConnectionUrlOverride()) {
                        $live_connection_url = "http://".$_SERVER['SERVER_NAME'].":".$values["dj_harbor_input_port"]."/".$values["dj_harbor_input_mount_point"];
                        if (empty($values["dj_harbor_input_port"]) || empty($values["dj_harbor_input_mount_point"])) {
                            Application_Model_Preference::SetLiveDJSourceConnectionURL('N/A');
                        } else {
                            Application_Model_Preference::SetLiveDJSourceConnectionURL($live_connection_url);
                        }
                    } else {
                        Application_Model_Preference::SetLiveDJSourceConnectionURL($values["live_dj_connection_url"]);
                    }

                    // extra info that goes into cc_stream_setting
                    Application_Model_StreamSetting::setMasterLiveStreamPort($values["master_harbor_input_port"]);
                    Application_Model_StreamSetting::setMasterLiveStreamMountPoint($values["master_harbor_input_mount_point"]);
                    Application_Model_StreamSetting::setDjLiveStreamPort($values["dj_harbor_input_port"]);
                    Application_Model_StreamSetting::setDjLiveStreamMountPoint($values["dj_harbor_input_mount_point"]);
                }

                // store stream update timestamp
                Application_Model_Preference::SetStreamUpdateTimestamp();

                $data = array();
                $info = Application_Model_StreamSetting::getStreamSetting();
                $data['setting'] = $info;
                for ($i=1; $i<=$num_of_stream; $i++) {
                    Application_Model_StreamSetting::setLiquidsoapError($i, "waiting");
                }

                Application_Model_RabbitMq::SendMessageToPypo("update_stream_setting", $data);
                $this->view->statusMsg = "<div class='success'>Stream Setting Updated.</div>";
            }
        }

        $live_stream_subform->updateVariables();
        $this->view->confirm_pypo_restart_text = "If you change the username or password values for an enabled stream the playout engine will be rebooted and your listeners will hear silence for 5-10 seconds. Changing the following fields will NOT cause a reboot: Stream Label (Global Settings), and Switch Transition Fade(s), Master Username, and Master Password (Input Stream Settings). If Airtime is recording, and if the change causes a playout engine restart, the recording will be interrupted.";

        $this->view->num_stream = $num_of_stream;
        $this->view->enable_stream_conf = Application_Model_Preference::GetEnableStreamConf();
        $this->view->form = $form;
    }

    public function serverBrowseAction()
    {
        $request = $this->getRequest();
        $path = $request->getParam("path", null);

        $result = array();

        if (is_null($path)) {
            $element = array();
            $element["name"] = "path should be specified";
            $element["isFolder"] = false;
            $element["isError"] = true;
            $result[$path] = $element;
        } else {
            $path = $path.'/';
            $handle = opendir($path);
            if ($handle !== false) {
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

        $res = Application_Model_MusicDir::setStorDir($chosen);
        if ($res['code'] != 0) {
            $watched_dirs_form->populate(array('storageFolder' => $chosen));
            $watched_dirs_form->getElement($element)->setErrors(array($res['error']));
        }

        $this->view->subform = $watched_dirs_form->render();
    }

    public function reloadWatchDirectoryAction()
    {
        $chosen = $this->getRequest()->getParam("dir");
        $element = $this->getRequest()->getParam("element");
        $watched_dirs_form = new Application_Form_WatchedDirPreferences();

        $res = Application_Model_MusicDir::addWatchedDir($chosen);
        if ($res['code'] != 0) {
            $watched_dirs_form->populate(array('watchedFolder' => $chosen));
            $watched_dirs_form->getElement($element)->setErrors(array($res['error']));
        }

        $this->view->subform = $watched_dirs_form->render();
    }

    public function rescanWatchDirectoryAction()
    {
        $dir = Application_Model_MusicDir::getDirByPath($this->getRequest()->getParam("dir"));
        $data = array( 'directory' => $dir->getDirectory(),
            'id' => $dir->getId());
        Application_Model_RabbitMq::SendMessageToMediaMonitor('rescan_watch', $data);
        die(); # Get rid of this ugliness later
    }

    public function removeWatchDirectoryAction()
    {
        $chosen = $this->getRequest()->getParam("dir");

        $dir = Application_Model_MusicDir::removeWatchedDir($chosen);

        $watched_dirs_form = new Application_Form_WatchedDirPreferences();
        $this->view->subform = $watched_dirs_form->render();
    }

    public function isImportInProgressAction()
    {
        $now = time();
        $res = false;
        if (Application_Model_Preference::GetImportTimestamp()+10 > $now) {
            $res = true;
        }
        die(json_encode($res));
    }

    public function getLiquidsoapStatusAction()
    {
        $out = array();
        $num_of_stream = intval(Application_Model_Preference::GetNumOfStreams());
        for ($i=1; $i<=$num_of_stream; $i++) {
            $status = Application_Model_StreamSetting::getLiquidsoapError($i);
            $status = $status == NULL?"Problem with Liquidsoap...":$status;
            if (!Application_Model_StreamSetting::getStreamEnabled($i)) {
                $status = "N/A";
            }
            $out[] = array("id"=>$i, "status"=>$status);
        }
        die(json_encode($out));
    }

    public function setSourceConnectionUrlAction()
    {
        $request = $this->getRequest();
        $type = $request->getParam("type", null);
        $url = urldecode($request->getParam("url", null));
        $override = $request->getParam("override", false);

        if ($type == 'masterdj') {
            Application_Model_Preference::SetMasterDJSourceConnectionURL($url);
            Application_Model_Preference::SetMasterDjConnectionUrlOverride($override);
        } elseif ($type == 'livedj') {
            Application_Model_Preference::SetLiveDJSourceConnectionURL($url);
            Application_Model_Preference::SetLiveDjConnectionUrlOverride($override);
        }

        die();
    }
}
