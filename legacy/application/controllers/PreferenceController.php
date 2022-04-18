<?php

class PreferenceController extends Zend_Controller_Action
{
    public function init()
    {
        // Initialize action controller here
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

        Zend_Layout::getMvcInstance()->assign('parent_page', 'Settings');

        $baseUrl = Application_Common_OsPath::getBaseDir();

        $this->view->headScript()->appendFile($baseUrl . 'js/airtime/preferences/preferences.js?' . $CC_CONFIG['airtime_version'], 'text/javascript');
        $this->view->statusMsg = '';

        $form = new Application_Form_Preferences();
        $values = [];

        SessionHelper::reopenSessionForWriting();

        if ($request->isPost()) {
            $values = $request->getPost();
            if ($form->isValid($values)) {
                Application_Model_Preference::SetHeadTitle($values['stationName'], $this->view);
                Application_Model_Preference::SetStationDescription($values['stationDescription']);
                Application_Model_Preference::SetTrackTypeDefault($values['tracktypeDefault']);
                Application_Model_Preference::SetDefaultCrossfadeDuration($values['stationDefaultCrossfadeDuration']);
                Application_Model_Preference::SetDefaultFadeIn($values['stationDefaultFadeIn']);
                Application_Model_Preference::SetDefaultFadeOut($values['stationDefaultFadeOut']);
                Application_Model_Preference::SetPodcastAlbumOverride($values['podcastAlbumOverride']);
                Application_Model_Preference::SetPodcastAutoSmartblock($values['podcastAutoSmartblock']);
                Application_Model_Preference::SetIntroPlaylist($values['introPlaylistSelect']);
                Application_Model_Preference::SetOutroPlaylist($values['outroPlaylistSelect']);
                Application_Model_Preference::SetAllow3rdPartyApi($values['thirdPartyApi']);
                Application_Model_Preference::SetAllowedCorsUrls($values['allowedCorsUrls']);
                Application_Model_Preference::SetDefaultLocale($values['locale']);
                Application_Model_Preference::SetDefaultTimezone($values['timezone']);
                Application_Model_Preference::SetWeekStartDay($values['weekStartDay']);
                Application_Model_Preference::setRadioPageDisplayLoginButton($values['radioPageLoginButton']);
                Application_Model_Preference::SetFeaturePreviewMode($values['featurePreviewMode']);

                $logoUploadElement = $form->getSubForm('preferences_general')->getElement('stationLogo');
                $logoUploadElement->receive();
                $imagePath = $logoUploadElement->getFileName();

                // Only update the image logo if the new logo is non-empty
                if (!empty($imagePath) && $imagePath != '') {
                    Application_Model_Preference::SetStationLogo($imagePath);
                }

                Application_Model_Preference::setTuneinEnabled($values['enable_tunein']);
                Application_Model_Preference::setTuneinStationId($values['tunein_station_id']);
                Application_Model_Preference::setTuneinPartnerKey($values['tunein_partner_key']);
                Application_Model_Preference::setTuneinPartnerId($values['tunein_partner_id']);

                $this->view->statusMsg = "<div class='success'>" . _('Preferences updated.') . '</div>';
                $form = new Application_Form_Preferences();
                $this->view->form = $form;
            // $this->_helper->json->sendJson(array("valid"=>"true", "html"=>$this->view->render('preference/index.phtml')));
            } else {
                $this->view->form = $form;
                // $this->_helper->json->sendJson(array("valid"=>"false", "html"=>$this->view->render('preference/index.phtml')));
            }
        }
        $this->view->logoImg = Application_Model_Preference::GetStationLogo();

        $this->view->form = $form;
    }

