<?php

class Application_Model_Preference
{

    private static function getUserId()
    {
        //pass in true so the check is made with the autoloader
        //we need this check because saas calls this function from outside Zend
        if (!class_exists("Zend_Session", true) || !Zend_Session::isStarted() || !class_exists("Zend_Auth", true) || !Zend_Auth::getInstance()->hasIdentity()) {
            $userId = null;
        } else {
            $auth = Zend_Auth::getInstance();
            $userId = $auth->getIdentity()->id;
        }

        return $userId;
    }

    /**
     *
     * @param boolean $isUserValue is true when we are setting a value for the current user
     */
    private static function setValue($key, $value, $isUserValue = false)
    {
        $con = Propel::getConnection(CcPrefPeer::DATABASE_NAME);

        //We are using row-level locking in Postgres via "FOR UPDATE" instead of a transaction here
        //because sometimes this function needs to be called while a transaction is already started.

        try {
            /* Comment this out while we reevaluate it in favor of a unique constraint
            static::_lock($con); */
            $userId = self::getUserId();

            if ($isUserValue && is_null($userId)) {
                throw new Exception("User id can't be null for a user preference {$key}.");
            }

            //Check if key already exists
            $sql = "SELECT valstr FROM cc_pref"
                ." WHERE keystr = :key";

            $paramMap = array();
            $paramMap[':key'] = $key;

            //For user specific preference, check if id matches as well
            if ($isUserValue) {
                $sql .= " AND subjid = :id";
                $paramMap[':id'] = $userId;
            }

            $sql .= " FOR UPDATE";

            $result = Application_Common_Database::prepareAndExecute($sql,
                    $paramMap,
                    Application_Common_Database::ROW_COUNT,
                    PDO::FETCH_ASSOC,
                    $con);

            $paramMap = array();
            if ($result > 1) {
                //this case should not happen.
                throw new Exception("Invalid number of results returned. Should be ".
                    "0 or 1, but is '$result' instead");
            } else if ($result == 1) {

                // result found
                if (!$isUserValue) {
                    // system pref
                    $sql = "UPDATE cc_pref"
                        ." SET subjid = NULL, valstr = :value"
                        ." WHERE keystr = :key";
                } else {
                    // user pref
                    $sql = "UPDATE cc_pref"
                        . " SET valstr = :value"
                        . " WHERE keystr = :key AND subjid = :id";

                    $paramMap[':id'] = $userId;
                }
            } else {

                // result not found
                if (!$isUserValue) {
                    // system pref
                    $sql = "INSERT INTO cc_pref (keystr, valstr)"
                        ." VALUES (:key, :value)";
                } else {
                    // user pref
                    $sql = "INSERT INTO cc_pref (subjid, keystr, valstr)"
                        ." VALUES (:id, :key, :value)";

                    $paramMap[':id'] = $userId;
                }
            }
            $paramMap[':key'] = $key;
            $paramMap[':value'] = $value;

            Application_Common_Database::prepareAndExecute($sql,
                    $paramMap,
                    Application_Common_Database::EXECUTE,
                    PDO::FETCH_ASSOC,
                    $con);

        } catch (Exception $e) {
            header('HTTP/1.0 503 Service Unavailable');
            Logging::info("Database error: ".$e->getMessage());
            exit;
        }
    }

    /**
     * Given a PDO connection, lock the cc_pref table for the current transaction
     *
     * Creates a table level lock, which defaults to ACCESS EXCLUSIVE mode;
     * see http://www.postgresql.org/docs/9.1/static/explicit-locking.html
     *
     * @param PDO $con
     */
    private static function _lock($con) {
        // If we're not in a transaction, a lock is pointless
        if (!$con->inTransaction()) {
            return;
        }
        // Don't specify NOWAIT here; we should block on obtaining this lock
        // in case we're handling simultaneous requests.
        // Locks only last until the end of the transaction, so we shouldn't have to
        // worry about this causing any noticeable difference in request processing speed
        $sql = "LOCK TABLE cc_pref";
        $st = $con->prepare($sql);
        $st->execute();
    }

    /**
     * @param string $key               the preference key string
     * @param bool|false $isUserValue   select the preference for the current user
     * @param bool|false $forceDefault  only look for default (no user ID) values
     * @return mixed the preference value
     */
    private static function getValue($key, $isUserValue = false, $forceDefault = false)
    {
        try {

            $userId = null;
            if ($isUserValue) {
                //This is nested in here because so we can still use getValue() when the session hasn't started yet.
                $userId = self::getUserId();
                if (is_null($userId)) {
                    throw new Exception("User id can't be null for a user preference.");
                }
            }

            //Check if key already exists
            $sql = "SELECT COUNT(*) FROM cc_pref"
            ." WHERE keystr = :key";

            $paramMap = array();
            $paramMap[':key'] = $key;

            //For user specific preference, check if id matches as well
            if ($isUserValue) {
                $sql .= " AND subjid = :id";
                $paramMap[':id'] = $userId;
            } else if ($forceDefault) {
                $sql .= " AND subjid IS NULL";
            }

            $result = Application_Common_Database::prepareAndExecute($sql, $paramMap, Application_Common_Database::COLUMN);

            //return an empty string if the result doesn't exist.
            if ($result == 0) {
                $res = "";
            } else {
                $sql = "SELECT valstr FROM cc_pref"
                ." WHERE keystr = :key";

                $paramMap = array();
                $paramMap[':key'] = $key;

                //For user specific preference, check if id matches as well
                if ($isUserValue) {
                    $sql .= " AND subjid = :id";
                    $paramMap[':id'] = $userId;
                }

                $result = Application_Common_Database::prepareAndExecute($sql, $paramMap, Application_Common_Database::COLUMN);

                $res = ($result !== false) ? $result : "";
            }

            return $res;
        }
        catch (Exception $e) {
            header('HTTP/1.0 503 Service Unavailable');
            Logging::info("Could not connect to database: ".$e);
            exit;
        }
    }

