<?php

class Application_Common_DateHelper
{
    private $_dateTime;

    public function __construct()
    {
        $this->_dateTime = date('U');
    }

    /**
     * Get time of object construction in the format
     * YYYY-MM-DD HH:mm:ss.
     */
    public function getTimestamp()
    {
        return date(DEFAULT_TIMESTAMP_FORMAT, $this->_dateTime);
    }

    /**
     * Get time of object construction in the format
     * YYYY-MM-DD HH:mm:ss.
     */
    public function getUtcTimestamp()
    {
        return gmdate(DEFAULT_TIMESTAMP_FORMAT, $this->_dateTime);
    }

    /**
     * Get date of object construction in the format
     * YYYY-MM-DD.
     */
    public function getDate()
    {
        return gmdate('Y-m-d', $this->_dateTime);
    }

    /**
     * Get time of object construction in the format
     * HH:mm:ss.
     */
    public function getTime()
    {
        return gmdate('H:i:s', $this->_dateTime);
    }

    /** Get the abbreviated timezone for the currently logged in user.
     * @return string A string containing the short form of the timezone set in the preferences for the current user (eg. EST, CEST, etc.)
     */
    public static function getUserTimezoneAbbreviation()
    {
        return self::getTimezoneAbbreviation(Application_Model_Preference::GetUserTimezone());
    }

    /** Get the abbreviated timezone string of the timezone the station is set to.
     * @return string A string containing the short form of the station's timezone (eg. EST, CEST, etc.)
     */
    public static function getStationTimezoneAbbreviation()
    {
        return self::getTimezoneAbbreviation(Application_Model_Preference::GetDefaultTimezone());
    }

    private static function getTimezoneAbbreviation($fullTimeZoneName)
    {
        $timeZone = new DateTimeZone($fullTimeZoneName);
        $now = new DateTime('now', $timeZone);

        return $now->format('T');
    }

    public static function getUserTimezoneOffset()
    {
        $userTimezone = new DateTimeZone(Application_Model_Preference::GetUserTimezone());
        $now = new DateTime('now', $userTimezone);

        return $now->format('Z');
    }

    public static function getStationTimezoneOffset()
    {
        $stationTimezone = new DateTimeZone(Application_Model_Preference::GetDefaultTimezone());
        $now = new DateTime('now', $stationTimezone);

        return $now->format('Z');
    }

    /**
     * @return DateTime - YYYY-MM-DD 00:00 in station timezone of today
     */
    public static function getTodayStationStartDateTime()
    {
        $stationTimezone = new DateTimeZone(Application_Model_Preference::GetDefaultTimezone());
        $now = new DateTime('now', $stationTimezone);

        $now->setTime(0, 0, 0);

        return $now;
    }

    /**
     * @return DateTime - YYYY-MM-DD 00:00 in station timezone of tomorrow
     */
    public static function getTodayStationEndDateTime()
    {
        $stationTimezone = new DateTimeZone(Application_Model_Preference::GetDefaultTimezone());
        $now = new DateTime('now', $stationTimezone);

        $now->add(new DateInterval('P1D'));
        $now->setTime(0, 0, 0);

        return $now;
    }

    /**
     * @return DateTime - YYYY-MM-DD 00:00 in station timezone
     */
    public static function getWeekStartDateTime()
    {
        $now = self::getTodayStationStartDateTime();

        // our week starts on monday, but php week starts on sunday.
        $day = $now->format('w');
        if ($day == 0) {
            $day = 7;
        }

        $dayDiff = $day - 1;
        if ($dayDiff > 0) {
            $now->sub(new DateInterval("P{$dayDiff}D"));
        }

        return $now;
    }

