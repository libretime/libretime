<?php

class ApiController extends Zend_Controller_Action
{
    public const DEFAULT_SHOWS_TO_RETRIEVE = '5';

    public const DEFAULT_DAYS_TO_RETRIEVE = '2';

    public function init()
    {
        if ($this->view) { // skip if already missing (ie in tests)
            $this->view->layout()->disableLayout();
            $this->_helper->viewRenderer->setNoRender(true);
        }

        // Ignore API key and session authentication for these APIs:
        $ignoreAuth = [
            'live-info',
            'live-info-v2',
            'week-info',
            'station-metadata',
            'station-logo',
            'show-history-feed',
            'item-history-feed',
            'shows',
            'show-tracks',
            'show-schedules',
            'show-logo',
            'track',
            'track-types',
            'stream-m3u',
        ];

        if (Zend_Session::isStarted()) {
            Logging::error('Session already started for an API request. Check your code because
                            this will negatively impact performance.');
        }

        $params = $this->getRequest()->getParams();
        if (!in_array($params['action'], $ignoreAuth)) {
            $this->checkAuth();
        }
        // Initialize action controller here
        $context = $this->_helper->getHelper('contextSwitch');
        $context->addActionContext('version', 'json')
            ->addActionContext('recorded-shows', 'json')
            ->addActionContext('calendar-init', 'json')
            ->addActionContext('upload-file', 'json')
            ->addActionContext('upload-recorded', 'json')
            ->addActionContext('media-monitor-setup', 'json')
            ->addActionContext('media-item-status', 'json')
            ->addActionContext('reload-metadata', 'json')
            ->addActionContext('list-all-files', 'json')
            ->addActionContext('list-all-watched-dirs', 'json')
            ->addActionContext('add-watched-dir', 'json')
            ->addActionContext('remove-watched-dir', 'json')
            ->addActionContext('set-storage-dir', 'json')
            ->addActionContext('get-stream-setting', 'json')
            ->addActionContext('status', 'json')
            ->addActionContext('register-component', 'json')
            ->addActionContext('update-liquidsoap-status', 'json')
            ->addActionContext('update-file-system-mount', 'json')
            ->addActionContext('handle-watched-dir-missing', 'json')
            ->addActionContext('rabbitmq-do-push', 'json')
            ->addActionContext('check-live-stream-auth', 'json')
            ->addActionContext('update-source-status', 'json')
            ->addActionContext('get-bootstrap-info', 'json')
            ->addActionContext('get-files-without-replay-gain', 'json')
            ->addActionContext('get-files-without-silan-value', 'json')
            ->addActionContext('reload-metadata-group', 'json')
            ->addActionContext('notify-webstream-data', 'json')
            ->addActionContext('get-stream-parameters', 'json')
            ->addActionContext('push-stream-stats', 'json')
            ->addActionContext('update-stream-setting-table', 'json')
            ->addActionContext('update-replay-gain-value', 'json')
            ->addActionContext('update-cue-values-by-silan', 'json')
            ->addActionContext('get-usability-hint', 'json')
            ->addActionContext('poll-celery', 'json')
            ->addActionContext('recalculate-schedule', 'json') // RKTN-260
            ->initContext();
    }

    public function checkAuth()
    {
        $CC_CONFIG = Config::getConfig();
        $apiKey = $this->_getParam('api_key');

        if (in_array($apiKey, $CC_CONFIG['apiKey'])) {
            return true;
        }

        $authHeader = $this->getRequest()->getHeader('Authorization');
        $authHeaderArray = explode(' ', $authHeader);
        if (
            count($authHeaderArray) >= 2
            && $authHeaderArray[0] == 'Api-Key'
            && in_array($authHeaderArray[1], $CC_CONFIG['apiKey'])
        ) {
            return true;
        }

        // Start the session so the authentication is
        // enforced by the ACL plugin.
        Zend_Session::start();
        $authAdapter = Zend_Auth::getInstance();
        Application_Model_Auth::pinSessionToClient($authAdapter);

        if (Zend_Auth::getInstance()->hasIdentity()) {
            return true;
        }

        header('HTTP/1.0 401 Unauthorized');
        echo _('You are not allowed to access this resource.');

        exit;
    }

    public function versionAction()
    {
        $config = Config::getConfig();
        $this->_helper->json->sendJson([
            'airtime_version' => $config['airtime_version'],
            'api_version' => AIRTIME_API_VERSION,
        ]);
    }

    /**
     * Allows remote client to download requested media file.
     */
    public function getMediaAction()
    {
        // Close the session so other HTTP requests can be completed while
        // tracks are read for previewing or downloading.
        session_write_close();

        $fileId = $this->_getParam('file');

        $inline = !($this->_getParam('download', false) == true);
        Application_Service_MediaService::streamFileDownload($fileId, $inline);

        $this->_helper->json->sendJson([]);
    }

    /**
     * Manually trigger the TaskManager task to poll for completed Celery tasks.
     */
    public function pollCeleryAction()
    {
        $taskManager = TaskManager::getInstance();
        $taskManager->runTask('CeleryTask');
    }

    /**
     * TODO: move this function into a more generic analytics REST controller.
     *
     * Update station bandwidth usage based on icecast log data
     */
    public function bandwidthUsageAction()
    {
        $bandwidthUsage = json_decode($this->getRequest()->getParam('bandwidth_data'));
        $usageBytes = 0;
        if (!empty($bandwidthUsage)) {
            foreach ($bandwidthUsage as $entry) {
                // TODO: store the IP address for future use
                $ts = strtotime($entry->timestamp);
                if ($ts > Application_Model_Preference::getBandwidthLimitUpdateTimer()) {
                    $usageBytes += $entry->bytes;
                }
            }
        }
        Application_Model_Preference::incrementBandwidthLimitCounter($usageBytes);
        Application_Model_Preference::setBandwidthLimitUpdateTimer();

        $usage = Application_Model_Preference::getBandwidthLimitCounter();
        if (($usage > Application_Model_Preference::getBandwidthLimit())
            && (Application_Model_Preference::getProvisioningStatus() == PROVISIONING_STATUS_ACTIVE)
        ) {
            $CC_CONFIG = Config::getConfig();
            // Hacky way to get the user ID...
            $url = AIRTIMEPRO_API_URL . '/station/' . $CC_CONFIG['rabbitmq']['user'] . '/suspend';
            $user = ['', $CC_CONFIG['apiKey'][0]];
            $data = ['reason' => 'Bandwidth limit exceeded'];

            try {
                Application_Common_HTTPHelper::doPost($url, $user, $data);
            } catch (Exception $e) {
                throw $e;
            }
        }
    }

    // Used by the SaaS monitoring
    public function onAirLightAction()
    {
        $request = $this->getRequest();
        $this->view->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $result = [];
        $result['on_air_light'] = false;
        $result['on_air_light_expected_status'] = false;
        $result['station_down'] = false;
        $result['master_stream'] = false;
        $result['live_stream'] = false;
        $result['master_stream_on_air'] = false;
        $result['live_stream_on_air'] = false;

        $range = Application_Model_Schedule::GetPlayOrderRange();

        $isItemCurrentlyScheduled = false;
        $isCurrentItemPlaying = false;
        if (!is_null($range['tracks']['current']) && count($range['tracks']['current']) > 0) {
            $isItemCurrentlyScheduled = true;
            $isCurrentItemPlaying = $range['tracks']['current']['media_item_played'] ? true : false;
        }

        if (
            $isItemCurrentlyScheduled
            || Application_Model_Preference::GetSourceSwitchStatus('live_dj') == 'on'
            || Application_Model_Preference::GetSourceSwitchStatus('master_dj') == 'on'
        ) {
            $result['on_air_light_expected_status'] = true;
        }

        if (($isItemCurrentlyScheduled && $isCurrentItemPlaying)
            || Application_Model_Preference::GetSourceSwitchStatus('live_dj') == 'on'
            || Application_Model_Preference::GetSourceSwitchStatus('master_dj') == 'on'
        ) {
            $result['on_air_light'] = true;
        }

        if ($result['on_air_light_expected_status'] != $result['on_air_light']) {
            $result['station_down'] = true;
        }

        $live_dj_stream = Application_Model_Preference::GetSourceStatus('live_dj');
        $master_dj_stream = Application_Model_Preference::GetSourceStatus('master_dj');
        $live_dj_on_air = Application_Model_Preference::GetSourceSwitchStatus('live_dj');
        $master_dj_on_air = Application_Model_Preference::GetSourceSwitchStatus('master_dj');

        if ($live_dj_stream == true) {
            $result['live_stream'] = true;
            if ($live_dj_on_air == 'on') {
                $result['live_stream_on_air'] = true;
            }
        }

        if ($master_dj_stream == true) {
            $result['master_stream'] = true;
            if ($master_dj_on_air == 'on') {
                $result['master_stream_on_air'] = true;
            }
        }

        $this->returnJsonOrJsonp($request, $result);
    }

    /**
     * Retrieve the currently playing show as well as upcoming shows.
     * Number of shows returned and the time interval in which to
     * get the next shows can be configured as GET parameters.
     *
     * TODO: in the future, make interval length a parameter instead of hardcode to 48
     *
     * Possible parameters:
     * type - Can have values of "endofday" or "interval". If set to "endofday",
     *        the function will retrieve shows from now to end of day.
     *        If set to "interval", shows in the next 48 hours will be retrived.
     *        Default is "interval".
     * limit - How many shows to retrieve
     *         Default is "5".
     */
    public function liveInfoAction()
    {
        if (Application_Model_Preference::GetAllow3rdPartyApi() || $this->checkAuth()) {
            // disable the view and the layout
            $this->view->layout()->disableLayout();
            $this->_helper->viewRenderer->setNoRender(true);

            $request = $this->getRequest();

            $utcTimeNow = gmdate(DEFAULT_TIMESTAMP_FORMAT);
            $utcTimeEnd = '';   // if empty, getNextShows will use interval instead of end of day

            // default to the station timezone
            $timezone = Application_Model_Preference::GetDefaultTimezone();
            $userDefinedTimezone = strtolower($request->getParam('timezone'));
            $upcase = false; // only upcase the timezone abbreviations
            $this->updateTimezone($userDefinedTimezone, $timezone, $upcase);

            $type = $request->getParam('type');
            $limit = $request->getParam('limit');
            if ($limit == '' || !is_numeric($limit)) {
                $limit = '5';
            }
            /* This is some *extremely* lazy programming that needs to be fixed. For some reason
             * we are using two entirely different codepaths for very similar functionality (type = endofday
             * vs type = interval). Needs to be fixed for 2.3 - MK */
            if ($type == 'endofday') {
                // make getNextShows use end of day
                $end = Application_Common_DateHelper::getTodayStationEndDateTime();
                $end->setTimezone(new DateTimeZone('UTC'));
                $utcTimeEnd = $end->format(DEFAULT_TIMESTAMP_FORMAT);

                $result = [
                    'env' => APPLICATION_ENV,
                    'schedulerTime' => $utcTimeNow,
                    'currentShow' => Application_Model_Show::getCurrentShow($utcTimeNow),
                    'nextShow' => Application_Model_Show::getNextShows($utcTimeNow, $limit, $utcTimeEnd),
                ];
            } else {
                $result = Application_Model_Schedule::GetPlayOrderRangeOld($limit);
            }

            if (!isset($result)) {
                $this->returnJsonOrJsonp($request, []);
            }

            $stationUrl = Config::getPublicUrl();

            if (isset($result['previous'])) {
                if (
                    (isset($result['previous']['type']) && $result['previous']['type'] != 'livestream')
                    && isset($result['previous']['metadata'])
                ) {
                    $previousID = $result['previous']['metadata']['id'];
                    $get_prev_artwork_url = $stationUrl . 'api/track?id=' . $previousID . '&return=artwork';
                    $result['previous']['metadata']['artwork_url'] = $get_prev_artwork_url;
                }
            }

            if (isset($result['current'])) {
                if (
                    (isset($result['current']['type']) & $result['current']['type'] != 'livestream')
                    && isset($result['current']['metadata'])
                ) {
                    $currID = $result['current']['metadata']['id'];
                    $get_curr_artwork_url = $stationUrl . 'api/track?id=' . $currID . '&return=artwork';
                    $result['current']['metadata']['artwork_url'] = $get_curr_artwork_url;
                }
            }

            if (isset($result['next'])) {
                if (
                    (isset($result['next']['type']) && $result['next']['type'] != 'livestream')
                    && isset($result['next']['metadata'])
                ) {
                    $nextID = $result['next']['metadata']['id'];
                    $get_next_artwork_url = $stationUrl . 'api/track?id=' . $nextID . '&return=artwork';
                    $result['next']['metadata']['artwork_url'] = $get_next_artwork_url;
                }
            }

            // apply user-defined timezone, or default to station
            Application_Common_DateHelper::convertTimestampsToTimezone(
                $result['currentShow'],
                ['starts', 'ends', 'start_timestamp', 'end_timestamp'],
                $timezone
            );
            Application_Common_DateHelper::convertTimestampsToTimezone(
                $result['nextShow'],
                ['starts', 'ends', 'start_timestamp', 'end_timestamp'],
                $timezone
            );

            // Convert the UTC scheduler time ("now") to the user-defined timezone.
            $result['schedulerTime'] = Application_Common_DateHelper::UTCStringToTimezoneString($result['schedulerTime'], $timezone);
            $result['timezone'] = $upcase ? strtoupper($timezone) : $timezone;
            $result['timezoneOffset'] = Application_Common_DateHelper::getTimezoneOffset($timezone);

            // XSS exploit prevention
            SecurityHelper::htmlescape_recursive($result);

            // convert image paths to point to api endpoints
            WidgetHelper::findAndConvertPaths($result);

            // used by caller to determine if the airtime they are running or widgets in use is out of date.
            $result['AIRTIME_API_VERSION'] = AIRTIME_API_VERSION;

            $this->returnJsonOrJsonp($request, $result);
        } else {
            header('HTTP/1.0 401 Unauthorized');
            echo _('You are not allowed to access this resource. ');

            exit;
        }
    }

    /**
     * Retrieve the currently playing show as well as upcoming shows.
     * Number of shows returned and the time interval in which to
     * get the next shows can be configured as GET parameters.
     *
     * Possible parameters:
     * days - How many days to retrieve.
     *        Default is 2 (today + tomorrow).
     * shows - How many shows to retrieve
     *         Default is 5.
     * timezone - The timezone to send the times in
     *            Defaults to the station timezone
     */
    public function liveInfoV2Action()
    {
        if (Application_Model_Preference::GetAllow3rdPartyApi() || $this->checkAuth()) {
            // disable the view and the layout
            $this->view->layout()->disableLayout();
            $this->_helper->viewRenderer->setNoRender(true);

            $request = $this->getRequest();

            // default to the station timezone
            $timezone = Application_Model_Preference::GetDefaultTimezone();
            $userDefinedTimezone = strtolower($request->getParam('timezone'));
            $upcase = false; // only upcase the timezone abbreviations
            $this->updateTimezone($userDefinedTimezone, $timezone, $upcase);

            $daysToRetrieve = $request->getParam('days');
            $showsToRetrieve = $request->getParam('shows');
            if ($daysToRetrieve == '' || !is_numeric($daysToRetrieve)) {
                $daysToRetrieve = self::DEFAULT_DAYS_TO_RETRIEVE;
            }
            if ($showsToRetrieve == '' || !is_numeric($showsToRetrieve)) {
                $showsToRetrieve = self::DEFAULT_SHOWS_TO_RETRIEVE;
            }

            // set the end time to the day's start n days from now.
            // days=1 will return shows until the end of the current day,
            // days=2 will return shows until the end of tomorrow, etc.
            $end = Application_Common_DateHelper::getEndDateTime($timezone, $daysToRetrieve);
            $end->setTimezone(new DateTimeZone('UTC'));
            $utcTimeEnd = $end->format(DEFAULT_TIMESTAMP_FORMAT);

            $result = Application_Model_Schedule::GetPlayOrderRange($utcTimeEnd, $showsToRetrieve);

            // apply user-defined timezone, or default to station
            $this->applyLiveTimezoneAdjustments($result, $timezone, $upcase);

            // XSS exploit prevention
            SecurityHelper::htmlescape_recursive($result);

            // convert image paths to point to api endpoints
            WidgetHelper::findAndConvertPaths($result);

            // Expose the live source status
            $live_dj = Application_Model_Preference::GetSourceSwitchStatus('live_dj');
            $master_dj = Application_Model_Preference::GetSourceSwitchStatus('master_dj');
            $scheduled_play = Application_Model_Preference::GetSourceSwitchStatus('scheduled_play');
            $result['sources'] = [];
            $result['sources']['livedj'] = $live_dj;
            $result['sources']['masterdj'] = $master_dj;
            $result['sources']['scheduledplay'] = $scheduled_play;

            // used by caller to determine if the airtime they are running or widgets in use is out of date.
            $result['station']['AIRTIME_API_VERSION'] = AIRTIME_API_VERSION;

            $this->returnJsonOrJsonp($request, $result);
        } else {
            header('HTTP/1.0 401 Unauthorized');
            echo _('You are not allowed to access this resource. ');

            exit;
        }
    }

    /**
     * Check that the value for the timezone the user gave is valid.
     * If it is, override the default (station) timezone.
     * If it's an abbreviation (pst, edt) we upcase the output.
     *
     * @param string $userDefinedTimezone the requested timezone value
     * @param string $timezone            the default timezone
     * @param bool   $upcase              whether the timezone output should be upcased
     */
    private function updateTimezone($userDefinedTimezone, &$timezone, &$upcase)
    {
        $delimiter = '/';
        // if the user passes in a timezone in standard form ("Continent/City")
        // we need to fix the downcased string by upcasing each word delimited by a /
        if (strpos($userDefinedTimezone, $delimiter) !== false) {
            $userDefinedTimezone = implode($delimiter, array_map('ucfirst', explode($delimiter, $userDefinedTimezone)));
        }
        // if the timezone defined by the user exists, use that
        if (array_key_exists($userDefinedTimezone, timezone_abbreviations_list())) {
            $timezone = $userDefinedTimezone;
            $upcase = true;
        } elseif (in_array($userDefinedTimezone, timezone_identifiers_list())) {
            $timezone = $userDefinedTimezone;
        }
    }

    /**
     * If the user passed in a timezone parameter, adjust timezone-dependent
     * variables in the result to reflect the given timezone.
     *
     * @param array  $result   reference to the object to send back to the user
     * @param string $timezone the user's timezone parameter value
     * @param bool   $upcase   whether the timezone output should be upcased
     */
    private function applyLiveTimezoneAdjustments(&$result, $timezone, $upcase)
    {
        Application_Common_DateHelper::convertTimestampsToTimezone(
            $result,
            ['starts', 'ends', 'start_timestamp', 'end_timestamp'],
            $timezone
        );

        // Convert the UTC scheduler time ("now") to the user-defined timezone.
        $result['station']['schedulerTime'] = Application_Common_DateHelper::UTCStringToTimezoneString($result['station']['schedulerTime'], $timezone);
        $result['station']['timezone'] = $upcase ? strtoupper($timezone) : $timezone;
    }

    public function weekInfoAction()
    {
        if (Application_Model_Preference::GetAllow3rdPartyApi() || $this->checkAuth()) {
            // disable the view and the layout
            $this->view->layout()->disableLayout();
            $this->_helper->viewRenderer->setNoRender(true);

            $request = $this->getRequest();
            $result = WidgetHelper::getWeekInfo($this->getRequest()->getParam('timezone'));

            // used by caller to determine if the airtime they are running or widgets in use is out of date.
            $result['AIRTIME_API_VERSION'] = AIRTIME_API_VERSION;

            $this->returnJsonOrJsonp($request, $result);
        } else {
            header('HTTP/1.0 401 Unauthorized');
            echo _('You are not allowed to access this resource. ');

            exit;
        }
    }

    /**
     * API endpoint to display the show logo.
     */
    public function showLogoAction()
    {
        // Disable the view and the layout
        $this->view->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        if (Application_Model_Preference::GetAllow3rdPartyApi() || $this->checkAuth()) {
            $request = $this->getRequest();
            $showId = $request->getParam('id');
            if (empty($showId)) {
                throw new ZendActionHttpException($this, 400, 'ERROR: No ID was given.');
            }

            $show = CcShowQuery::create()->findPk($showId);
            if (empty($show)) {
                throw new ZendActionHttpException($this, 400, "ERROR: No show with ID {$showId} exists.");
            }

            $path = $show->getDbImagePath();

            try {
                $mime_type = mime_content_type($path);
                if (empty($path)) {
                    throw new ZendActionHttpException($this, 400, 'ERROR: Show does not have an associated image.');
                }
            } catch (Exception $e) {
                // To avoid broken images on your site, we return the station logo if we can't find the show logo.
                $this->_redirect('api/station-logo');

                return;
            }

            try {
                // Sometimes end users may be looking at stale data - if an image is removed
                // but has been cached in a client's browser this will throw an exception
                Application_Common_FileIO::smartReadFile($path, filesize($path), $mime_type);
            } catch (LibreTimeFileNotFoundException $e) {
                // throw new ZendActionHttpException($this, 404, "ERROR: No image found at $path");
                $this->_redirect('api/station-logo');

                return;
            } catch (Exception $e) {
                throw new ZendActionHttpException($this, 500, 'ERROR: ' . $e->getMessage());
            }
        } else {
            header('HTTP/1.0 401 Unauthorized');
            echo _('You are not allowed to access this resource. ');

            exit;
        }
    }

    /**
     * New API endpoint to display metadata from any single track.
     *
     * Find metadata to any track imported (eg. id=1&return=json)
     */
    public function trackAction()
    {
        // Disable the view and the layout
        $this->view->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        if (Application_Model_Preference::GetAllow3rdPartyApi() || $this->checkAuth()) {
            $request = $this->getRequest();
            $trackid = $request->getParam('id');
            $return = $request->getParam('return');

            if (empty($return)) {
                throw new ZendActionHttpException($this, 400, 'ERROR: No return was given.');
            }

            if (empty($trackid)) {
                throw new ZendActionHttpException($this, 400, 'ERROR: No ID was given.');
            }

            $fp = Config::getStoragePath();

            // $this->view->type = $type;
            $file = Application_Model_StoredFile::RecallById($trackid);
            $md = $file->getMetadata();

            if ($return === 'artwork-data') {
                foreach ($md as $key => $value) {
                    if ($key == 'MDATA_KEY_ARTWORK' && !is_null($value)) {
                        FileDataHelper::renderDataURI($fp . $md['MDATA_KEY_ARTWORK']);
                    }
                }
            } elseif ($return === 'artwork-data-32') {
                foreach ($md as $key => $value) {
                    if ($key == 'MDATA_KEY_ARTWORK' && !is_null($value)) {
                        FileDataHelper::renderDataURI($fp . $md['MDATA_KEY_ARTWORK'] . '-32');
                    }
                }
            } elseif ($return === 'artwork') {
                // default
                foreach ($md as $key => $value) {
                    if ($key == 'MDATA_KEY_ARTWORK' && !is_null($value)) {
                        FileDataHelper::renderImage($fp . $md['MDATA_KEY_ARTWORK'] . '-512.jpg');
                    }
                }
            } elseif ($return === 'artwork-32') {
                foreach ($md as $key => $value) {
                    if ($key == 'MDATA_KEY_ARTWORK' && !is_null($value)) {
                        FileDataHelper::renderImage($fp . $md['MDATA_KEY_ARTWORK'] . '-32.jpg');
                    }
                }
            } elseif ($return === 'artwork-64') {
                foreach ($md as $key => $value) {
                    if ($key == 'MDATA_KEY_ARTWORK' && !is_null($value)) {
                        FileDataHelper::renderImage($fp . $md['MDATA_KEY_ARTWORK'] . '-64.jpg');
                    }
                }
            } elseif ($return === 'artwork-128') {
                foreach ($md as $key => $value) {
                    if ($key == 'MDATA_KEY_ARTWORK' && !is_null($value)) {
                        FileDataHelper::renderImage($fp . $md['MDATA_KEY_ARTWORK'] . '-128.jpg');
                    }
                }
            } elseif ($return === 'artwork-512') {
                foreach ($md as $key => $value) {
                    if ($key == 'MDATA_KEY_ARTWORK' && !is_null($value)) {
                        FileDataHelper::renderImage($fp . $md['MDATA_KEY_ARTWORK'] . '-512.jpg');
                    }
                }
            } elseif ($return === 'json') {
                $data = json_encode($md);
                echo $data;
            }
        } else {
            header('HTTP/1.0 401 Unauthorized');
            echo _('You are not allowed to access this resource. ');

            exit;
        }
    }

    public function trackTypesAction()
    {
        if (Application_Model_Preference::GetAllow3rdPartyApi() || $this->checkAuth()) {
            // disable the view and the layout
            $this->view->layout()->disableLayout();
            $this->_helper->viewRenderer->setNoRender(true);

            $tracktypes = Application_Model_Tracktype::getTracktypes();
            $this->_helper->json->sendJson($tracktypes);
        } else {
            header('HTTP/1.0 401 Unauthorized');
            echo _('You are not allowed to access this resource. ');

            exit;
        }
    }

    /**
     * API endpoint to provide station metadata.
     */
    public function stationMetadataAction()
    {
        if (Application_Model_Preference::GetAllow3rdPartyApi() || $this->checkAuth()) {
            // disable the view and the layout
            $this->view->layout()->disableLayout();
            $this->_helper->viewRenderer->setNoRender(true);

            $request = $this->getRequest();

            $result['name'] = Application_Model_Preference::GetStationName();
            $result['logo'] = Config::getPublicUrl() . 'api/station-logo';
            $result['description'] = Application_Model_Preference::GetStationDescription();
            $result['timezone'] = Application_Model_Preference::GetDefaultTimezone();
            $result['locale'] = Application_Model_Preference::GetDefaultLocale();
            $result['stream_data'] = Application_Model_StreamSetting::getEnabledStreamData();

            // used by caller to determine if the airtime they are running or widgets in use is out of date.
            $result['AIRTIME_API_VERSION'] = AIRTIME_API_VERSION;

            $this->returnJsonOrJsonp($request, $result);
        } else {
            header('HTTP/1.0 401 Unauthorized');
            echo _('You are not allowed to access this resource. ');

            exit;
        }
    }

    /**
     * API endpoint to display the current station logo.
     */
    public function stationLogoAction()
    {
        if (Application_Model_Preference::GetAllow3rdPartyApi() || $this->checkAuth()) {
            // disable the view and the layout
            $this->view->layout()->disableLayout();
            $this->_helper->viewRenderer->setNoRender(true);

            $logo = Application_Model_Preference::GetStationLogo();
            // if there's no logo, just die - redirects to a 404
            if (!$logo || $logo === '') {
                return;
            }

            // we're passing this as an image instead of using it in a data uri, so decode it
            $blob = base64_decode($logo);

            // use finfo to get the mimetype from the decoded blob
            $f = finfo_open();
            $mime_type = finfo_buffer($f, $blob, FILEINFO_MIME_TYPE);
            finfo_close($f);

            header('Content-Type: ' . $mime_type);
            echo $blob;
        } else {
            header('HTTP/1.0 401 Unauthorized');
            echo _('You are not allowed to access this resource.');

            exit;
        }
    }

    public function scheduleAction()
    {
        $this->view->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        header('Content-Type: application/json');

        $data = Application_Model_Schedule::getSchedule();

        echo json_encode($data, JSON_FORCE_OBJECT);
    }

    public function notifyMediaItemStartPlayAction()
    {
        $media_id = $this->_getParam('media_id');

        // We send a fake media id when playing on-demand ads;
        // in this case, simply return
        if ($media_id === '0' || $media_id === '-1') {
            return;
        }

        Logging::debug("Received notification of new media item start: {$media_id}");
        Application_Model_Schedule::UpdateMediaPlayedStatus($media_id);

        try {
            $historyService = new Application_Service_HistoryService();
            $historyService->insertPlayedItem($media_id);

            // set a 'last played' timestamp for media item
            // needed for smart blocks
            $mediaType = Application_Model_Schedule::GetType($media_id);
            if ($mediaType == 'file') {
                $file_id = Application_Model_Schedule::GetFileId($media_id);
                if (!is_null($file_id)) {
                    // we are dealing with a file not a stream
                    $file = Application_Model_StoredFile::RecallById($file_id);
                    $now = new DateTime('now', new DateTimeZone('UTC'));
                    $file->setLastPlayedTime($now);

                    // Push metadata to TuneIn
                    if (Application_Model_Preference::getTuneinEnabled()) {
                        $filePropelOrm = $file->getPropelOrm();
                        $title = urlencode($filePropelOrm->getDbTrackTitle());
                        $artist = urlencode($filePropelOrm->getDbArtistName());
                        Application_Common_TuneIn::sendMetadataToTunein($title, $artist);
                    }
                }
            } else {
                // webstream
                $stream_id = Application_Model_Schedule::GetStreamId($media_id);
                if (!is_null($stream_id)) {
                    $webStream = new Application_Model_Webstream($stream_id);
                    $now = new DateTime('now', new DateTimeZone('UTC'));
                    $webStream->setLastPlayed($now);
                }
            }
        } catch (Exception $e) {
            Logging::info($e);
        }

        $this->_helper->json->sendJson(['status' => 1, 'message' => '']);
    }

    public function recordedShowsAction()
    {
        $utcTimezone = new DateTimeZone('UTC');
        $nowDateTime = new DateTime('now', $utcTimezone);
        $endDateTime = clone $nowDateTime;
        $endDateTime = $endDateTime->add(new DateInterval('PT2H'));

        $this->view->shows =
            Application_Model_Show::getShows(
                $nowDateTime,
                $endDateTime,
                $onlyRecord = true
            );

        $this->view->is_recording = false;
        $this->view->server_timezone = Application_Model_Preference::GetDefaultTimezone();

        $rows = Application_Model_Show::getCurrentShow();

        if (count($rows) > 0) {
            $this->view->is_recording = ($rows[0]['record'] == 1);
        }
    }

    public function uploadRecordedAction()
    {
        $show_instance_id = $this->_getParam('showinstanceid');
        $file_id = $this->_getParam('fileid');
        $this->view->fileid = $file_id;
        $this->view->showinstanceid = $show_instance_id;
        $this->uploadRecordedActionParam($show_instance_id, $file_id);
    }

    // The paramterized version of the uploadRecordedAction controller.
    // We want this controller's action to be invokable from other
    // controllers instead being of only through http
    public function uploadRecordedActionParam($show_instance_id, $file_id)
    {
        $showCanceled = false;
        $file = Application_Model_StoredFile::RecallById($file_id);
        // $show_instance  = $this->_getParam('show_instance');

        try {
            $show_inst = new Application_Model_ShowInstance($show_instance_id);
            $show_inst->setRecordedFile($file_id);
        } catch (Exception $e) {
            // we've reached here probably because the show was
            // cancelled, and therefore the show instance does not exist
            // anymore (ShowInstance constructor threw this error). We've
            // done all we can do (upload the file and put it in the
            // library), now lets just return.
            $showCanceled = true;
        }

        // TODO : the following is inefficient because it calls save on both
        // fields
        $file->setMetadataValue('MDATA_KEY_CREATOR', 'Airtime Show Recorder');
        $file->setMetadataValue('MDATA_KEY_TRACKNUMBER', $show_instance_id);
    }

    public function mediaMonitorSetupAction()
    {
        $this->view->stor = Config::getStoragePath();

        $watchedDirsPath = [];
        $this->view->watched_dirs = $watchedDirsPath;
    }

    public function dispatchMetadata($md, $mode)
    {
        $return_hash = [];
        Application_Model_Preference::SetImportTimestamp();

        $con = Propel::getConnection(CcFilesPeer::DATABASE_NAME);
        $con->beginTransaction();

        try {
            // create also modifies the file if it exists
            if ($mode == 'create') {
                $filepath = $md['MDATA_KEY_FILEPATH'];
                $filepath = Application_Common_OsPath::normpath($filepath);
                $file = Application_Model_StoredFile::RecallByFilepath($filepath, $con);
                if (is_null($file)) {
                    $file = Application_Model_StoredFile::Insert($md, $con);
                } else {
                    // If the file already exists we will update and make sure that
                    // it's marked as 'exists'.
                    $file->setFileExistsFlag(true);
                    $file->setFileHiddenFlag(false);
                    $file->setMetadata($md);
                }
                if ($md['is_record'] != 0) {
                    $this->uploadRecordedActionParam($md['MDATA_KEY_TRACKNUMBER'], $file->getId());
                }
            } elseif ($mode == 'modify') {
                $filepath = $md['MDATA_KEY_FILEPATH'];
                $file = Application_Model_StoredFile::RecallByFilepath($filepath, $con);

                // File is not in database anymore.
                if (is_null($file)) {
                    $return_hash['error'] = sprintf(_('File does not exist in %s'), PRODUCT_NAME);
                }
                // Updating a metadata change.
                else {
                    // CC-5207 - restart media-monitor causes it to reevaluate all
                    // files in watched directories, and reset their cue-in/cue-out
                    // values. Since media-monitor has nothing to do with cue points
                    // let's unset it here. Note that on mode == "create", we still
                    // want media-monitor sending info about cue_out which by default
                    // will be equal to length of track until silan can take over.
                    unset($md['MDATA_KEY_CUE_IN'], $md['MDATA_KEY_CUE_OUT']);

                    $file->setMetadata($md);
                }
            } elseif ($mode == 'moved') {
                $file = Application_Model_StoredFile::RecallByFilepath(
                    $md['MDATA_KEY_ORIGINAL_PATH'],
                    $con
                );

                if (is_null($file)) {
                    $return_hash['error'] = sprintf(_('File does not exist in %s'), PRODUCT_NAME);
                } else {
                    $filepath = $md['MDATA_KEY_FILEPATH'];
                    // $filepath = str_replace("\\", "", $filepath);
                    $file->setFilePath($filepath);
                }
            } elseif ($mode == 'delete') {
                $filepath = $md['MDATA_KEY_FILEPATH'];
                $filepath = str_replace('\\', '', $filepath);
                $file = Application_Model_StoredFile::RecallByFilepath($filepath, $con);

                if (is_null($file)) {
                    $return_hash['error'] = sprintf(_('File does not exist in %s'), PRODUCT_NAME);
                    Logging::warn("Attempt to delete file that doesn't exist.
                        Path: '{$filepath}'");
                } else {
                    $file->deleteByMediaMonitor();
                }
            } elseif ($mode == 'delete_dir') {
                $filepath = $md['MDATA_KEY_FILEPATH'];
                // $filepath = str_replace("\\", "", $filepath);
                $files = Application_Model_StoredFile::RecallByPartialFilepath($filepath, $con);

                foreach ($files as $file) {
                    $file->deleteByMediaMonitor();
                }
                $return_hash['success'] = 1;
            }

            if (!isset($return_hash['error'])) {
                $return_hash['fileid'] = is_null($file) ? '-1' : $file->getId();
            }
            $con->commit();
        } catch (Exception $e) {
            Logging::warn('rolling back');
            Logging::warn($e->getMessage());
            $con->rollback();
            $return_hash['error'] = $e->getMessage();
        }

        return $return_hash;
    }

    public function reloadMetadataGroupAction()
    {
        // extract all file metadata params from the request.
        // The value is a json encoded hash that has all the information related to this action
        // The key(mdXXX) does not have any meaning as of yet but it could potentially correspond
        // to some unique id.
        $request = $this->getRequest();
        $responses = [];
        $params = $request->getParams();
        $valid_modes = ['delete_dir', 'delete', 'moved', 'modify', 'create'];
        foreach ($params as $k => $raw_json) {
            // Valid requests must start with mdXXX where XXX represents at
            // least 1 digit
            if (!preg_match('/^md\d+$/', $k)) {
                continue;
            }
            $info_json = json_decode($raw_json, $assoc = true);

            // Log invalid requests
            if (!array_key_exists('mode', $info_json)) {
                Logging::info("Received bad request(key={$k}), no 'mode' parameter. Bad request is:");
                Logging::info($info_json);
                array_push($responses, [
                    'error' => _("Bad request. no 'mode' parameter passed."),
                    'key' => $k,
                ]);

                continue;
            }
            if (!in_array($info_json['mode'], $valid_modes)) {
                // A request still has a chance of being invalid even if it
                // exists but it's validated by $valid_modes array
                $mode = $info_json['mode'];
                Logging::info("Received bad request(key={$k}). 'mode' parameter was invalid with value: '{$mode}'. Request:");
                Logging::info($info_json);
                array_push($responses, [
                    'error' => _("Bad request. 'mode' parameter is invalid"),
                    'key' => $k,
                    'mode' => $mode,
                ]);

                continue;
            }
            // Removing 'mode' key from $info_json might not be necessary...
            $mode = $info_json['mode'];
            unset($info_json['mode']);

            try {
                $response = $this->dispatchMetadata($info_json, $mode);
            } catch (Exception $e) {
                Logging::warn($e->getMessage());
                Logging::warn(gettype($e));
            }
            // We tack on the 'key' back to every request in case the would like to associate
            // his requests with particular responses
            $response['key'] = $k;
            array_push($responses, $response);
        }
        $this->_helper->json->sendJson($responses);
    }

    public function listAllFilesAction()
    {
        $request = $this->getRequest();
        $dir_id = $request->getParam('dir_id');
        $all = $request->getParam('all');

        $this->view->files =
            Application_Model_StoredFile::listAllFiles($dir_id, $all);
    }

    public function getStreamSettingAction()
    {
        $info = Application_Model_StreamSetting::getStreamSetting();
        $this->view->msg = $info;
    }

    public function statusAction()
    {
        $request = $this->getRequest();
        $getDiskInfo = $request->getParam('diskinfo') == 'true';
        $config = Config::getConfig();

        $status = [
            'platform' => Application_Model_Systemstatus::GetPlatformInfo(),
            'airtime_version' => $config['airtime_version'],
            'services' => [
                'pypo' => Application_Model_Systemstatus::GetPypoStatus(),
                'liquidsoap' => Application_Model_Systemstatus::GetLiquidsoapStatus(),
                'media_monitor' => Application_Model_Systemstatus::GetMediaMonitorStatus(),
            ],
        ];

        if ($getDiskInfo) {
            $status['partitions'] = Application_Model_Systemstatus::GetDiskInfo();
        }

        $this->view->status = $status;
    }

    public function registerComponentAction()
    {
        $request = $this->getRequest();

        $component = $request->getParam('component');
        $remoteAddr = Application_Model_ServiceRegister::GetRemoteIpAddr();
        Logging::info('Registered Component: ' . $component . '@' . $remoteAddr);

        Application_Model_ServiceRegister::Register($component, $remoteAddr);
    }

    public function updateLiquidsoapStatusAction()
    {
        $request = $this->getRequest();

        $msg = $request->getParam('msg_post');
        $stream_id = $request->getParam('stream_id');
        $boot_time = $request->getParam('boot_time');

        Application_Model_Preference::setLiquidsoapError($stream_id, $msg, $boot_time);
    }

    public function updateSourceStatusAction()
    {
        $request = $this->getRequest();

        $sourcename = $request->getParam('sourcename');
        $status = $request->getParam('status');

        // on source disconnection sent msg to pypo to turn off the switch
        // Added AutoTransition option
        if ($status == 'false' && Application_Model_Preference::GetAutoTransition()) {
            $data = ['sourcename' => $sourcename, 'status' => 'off'];
            Application_Model_RabbitMq::SendMessageToPypo('switch_source', $data);
            Application_Model_Preference::SetSourceSwitchStatus($sourcename, 'off');
            Application_Model_LiveLog::SetEndTime(
                $sourcename == 'scheduled_play' ? 'S' : 'L',
                new DateTime('now', new DateTimeZone('UTC'))
            );
        } elseif ($status == 'true' && Application_Model_Preference::GetAutoSwitch()) {
            $data = ['sourcename' => $sourcename, 'status' => 'on'];
            Application_Model_RabbitMq::SendMessageToPypo('switch_source', $data);
            Application_Model_Preference::SetSourceSwitchStatus($sourcename, 'on');
            Application_Model_LiveLog::SetNewLogTime(
                $sourcename == 'scheduled_play' ? 'S' : 'L',
                new DateTime('now', new DateTimeZone('UTC'))
            );
        }
        Application_Model_Preference::SetSourceStatus($sourcename, $status);
    }

    /* This action is for use by our dev scripts, that make
     * a change to the database and we want rabbitmq to send
     * out a message to pypo that a potential change has been made. */
    public function rabbitmqDoPushAction()
    {
        Logging::info('Notifying RabbitMQ to send message to pypo');

        Application_Model_RabbitMq::SendMessageToPypo('reset_liquidsoap_bootstrap', []);
        Application_Model_RabbitMq::PushSchedule();
    }

    public function getBootstrapInfoAction()
    {
        $live_dj = Application_Model_Preference::GetSourceSwitchStatus('live_dj');
        $master_dj = Application_Model_Preference::GetSourceSwitchStatus('master_dj');
        $scheduled_play = Application_Model_Preference::GetSourceSwitchStatus('scheduled_play');

        $res = ['live_dj' => $live_dj, 'master_dj' => $master_dj, 'scheduled_play' => $scheduled_play];
        $this->view->switch_status = $res;
        $this->view->station_name = Application_Model_Preference::GetStationName();
        $this->view->stream_label = Application_Model_Preference::GetStreamLabelFormat();
        $this->view->transition_fade = Application_Model_Preference::GetDefaultTransitionFade();
    }

    // This is used but Liquidsoap to check authentication of live streams
    public function checkLiveStreamAuthAction()
    {
        $request = $this->getRequest();

        $username = $request->getParam('username');
        $password = $request->getParam('password');
        $djtype = $request->getParam('djtype');

        if ($djtype == 'master') {
            // check against master
            if (
                $username == Application_Model_Preference::GetLiveStreamMasterUsername()
                && $password == Application_Model_Preference::GetLiveStreamMasterPassword()
            ) {
                $this->view->msg = true;
            } else {
                $this->view->msg = false;
            }
        } elseif ($djtype == 'dj') {
            // check against show dj auth
            $showInfo = Application_Model_Show::getCurrentShow();

            // there is current playing show
            if (isset($showInfo[0]['id'])) {
                $current_show_id = $showInfo[0]['id'];
                $CcShow = CcShowQuery::create()->findPK($current_show_id);

                // get custom pass info from the show
                $custom_user = $CcShow->getDbLiveStreamUser();
                $custom_pass = $CcShow->getDbLiveStreamPass();

                // get hosts ids
                $show = new Application_Model_Show($current_show_id);
                $hosts_ids = $show->getHostsIds();

                // check against hosts auth
                if ($CcShow->getDbLiveStreamUsingAirtimeAuth()) {
                    foreach ($hosts_ids as $host) {
                        $h = new Application_Model_User($host['subjs_id']);
                        if ($username == $h->getLogin() && md5($password) == $h->getPassword()) {
                            $this->view->msg = true;

                            return;
                        }
                    }
                }
                // check against custom auth
                if ($CcShow->getDbLiveStreamUsingCustomAuth()) {
                    if ($username == $custom_user && $password == $custom_pass) {
                        $this->view->msg = true;
                    } else {
                        $this->view->msg = false;
                    }
                } else {
                    $this->view->msg = false;
                }
            } else {
                // no show is currently playing
                $this->view->msg = false;
            }
        }
    }

    /* This action is for use by our dev scripts, that make
     * a change to the database and we want rabbitmq to send
     * out a message to pypo that a potential change has been made. */
    public function getFilesWithoutReplayGainAction()
    {
        $dir_id = $this->_getParam('dir_id');

        // connect to db and get get sql
        $rows = Application_Model_StoredFile::listAllFiles2($dir_id, 100);

        $this->_helper->json->sendJson($rows);
    }

    public function getFilesWithoutSilanValueAction()
    {
        // connect to db and get get sql
        $rows = Application_Model_StoredFile::getAllFilesWithoutSilan();

        $this->_helper->json->sendJson($rows);
    }

    public function updateReplayGainValueAction()
    {
        $request = $this->getRequest();
        $data = json_decode($request->getParam('data'));

        foreach ($data as $pair) {
            [$id, $gain] = $pair;
            // TODO : move this code into model -- RG
            $file = Application_Model_StoredFile::RecallById($p_id = $id)->getPropelOrm();
            $file->setDbReplayGain($gain);
            $file->save();
        }

        $this->_helper->json->sendJson([]);
    }

    public function updateCueValuesBySilanAction()
    {
        $request = $this->getRequest();
        $data = json_decode($request->getParam('data'), $assoc = true);

        foreach ($data as $pair) {
            [$id, $info] = $pair;
            // TODO : move this code into model -- RG
            $file = Application_Model_StoredFile::RecallById($p_id = $id)->getPropelOrm();

            // What we are doing here is setting a more accurate length that was
            // calculated with silan by actually scanning the entire file. This
            // process takes a really long time, and so we only do it in the background
            // after the file has already been imported -MK
            try {
                $length = $file->getDbLength();
                if (isset($info['length'])) {
                    $length = $info['length'];
                    // length decimal number in seconds. Need to convert it to format
                    // HH:mm:ss to get around silly PHP limitations.
                    $length = Application_Common_DateHelper::secondsToPlaylistTime($length);
                    $file->setDbLength($length);
                }

                $cuein = isset($info['cuein']) ? $info['cuein'] : 0;
                $cueout = isset($info['cueout']) ? $info['cueout'] : $length;

                $file->setDbCuein($cuein);
                $file->setDbCueout($cueout);
                $file->setDbSilanCheck(true);
                $file->save();
            } catch (Exception $e) {
                Logging::info('Failed to update silan values for ' . $file->getDbTrackTitle());
                Logging::info('File length analyzed by Silan is: ' . $length);
                // set silan_check to true so we don't attempt to re-anaylze again
                $file->setDbSilanCheck(true);
                $file->save();
            }
        }

        $this->_helper->json->sendJson([]);
    }

    public function notifyWebstreamDataAction()
    {
        $request = $this->getRequest();
        $data = $request->getParam('data');
        $media_id = intval($request->getParam('media_id'));
        $data_arr = json_decode($data);

        // $media_id is -1 sometimes when a stream has stopped playing
        if (!is_null($media_id) && $media_id > 0) {
            if (isset($data_arr->title)) {
                $data_title = substr($data_arr->title, 0, 1024);

                $previous_metadata = CcWebstreamMetadataQuery::create()
                    ->orderByDbStartTime('desc')
                    ->filterByDbInstanceId($media_id)
                    ->findOne();

                $do_insert = true;
                if ($previous_metadata) {
                    if ($previous_metadata->getDbLiquidsoapData() == $data_title) {
                        Logging::debug('Duplicate found: ' . $data_title);
                        $do_insert = false;
                    }
                }

                if ($do_insert) {
                    $startDT = new DateTime('now', new DateTimeZone('UTC'));

                    $webstream_metadata = new CcWebstreamMetadata();
                    $webstream_metadata->setDbInstanceId($media_id);
                    $webstream_metadata->setDbStartTime($startDT);
                    $webstream_metadata->setDbLiquidsoapData($data_title);
                    $webstream_metadata->save();

                    $historyService = new Application_Service_HistoryService();
                    $historyService->insertWebstreamMetadata($media_id, $startDT, $data_arr);
                }
            }
        }

        $this->view->response = $data;
        $this->view->media_id = $media_id;
    }

    public function getStreamParametersAction()
    {
        $streams = ['s1', 's2', 's3', 's4'];
        $stream_params = [];
        foreach ($streams as $s) {
            $stream_params[$s] =
                Application_Model_StreamSetting::getStreamDataNormalized($s);
        }
        $this->view->stream_params = $stream_params;
    }

    public function pushStreamStatsAction()
    {
        $request = $this->getRequest();

        $data_blob = $request->getParam('data');
        $data = json_decode($data_blob, true);

        if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
            $message = "An error occured while decoding the 'data' JSON blob: '{$data_blob}'";
            Logging::error($message);
            $this->jsonError(400, $message);

            return;
        }

        Application_Model_ListenerStat::insertDataPoints($data);
        $this->view->data = $data;
    }

    public function updateStreamSettingTableAction()
    {
        $request = $this->getRequest();

        $data_blob = $request->getParam('data');
        $data = json_decode($data_blob, true);

        if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
            $message = "An error occured while decoding the 'data' JSON blob: '{$data_blob}'";
            Logging::error($message);
            $this->jsonError(400, $message);

            return;
        }

        foreach ($data as $stream_id => $status) {
            Application_Model_Preference::SetListenerStatError($stream_id, $status);
        }
    }