    public static function GetHeadTitle()
    {
        $title = self::getValue("station_name");
        if (empty($title)) {
            $title = PRODUCT_NAME;
        }

        return $title;
    }

    public static function SetHeadTitle($title, $view=null)
    {
        self::setValue("station_name", $title);

        // in case this is called from airtime-saas script
        if ($view !== null) {
            //set session variable to new station name so that html title is updated.
            //should probably do this in a view helper to keep this controller as minimal as possible.
            $view->headTitle()->exchangeArray(array()); //clear headTitle ArrayObject
            $view->headTitle(self::GetHeadTitle());
        }

        $eventType = "update_station_name";
        $md = array("station_name"=>$title);

        Application_Model_RabbitMq::SendMessageToPypo($eventType, $md);
    }

    /**
     * Set the furthest date that a never-ending show
     * should be populated until.
     *
     * @param DateTime $dateTime
     *        A row from cc_show_days table
     */
    public static function SetShowsPopulatedUntil($dateTime)
    {
        $dateTime->setTimezone(new DateTimeZone("UTC"));
        self::setValue("shows_populated_until", $dateTime->format(DEFAULT_TIMESTAMP_FORMAT));
    }

    /**
     * Get the furthest date that a never-ending show
     * should be populated until.
     *
     * Returns null if the value hasn't been set, otherwise returns
     * a DateTime object representing the date.
     *
     * @return DateTime (in UTC Timezone)
     */
    public static function GetShowsPopulatedUntil()
    {
        $date = self::getValue("shows_populated_until");

        if ($date == "") {
            return null;
        } else {
            return new DateTime($date, new DateTimeZone("UTC"));
        }
    }

    public static function SetDefaultCrossfadeDuration($duration)
    {
        self::setValue("default_crossfade_duration", $duration);
    }

    public static function GetDefaultCrossfadeDuration()
    {
        $duration = self::getValue("default_crossfade_duration");

        if ($duration === "") {
            // the default value of the fade is 00.5
            return "0";
        }

        return $duration;
    }

    public static function SetDefaultFadeIn($fade)
    {
        self::setValue("default_fade_in", $fade);
    }

    public static function GetDefaultFadeIn()
    {
        $fade = self::getValue("default_fade_in");

        if ($fade === "") {
            // the default value of the fade is 00.5
            return "0.5";
        }

        return $fade;
    }

    public static function SetDefaultFadeOut($fade)
    {
        self::setValue("default_fade_out", $fade);
    }

    public static function GetDefaultFadeOut()
    {
        $fade = self::getValue("default_fade_out");

        if ($fade === "") {
            // the default value of the fade is 0.5
            return "0.5";
        }

        return $fade;
    }

    public static function SetDefaultFade($fade)
    {
        self::setValue("default_fade", $fade);
    }

    public static function SetDefaultTransitionFade($fade)
    {
        self::setValue("default_transition_fade", $fade);

        $eventType = "update_transition_fade";
        $md = array("transition_fade"=>$fade);
        Application_Model_RabbitMq::SendMessageToPypo($eventType, $md);
    }

    public static function GetDefaultTransitionFade()
    {
        $transition_fade = self::getValue("default_transition_fade");
        return ($transition_fade == "") ? "0.000" : $transition_fade;
    }

    public static function SetStreamLabelFormat($type)
    {
        self::setValue("stream_label_format", $type);

        $eventType = "update_stream_format";
        $md = array("stream_format"=>$type);

        Application_Model_RabbitMq::SendMessageToPypo($eventType, $md);
    }

    public static function GetStreamLabelFormat()
    {
        return self::getValue("stream_label_format");
    }

    public static function GetStationName()
    {
        return self::getValue("station_name");
    }

    public static function SetStationName($station_name)
    {
        self::setValue("station_name", $station_name);
    }

    public static function SetAllow3rdPartyApi($bool)
    {
        self::setValue("third_party_api", $bool);
    }

    public static function GetAllow3rdPartyApi()
    {
        $val = self::getValue("third_party_api");
        return (strlen($val) == 0 ) ? "1" : $val;
    }

    public static function SetPodcastAlbumOverride($bool)
    {
        self::setValue("podcast_album_override", $bool);
    }

    public static function GetPodcastAlbumOverride()
    {
        $val = self::getValue("podcast_album_override");
        return $val === '1' ? true : false;
    }

    public static function SetPodcastAutoSmartblock($bool)
    {
        self::setValue("podcast_auto_smartblock", $bool);
    }

    public static function GetPodcastAutoSmartblock()
    {
        $val = self::getValue("podcast_auto_smartblock");
        return $val === '1' ? true : false;
    }

    public static function SetTrackTypeDefault($tracktype)
    {
        self::setValue("tracktype_default", $tracktype);
    }

    public static function GetTrackTypeDefault()
    {
        return self::getValue("tracktype_default");
    }

    public static function GetIntroPlaylist()
    {
        return self::getValue("intro_playlist");
    }

    public static function GetOutroPlaylist()
    {
        return self::getValue("outro_playlist");
    }


    public static function SetIntroPlaylist($playlist)
    {
        self::setValue("intro_playlist", $playlist);
    }

    public static function SetOutroPlaylist($playlist)
    {
        self::setValue("outro_playlist", $playlist);
    }

