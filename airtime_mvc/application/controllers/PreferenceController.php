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
                    ->initContext();
    }

    public function indexAction()
    {
        global $CC_CONFIG;

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
                if (!$isSass && $values["Publicise"] != 1){
                    Application_Model_Preference::SetSupportFeedback($values["SupportFeedback"]);
                    if(isset($values["Privacy"])){
                        Application_Model_Preference::SetPrivacyPolicyCheck($values["Privacy"]);
                    }
                }else{
                    Application_Model_Preference::SetHeadTitle($values["stationName"], $this->view);
                    Application_Model_Preference::SetPhone($values["Phone"]);
                    Application_Model_Preference::SetEmail($values["Email"]);
                    Application_Model_Preference::SetStationWebSite($values["StationWebSite"]);
                    if(!$isSass){
                        Application_Model_Preference::SetSupportFeedback($values["SupportFeedback"]);
                        Application_Model_Preference::SetPublicise($values["Publicise"]);
                    }

                    $form->Logo->receive();
                    $imagePath = $form->Logo->getFileName();

                    Application_Model_Preference::SetStationCountry($values["Country"]);
                    Application_Model_Preference::SetStationCity($values["City"]);
                    Application_Model_Preference::SetStationDescription($values["Description"]);
                    Application_Model_Preference::SetStationLogo($imagePath);
                    if(!$isSass && isset($values["Privacy"])){
                        Application_Model_Preference::SetPrivacyPolicyCheck($values["Privacy"]);
                    }
                }
                $this->view->statusMsg = "<div class='success'>Support setting updated.</div>";
            }
        }
        $logo = Application_Model_Preference::GetStationLogo();
        if($logo){
            $this->view->logoImg = $logo;
        }
        $privacyChecked = false;
        if(Application_Model_Preference::GetPrivacyPolicyCheck() == 1){
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

        if(Application_Model_Preference::GetPlanLevel() == 'disabled'){
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
        $this->view->headScript()->appendFile($baseUrl.'/js/meioMask/jquery.meio.mask.js?'.$CC_CONFIG['airtime_version'],'text/javascript');

        // get current settings
        $temp = Application_Model_StreamSetting::getStreamSetting();
        $setting = array();
        foreach ($temp as $t){
            $setting[$t['keyname']] = $t['value'];
        }

        // get predefined type and bitrate from pref table
        $temp_types = Application_Model_Preference::GetStreamType();
        $stream_types = array();
        foreach ($temp_types as $type){
            if(trim($type) == "ogg"){
                $temp = "OGG/VORBIS";
            }else{
                $temp = strtoupper(trim($type));
            }
            $stream_types[trim($type)] = $temp;
        }

        $temp_bitrate = Application_Model_Preference::GetStreamBitrate();
        $max_bitrate = intval(Application_Model_Preference::GetMaxBitrate());
        $stream_bitrates = array();
        foreach ($temp_bitrate as $type){
            if(intval($type) <= $max_bitrate){
                $stream_bitrates[trim($type)] = strtoupper(trim($type))." Kbit/s";
            }
        }

        $num_of_stream = intval(Application_Model_Preference::GetNumOfStreams());
        $form = new Application_Form_StreamSetting();

        $form->setSetting($setting);
        $form->startFrom();
        
        $live_stream_subform = new Application_Form_LiveStreamingPreferences();
        $form->addSubForm($live_stream_subform, "live_stream_subform");

        for($i=1; $i<=$num_of_stream; $i++){
            $subform = new Application_Form_StreamSettingSubForm();
            $subform->setPrefix($i);
            $subform->setSetting($setting);
            $subform->setStreamTypes($stream_types);
            $subform->setStreamBitrates($stream_bitrates);
            $subform->startForm();
            $form->addSubForm($subform, "s".$i."_subform");
        }
        if ($request->isPost()) {
            $post_data = $request->getPost();

            $error = false;
            $values = $post_data;
            
            if($form->isValid($post_data)){
                if(Application_Model_Preference::GetPlanLevel() == 'disabled'){
                    $values['output_sound_device'] = $form->getValue('output_sound_device');
                }


                $values['icecast_vorbis_metadata'] = $form->getValue('icecast_vorbis_metadata');
                $values['output_sound_device_type'] = $form->getValue('output_sound_device_type');
                $values['streamFormat'] = $form->getValue('streamFormat');

                Application_Model_StreamSetting::setStreamSetting($values);
                $data = array();
                $data['setting'] = Application_Model_StreamSetting::getStreamSetting();
                $data['setting']['harbor_input_port'] = $values["harbor_input_port"];
                $data['setting']['harbor_input_mount_point'] = $values["harbor_input_mount_point"];
                for($i=1;$i<=$num_of_stream;$i++){
                    Application_Model_StreamSetting::setLiquidsoapError($i, "waiting");
                }
                // this goes into cc_pref table
                Application_Model_Preference::SetStreamLabelFormat($values['streamFormat']);
                Application_Model_Preference::SetLiveSteamAutoEnable($values["auto_enable_live_stream"]);
                Application_Model_Preference::SetLiveSteamMasterUsername($values["master_username"]);
                Application_Model_Preference::SetLiveSteamMasterPassword($values["master_password"]);
                Application_Model_Preference::SetLiveSteamPort($values["harbor_input_port"]);
                Application_Model_Preference::SetLiveSteamMountPoint($values["harbor_input_mount_point"]);
                // store stream update timestamp
                Application_Model_Preference::SetStreamUpdateTimestamp();
                Application_Model_RabbitMq::SendMessageToPypo("update_stream_setting", $data);
                $this->view->statusMsg = "<div class='success'>Stream Setting Updated.</div>";
            }
        }
        $this->view->num_stream = $num_of_stream;
        $this->view->enable_stream_conf = Application_Model_Preference::GetEnableStreamConf();
        $this->view->form = $form;
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

        $res = Application_Model_MusicDir::setStorDir($chosen);
        if($res['code'] != 0){
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
        if($res['code'] != 0){
            $watched_dirs_form->populate(array('watchedFolder' => $chosen));
            $watched_dirs_form->getElement($element)->setErrors(array($res['error']));
        }

        $this->view->subform = $watched_dirs_form->render();
    }

    public function removeWatchDirectoryAction()
    {
        $chosen = $this->getRequest()->getParam("dir");

        $dir = Application_Model_MusicDir::removeWatchedDir($chosen);

        $watched_dirs_form = new Application_Form_WatchedDirPreferences();
        $this->view->subform = $watched_dirs_form->render();
    }

    public function isImportInProgressAction(){
        $now = time();
        $res = false;
        if(Application_Model_Preference::GetImportTimestamp()+10 > $now){
            $res = true;
        }
        die(json_encode($res));
    }

    public function getLiquidsoapStatusAction(){
        $out = array();
        $num_of_stream = intval(Application_Model_Preference::GetNumOfStreams());
        for($i=1; $i<=$num_of_stream; $i++){
            $status = Application_Model_StreamSetting::getLiquidsoapError($i);
            $status = $status == NULL?"Problem with Liquidsoap...":$status;
            if(!Application_Model_StreamSetting::getStreamEnabled($i)){
                $status = "N/A";
            }
            $out[] = array("id"=>$i, "status"=>$status);
        }
        die(json_encode($out));
    }
}