    public function stationPodcastSettingsAction()
    {
        $this->view->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $values = json_decode($this->getRequest()->getRawBody());

        if (!Application_Model_Preference::getStationPodcastPrivacy() && $values->stationPodcastPrivacy == 1) {
            // Refresh the download key when enabling privacy
            Application_Model_Preference::setStationPodcastDownloadKey();
        }

        // Append sharing token (download key) to Station podcast URL
        $stationPodcast = PodcastQuery::create()->findOneByDbId(Application_Model_Preference::getStationPodcastId());
        $key = Application_Model_Preference::getStationPodcastDownloadKey();
        $url = Application_Common_HTTPHelper::getStationUrl() .
            (((int) $values->stationPodcastPrivacy) ? "feeds/station-rss?sharing_token={$key}" : 'feeds/station-rss');
        $stationPodcast->setDbUrl($url)->save();
        Application_Model_Preference::setStationPodcastPrivacy($values->stationPodcastPrivacy);

        $this->_helper->json->sendJson(['url' => $url]);
    }

    public function directoryConfigAction()
    {
    }

    public function removeLogoAction()
    {
        SessionHelper::reopenSessionForWriting();

        $this->view->layout()->disableLayout();
        // Remove reliance on .phtml files to render requests
        $this->_helper->viewRenderer->setNoRender(true);

        if (!SecurityHelper::verifyCSRFToken($this->_getParam('csrf_token'))) {
            Logging::error(__FILE__ . ': Invalid CSRF token');
            $this->_helper->json->sendJson(['jsonrpc' => '2.0', 'valid' => false, 'error' => 'CSRF token did not match.']);

            return;
        }

        Application_Model_Preference::SetStationLogo('');
    }