    public static function SetPhone($phone)
    {
        self::setValue("phone", $phone);
    }

    public static function GetPhone()
    {
        return self::getValue("phone");
    }

    public static function SetEmail($email)
    {
        self::setValue("email", $email);
    }

    public static function GetEmail()
    {
        return self::getValue("email");
    }

    public static function SetStationWebSite($site)
    {
        self::setValue("station_website", $site);
    }

    public static function GetStationWebSite()
    {
        return self::getValue("station_website");
    }

    public static function SetSupportFeedback($feedback)
    {
        self::setValue("support_feedback", $feedback);
    }

    public static function GetSupportFeedback()
    {
        return self::getValue("support_feedback");
    }

    public static function SetPublicise($publicise)
    {
        self::setValue("publicise", $publicise);
    }

    public static function GetPublicise()
    {
        return self::getValue("publicise");
    }

    public static function SetRegistered($registered)
    {
        self::setValue("registered", $registered);
    }

    public static function GetRegistered()
    {
        return self::getValue("registered");
    }

    public static function SetStationCountry($country)
    {
        self::setValue("country", $country);
    }

    public static function GetStationCountry()
    {
        return self::getValue("country");
    }

    public static function SetStationCity($city)
    {
        self::setValue("city", $city);
    }

    public static function GetStationCity()
    {
        return self::getValue("city");
    }

    public static function SetStationDescription($description)
    {
        self::setValue("description", $description);
    }

    public static function GetStationDescription()
    {
        $description = self::getValue("description");
        if (!empty($description)) {
            return $description;
        } else {
            return sprintf(_("Powered by %s"), SAAS_PRODUCT_BRANDING_NAME);
        }
    }

    // Sets station default timezone (from preferences)
    public static function SetDefaultTimezone($timezone)
    {
        self::setValue("timezone", $timezone);
    }

    // Returns station default timezone (from preferences)
    public static function GetDefaultTimezone()
    {
        $stationTimezone = self::getValue("timezone");
        if (is_null($stationTimezone) || $stationTimezone == "") {
            $stationTimezone = "UTC";
        }
        return $stationTimezone;
    }

    public static function SetUserTimezone($timezone = null)
    {
        self::setValue("user_timezone", $timezone, true);
    }

    public static function GetUserTimezone()
    {
        $timezone = self::getValue("user_timezone", true);
        if (!$timezone) {
            return self::GetDefaultTimezone();
        } else {
            return $timezone;
        }
    }

    // Always attempts to returns the current user's personal timezone setting
    public static function GetTimezone()
    {
        $userId = self::getUserId();

        if (!is_null($userId)) {
            return self::GetUserTimezone();
        } else {
            return self::GetDefaultTimezone();
        }
    }

    // This is the language setting on preferences page
    public static function SetDefaultLocale($locale)
    {
        self::setValue("locale", $locale);
    }

    public static function GetDefaultLocale()
    {
        return self::getValue("locale");
    }

    public static function GetUserLocale()
    {
        $locale = self::getValue("user_locale", true);
        // empty() checks for null and empty strings - more robust than !val
        if (empty($locale)) {
            return self::GetDefaultLocale();
        } else {
            return $locale;
        }
    }

    public static function SetUserLocale($locale = null)
    {
        // When a new user is created they will get the default locale
        // setting which the admin sets on preferences page
        if (is_null($locale))
            $locale = self::GetDefaultLocale();
        self::setValue("user_locale", $locale, true);
    }

    public static function GetLocale()
    {
        $userId = self::getUserId();

        if (!is_null($userId)) {
            return self::GetUserLocale();
        } else {
            return self::GetDefaultLocale();
        }
    }

    public static function SetStationLogo($imagePath)
    {
        if (empty($imagePath)) {
            Logging::info("Removed station logo");
        }
        $image = @file_get_contents($imagePath);
        $image = base64_encode($image);
        self::setValue("logoImage", $image);
    }

    public static function GetStationLogo()
    {
        $logoImage = self::getValue("logoImage");
        if (!empty($logoImage)) {
            return $logoImage;
        } else {
            // We return the Airtime logo if no logo is set in the database.
            // airtime_logo.png is stored under the public directory
            $image = @file_get_contents(Application_Common_HTTPHelper::getStationUrl() . DEFAULT_LOGO_FILE);
            $image = base64_encode($image);
            return $image;
        }
    }

    public static function SetUniqueId($id)
    {
        self::setValue("uniqueId", $id);
    }

    public static function GetUniqueId()
    {
        return self::getValue("uniqueId");
    }

    public static function GetCountryList()
    {
        $sql = "SELECT * FROM cc_country";

        $res = Application_Common_Database::prepareAndExecute($sql, array());

        $out = array();
        $out[""] = _("Select Country");
        foreach ($res as $r) {
            $out[$r["isocode"]] = $r["name"];
        }

        return $out;
    }

