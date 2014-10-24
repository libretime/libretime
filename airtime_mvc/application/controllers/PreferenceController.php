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
                    ->addActionContext('get-admin-password-status', 'json')
                    ->initContext();
    }

    public function indexAction()
    {
        $CC_CONFIG = Config::getConfig();
        $request = $this->getRequest();
        
        $isSaas = Application_Model_Preference::GetPlanLevel() == 'disabled'?false:true;
        
        $baseUrl = Application_Common_OsPath::getBaseDir();

        $this->view->headScript()->appendFile($baseUrl.'js/airtime/preferences/preferences.js?'.$CC_CONFIG['airtime_version'],'text/javascript');
        $this->view->statusMsg = "";

        $form = new Application_Form_Preferences();
        $values = array();

        if ($request->isPost()) {
            $params = $request->getPost();
            $postData = explode('&', $params['data']);
            foreach($postData as $k=>$v) {
                $v = explode('=', $v);
                $values[$v[0]] = urldecode($v[1]);
            }
            if ($form->isValid($values)) {

                Application_Model_Preference::SetHeadTitle($values["stationName"], $this->view);
                Application_Model_Preference::SetDefaultCrossfadeDuration($values["stationDefaultCrossfadeDuration"]);
                Application_Model_Preference::SetDefaultFadeIn($values["stationDefaultFadeIn"]);
                Application_Model_Preference::SetDefaultFadeOut($values["stationDefaultFadeOut"]);
                Application_Model_Preference::SetAllow3rdPartyApi($values["thirdPartyApi"]);
                Application_Model_Preference::SetDefaultLocale($values["locale"]);
                Application_Model_Preference::SetDefaultTimezone($values["timezone"]);
                Application_Model_Preference::SetWeekStartDay($values["weekStartDay"]);

                Application_Model_Preference::SetEnableSystemEmail($values["enableSystemEmail"]);
                Application_Model_Preference::SetSystemEmail($values["systemEmail"]);
                Application_Model_Preference::SetMailServerConfigured($values["configureMailServer"]);
                Application_Model_Preference::SetMailServer($values["mailServer"]);
                Application_Model_Preference::SetMailServerEmailAddress($values["email"]);
                Application_Model_Preference::SetMailServerPassword($values["ms_password"]);
                Application_Model_Preference::SetMailServerPort($values["port"]);
                Application_Model_Preference::SetMailServerRequiresAuth($values["msRequiresAuth"]);

                Application_Model_Preference::SetAutoUploadRecordedShowToSoundcloud($values["UseSoundCloud"]);
                Application_Model_Preference::SetUploadToSoundcloudOption($values["UploadToSoundcloudOption"]);
                Application_Model_Preference::SetSoundCloudDownloadbleOption($values["SoundCloudDownloadbleOption"]);
                Application_Model_Preference::SetSoundCloudUser($values["SoundCloudUser"]);
                Application_Model_Preference::SetSoundCloudPassword($values["SoundCloudPassword"]);
                Application_Model_Preference::SetSoundCloudTags($values["SoundCloudTags"]);
                Application_Model_Preference::SetSoundCloudGenre($values["SoundCloudGenre"]);
                Application_Model_Preference::SetSoundCloudTrackType($values["SoundCloudTrackType"]);
                Application_Model_Preference::SetSoundCloudLicense($values["SoundCloudLicense"]);

                $this->view->statusMsg = "<div class='success'>". _("Preferences updated.")."</div>";
                $this->view->form = $form;
                $this->_helper->json->sendJson(array("valid"=>"true", "html"=>$this->view->render('preference/index.phtml')));
            } else {
                $this->view->form = $form;
                $this->_helper->json->sendJson(array("valid"=>"false", "html"=>$this->view->render('preference/index.phtml')));
            }
        }
        $this->view->form = $form;
    }

    public function supportSettingAction()
    {
        $CC_CONFIG = Config::getConfig();

        $request = $this->getRequest();

        $baseUrl = Application_Common_OsPath::getBaseDir();

        $this->view->headScript()->appendFile($baseUrl.'js/airtime/preferences/support-setting.js?'.$CC_CONFIG['airtime_version'],'text/javascript');
        $this->view->statusMsg = "";

        $form = new Application_Form_SupportSettings();
        if ($request->isPost()) {
            $values = $request->getPost();

           if ($values["Publicise"] != 1) {
                Application_Model_Preference::SetSupportFeedback($values["SupportFeedback"]);
                Application_Model_Preference::SetPublicise($values["Publicise"]);
                if (isset($values["Privacy"])) {
                    Application_Model_Preference::SetPrivacyPolicyCheck($values["Privacy"]);
                }
            } else if ($form->isValid($values)) {
                Application_Model_Preference::SetHeadTitle($values["stationName"], $this->view);
                Application_Model_Preference::SetPhone($values["Phone"]);
                Application_Model_Preference::SetEmail($values["Email"]);
                Application_Model_Preference::SetStationWebSite($values["StationWebSite"]);
                Application_Model_Preference::SetSupportFeedback($values["SupportFeedback"]);
                Application_Model_Preference::SetPublicise($values["Publicise"]);

                $form->Logo->receive();
                $imagePath = $form->Logo->getFileName();

                Application_Model_Preference::SetStationCountry($values["Country"]);
                Application_Model_Preference::SetStationCity($values["City"]);
                Application_Model_Preference::SetStationDescription($values["Description"]);
                Application_Model_Preference::SetStationLogo($imagePath);
                if (isset($values["Privacy"])) {
                    Application_Model_Preference::SetPrivacyPolicyCheck($values["Privacy"]);
                }
            }
            $this->view->statusMsg = "<div class='success'>"._("Support setting updated.")."</div>";
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
        $this->view->section_title = _('Support Feedback');
        $this->view->form = $form;
    }

    public function directoryConfigAction()
    {
        $CC_CONFIG = Config::getConfig();

        $baseUrl = Application_Common_OsPath::getBaseDir();

        $this->view->headScript()->appendFile($baseUrl.'js/serverbrowse/serverbrowser.js?'.$CC_CONFIG['airtime_version'],'text/javascript');
        $this->view->headScript()->appendFile($baseUrl.'js/airtime/preferences/musicdirs.js?'.$CC_CONFIG['airtime_version'],'text/javascript');

        $watched_dirs_pref = new Application_Form_WatchedDirPreferences();
        $this->view->form = $watched_dirs_pref;
    }

    public function streamSettingAction()
    {
        $CC_CONFIG = Config::getConfig();

        $request = $this->getRequest();

        $baseUrl = Application_Common_OsPath::getBaseDir();

        $this->view->headScript()->appendFile($baseUrl.'js/airtime/preferences/streamsetting.js?'.$CC_CONFIG['airtime_version'],'text/javascript');

        // get current settings
        $temp = Application_Model_StreamSetting::getStreamSetting();
        $setting = array();
        foreach ($temp as $t) {
            $setting[$t['keyname']] = $t['value'];
        }

        $name_map = array(
				'ogg' => 'Ogg Vorbis',
                'fdkaac' => 'AAC+',
                'aac' => 'AAC',
                'opus' => 'Opus',
                'mp3' => 'MP3',
        );

        // get predefined type and bitrate from pref table
        $temp_types = Application_Model_Preference::GetStreamType();
        $stream_types = array();
        foreach ($temp_types as $type) {
            $type = strtolower(trim($type));
            if (isset($name_map[$type])) {
                $name = $name_map[$type];
            } else {
                $name = $type;
            }
            $stream_types[$type] = $name;
        }

        $temp_bitrate = Application_Model_Preference::GetStreamBitrate();
        $max_bitrate = intval(Application_Model_Preference::GetMaxBitrate());
        $stream_bitrates = array();
        foreach ($temp_bitrate as $type) {
            if (intval($type) <= $max_bitrate) {
                $stream_bitrates[trim($type)] = strtoupper(trim($type))." kbit/s";
            }
        }

        $num_of_stream = intval(Application_Model_Preference::GetNumOfStreams());
        $form = new Application_Form_StreamSetting();

        $form->addElement('hash', 'csrf', array(
           'salt' => 'unique'
        ));

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
            $params = $request->getPost();
            /* Parse through post data and put in format
             * $form->isValid() is expecting it in
             */
            $postData = explode('&', $params['data']);
            $s1_data = array();
            $s2_data = array();
            $s3_data = array();
            $values = array();
            foreach($postData as $k=>$v) {
                $v = explode('=', urldecode($v));
                if (strpos($v[0], "s1_data") !== false) {
                    /* In this case $v[0] may be 's1_data[enable]' , for example.
                     * We only want the 'enable' part
                     */
                    preg_match('/\[(.*)\]/', $v[0], $matches);
                    $s1_data[$matches[1]] = $v[1];
                } elseif (strpos($v[0], "s2_data") !== false) {
                    preg_match('/\[(.*)\]/', $v[0], $matches);
                    $s2_data[$matches[1]] = $v[1];
                } elseif (strpos($v[0], "s3_data") !== false) {
                   preg_match('/\[(.*)\]/', $v[0], $matches);
                    $s3_data[$matches[1]] = $v[1];
                } else {
                    $values[$v[0]] = $v[1];
                }
            }
            $values["s1_data"] = $s1_data;
            $values["s2_data"] = $s2_data;
            $values["s3_data"] = $s3_data;

            $error = false;
            if ($form->isValid($values)) {
                    $values['output_sound_device'] = $form->getValue('output_sound_device');
                    $values['output_sound_device_type'] = $form->getValue('output_sound_device_type');

                $values['icecast_vorbis_metadata'] = $form->getValue('icecast_vorbis_metadata');
                $values['streamFormat'] = $form->getValue('streamFormat');

                Application_Model_StreamSetting::setStreamSetting($values);

                /* If the admin password values are empty then we should not
                 * set the pseudo password ('xxxxxx') on the front-end
                 */
                $s1_set_admin_pass = !empty($values["s1_data"]["admin_pass"]);
                $s2_set_admin_pass = !empty($values["s2_data"]["admin_pass"]);
                $s3_set_admin_pass = !empty($values["s3_data"]["admin_pass"]);

                // this goes into cc_pref table
                Application_Model_Preference::SetStreamLabelFormat($values['streamFormat']);
                Application_Model_Preference::SetLiveStreamMasterUsername($values["master_username"]);
                Application_Model_Preference::SetLiveStreamMasterPassword($values["master_password"]);
                Application_Model_Preference::SetDefaultTransitionFade($values["transition_fade"]);
                Application_Model_Preference::SetAutoTransition($values["auto_transition"]);
                Application_Model_Preference::SetAutoSwitch($values["auto_switch"]);
                
                // compare new values with current value
                $changeRGenabled = Application_Model_Preference::GetEnableReplayGain() != $values["enableReplayGain"];
                $changeRGmodifier = Application_Model_Preference::getReplayGainModifier() != $values["replayGainModifier"];
                if ($changeRGenabled || $changeRGmodifier) {
                    Application_Model_Preference::SetEnableReplayGain($values["enableReplayGain"]);
                    Application_Model_Preference::setReplayGainModifier($values["replayGainModifier"]);
                    $md = array('schedule' => Application_Model_Schedule::getSchedule());
                    Application_Model_RabbitMq::SendMessageToPypo("update_schedule", $md);
                    //Application_Model_RabbitMq::PushSchedule();
                }

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
                Application_Model_StreamSetting::setOffAirMeta($values['offAirMeta']);

                // store stream update timestamp
                Application_Model_Preference::SetStreamUpdateTimestamp();

                $data = array();
                $info = Application_Model_StreamSetting::getStreamSetting();
                $data['setting'] = $info;
                for ($i=1; $i<=$num_of_stream; $i++) {
                    Application_Model_StreamSetting::setLiquidsoapError($i, "waiting");
                }

                Application_Model_RabbitMq::SendMessageToPypo("update_stream_setting", $data);

                $live_stream_subform->updateVariables();
                $this->view->enable_stream_conf = Application_Model_Preference::GetEnableStreamConf();
                $this->view->form = $form;
                $this->view->num_stream = $num_of_stream;
                $this->view->statusMsg = "<div class='success'>"._("Stream Setting Updated.")."</div>";
                $this->_helper->json->sendJson(array(
                    "valid"=>"true",
                    "html"=>$this->view->render('preference/stream-setting.phtml'),
                    "s1_set_admin_pass"=>$s1_set_admin_pass,
                    "s2_set_admin_pass"=>$s2_set_admin_pass,
                    "s3_set_admin_pass"=>$s3_set_admin_pass,
                ));
            } else {
                $live_stream_subform->updateVariables();
                $this->view->enable_stream_conf = Application_Model_Preference::GetEnableStreamConf();
                $this->view->form = $form;
                $this->view->num_stream = $num_of_stream;
                $this->_helper->json->sendJson(array("valid"=>"false", "html"=>$this->view->render('preference/stream-setting.phtml')));
            }
        }

        $live_stream_subform->updateVariables();

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
            $element["name"] = _("path should be specified");
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
        $this->_helper->json->sendJson($result);
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
        $dir_path = $this->getRequest()->getParam('dir');
        $dir = Application_Model_MusicDir::getDirByPath($dir_path);
        $data = array( 'directory' => $dir->getDirectory(),
                              'id' => $dir->getId());
        Application_Model_RabbitMq::SendMessageToMediaMonitor('rescan_watch', $data);
        Logging::info("Unhiding all files belonging to:: $dir_path");
        $dir->unhideFiles();
        $this->_helper->json->sendJson(null);
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
        $this->_helper->json->sendJson($res);
    }

    public function getLiquidsoapStatusAction()
    {
        $out = array();
        $num_of_stream = intval(Application_Model_Preference::GetNumOfStreams());
        for ($i=1; $i<=$num_of_stream; $i++) {
            $status = Application_Model_StreamSetting::getLiquidsoapError($i);
            $status = $status == NULL?_("Problem with Liquidsoap..."):$status;
            if (!Application_Model_StreamSetting::getStreamEnabled($i)) {
                $status = "N/A";
            }
            $out[] = array("id"=>$i, "status"=>$status);
        }
        $this->_helper->json->sendJson($out);
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

        $this->_helper->json->sendJson(null);
    }

    public function getAdminPasswordStatusAction()
    {
        $out = array();
        for ($i=1; $i<=3; $i++) {
            if (Application_Model_StreamSetting::getAdminPass('s'.$i)=='') {
                $out["s".$i] = false;
            } else {
                $out["s".$i] = true;
            }
        }
        $this->_helper->json->sendJson($out);
    }
}