    public function streamSettingAction()
    {
        $CC_CONFIG = Config::getConfig();

        $request = $this->getRequest();

        Zend_Layout::getMvcInstance()->assign('parent_page', 'Settings');

        $baseUrl = Application_Common_OsPath::getBaseDir();

        $this->view->headScript()->appendFile($baseUrl . 'js/airtime/preferences/streamsetting.js?' . $CC_CONFIG['airtime_version'], 'text/javascript');

        SessionHelper::reopenSessionForWriting();

        $name_map = [
            'ogg' => 'Ogg Vorbis',
            'fdkaac' => 'AAC+',
            'aac' => 'AAC',
            'opus' => 'Opus',
            'mp3' => 'MP3',
        ];

        $num_of_stream = intval(Application_Model_Preference::GetNumOfStreams());
        $form = new Application_Form_StreamSetting();

        $csrf_namespace = new Zend_Session_Namespace('csrf_namespace');
        $csrf_element = new Zend_Form_Element_Hidden('csrf');
        $csrf_element->setValue($csrf_namespace->authtoken)->setRequired('true')->removeDecorator('HtmlTag')->removeDecorator('Label');
        $form->addElement($csrf_element);

        $live_stream_subform = new Application_Form_LiveStreamingPreferences();
        $form->addSubForm($live_stream_subform, 'live_stream_subform');

        // get predefined type and bitrate from pref table
        $temp_types = Application_Model_Preference::GetStreamType();
        $stream_types = [];
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
        $stream_bitrates = [];
        foreach ($temp_bitrate as $type) {
            if (intval($type) <= $max_bitrate) {
                $stream_bitrates[trim($type)] = strtoupper(trim($type)) . ' kbit/s';
            }
        }

        // get current settings
        $setting = Application_Model_StreamSetting::getStreamSetting();
        $form->setSetting($setting);

        for ($i = 1; $i <= $num_of_stream; ++$i) {
            $subform = new Application_Form_StreamSettingSubForm();
            $subform->setPrefix($i);
            $subform->setSetting($setting);
            $subform->setStreamTypes($stream_types);
            $subform->setStreamBitrates($stream_bitrates);
            $subform->startForm();
            $subform->toggleState();
            $form->addSubForm($subform, 's' . $i . '_subform');
        }

        $live_stream_subform->updateVariables();
        $form->startFrom();

        if ($request->isPost()) {
            $params = $request->getPost();
            /* Parse through post data and put in format
             * $form->isValid() is expecting it in
             */
            $postData = explode('&', $params['data']);
            $s1_data = [];
            $s2_data = [];
            $s3_data = [];
            $s4_data = [];
            $values = [];
            foreach ($postData as $k => $v) {
                $v = explode('=', urldecode($v));
                if (strpos($v[0], 's1_data') !== false) {
                    /* In this case $v[0] may be 's1_data[enable]' , for example.
                     * We only want the 'enable' part
                     */
                    preg_match('/\[(.*)\]/', $v[0], $matches);
                    $s1_data[$matches[1]] = $v[1];
                } elseif (strpos($v[0], 's2_data') !== false) {
                    preg_match('/\[(.*)\]/', $v[0], $matches);
                    $s2_data[$matches[1]] = $v[1];
                } elseif (strpos($v[0], 's3_data') !== false) {
                    preg_match('/\[(.*)\]/', $v[0], $matches);
                    $s3_data[$matches[1]] = $v[1];
                } elseif (strpos($v[0], 's4_data') !== false) {
                    preg_match('/\[(.*)\]/', $v[0], $matches);
                    $s4_data[$matches[1]] = $v[1];
                } else {
                    $values[$v[0]] = $v[1];
                }
            }
            $values['s1_data'] = $s1_data;
            $values['s2_data'] = $s2_data;
            $values['s3_data'] = $s3_data;
            $values['s4_data'] = $s4_data;

            if ($form->isValid($values)) {
                Application_Model_StreamSetting::setStreamSetting($values);

                /* If the admin password values are empty then we should not
                 * set the pseudo password ('xxxxxx') on the front-end
                 */
                $s1_set_admin_pass = !empty($values['s1_data']['admin_pass']);
                $s2_set_admin_pass = !empty($values['s2_data']['admin_pass']);
                $s3_set_admin_pass = !empty($values['s3_data']['admin_pass']);
                $s4_set_admin_pass = !empty($values['s4_data']['admin_pass']);

                // this goes into cc_pref table
                $this->setStreamPreferences($values);

                // compare new values with current value
                $changeRGenabled = Application_Model_Preference::GetEnableReplayGain() != $values['enableReplayGain'];
                $changeRGmodifier = Application_Model_Preference::getReplayGainModifier() != $values['replayGainModifier'];
                if ($changeRGenabled || $changeRGmodifier) {
                    Application_Model_Preference::SetEnableReplayGain($values['enableReplayGain']);
                    Application_Model_Preference::setReplayGainModifier($values['replayGainModifier']);
                    $md = ['schedule' => Application_Model_Schedule::getSchedule()];
                    Application_Model_RabbitMq::SendMessageToPypo('update_schedule', $md);
                    // Application_Model_RabbitMq::PushSchedule();
                }

                // pulling this from the 2.5.x branch
                if (!Application_Model_Preference::GetMasterDjConnectionUrlOverride()) {
                    $master_connection_url = 'http://' . $_SERVER['SERVER_NAME'] . ':' . $values['master_source_port'] . $values['master_source_mount'];
                    if (empty($values['master_source_port']) || empty($values['master_source_mount'])) {
                        Application_Model_Preference::SetMasterDJSourceConnectionURL('N/A');
                    } else {
                        Application_Model_Preference::SetMasterDJSourceConnectionURL($master_connection_url);
                    }
                } else {
                    Application_Model_Preference::SetMasterDJSourceConnectionURL($values['master_source_host']);
                }

                if (!Application_Model_Preference::GetLiveDjConnectionUrlOverride()) {
                    $live_connection_url = 'http://' . $_SERVER['SERVER_NAME'] . ':' . $values['show_source_port'] . $values['show_source_mount'];
                    if (empty($values['show_source_port']) || empty($values['show_source_mount'])) {
                        Application_Model_Preference::SetLiveDJSourceConnectionURL('N/A');
                    } else {
                        Application_Model_Preference::SetLiveDJSourceConnectionURL($live_connection_url);
                    }
                } else {
                    Application_Model_Preference::SetLiveDJSourceConnectionURL($values['show_source_host']);
                }

                Application_Model_StreamSetting::setMasterLiveStreamPort($values['master_source_port']);
                Application_Model_StreamSetting::setMasterLiveStreamMountPoint($values['master_source_mount']);
                Application_Model_StreamSetting::setDjLiveStreamPort($values['show_source_port']);
                Application_Model_StreamSetting::setDjLiveStreamMountPoint($values['show_source_mount']);

                Application_Model_StreamSetting::setOffAirMeta($values['offAirMeta']);

                // store stream update timestamp
                Application_Model_Preference::SetStreamUpdateTimestamp();

                $data = [];
                $info = Application_Model_StreamSetting::getStreamSetting();
                $data['setting'] = $info;
                for ($i = 1; $i <= $num_of_stream; ++$i) {
                    Application_Model_StreamSetting::setLiquidsoapError($i, 'waiting');
                }

                Application_Model_RabbitMq::SendMessageToPypo('update_stream_setting', $data);
                $this->view->statusMsg = "<div class='success'>" . _('Stream Setting Updated.') . '</div>';
            }
        }

        $this->view->num_stream = $num_of_stream;
        $this->view->enable_stream_conf = Application_Model_Preference::GetEnableStreamConf();
        $this->view->form = $form;
        if ($request->isPost()) {
            if ($form->isValid($values)) {
                $this->_helper->json->sendJson([
                    'valid' => 'true',
                    'html' => $this->view->render('preference/stream-setting.phtml'),
                    's1_set_admin_pass' => $s1_set_admin_pass,
                    's2_set_admin_pass' => $s2_set_admin_pass,
                    's3_set_admin_pass' => $s3_set_admin_pass,
                    's4_set_admin_pass' => $s4_set_admin_pass,
                ]);
            } else {
                $this->_helper->json->sendJson(['valid' => 'false', 'html' => $this->view->render('preference/stream-setting.phtml')]);
            }
        }
    }