    public static function GetSystemInfo($returnArray=false, $p_testing=false)
    {
        exec('/usr/bin/airtime-check-system --no-color', $output);
        $output = preg_replace('/\s+/', ' ', $output);

        $systemInfoArray = array();
        foreach ($output as $key => &$out) {
            $info = explode('=', $out);
            if (isset($info[1])) {
                $key = str_replace(' ', '_', trim($info[0]));
                $key = strtoupper($key);
                if ($key == 'WEB_SERVER' || $key == 'CPU' || $key == 'OS'  || $key == 'TOTAL_RAM' ||
                          $key == 'FREE_RAM' || $key == 'AIRTIME_VERSION' || $key == 'KERNAL_VERSION'  ||
                          $key == 'MACHINE_ARCHITECTURE' || $key == 'TOTAL_MEMORY_MBYTES' || $key == 'TOTAL_SWAP_MBYTES' ||
                          $key == 'PLAYOUT_ENGINE_CPU_PERC') {
                    if ($key == 'AIRTIME_VERSION') {
                        // remove hash tag on the version string
                        $version = explode('+', $info[1]);
                        $systemInfoArray[$key] = $version[0];
                    } else {
                        $systemInfoArray[$key] = $info[1];
                    }
                }
            }
        }

        $outputArray = array();

        $outputArray['LIVE_DURATION'] = Application_Model_LiveLog::GetLiveShowDuration($p_testing);
        $outputArray['SCHEDULED_DURATION'] = Application_Model_LiveLog::GetScheduledDuration($p_testing);

        $outputArray['STATION_NAME'] = self::GetStationName();
        $outputArray['PHONE'] = self::GetPhone();
        $outputArray['EMAIL'] = self::GetEmail();
        $outputArray['STATION_WEB_SITE'] = self::GetStationWebSite();
        $outputArray['STATION_COUNTRY'] = self::GetStationCountry();
        $outputArray['STATION_CITY'] = self::GetStationCity();
        $outputArray['STATION_DESCRIPTION'] = self::GetStationDescription();

        // get web server info
        if (isset($systemInfoArray["AIRTIME_VERSION_URL"])) {
           $url = $systemInfoArray["AIRTIME_VERSION_URL"];
           $index = strpos($url,'/api/');
           $url = substr($url, 0, $index);

           $headerInfo = get_headers(trim($url),1);
           $outputArray['WEB_SERVER'] = $headerInfo['Server'][0];
        }

        $outputArray['NUM_OF_USERS'] = Application_Model_User::getUserCount();
        $outputArray['NUM_OF_SONGS'] = Application_Model_StoredFile::getFileCount();
        $outputArray['NUM_OF_PLAYLISTS'] = Application_Model_Playlist::getPlaylistCount();
        $outputArray['NUM_OF_SCHEDULED_PLAYLISTS'] = Application_Model_Schedule::getSchduledPlaylistCount();
        $outputArray['NUM_OF_PAST_SHOWS'] = Application_Model_ShowInstance::GetShowInstanceCount(gmdate(DEFAULT_TIMESTAMP_FORMAT));
        $outputArray['UNIQUE_ID'] = self::GetUniqueId();
        $outputArray['INSTALL_METHOD'] = self::GetInstallMethod();
        $outputArray['NUM_OF_STREAMS'] = self::GetNumOfStreams();
        $outputArray['STREAM_INFO'] = Application_Model_StreamSetting::getStreamInfoForDataCollection();

        $outputArray = array_merge($systemInfoArray, $outputArray);

        $outputString = "\n";
        foreach ($outputArray as $key => $out) {
            if ($key == "STREAM_INFO") {
                $outputString .= $key." :\n";
                foreach ($out as $s_info) {
                    foreach ($s_info as $k => $v) {
                        $outputString .= "\t".strtoupper($k)." : ".$v."\n";
                    }
                }
            } else {
                $outputString .= $key.' : '.$out."\n";
            }
        }
        if ($returnArray) {
            $outputArray['PROMOTE'] = self::GetPublicise();
            $outputArray['LOGOIMG'] = self::GetStationLogo();

            return $outputArray;
        } else {
            return $outputString;
        }
    }

    public static function GetInstallMethod()
    {
        $easy_install = file_exists('/usr/bin/airtime-easy-setup');
        $debian_install = file_exists('/var/lib/dpkg/info/airtime.config');
        if ($debian_install) {
            if ($easy_install) {
                return "easy_install";
            } else {
                return "debian_install";
            }
        } else {
            return "manual_install";
        }
    }

    public static function SetRemindMeDate($p_never = false)
    {
        if ($p_never) {
            self::setValue("remindme", -1);
        } else {
            $weekAfter = mktime(0, 0, 0, gmdate("m"), gmdate("d")+7, gmdate("Y"));
            self::setValue("remindme", $weekAfter);
        }
    }

    public static function GetRemindMeDate()
    {
        return self::getValue("remindme");
    }

    public static function SetImportTimestamp()
    {
        $now = time();
        if (self::GetImportTimestamp()+5 < $now) {
            self::setValue("import_timestamp", $now);
        }
    }

    public static function GetImportTimestamp()
    {
        return (int) self::getValue("import_timestamp");
    }

    public static function GetStreamType()
    {
        $st = self::getValue("stream_type");

        return explode(',', $st);
    }

    public static function GetStreamBitrate()
    {
        $sb = self::getValue("stream_bitrate");

        return explode(',', $sb);
    }

    public static function SetPrivacyPolicyCheck($flag)
    {
        self::setValue("privacy_policy", $flag);
    }

    public static function GetPrivacyPolicyCheck()
    {
        return self::getValue("privacy_policy");
    }

    public static function SetNumOfStreams($num)
    {
        self::setValue("num_of_streams", intval($num));

        //Enable or disable each stream according to whatever was just changed.
        for ($streamIdx = 1; $streamIdx <= MAX_NUM_STREAMS; $streamIdx++)
        {
            $prefix = 's' . $streamIdx . '_';
            $enable = 'false';
            if ($streamIdx <= intval($num)) {
                $enable = 'true';
            }

            //Enable or disable the stream in the Stream Settings DB table.
            Application_Model_StreamSetting::setIndividualStreamSetting(array($prefix.'enable' => $enable));
        }
    }

    public static function GetNumOfStreams()
    {
        return self::getValue("num_of_streams");
    }

