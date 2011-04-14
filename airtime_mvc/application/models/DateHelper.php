<?php

class DateHelper
{
    private $_timestamp;

    function __construct()
    {
        $this->_timestamp = date("U");
    }

    /**
     * Get time of object construction in the format
     * YYYY-MM-DD HH:mm:ss
     */
    function getTimestamp()
    {
        return date("Y-m-d H:i:s", $this->_timestamp);
    }

    /**
     * Get time of object construction in the format
     * HH:mm:ss
     */
    function getTime()
    {
        return date("H:i:s", $this->_timestamp);
    }

    /**
     * Set the internal timestamp of the object.
     */
    function setDate($dateString)
    {
        $this->_timestamp = strtotime($dateString);
    }

    /**
     *
     * Enter description here ...
     */
    function getNowDayStartDiff()
    {
        $dayStartTS = strtotime(date("Y-m-d", $this->_timestamp));
        return $this->_timestamp - $dayStartTS;
    }

    function getNowDayEndDiff()
    {
        $dayEndTS = strtotime(date("Y-m-d", $this->_timestamp+(86400)));
        return $dayEndTS - $this->_timestamp;
    }

    function getEpochTime()
    {
        return $this->_timestamp;
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
}