    /**
     * display played items for a given time range and show instance_id.
     *
     * @return json array
     */
    public function itemHistoryFeedAction()
    {
        try {
            $request = $this->getRequest();
            $params = $request->getParams();
            $instance = $request->getParam('instance_id', null);

            [$startsDT, $endsDT] = Application_Common_HTTPHelper::getStartEndFromRequest($request);

            $historyService = new Application_Service_HistoryService();
            $results = $historyService->getPlayedItemData($startsDT, $endsDT, $params, $instance);

            $this->_helper->json->sendJson($results['history']);
        } catch (Exception $e) {
            Logging::info($e);
            Logging::info($e->getMessage());
        }
    }

    /**
     * display show schedules for a given time range and show instance_id.
     *
     * @return json array
     */
    public function showHistoryFeedAction()
    {
        try {
            $request = $this->getRequest();
            $params = $request->getParams();
            $userId = $request->getParam('user_id', null);

            [$startsDT, $endsDT] = Application_Common_HTTPHelper::getStartEndFromRequest($request);

            $historyService = new Application_Service_HistoryService();
            $shows = $historyService->getShowList($startsDT, $endsDT, $userId);

            $this->_helper->json->sendJson($shows);
        } catch (Exception $e) {
            Logging::info($e);
            Logging::info($e->getMessage());
        }
    }