    public static function SetMaxBitrate($bitrate)
    {
        self::setValue("max_bitrate", intval($bitrate));
    }

    public static function GetMaxBitrate()
    {
        return self::getValue("max_bitrate");
    }

    public static function SetEnableStreamConf($bool)
    {
        self::setValue("enable_stream_conf", $bool);
    }

    public static function GetEnableStreamConf()
    {
        if (self::getValue("enable_stream_conf") == Null) {
            return "true";
        }

        return self::getValue("enable_stream_conf");
    }

    public static function GetSchemaVersion()
    {
        CcPrefPeer::clearInstancePool(); //Ensure we don't get a cached Propel object (cached DB results)
        //because we're updating this version number within this HTTP request as well.

        //New versions use schema_version
        $pref = CcPrefQuery::create()
            ->filterByKeystr('schema_version')
            ->findOne();

        if (empty($pref)) {
            //Pre-2.5.2 releases all used this ambiguous "system_version" key to represent both the code and schema versions...
            $pref = CcPrefQuery::create()
                ->filterByKeystr('system_version')
                ->findOne();
        }
        $schemaVersion = $pref->getValStr();
        return $schemaVersion;
    }

    public static function SetSchemaVersion($version)
    {
        self::setValue("schema_version", $version);
    }

    public static function GetLatestVersion()
    {
        $config = Config::getConfig();

        $latest = json_decode(self::getValue('latest_version'));
        $nextCheck = self::getValue('latest_version_nextcheck');
        if ($latest && $nextCheck > time()) {
            return $latest;
        }

        $rss = new SimplePie();
        $rss->set_feed_url(array(LIBRETIME_UPDATE_FEED));
        $rss->enable_cache(false);
        $rss->init();
        $rss->handle_content_type();
        // get all available versions ut to default github api limit
        $versions = array();
        foreach ($rss->get_items() as $item) {
            $versions[] = $item->get_title();
        }
        $latest = $versions;
        self::setValue('latest_version_nextcheck', strtotime('+1 week'));
        if (empty($latest)) {
            return array($config['airtime_version']);
        }

        self::setValue('latest_version', json_encode($latest));
        return $latest;
    }

    public static function SetLatestVersion($version)
    {
        $pattern = "/^[0-9]+\.[0-9]+\.[0-9]+/";
        if (preg_match($pattern, $version)) {
            self::setValue("latest_version", $version);
        }
    }

    public static function GetLatestLink()
    {
        $link = self::getValue("latest_link");
        if ($link == null || strlen($link) == 0) {
            return LIBRETIME_WHATS_NEW_URL;
        } else {
            return $link;
        }
    }

    public static function SetLatestLink($link)
    {
        $pattern = "#^(http|https|ftp)://" .
                    "([a-zA-Z0-9]+\.)*[a-zA-Z0-9]+" .
                    "(/[a-zA-Z0-9\-\.\_\~\:\?\#\[\]\@\!\$\&\'\(\)\*\+\,\;\=]+)*/?$#";
        if (preg_match($pattern, $link)) {
            self::setValue("latest_link", $link);
        }
    }

    public static function SetWeekStartDay($day)
    {
        self::setValue("week_start_day", $day);
    }

    public static function GetWeekStartDay()
    {
        $val = self::getValue("week_start_day");
        return (strlen($val) == 0) ? "0" : $val;
    }

    /**
    * Stores the last timestamp of user updating stream setting
    */
    public static function SetStreamUpdateTimestamp()
    {
        $now = time();
        self::setValue("stream_update_timestamp", $now);
    }

    /**
     * Gets the last timestamp of user updating stream setting
     */
    public static function GetStreamUpdateTimestemp()
    {
        $update_time = self::getValue("stream_update_timestamp");
        return ($update_time == null) ? 0 : $update_time;
    }

    public static function GetClientId()
    {
        return self::getValue("client_id");
    }

    public static function SetClientId($id)
    {
        if (is_numeric($id)) {
            self::setValue("client_id", $id);
        } else {
            Logging::warn("Attempting to set client_id to invalid value: $id");
        }
    }

    /* User specific preferences start */

    /**
     * Sets the time scale preference (agendaDay/agendaWeek/month) in Calendar.
     *
     * @param $timeScale    new time scale
     */
    public static function SetCalendarTimeScale($timeScale)
    {
        self::setValue("calendar_time_scale", $timeScale, true /* user specific */);
    }

    /**
     * Retrieves the time scale preference for the current user.
     * Defaults to month if no entry exists
     */
    public static function GetCalendarTimeScale()
    {
        $val = self::getValue("calendar_time_scale", true /* user specific */);
        if (strlen($val) == 0) {
            $val = "month";
        }

        return $val;
    }

    /**
     * Sets the number of entries to show preference in library under Playlist Builder.
     *
     * @param $numEntries    new number of entries to show
     */
    public static function SetLibraryNumEntries($numEntries)
    {
        self::setValue("library_num_entries", $numEntries, true /* user specific */);
    }

    /**
     * Retrieves the number of entries to show preference in library under Playlist Builder.
     * Defaults to 10 if no entry exists
     */
    public static function GetLibraryNumEntries()
    {
        $val = self::getValue("library_num_entries", true /* user specific */);
        if (strlen($val) == 0) {
            $val = "10";
        }

        return $val;
    }

    /**
     * Sets the time interval preference in Calendar.
     *
     * @param $timeInterval        new time interval
     */
    public static function SetCalendarTimeInterval($timeInterval)
    {
        self::setValue("calendar_time_interval", $timeInterval, true /* user specific */);
    }