    /**
     * This function formats a time by removing seconds.
     *
     * When we receive a time from the database we get the
     * format "hh:mm:ss". But when dealing with show times, we
     * do not care about the seconds.
     *
     * @param int $p_dateTime
     *                        The value which to format
     *
     * @return int
     *             The timestamp with the new format "hh:mm", or
     *             the original input parameter, if it does not have
     *             the correct format
     */
    public static function removeSecondsFromTime($p_dateTime)
    {
        // Format is in hh:mm:ss. We want hh:mm
        $timeExplode = explode(':', $p_dateTime);

        if (count($timeExplode) == 3) {
            return $timeExplode[0] . ':' . $timeExplode[1];
        }

        return $p_dateTime;
    }

    /* Given a track length in the format HH:MM:SS.mm, we want to
     * convert this to seconds. This is useful for Liquidsoap which
     * likes input parameters give in seconds.
     * For example, 00:06:31.444, should be converted to 391.444 seconds
     * @param int $p_time
     *      The time interval in format HH:MM:SS.mm we wish to
     *      convert to seconds.
     * @return float
     *      The input parameter converted to seconds.
     */
    public static function calculateLengthInSeconds($p_time)
    {
        if (2 !== substr_count($p_time, ':')) {
            return false;
        }

        if (1 === substr_count($p_time, '.')) {
            [$hhmmss, $ms] = explode('.', $p_time);
        } else {
            $hhmmss = $p_time;
            $ms = 0;
        }

        [$hours, $minutes, $seconds] = explode(':', $hhmmss);

        $totalSeconds = ($hours * 3600 + $minutes * 60 + $seconds) . ".{$ms}";

        return round($totalSeconds, 3);
    }

    /**
     * returns true or false depending on input is wether in
     * valid range of SQL date/time.
     *
     * @param string $p_datetime
     *                           should be in format of '0000-00-00 00:00:00'
     */
    public static function checkDateTimeRangeForSQL($p_datetime)
    {
        $info = explode(' ', $p_datetime);
        $dateInfo = explode('-', $info[0]);
        if (isset($info[1])) {
            $timeInfo = explode(':', $info[1]);
        }
        $retVal = [];
        $retVal['success'] = true;

        $year = $dateInfo[0];
        $month = $dateInfo[1];
        $day = $dateInfo[2];
        // if year is < 1753 or > 9999 it's out of range
        if ($year < 1753) {
            $retVal['success'] = false;
            $retVal['errMsg'] = sprintf(_('The year %s must be within the range of 1753 - 9999'), $year);
        } elseif (!checkdate($month, $day, $year)) {
            $retVal['success'] = false;
            $retVal['errMsg'] = sprintf(_('%s-%s-%s is not a valid date'), $year, $month, $day);
        } else {
            // check time
            if (isset($timeInfo)) {
                if (isset($timeInfo[0]) && $timeInfo[0] != '') {
                    $hour = intval($timeInfo[0]);
                } else {
                    $hour = -1;
                }

                if (isset($timeInfo[1]) && $timeInfo[1] != '') {
                    $min = intval($timeInfo[1]);
                } else {
                    $min = -1;
                }

                if (isset($timeInfo[2]) && $timeInfo[2] != '') {
                    $sec = intval($timeInfo[2]);
                } else {
                    $sec = -1;
                }

                if (($hour < 0 || $hour > 23) || ($min < 0 || $min > 59) || ($sec < 0 || $sec > 59)) {
                    $retVal['success'] = false;
                    $retVal['errMsg'] = sprintf(_('%s:%s:%s is not a valid time'), $timeInfo[0], $timeInfo[1], $timeInfo[2]);
                }
            }
        }

        return $retVal;
    }

    /*
     * @param $datetime string Y-m-d H:i:s in UTC timezone
     *
     * @return string in $format default Y-m-d H:i:s in station timezone
     */
    public static function UTCStringToStationTimezoneString($datetime, $format = DEFAULT_TIMESTAMP_FORMAT)
    {
        $stationTimezone = new DateTimeZone(Application_Model_Preference::GetDefaultTimezone());
        $utcTimezone = new DateTimeZone('UTC');

        $d = new DateTime($datetime, $utcTimezone);
        $d->setTimezone($stationTimezone);

        return $d->format($format);
    }