    /**
     * display show info (without schedule) for given show_id.
     *
     * @return json array
     */
    public function showsAction()
    {
        try {
            $request = $this->getRequest();
            $params = $request->getParams();
            $showId = $request->getParam('show_id', null);
            $results = [];

            if (empty($showId)) {
                $shows = CcShowQuery::create()->find();
                foreach ($shows as $show) {
                    $results[] = $show->getShowInfo();
                }
            } else {
                $show = CcShowQuery::create()->findPK($showId);
                $results[] = $show->getShowInfo();
            }

            $this->_helper->json->sendJson($results);
        } catch (Exception $e) {
            Logging::info($e);
            Logging::info($e->getMessage());
        }
    }

    /**
     * display show schedule for given show_id.
     *
     * @return json array
     */
    public function showSchedulesAction()
    {
        try {
            $request = $this->getRequest();
            $params = $request->getParams();
            $showId = $request->getParam('show_id', null);

            [$startsDT, $endsDT] = Application_Common_HTTPHelper::getStartEndFromRequest($request);

            if ((!isset($showId)) || (!is_numeric($showId))) {
                // if (!isset($showId)) {
                $this->_helper->json->sendJson(
                    ['jsonrpc' => '2.0', 'error' => ['code' => 400, 'message' => 'missing invalid type for required show_id parameter. use type int.' . $showId]]
                );
            }

            $shows = Application_Model_Show::getShows($startsDT, $endsDT, false, $showId);

            // is this a valid show?
            if (empty($shows)) {
                $this->_helper->json->sendJson(
                    ['jsonrpc' => '2.0', 'error' => ['code' => 204, 'message' => 'no content for requested show_id']]
                );
            }

            $this->_helper->json->sendJson($shows);
        } catch (Exception $e) {
            Logging::info($e);
            Logging::info($e->getMessage());
        }
    }