    /**
     * Retrieves the time interval preference for the current user.
     * Defaults to 30 min if no entry exists
     */
    public static function GetCalendarTimeInterval()
    {
        $val = self::getValue("calendar_time_interval", true /* user specific */);
        return (strlen($val) == 0) ? "30" : $val;
    }

    public static function SetDiskQuota($value)
    {
        self::setValue("disk_quota", $value, false);
    }

    public static function GetDiskQuota()
    {
        $val = self::getValue("disk_quota");
        return empty($val) ? 2147483648 : $val;  # If there is no value for disk quota, return 2GB
    }

    public static function SetLiveStreamMasterUsername($value)
    {
        self::setValue("live_stream_master_username", $value, false);
    }

    public static function GetLiveStreamMasterUsername()
    {
        return self::getValue("live_stream_master_username");
    }

    public static function SetLiveStreamMasterPassword($value)
    {
        self::setValue("live_stream_master_password", $value, false);
    }

    public static function GetLiveStreamMasterPassword()
    {
        return self::getValue("live_stream_master_password");
    }

    public static function SetSourceStatus($sourcename, $status)
    {
        self::setValue($sourcename, $status, false);
    }

    public static function GetSourceStatus($sourcename)
    {
        $value = self::getValue($sourcename);
        return !($value == null || $value == "false");
    }

    public static function SetSourceSwitchStatus($sourcename, $status)
    {
        self::setValue($sourcename."_switch", $status, false);
    }

    public static function GetSourceSwitchStatus($sourcename)
    {
        // Scheduled play switch should always be "on".
        // Even though we've hidden this element in the dashboard we should
        // always make sure it's on or else a station's stream could go offline.
        if ($sourcename == "scheduled_play") {
            return "on";
        }

        $value = self::getValue($sourcename."_switch");
        return ($value == null || $value == "off") ? 'off' : 'on';
    }

    public static function SetMasterDJSourceConnectionURL($value)
    {
        self::setValue("master_dj_source_connection_url", $value, false);
    }

    public static function GetMasterDJSourceConnectionURL()
    {
        $master_connection_url = self::getValue("master_dj_source_connection_url");
        if ($master_connection_url == "") {
            $master_connection_url = "http://".$_SERVER['SERVER_NAME'].":".  Application_Model_StreamSetting::getMasterLiveStreamPort() . Application_Model_StreamSetting::getMasterLiveStreamMountPoint();
            }

        return $master_connection_url;

    }

    public static function SetLiveDJSourceConnectionURL($value)
    {
        self::setValue("live_dj_source_connection_url", $value, false);
    }

    public static function GetLiveDJSourceConnectionURL()
    {
        $livedj_connection_url = self::getValue("live_dj_source_connection_url");
        if ($livedj_connection_url == "") {
            $livedj_connection_url = "http://".$_SERVER['SERVER_NAME'].":".  Application_Model_StreamSetting::getDjLiveStreamPort() . Application_Model_StreamSetting::getDjLiveStreamMountPoint();
        }
        return $livedj_connection_url;
    }

    /* Source Connection URL override status starts */
    public static function GetLiveDjConnectionUrlOverride()
    {
        return self::getValue("live_dj_connection_url_override");
    }

    public static function SetLiveDjConnectionUrlOverride($value)
    {
        self::setValue("live_dj_connection_url_override", $value, false);
    }

    public static function GetMasterDjConnectionUrlOverride()
    {
        return self::getValue("master_dj_connection_url_override");
    }

    public static function SetMasterDjConnectionUrlOverride($value)
    {
        self::setValue("master_dj_connection_url_override", $value, false);
    }
    /* Source Connection URL override status ends */

    public static function SetAutoTransition($value)
    {
        self::setValue("auto_transition", $value, false);
    }

    public static function GetAutoTransition()
    {
        return self::getValue("auto_transition");
    }

    public static function SetAutoSwitch($value)
    {
        self::setValue("auto_switch", $value, false);
    }

    public static function GetAutoSwitch()
    {
        return self::getValue("auto_switch");
    }
    /* User specific preferences end */

    public static function ShouldShowPopUp()
    {
        $today = mktime(0, 0, 0, gmdate("m"), gmdate("d"), gmdate("Y"));
        $remindDate = Application_Model_Preference::GetRemindMeDate();
        $retVal = false;

        if (is_null($remindDate) || ($remindDate != -1 && $today >= $remindDate)) {
            $retVal = true;
        }

        return $retVal;
    }



    public static function getOrderingMap($pref_param)
    {
        $v = self::getValue($pref_param, true);

        $id = function ($x) { return $x; };

        if ($v === '') {
            return $id;
        }

        $ds = unserialize($v);


        if (is_null($ds) || !is_array($ds)) {
            return $id;
        }

        if (!array_key_exists('ColReorder', $ds)) {
            return $id;
        }

        return function ($x) use ($ds) {
            if (array_key_exists($x, $ds['ColReorder'])) {
                return $ds['ColReorder'][$x];
            } else {
                /*For now we just have this hack for debugging. We should not
                    rely on this behaviour in case of failure*/
                Logging::warn("Index $x does not exist preferences");
                Logging::warn("Defaulting to identity and printing preferences");
                Logging::warn($ds);
                return $x;
            }
        };
    }

    public static function getCurrentLibraryTableColumnMap()
    {
        return self::getOrderingMap("library_datatable");
    }

    public static function setCurrentLibraryTableSetting($settings)
    {
        $data = serialize($settings);
        self::setValue("library_datatable", $data, true);
    }

    public static function getCurrentLibraryTableSetting()
    {
        $data = self::getValue("library_datatable", true);
        return ($data != "") ? unserialize($data) : null;
    }


