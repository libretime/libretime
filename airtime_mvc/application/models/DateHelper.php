<?php

class Application_Model_DateHelper
{
    private $_dateTime;

    function __construct()
    {
        $this->_dateTime = date("U");
    }

    /**
     * Get time of object construction in the format
     * YYYY-MM-DD HH:mm:ss
     */
    function getTimestamp()
    {
        return date("Y-m-d H:i:s", $this->_dateTime);
    }

    /**
     * Get time of object construction in the format
     * YYYY-MM-DD HH:mm:ss
     */
    function getUtcTimestamp()
    {
        return gmdate("Y-m-d H:i:s", $this->_dateTime);
    }

    /**
     * Get date of object construction in the format
     * YYYY-MM-DD
     */
    function getDate()
    {
        return gmdate("Y-m-d", $this->_dateTime);
    }

    /**
     * Get time of object construction in the format
     * HH:mm:ss
     */
    function getTime()
    {
        return gmdate("H:i:s", $this->_dateTime);
    }
    
    /**
     * Get the week start date of this week in the format
     * YYYY-MM-DD
     * 
     * @return String - week start date
     */
    function getWeekStartDate()
    {
        $startDate = date('w') == 0 ? date('Y-m-d') : date('Y-m-d', strtotime('last sunday'));
        $startDateTime = new DateTime($startDate);
        return $startDateTime->format('Y-m-d H:i:s');
    }

    /**
     * Set the internal timestamp of the object.
     */
    function setDate($dateString)
    {
        $dateTime = new DateTime($dateString, new DateTimeZone("UTC"));
        $this->_dateTime = $dateTime->getTimestamp();
    }

    /**
     * Calculate and return the timestamp for end of day today
     * in local time.
     *
     * For example, if local time is 2PM on 2011-11-01,
     * then the function would return 2011-11-02 00:00:00
     *
     * @return  End of day timestamp in local timezone
     */
    public static function GetDayEndTimestamp($time = "") {
        $dateTime = $time == "" ? new DateTime(date("Y-m-d")) : new DateTime($time);
        $dateTime->add(new DateInterval('P1D'));
        return $dateTime->format('Y-m-d H:i:s');
    }
    
    public static function GetDayEndTimestampInUtc($time = "") {
        $dayEndTimestamp = Application_Model_DateHelper::GetDayEndTimestamp($time);
        return Application_Model_DateHelper::ConvertToUtcDateTimeString($dayEndTimestamp);
    }

    /**
     * Find the epoch timestamp difference from "now" to the beginning of today.
     */
    function getNowDayStartDiff()
    {
        $dayStartTs = ((int)($this->_dateTime/86400))*86400;
        return $this->_dateTime - $dayStartTs;
    }

    /**
     * Find the epoch timestamp difference from "now" to the end of today.
     */
    function getNowDayEndDiff()
    {
        $dayEndTs = ((int)(($this->_dateTime+86400)/86400))*86400;
        return $dayEndTs - $this->_dateTime;
    }

    function getEpochTime()
    {
        return $this->_dateTime;
    }

    /**
     * Returns the offset in seconds, between local and UTC timezones.
     * E.g., if local timezone is -4, this function
     * returns -14400.
     *
     * @return type     offset in int, between local and UTC timezones
     */
    function getLocalTimeZoneOffset() {
        $dateTime = new DateTime("@".$this->_dateTime, new DateTimeZone("UTC"));
        $timezone = new DateTimeZone(date_default_timezone_get());
        return $timezone->getOffset($dateTime);
    }

    /**
     * Returns the offset hour in int, between local and UTC timezones.
     * E.g., if local timezone is -4:30, this function
     * returns -4.
     *
     * @return type     offset hour in int, between local and UTC timezones
     */
    function getLocalOffsetHour() {
        $offset = $this->getLocalTimeZoneOffset();
        return (int)($offset / 3600);
    }

    /**
     * Returns the offset minute in int, between local and UTC timezones.
     * E.g., if local timezone is -4:30, this function
     * returns -30.
     *
     * @return type     offset minute in int, between local and UTC timezones
     */
    function getLocalOffsetMinute() {
        $offset = $this->getLocalTimeZoneOffset();
        return (int)(($offset % 3600) / 60);
    }

    public static function TimeDiff($time1, $time2)
    {
        return strtotime($time2) - strtotime($time1);
    }

