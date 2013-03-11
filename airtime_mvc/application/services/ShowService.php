<?php

class Application_Service_ShowService
{
    const MAX_REBROADCAST_DATES = 10;

    /**
     * 
     * Sets the fields for a cc_show table row
     * @param $ccShow
     * @param $showData
     */
    public function setShow($ccShow, $showData)
    {
        $ccShow->setDbName($showData['add_show_name']);
        $ccShow->setDbDescription($showData['add_show_description']);
        $ccShow->setDbUrl($showData['add_show_url']);
        $ccShow->setDbGenre($showData['add_show_genre']);
        $ccShow->setDbColor($showData['add_show_color']);
        $ccShow->setDbBackgroundColor($showData['add_show_background_color']);
        $ccShow->setDbLiveStreamUsingAirtimeAuth($showData['cb_airtime_auth'] == 1);
        $ccShow->setDbLiveStreamUsingCustomAuth($showData['cb_custom_auth'] == 1);
        $ccShow->setDbLiveStreamUser($showData['custom_username']);
        $ccShow->setDbLiveStreamPass($showData['custom_password']);

        $ccShow->save();
        return $ccShow;
    }

    /**
     * 
     * Sets the fields for a cc_show_rebroadcast table row
     * @param $showData
     * @param $showId
     * @param $repeatType
     * @param $isRecorded
     */
    public function createShowRebroadcasts($showData, $showId, $repeatType, $isRecorded)
    {
        if (($isRecorded && $showData['add_show_rebroadcast']) && ($repeatType != -1)) {
            for ($i=1; $i<=self::MAX_REBROADCAST_DATES; $i++) {
                if ($showData['add_show_rebroadcast_date_'.$i]) {
                    $showRebroad = new CcShowRebroadcast();
                    $showRebroad->setDbDayOffset($showData['add_show_rebroadcast_date_'.$i]);
                    $showRebroad->setDbStartTime($showData['add_show_rebroadcast_time_'.$i]);
                    $showRebroad->setDbShowId($showId);
                    $showRebroad->save();
                }
            }
        } elseif ($isRecorded && $showData['add_show_rebroadcast'] && ($repeatType == -1)) {
            for ($i=1; $i<=self::MAX_REBROADCAST_DATES; $i++) {
                if ($showData['add_show_rebroadcast_date_absolute_'.$i]) {
                    $rebroadcastDate = new DateTime($showData["add_show_rebroadcast_date_absolute_$i"]);
                    $startDate = new DateTime($showData['add_show_start_date']);
                    $offsetDays = $startDate->diff($rebroadcastDate);

                    $showRebroad = new CcShowRebroadcast();
                    $showRebroad->setDbDayOffset($offsetDays->format("%a days"));
                    $showRebroad->setDbStartTime($showData['add_show_rebroadcast_time_absolute_'.$i]);
                    $showRebroad->setDbShowId($showId);
                    $showRebroad->save();
                }
            }
        }
    }

    /**
     * 
     * Sets the fields for a cc_show_hosts table row
     * @param $showData
     * @param $showId
     */
    public function createShowHosts($showData, $showId)
    {
        if (is_array($showData['add_show_hosts'])) {
            foreach ($showData['add_show_hosts'] as $host) {
                $showHost = new CcShowHosts();
                $showHost->setDbShow($showId);
                $showHost->setDbHost($host);
                $showHost->save();
            }
        }
    }

    /**
     * 
     * Gets the date and time shows (particularly repeating shows)
     * can be populated until.
     * 
     * @return DateTime object
     */
    public function getPopulateShowUntilDateTIme()
    {
        $populateUntil = Application_Model_Preference::GetShowsPopulatedUntil();

        if (is_null($populateUntil)) {
            $populateUntil = new DateTime("now", new DateTimeZone('UTC'));
            Application_Model_Preference::SetShowsPopulatedUntil($populateUntil);
        }
        return $populateUntil;
    }

    /**
     * 
     * Enter description here ...
     * @param $localStart timestring format "Y-m-d H:i:s" (not UTC)
     * @param $duration string time interval (h)h:(m)m(:ss)
     * @param $timezone string "Europe/Prague"
     * @param $offset array (days, hours, mins) used for rebroadcast shows
     * 
     * @return array of 2 DateTime objects, start/end time of the show in UTC
     */
    public function createUTCStartEndDateTime($localStart, $duration, $timezone=null, $offset=null)
    {
        $userInfo = Zend_Auth::getInstance()->getStorage()->read();
        $user = new Application_Model_User($userInfo->id);
        $isAdminOrPM = $user->isUserType(array(UTYPE_ADMIN, UTYPE_PROGRAM_MANAGER));

        if (!isset($timezone)) {
            $timezone = Application_Model_Preference::GetUserTimezone($user->getId());
        }

        $startDateTime = new DateTime($localStart, new DateTimeZone($timezone));
        if (isset($offset)) {
            $startDateTime->add(new DateInterval("P{$offset["days"]}DT{$offset["hours"]}H{$offset["mins"]}M"));
        }
        //convert time to UTC
        $startDateTime->setTimezone(new DateTimeZone('UTC'));

        $endDateTime = clone $startDateTime;
        $duration = explode(":", $duration);
        list($hours, $mins) = array_slice($duration, 0, 2);
        $endDateTime->add(new DateInterval("PT{$hours}H{$mins}M"));

        return array($startDateTime, $endDateTime);
    }

    /**
     * 
     * Show instances for repeating shows only get created up
     * until what is visible on the calendar. We need to set the
     * date for when the next repeating show instance should be created
     * as the user browses the calendar further.
     * 
     * @param $nextDate
     * @param $showId
     * @param $day
     */
    public function setNextRepeatingShowDate($nextDate, $showId, $day)
    {
        $nextInfo = explode(" ", $nextDate);

        $repeatInfo = CcShowDaysQuery::create()
            ->filterByDbShowId($showId)
            ->filterByDbDay($day)
            ->findOne();

        $repeatInfo->setDbNextPopDate($nextInfo[0])
            ->save();
    }
}