    /**
     * displays track listing for given instance_id.
     *
     * @return json array
     */
    public function showTracksAction()
    {
        $baseUrl = Config::getBasePath();
        $prefTimezone = Application_Model_Preference::GetTimezone();

        $instanceId = $this->_getParam('instance_id');

        if ((!isset($instanceId)) || (!is_numeric($instanceId))) {
            $this->_helper->json->sendJson(
                ['jsonrpc' => '2.0', 'error' => ['code' => 400, 'message' => 'missing invalid type for required instance_id parameter. use type int']]
            );
        }

        $showInstance = new Application_Model_ShowInstance($instanceId);
        $showInstanceContent = $showInstance->getShowListContent($prefTimezone);

        // is this a valid show instance with content?
        if (empty($showInstanceContent)) {
            $this->_helper->json->sendJson(
                ['jsonrpc' => '2.0', 'error' => ['code' => 204, 'message' => 'no content for requested instance_id']]
            );
        }

        $result = [];
        $position = 0;
        foreach ($showInstanceContent as $track) {
            $elementMap = [
                'title' => isset($track['track_title']) ? $track['track_title'] : '',
                'artist' => isset($track['creator']) ? $track['creator'] : '',
                'position' => $position,
                'id' => ++$position,
                'mime' => isset($track['mime']) ? $track['mime'] : '',
                'starts' => isset($track['starts']) ? $track['starts'] : '',
                'length' => isset($track['length']) ? $track['length'] : '',
                'file_id' => ($track['type'] == 0) ? $track['item_id'] : $track['filepath'],
            ];

            $result[] = $elementMap;
        }

        $this->_helper->json($result);
    }