    public static function ConvertMSToHHMMSSmm($time)
    {
        $hours = floor($time / 3600000);
        $time -= 3600000*$hours;

        $minutes = floor($time / 60000);
        $time -= 60000*$minutes;

        $seconds = floor($time / 1000);
        $time -= 1000*$seconds;

        $ms = $time;

        if (strlen($hours) == 1)
        $hours = "0".$hours;
        if (strlen($minutes) == 1)
        $minutes = "0".$minutes;
        if (strlen($seconds) == 1)
        $seconds = "0".$seconds;

        return $hours.":".$minutes.":".$seconds.".".$ms;
    }

    /**
     * This function formats a time by removing seconds
     *
     * When we receive a time from the database we get the
     * format "hh:mm:ss". But when dealing with show times, we
     * do not care about the seconds.
     *
     * @param int $p_dateTime
     *      The value which to format.
     * @return int
     *      The timestamp with the new format "hh:mm", or
     *      the original input parameter, if it does not have
     *      the correct format.
     */
    public static function removeSecondsFromTime($p_dateTime)
    {
        //Format is in hh:mm:ss. We want hh:mm
        $timeExplode = explode(":", $p_dateTime);

        if (count($timeExplode) == 3)
            return $timeExplode[0].":".$timeExplode[1];
        else
            return $p_dateTime;
    }

    public static function getDateFromTimestamp($p_dateTime){
        $explode = explode(" ", $p_dateTime);
        return $explode[0];
    }

    public static function getTimeFromTimestamp($p_dateTime){
        $explode = explode(" ", $p_dateTime);
        return $explode[1];
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
    public static function calculateLengthInSeconds($p_time){

        if (2 !== substr_count($p_time, ":")){
            return FALSE;
        }

        if (1 === substr_count($p_time, ".")){
            list($hhmmss, $ms) = explode(".", $p_time);
        } else {
            $hhmmss = $p_time;
            $ms = 0;
        }

        list($hours, $minutes, $seconds) = explode(":", $hhmmss);
        
        // keep ms in 3 digits
        $ms = substr($ms, 0, 3);
        
        $totalSeconds = $hours*3600 + $minutes*60 + $seconds + $ms/1000;

        return $totalSeconds;
    }

    public static function ConvertToUtcDateTime($p_dateString, $timezone=null){
        if (isset($timezone)) {
            $dateTime = new DateTime($p_dateString, new DateTimeZone($timezone));
        }
        else {
            $dateTime = new DateTime($p_dateString, new DateTimeZone(date_default_timezone_get()));
        }
        $dateTime->setTimezone(new DateTimeZone("UTC"));

        return $dateTime;
    }

    public static function ConvertToSpecificTimezoneDateTime($p_dateString, $timezone){
        $dateTime = new DateTime($p_dateString, new DateTimeZone("UTC"));
        $dateTime->setTimezone(new DateTimeZone($timezone));

        return $dateTime;
    }

    public static function ConvertToLocalDateTime($p_dateString){
        $dateTime = new DateTime($p_dateString, new DateTimeZone("UTC"));
        $dateTime->setTimezone(new DateTimeZone(date_default_timezone_get()));

        return $dateTime;
    }
    
    /* Convenience method to return a date formatted into a String rather than a 
     * DateTime object. Note that if an empty string is provided for $p_dateString
     * then the current time is provided. 
     * 
     * @param $p_dateString
     *      Date string in UTC timezone.
     * @param $p_format
     *      Format which the string should be returned in.
     * 
     * @return string 
     *      Date String in localtime 
     * */
    public static function ConvertToLocalDateTimeString($p_dateString, $p_format="Y-m-d H:i:s"){
        if (is_null($p_dateString) || strlen($p_dateString) == 0)
            return $p_dateString;
        return self::ConvertToLocalDateTime($p_dateString)->format($p_format);
    }
    
    public static function ConvertToUtcDateTimeString($p_dateString, $p_format="Y-m-d H:i:s"){
        if (is_null($p_dateString) || strlen($p_dateString) == 0)
            return $p_dateString;
        return self::ConvertToUtcDateTime($p_dateString)->format($p_format);
    }
    
    /*
     * Example input: "00:02:32.746562". Output is a DateInterval object
     * representing that 2 minute, 32.746562 second interval.
     * 
     */
    public static function getDateIntervalFromString($p_interval){
        list($hour_min_sec, $subsec) = explode(".", $p_interval);
        list($hour, $min, $sec) = explode(":", $hour_min_sec);
        
        return new DateInterval("PT{$hour}H{$min}M{$sec}S");
    }
}