    /**
     * Set stream settings preferences.
     *
     * @param array $values stream setting preference values
     */
    private function setStreamPreferences($values)
    {
        Application_Model_Preference::setUsingCustomStreamSettings($values['customStreamSettings']);
        Application_Model_Preference::SetStreamLabelFormat($values['streamFormat']);
        Application_Model_Preference::SetLiveStreamMasterUsername($values['master_username']);
        Application_Model_Preference::SetLiveStreamMasterPassword($values['master_password']);
        Application_Model_Preference::SetDefaultTransitionFade($values['transition_fade']);
        Application_Model_Preference::SetAutoTransition($values['auto_transition']);
        Application_Model_Preference::SetAutoSwitch($values['auto_switch']);
    }

    public function serverBrowseAction()
    {
        $request = $this->getRequest();
        $path = $request->getParam('path', null);

        $result = [];

        if (is_null($path)) {
            $element = [];
            $element['name'] = _('path should be specified');
            $element['isFolder'] = false;
            $element['isError'] = true;
            $result[$path] = $element;
        } else {
            $path = $path . '/';
            $handle = opendir($path);
            if ($handle !== false) {
                while (false !== ($file = readdir($handle))) {
                    if ($file != '.' && $file != '..') {
                        // only show directories that aren't private.
                        if (is_dir($path . $file) && substr($file, 0, 1) != '.') {
                            $element = [];
                            $element['name'] = $file;
                            $element['isFolder'] = true;
                            $element['isError'] = false;
                            $result[$file] = $element;
                        }
                    }
                }
            }
        }
        ksort($result);
        // returns format serverBrowse is looking for.
        $this->_helper->json->sendJson($result);
    }

    public function isImportInProgressAction()
    {
        $now = time();
        $res = false;
        if (Application_Model_Preference::GetImportTimestamp() + 10 > $now) {
            $res = true;
        }
        $this->_helper->json->sendJson($res);
    }

