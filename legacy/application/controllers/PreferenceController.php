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
            ->addActionContext('get-admin-password-status', 'json')
            ->initContext();
    }

    public function indexAction()
    {
        $request = $this->getRequest();

        Zend_Layout::getMvcInstance()->assign('parent_page', 'Settings');

        $baseUrl = Config::getBasePath();

        $this->view->headScript()->appendFile(Assets::url('js/airtime/preferences/preferences.js'), 'text/javascript');
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
                Application_Model_Preference::SetDefaultLocale($values['locale']);
                Application_Model_Preference::SetWeekStartDay($values['weekStartDay']);
                Application_Model_Preference::setScheduleTrimOverbooked($values['scheduleTrimOverbooked']);
                Application_Model_Preference::setRadioPageDisplayLoginButton($values['radioPageLoginButton']);
                Application_Model_Preference::setRadioPageDisabled($values['radioPageDisabled']);
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
        $url = Config::getPublicUrl() . (((int) $values->stationPodcastPrivacy)
            ? "feeds/station-rss?sharing_token={$key}"
            : 'feeds/station-rss');
        $stationPodcast->setDbUrl($url)->save();
        Application_Model_Preference::setStationPodcastPrivacy($values->stationPodcastPrivacy);

        $this->_helper->json->sendJson(['url' => $url]);
    }

    public function directoryConfigAction() {}

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
        $request = $this->getRequest();

        Zend_Layout::getMvcInstance()->assign('parent_page', 'Settings');

        $this->view->headScript()->appendFile(Assets::url('js/airtime/preferences/streamsetting.js'), 'text/javascript');

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

        // get current settings
        $setting = Application_Model_StreamSetting::getStreamSetting();
        $form->setSetting($setting);

        if ($num_of_stream > MAX_NUM_STREAMS) {
            Logging::error('Your streams count (' . $num_of_stream . ') exceed the maximum, some of them will not be displayed');
            $num_of_stream = MAX_NUM_STREAMS;
        }

        for ($i = 1; $i <= $num_of_stream; ++$i) {
            $subform = new Application_Form_StreamSettingSubForm();
            $subform->setPrefix($i);
            $subform->setSetting($setting);
            $subform->startForm();
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
            $values = [];

            foreach ($postData as $k => $v) {
                $v = explode('=', urldecode($v));
                $values[$v[0]] = $v[1];
            }

            if ($form->isValid($values)) {
                // this goes into cc_pref table
                $this->setStreamPreferences($values);

                // compare new values with current value
                $changeRGenabled = Application_Model_Preference::GetEnableReplayGain() != $values['enableReplayGain'];
                $changeRGmodifier = Application_Model_Preference::getReplayGainModifier() != $values['replayGainModifier'];
                if ($changeRGenabled || $changeRGmodifier) {
                    Application_Model_Preference::SetEnableReplayGain($values['enableReplayGain']);
                    Application_Model_Preference::setReplayGainModifier($values['replayGainModifier']);
                    // The side effects of this function are still required to fill the schedule, we
                    // don't use the returned schedule.
                    Application_Model_Schedule::getSchedule();
                    Application_Model_RabbitMq::SendMessageToPypo('update_schedule', []);
                    // Application_Model_RabbitMq::PushSchedule();
                }

                // store stream update timestamp
                Application_Model_Preference::SetStreamUpdateTimestamp();

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
        Application_Model_Preference::setOffAirMeta($values['offAirMeta']);
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
            $path .= '/';
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
            $status = Application_Model_Preference::getLiquidsoapError($i);
            $status = $status == null ? _('Problem with Liquidsoap...') : $status;
            if (!Application_Model_StreamSetting::getStreamEnabled($i)) {
                $status = 'N/A';
            }
            $out[] = ['id' => $i, 'status' => $status];
        }
        $this->_helper->json->sendJson($out);
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