    public static function setTimelineDatatableSetting($settings)
    {
        $data = serialize($settings);
        self::setValue("timeline_datatable", $data, true);
    }

    public static function getTimelineDatatableSetting()
    {
        $data = self::getValue("timeline_datatable", true);
        return ($data != "") ? unserialize($data) : null;
    }


    public static function setNowPlayingScreenSettings($settings)
    {
        $data = serialize($settings);
        self::setValue("nowplaying_screen", $data, true);
    }

    public static function getNowPlayingScreenSettings()
    {
        $data = self::getValue("nowplaying_screen", true);
        return ($data != "") ? unserialize($data) : null;
    }

    public static function setLibraryScreenSettings($settings)
    {
        $data = serialize($settings);
        self::setValue("library_screen", $data, true);
    }

    public static function getLibraryScreenSettings()
    {
        $data = self::getValue("library_screen", true);
        return ($data != "") ? unserialize($data) : null;
    }

    public static function SetEnableReplayGain($value) {
        self::setValue("enable_replay_gain", $value, false);
    }

    public static function GetEnableReplayGain() {
        return self::getValue("enable_replay_gain", false);
    }

    public static function getReplayGainModifier() {
        $rg_modifier = self::getValue("replay_gain_modifier");

        if ($rg_modifier === "")
            return "0";

        return $rg_modifier;
    }

    public static function setReplayGainModifier($rg_modifier)
    {
        self::setValue("replay_gain_modifier", $rg_modifier, true);
    }

    public static function SetHistoryItemTemplate($value) {
        self::setValue("history_item_template", $value);
    }

    public static function GetHistoryItemTemplate() {
        return self::getValue("history_item_template");
    }

    public static function SetHistoryFileTemplate($value) {
        self::setValue("history_file_template", $value);
    }

    public static function GetHistoryFileTemplate() {
        return self::getValue("history_file_template");
    }

    public static function getDiskUsage()
    {
        $val = self::getValue("disk_usage");
        return (strlen($val) == 0) ? 0 : $val;
    }

    public static function setDiskUsage($value)
    {
        self::setValue("disk_usage", $value);
    }

    public static function updateDiskUsage($filesize)
    {
        $currentDiskUsage = self::getDiskUsage();
        if (empty($currentDiskUsage)) {
            $currentDiskUsage = 0;
        }

        self::setDiskUsage($currentDiskUsage + $filesize);
    }

    public static function setTuneinEnabled($value)
    {
        self::setValue("tunein_enabled", $value);
    }

    public static function getTuneinEnabled()
    {
        return self::getValue("tunein_enabled");
    }

    public static function setTuneinPartnerKey($value)
    {
        self::setValue("tunein_partner_key", $value);
    }

    public static function getTuneinPartnerKey()
    {
        return self::getValue("tunein_partner_key");
    }

    public static function setTuneinPartnerId($value)
    {
        self::setValue("tunein_partner_id", $value);
    }

    public static function getTuneinPartnerId()
    {
        return self::getValue("tunein_partner_id");
    }

    public static function setTuneinStationId($value)
    {
        self::setValue("tunein_station_id", $value);
    }

    public static function getTuneinStationId()
    {
        return self::getValue("tunein_station_id");
    }

    public static function geLastTuneinMetadataUpdate()
    {
        return self::getValue("last_tunein_metadata_update");
    }

    public static function setLastTuneinMetadataUpdate($value)
    {
        self::setValue("last_tunein_metadata_update", $value);
    }

    /* Third Party */

    // SoundCloud

    public static function getDefaultSoundCloudLicenseType() {
        $val = self::getValue("soundcloud_license_type");
        // If we don't have a value set, return all-rights-reserved by default
        return empty($val) ? DEFAULT_SOUNDCLOUD_LICENSE_TYPE : $val;
    }

    public static function setDefaultSoundCloudLicenseType($value) {
        self::setValue("soundcloud_license_type", $value);
    }

    public static function getDefaultSoundCloudSharingType() {
        $val = self::getValue("soundcloud_sharing_type");
        // If we don't have a value set, return public by default
        return empty($val) ? DEFAULT_SOUNDCLOUD_SHARING_TYPE : $val;
    }

    public static function setDefaultSoundCloudSharingType($value) {
        self::setValue("soundcloud_sharing_type", $value);
    }

    public static function getSoundCloudRequestToken() {
        return self::getValue("soundcloud_request_token");
    }

    public static function setSoundCloudRequestToken($value) {
        self::setValue("soundcloud_request_token", $value);
    }

    // TaskManager Lock Timestamp

    public static function getTaskManagerLock() {
        return self::getValue("task_manager_lock");
    }

    public static function setTaskManagerLock($value) {
        self::setValue("task_manager_lock", $value);
    }

    // SAAS-876 - Toggle indicating whether user is using custom stream settings

    public static function getUsingCustomStreamSettings() {
        $val = self::getValue("using_custom_stream_settings");
        return empty($val) ? false : $val;
    }

    public static function setUsingCustomStreamSettings($value) {
        self::setValue("using_custom_stream_settings", $value);
    }

    // SAAS-876 - Store the default Icecast password to restore when switching
    //            back to Airtime Pro streaming settings

    public static function getDefaultIcecastPassword() {
        $val = self::getValue("default_icecast_password");
        return empty($val) ? DEFAULT_ICECAST_PASS : $val;
    }

    public static function setDefaultIcecastPassword($value) {
        self::setValue("default_icecast_password", $value);
    }