    /*
     * @param $datetime string Y-m-d H:i:s in UTC timezone
    *
    * @return string Y-m-d H:i:s in user's timezone
    */
    public static function UTCStringToUserTimezoneString($datetime, $format = DEFAULT_TIMESTAMP_FORMAT)
    {
        $userTimezone = new DateTimeZone(Application_Model_Preference::GetUserTimezone());
        $utcTimezone = new DateTimeZone('UTC');

        $d = new DateTime($datetime, $utcTimezone);
        $d->setTimezone($userTimezone);

        return $d->format($format);
    }

    /*
     * @param $datetime string Y-m-d H:i:s in user timezone
    *
    * @return string Y-m-d H:i:s in UTC timezone
    */
    public static function UserTimezoneStringToUTCString($datetime, $format = DEFAULT_TIMESTAMP_FORMAT)
    {
        $userTimezone = new DateTimeZone(Application_Model_Preference::GetUserTimezone());
        $utcTimezone = new DateTimeZone('UTC');

        $d = new DateTime($datetime, $userTimezone);
        $d->setTimezone($utcTimezone);

        return $d->format($format);
    }

    /**
     * Convert the columns given in the array $columnsToConvert in the
     * database result $rows to local timezone.
     *
     * @param array $rows             arrays of arrays containing database query result
     * @param array $columnsToConvert array of column names to convert
     * @param string (station|user) convert to either station or user timezone
     * @param mixed $domain
     */
    public static function convertTimestamps(&$rows, $columnsToConvert, $domain = 'station')
    {
        if (!is_array($rows)) {
            return;
        }

        $converter = 'UTCStringTo' . ucfirst($domain) . 'TimezoneString';

        foreach ($rows as &$row) {
            foreach ($columnsToConvert as $column) {
                $row[$column] = self::$converter($row[$column]);
            }
        }
    }

    /**
     * Convert the columns given in the array $columnsToConvert in the
     * database result $rows to local timezone.
     *
     * @param array  $rows             arrays of arrays containing database query result
     * @param array  $columnsToConvert array of column names to convert
     * @param string $timezone         convert to the given timezone
     * @param string $format           time format to convert to
     */
    public static function convertTimestampsToTimezone(&$rows, $columnsToConvert, $timezone, $format = DEFAULT_TIMESTAMP_FORMAT)
    {
        $timezone = strtolower($timezone);
        // Check that the timezone is valid and rows is an array
        if (!is_array($rows)) {
            return;
        }

        foreach ($rows as &$row) {
            if (is_array($row)) {
                foreach ($columnsToConvert as $column) {
                    if (array_key_exists($column, $row)) {
                        $newTimezone = new DateTimeZone($timezone);
                        $utcTimezone = new DateTimeZone('UTC');

                        $d = new DateTime($row[$column], $utcTimezone);
                        $d->setTimezone($newTimezone);
                        $row[$column] = $d->format($format);
                    }
                }
                self::convertTimestampsToTimezone($row, $columnsToConvert, $timezone, $format);
            }
        }
    }

    /**
     * Return the end date time in the given timezone.
     *
     * @param mixed $timezoneString
     * @param mixed $days
     *
     * @return DateTime
     */
    public static function getEndDateTime($timezoneString, $days)
    {
        $timezone = new DateTimeZone($timezoneString);
        $now = new DateTime('now', $timezone);

        $now->add(new DateInterval('P' . $days . 'D'));
        $now->setTime(0, 0, 0);

        return $now;
    }

    /**
     * Return a formatted string representing the
     * given datetime in the given timezone.
     *
     * @param unknown $datetime the time to convert
     * @param unknown $timezone the timezone to convert to
     * @param string  $format   the formatted string
     */
    public static function UTCStringToTimezoneString($datetime, $timezone, $format = DEFAULT_TIMESTAMP_FORMAT)
    {
        $d = new DateTime($datetime, new DateTimeZone('UTC'));
        $timezone = strtolower($timezone);
        $newTimezone = new DateTimeZone($timezone);
        $d->setTimezone($newTimezone);

        return $d->format($format);
    }