    /**
     * This function is called from PYPO (pypofetch) every 2 minutes and updates
     * metadata on TuneIn if we haven't done so in the last 4 minutes. We have
     * to do this because TuneIn turns off metadata if it has not received a
     * request within 5 minutes. This is necessary for long tracks > 5 minutes.
     */
    public function updateMetadataOnTuneinAction()
    {
        if (!Application_Model_Preference::getTuneinEnabled()) {
            $this->_helper->json->sendJson([0]);
        }

        $lastTuneInMetadataUpdate = Application_Model_Preference::geLastTuneinMetadataUpdate();
        if (time() - $lastTuneInMetadataUpdate >= 240) {
            $metadata = $metadata = Application_Model_Schedule::getCurrentPlayingTrack();
            if (!is_null($metadata)) {
                Application_Common_TuneIn::sendMetadataToTunein(
                    $metadata['title'],
                    $metadata['artist']
                );
            }
        }
        $this->_helper->json->sendJson([1]);
    }

    public function getUsabilityHintAction()
    {
        $userPath = $this->_getParam('userPath');

        $hint = Application_Common_UsabilityHints::getUsabilityHint($userPath);
        $this->_helper->json->sendJson($hint);
    }

    public function streamM3uAction()
    {
        $this->view->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        header('Content-Type: application/x-mpegurl');
        header('Content-Disposition: attachment; filename=stream.m3u');
        $m3uFile = "#EXTM3U\r\n\r\n"; // Windows linebreaks eh

        $stationName = Application_Model_Preference::GetStationName();
        $streamData = Application_Model_StreamSetting::getEnabledStreamData();

        foreach ($streamData as $stream) {
            $m3uFile .= '#EXTINF,' . $stationName . ' - ' . strtoupper($stream['codec']) . "\r\n";
            $m3uFile .= $stream['url'] . "\r\n\r\n";
        }
        echo $m3uFile;
    }