    public function getLiquidsoapStatusAction()
    {
        $out = [];
        $num_of_stream = intval(Application_Model_Preference::GetNumOfStreams());
        for ($i = 1; $i <= $num_of_stream; ++$i) {
            $status = Application_Model_StreamSetting::getLiquidsoapError($i);
            $status = $status == null ? _('Problem with Liquidsoap...') : $status;
            if (!Application_Model_StreamSetting::getStreamEnabled($i)) {
                $status = 'N/A';
            }
            $out[] = ['id' => $i, 'status' => $status];
        }
        $this->_helper->json->sendJson($out);
    }

    public function setSourceConnectionUrlAction()
    {
        SessionHelper::reopenSessionForWriting();

        $request = $this->getRequest();
        $type = $request->getParam('type', null);
        $url = urldecode($request->getParam('url', null));
        $override = $request->getParam('override', false);

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
        SessionHelper::reopenSessionForWriting();

        $out = [];
        $num_of_stream = intval(Application_Model_Preference::GetNumOfStreams());
        for ($i = 1; $i <= $num_of_stream; ++$i) {
            if (Application_Model_StreamSetting::getAdminPass('s' . $i) == '') {
                $out['s' . $i] = false;
            } else {
                $out['s' . $i] = true;
            }
        }
        $this->_helper->json->sendJson($out);
    }

    public function deleteAllFilesAction()
    {
        $this->view->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        if (!SecurityHelper::verifyCSRFToken($this->_getParam('csrf_token'))) {
            Logging::error(__FILE__ . ': Invalid CSRF token');
            $this->_helper->json->sendJson(['jsonrpc' => '2.0', 'valid' => false, 'error' => 'CSRF token did not match.']);

            return;
        }

        // Only admin users should get here through ACL permissioning
        // Only allow POST requests
        $method = $_SERVER['REQUEST_METHOD'];
        if (!($method == 'POST')) {
            $this->getResponse()
                ->setHttpResponseCode(405)
                ->appendBody(_('Request method not accepted') . ": {$method}");

            return;
        }

        $this->deleteFutureScheduleItems();
        $this->deleteCloudFiles();
        $this->deleteStoredFiles();

        $this->getResponse()
            ->setHttpResponseCode(200)
            ->appendBody('OK');
    }

    private function deleteFutureScheduleItems()
    {
        $utcTimezone = new DateTimeZone('UTC');
        $nowDateTime = new DateTime('now', $utcTimezone);
        $scheduleItems = CcScheduleQuery::create()
            ->filterByDbEnds($nowDateTime->format(DEFAULT_TIMESTAMP_FORMAT), Criteria::GREATER_THAN)
            ->find();

        // Delete all the schedule items
        foreach ($scheduleItems as $i) {
            // If this is the currently playing track, cancel the current show
            if ($i->isCurrentItem()) {
                $instanceId = $i->getDbInstanceId();
                $instance = CcShowInstancesQuery::create()->findPk($instanceId);
                $showId = $instance->getDbShowId();

                // From ScheduleController
                $scheduler = new Application_Model_Scheduler();
                $scheduler->cancelShow($showId);
                Application_Model_StoredFile::updatePastFilesIsScheduled();
            }

            $i->delete();
        }
    }

    private function deleteCloudFiles()
    {
        try {
            $CC_CONFIG = Config::getConfig();

            foreach ($CC_CONFIG['supportedStorageBackends'] as $storageBackend) {
                $proxyStorageBackend = new ProxyStorageBackend($storageBackend);
                $proxyStorageBackend->deleteAllCloudFileObjects();
            }
        } catch (Exception $e) {
            Logging::info($e->getMessage());
        }
    }

    private function deleteStoredFiles()
    {
        // Delete all files from the database
        $files = CcFilesQuery::create()->find();
        foreach ($files as $file) {
            $storedFile = new Application_Model_StoredFile($file, null);
            // Delete the files quietly to avoid getting Sentry errors for
            // every S3 file we delete.
            $storedFile->delete(true);
        }
    }
}