    public static function getRadioPageDisplayLoginButton()
    {
        return self::getValue("radio_page_display_login_button");
    }

    public static function setRadioPageDisplayLoginButton($value)
    {
        self::setValue("radio_page_display_login_button", $value);
    }

    public static function getLangTimezoneSetupComplete()
    {
        return self::getValue("lang_tz_setup_complete");
    }

    public static function setLangTimezoneSetupComplete($value)
    {
        self::setValue("lang_tz_setup_complete", $value);
    }

    public static function getWhatsNewDialogViewed()
    {
        $val = self::getValue("whats_new_dialog_viewed", true);
        if (empty($val)) {
            // Check the default (no user ID) value if the user value is empty
            // This is so that new stations won't see the popup
            $val = self::getValue("whats_new_dialog_viewed", false, true);
        }
        return empty($val) ? false : $val;
    }

    public static function setWhatsNewDialogViewed($value)
    {
        self::setValue("whats_new_dialog_viewed", $value, true);
    }

    public static function getAutoPlaylistPollLock() {
        return self::getValue("autoplaylist_poll_lock");
    }

    public static function setAutoPlaylistPollLock($value)
    {
        self::setValue("autoplaylist_poll_lock", $value);
    }

    public static function getPodcastPollLock() {
        return self::getValue("podcast_poll_lock");
    }

    public static function setPodcastPollLock($value)
    {
        self::setValue("podcast_poll_lock", $value);
    }

    public static function getStationPodcastId()
    {
        // Create the Station podcast if it doesn't exist.
        $stationPodcastId = self::getValue("station_podcast_id");
        if (empty($stationPodcastId)) {
            $stationPodcastId = Application_Service_PodcastService::createStationPodcast();
        }
        return $stationPodcastId;
    }

    public static function setStationPodcastId($value)
    {
        self::setValue("station_podcast_id", $value);
    }

    // SAAS-1081 - Implement a universal download key for downloading episodes from the station podcast
    //             Store and increment the download counter, resetting every month

    public static function getStationPodcastDownloadKey() {
        return self::getValue("station_podcast_download_key");
    }

    public static function setStationPodcastDownloadKey($value = null) {
        $value = empty($value) ? (new Application_Model_Auth())->generateRandomString() : $value;
        self::setValue("station_podcast_download_key", $value);
    }

    public static function getStationPodcastDownloadResetTimer() {
        return self::getValue("station_podcast_download_reset_timer");
    }

    public static function setStationPodcastDownloadResetTimer($value) {
        self::setValue("station_podcast_download_reset_timer", $value);
    }

    public static function getStationPodcastDownloadCounter() {
        return self::getValue("station_podcast_download_counter");
    }

    public static function resetStationPodcastDownloadCounter() {
        self::setValue("station_podcast_download_counter", 0);
    }

    public static function incrementStationPodcastDownloadCounter() {
        $c = self::getStationPodcastDownloadCounter();
        self::setValue("station_podcast_download_counter", empty($c) ? 1 : ++$c);
    }

    // For fail cases, we may need to decrement the download counter
    public static function decrementStationPodcastDownloadCounter() {
        $c = self::getStationPodcastDownloadCounter();
        self::setValue("station_podcast_download_counter", empty($c) ? 0 : --$c);
    }

    /**
     * @return int either 0 (public) or 1 (private)
     */
    public static function getStationPodcastPrivacy() {
        return self::getValue("station_podcast_privacy");
    }

    public static function setStationPodcastPrivacy($value) {
        self::setValue("station_podcast_privacy", $value);
    }

    /**
     * Accessors for station bandwidth limit.
     */
    public static function getBandwidthLimit() {
        return self::getValue("bandwidth_limit");
    }

    public static function setBandwidthLimit($value) {
        self::setValue("bandwidth_limit", $value);
    }

    /**
     * Accessors for bandwidth limit counter.
     * Tracks bandwidth usage.
     *
     * @see ApiController::bandwidthUsageAction()
     * @see BandwidthLimitTask
     */

    public static function getBandwidthLimitCounter() {
        return self::getValue("bandwidth_limit_counter");
    }

    public static function incrementBandwidthLimitCounter($value) {
        $counter = intval(self::getValue("bandwidth_limit_counter"));
        self::setValue("bandwidth_limit_counter", $counter + intval($value));
    }

    public static function resetBandwidthLimitCounter() {
        self::setValue("bandwidth_limit_counter", 0);
    }

    /**
     * Accessors for bandwidth limit reset timer.
     * Used to determine when to reset the bandwidth counter for the station.
     *
     * @see BandwidthLimitTask
     */

    public static function getBandwidthLimitResetTimer() {
        return self::getValue("bandwidth_limit_reset_timer");
    }

    public static function setBandwidthLimitResetTimer($value) {
        self::setValue("bandwidth_limit_reset_timer", $value);
    }

    /**
     * Accessors for bandwidth limit update timer.
     * Used to determine when the bandwidth was last updated for this station.
     *
     * @see ApiController::bandwidthUsageAction()
     */

    public static function getBandwidthLimitUpdateTimer() {
        return self::getValue("bandwidth_limit_update_timer");
    }

    public static function setBandwidthLimitUpdateTimer() {
        self::setValue("bandwidth_limit_update_timer", microtime(true));
    }

    /**
     * Getter for CORS URLs
     *
     * @return string
     */
    public static function GetAllowedCorsUrls() {
        return self::getValue('allowed_cors_urls');
    }

    /**
     * Setter for CORS URLs
     *
     * @param string $value
     * @return void
     */
    public static function SetAllowedCorsUrls($value) {
        self::setValue('allowed_cors_urls', $value);
    }
}