    public function recalculateScheduleAction()
    {
        $this->view->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        Zend_Session::start();

        $scheduler = new Application_Model_Scheduler();
        session_write_close();

        $now = new DateTime('now', new DateTimeZone('UTC'));

        $showInstances = CcShowInstancesQuery::create()
            ->filterByDbStarts($now, Criteria::GREATER_THAN)
            // ->filterByDbModifiedInstance(false)
            ->orderByDbStarts()
            ->find();
        // ->find($this->con);
        $total = $showInstances->count();
        $progress = 0;
        foreach ($showInstances as $instance) {
            echo round(floatval($progress / $total) * 100) . '% - ' . $instance->getDbId() . "\n<br>";
            flush();
            ob_flush();
            // while(@ob_end_clean());
            $scheduler->removeGaps2($instance->getDbId());
            ++$progress;
        }
        echo "Recalculated {$total} shows.";
    }

    final private function returnJsonOrJsonp($request, $result)
    {
        $callback = $request->getParam('callback');
        $response = $this->getResponse();
        $response->setHeader('Content-Type', 'application/json');

        $body = $this->_helper->json->encodeJson($result, false);

        if ($callback) {
            $response->setHeader('Content-Type', 'application/javascript');
            $body = sprintf('%s(%s)', $callback, $body);
        }
        $response->setBody($body);

        // enable cors access from configured URLs
        CORSHelper::enableCrossOriginRequests($request, $response);
    }

    /**
     * Respond with a JSON error message with a custom HTTP status code.
     *
     * This logic should be handled by Zend, but I lack understanding of this
     * framework, and prefer not break it or spend too much time on it.
     *
     * @param mixed $status
     * @param mixed $message
     */
    final private function jsonError($status, $message)
    {
        $this->getResponse()
            ->setHttpResponseCode($status)
            ->setHeader('Content-Type', 'application/json')
            ->setBody(json_encode(['error' => $message]));
    }
}