    /**
     * Return the timezone offset in seconds for the given timezone.
     *
     * @param unknown $userDefinedTimezone the timezone used to determine the offset
     */
    public static function getTimezoneOffset($userDefinedTimezone)
    {
        $now = new DateTimeZone($userDefinedTimezone);

        $d = new DateTime('now', $now);

        return $d->format('Z');
    }

    /**
     * This function is used for calculations! Don't modify for display purposes!
     *
     * Convert playlist time value to float seconds
     *
     * @param string $plt
     *                    playlist interval value (HH:mm:ss.dddddd)
     *
     * @return int
     *             seconds
     */
    public static function playlistTimeToSeconds($plt)
    {
        $arr = preg_split('/:/', $plt);
        if (isset($arr[2])) {
            return (intval($arr[0]) * 60 + intval($arr[1])) * 60 + floatval($arr[2]);
        }
        if (isset($arr[1])) {
            return intval($arr[0]) * 60 + floatval($arr[1]);
        }

        return floatval($arr[0]);
    }

    /**
     *  This function is used for calculations! Don't modify for display purposes!
     *
     * Convert float seconds value to playlist time format
     *
     * @param mixed $p_seconds
     *
     * @return string
     *                interval in playlist time format (HH:mm:ss.d)
     */
    public static function secondsToPlaylistTime($p_seconds)
    {
        $info = explode('.', $p_seconds);
        $seconds = $info[0];
        if (!isset($info[1])) {
            $milliStr = 0;
        } else {
            $milliStr = $info[1];
        }
        $hours = floor($seconds / 3600);
        $seconds -= $hours * 3600;
        $minutes = floor($seconds / 60);
        $seconds -= $minutes * 60;

        return sprintf('%02d:%02d:%02d.%s', $hours, $minutes, $seconds, $milliStr);
    }

    /**
     * Returns date fields from give start and end teimstamp strings
     * if no start or end parameter is passed start will be set to 1
     * in the past and end to now.
     *
     * @param string  startTimestamp Y-m-d H:i:s
     * @param string  endTImestamp Y-m-d H:i:s
     * @param string  timezone (ex UTC) of the start and end parameters
     * @param mixed $startTimestamp
     * @param mixed $endTimestamp
     * @param mixed $timezone
     *
     * @return array (start DateTime, end DateTime) in UTC timezone
     */
    public static function getStartEnd($startTimestamp, $endTimestamp, $timezone)
    {
        $prefTimezone = Application_Model_Preference::GetTimezone();
        $utcTimezone = new DateTimeZone('UTC');
        $utcNow = new DateTime('now', $utcTimezone);

        if (empty($timezone)) {
            $userTimezone = new DateTimeZone($prefTimezone);
        } else {
            $userTimezone = new DateTimeZone($timezone);
        }

        // default to 1 day
        if (empty($startTimestamp) || empty($endTimestamp)) {
            $startsDT = clone $utcNow;
            $startsDT->sub(new DateInterval('P1D'));
            $endsDT = clone $utcNow;
        } else {
            try {
                $startsDT = new DateTime($startTimestamp, $userTimezone);
                $startsDT->setTimezone($utcTimezone);

                $endsDT = new DateTime($endTimestamp, $userTimezone);
                $endsDT->setTimezone($utcTimezone);

                if ($startsDT > $endsDT) {
                    throw new Exception('start greater than end');
                }
            } catch (Exception $e) {
                Logging::info($e);
                Logging::info($e->getMessage());

                $startsDT = clone $utcNow;
                $startsDT->sub(new DateInterval('P1D'));
                $endsDT = clone $utcNow;
            }
        }

        return [$startsDT, $endsDT];
    }
}
