<?php

define("DAYS_PER_WEEK", 7);

class WidgetHelper
{
    public static function getWeekInfo($timezone)
    {
        //weekStart is in station time.
        $weekStartDateTime = Application_Common_DateHelper::getWeekStartDateTime();

        $dow = array("monday", "tuesday", "wednesday", "thursday", "friday",
            "saturday", "sunday", "nextmonday", "nexttuesday", "nextwednesday",
            "nextthursday", "nextfriday", "nextsaturday", "nextsunday");

        $result = array();

        // default to the station timezone
        $timezone = Application_Model_Preference::GetDefaultTimezone();
        $userDefinedTimezone = strtolower($timezone);
        // if the timezone defined by the user exists, use that
        if (array_key_exists($userDefinedTimezone, timezone_abbreviations_list())) {
            $timezone = $userDefinedTimezone;
        }
        $utcTimezone = new DateTimeZone("UTC");

        $weekStartDateTime->setTimezone($utcTimezone);
        $utcDayStart = $weekStartDateTime->format("Y-m-d H:i:s");
        for ($i = 0; $i < 14; $i++) {
            //have to be in station timezone when adding 1 day for daylight savings.
            $weekStartDateTime->setTimezone(new DateTimeZone($timezone));
            $weekStartDateTime->add(new DateInterval('P1D'));

            //convert back to UTC to get the actual timestamp used for search.
            $weekStartDateTime->setTimezone($utcTimezone);

            $utcDayEnd = $weekStartDateTime->format("Y-m-d H:i:s");
            $shows = Application_Model_Show::getNextShows($utcDayStart, "ALL", $utcDayEnd);
            $utcDayStart = $utcDayEnd;

            // convert to user-defined timezone, or default to station
            Application_Common_DateHelper::convertTimestampsToTimezone(
                $shows,
                array("starts", "ends", "start_timestamp","end_timestamp"),
                $timezone
            );

            $result[$dow[$i]] = $shows;

            // XSS exploit prevention
            self::convertSpecialChars($result, array("name", "url"));
            // convert image paths to point to api endpoints
            self::findAndConvertPaths($result);
        }

        return $result;
    }

    // Second version of this function.
    // Removing "next" days and creating two weekly arrays
    public static function getWeekInfoV2($timezone)
    {
        //weekStart is in station time.
        //$weekStartDateTime = Application_Common_DateHelper::getWeekStartDateTime();
        $weekStartDateTime = new DateTime("now", new DateTimeZone(Application_Model_Preference::GetTimezone()));

        $maxNumOFWeeks = 2;

        $result = array();

        // default to the station timezone
        $timezone = Application_Model_Preference::GetDefaultTimezone();
        $userDefinedTimezone = strtolower($timezone);
        // if the timezone defined by the user exists, use that
        if (array_key_exists($userDefinedTimezone, timezone_abbreviations_list())) {
            $timezone = $userDefinedTimezone;
        }
        $utcTimezone = new DateTimeZone("UTC");

        $weekStartDateTime->setTimezone($utcTimezone);

        // When querying for shows we need the start and end date range to have
        // a time of "00:00". $utcDayStart is used below when querying for shows.
        $utcDayStartDT = clone $weekStartDateTime;
        $utcDayStartDT->setTime(0, 0, 0);
        $utcDayStart = $utcDayStartDT->format("Y-m-d H:i:s");
        $weekCounter = 0;
        while ($weekCounter < $maxNumOFWeeks) {
            for ($dayOfWeekCounter = 0; $dayOfWeekCounter < DAYS_PER_WEEK; $dayOfWeekCounter++) {
                $dateParse = date_parse($weekStartDateTime->format("Y-m-d H:i:s"));

                $result[$weekCounter][$dayOfWeekCounter]["dayOfMonth"] = $dateParse["day"];
                $result[$weekCounter][$dayOfWeekCounter]["dayOfWeek"] = strtoupper(date("D", $weekStartDateTime->getTimestamp()));

                //have to be in station timezone when adding 1 day for daylight savings.
                $weekStartDateTime->setTimezone(new DateTimeZone($timezone));
                $weekStartDateTime->add(new DateInterval('P1D'));

                //convert back to UTC to get the actual timestamp used for search.
                $weekStartDateTime->setTimezone($utcTimezone);

                // When querying for shows we need the start and end date range to have
                // a time of "00:00".
                $utcDayEndDT = clone $weekStartDateTime;
                $utcDayEndDT->setTime(0, 0, 0);
                $utcDayEnd = $utcDayEndDT->format("Y-m-d H:i:s");
                $shows = Application_Model_Show::getNextShows($utcDayStart, "ALL", $utcDayEnd);
                $utcDayStart = $utcDayEnd;

                // convert to user-defined timezone, or default to station
                Application_Common_DateHelper::convertTimestampsToTimezone(
                    $shows,
                    array("starts", "ends", "start_timestamp", "end_timestamp"),
                    $timezone
                );


                foreach($shows as &$show) {
                    $startParseDate = date_parse($show['starts']);
                    $show["show_start_hour"] = str_pad($startParseDate["hour"], 2, "0", STR_PAD_LEFT).":".str_pad($startParseDate["minute"], 2, 0, STR_PAD_LEFT);

                    $endParseDate = date_parse($show['ends']);
                    $show["show_end_hour"] = str_pad($endParseDate["hour"], 2, 0, STR_PAD_LEFT).":".str_pad($endParseDate["minute"],2, 0, STR_PAD_LEFT);
                }
                $result[$weekCounter][$dayOfWeekCounter]["shows"] = $shows;

                // XSS exploit prevention
                self::convertSpecialChars($result, array("name", "url"));
                // convert image paths to point to api endpoints
                self::findAndConvertPaths($result);

            }
            $weekCounter += 1;
        }

        return $result;
    }

    /**
     * Go through a given array and sanitize any potentially exploitable fields
     * by passing them through htmlspecialchars
     *
     * @param unknown $arr    the array to sanitize
     * @param unknown $keys    indexes of values to be sanitized
     */
    public static function convertSpecialChars(&$arr, $keys)
    {
        foreach ($arr as &$a) {
            if (is_array($a)) {
                foreach ($keys as &$key) {
                    if (array_key_exists($key, $a)) {
                        $a[$key] = htmlspecialchars($a[$key]);
                    }
                }
                self::convertSpecialChars($a, $keys);
            }
        }
    }

    /**
     * Recursively find image_path keys in the various $result subarrays,
     * and convert them to point to the show-logo endpoint
     *
     * @param unknown $arr the array to search
     */
    public static function findAndConvertPaths(&$arr)
    {
        $CC_CONFIG = Config::getConfig();
        $baseDir = Application_Common_OsPath::formatDirectoryWithDirectorySeparators($CC_CONFIG['baseDir']);

        foreach ($arr as &$a) {
            if (is_array($a)) {
                if (array_key_exists("image_path", $a)) {
                    $a["image_path"] = $a["image_path"] && $a["image_path"] !== '' ?
                        "http://".$_SERVER['HTTP_HOST'].$baseDir."api/show-logo?id=".$a["id"] : '';
                } else {
                    self::findAndConvertPaths($a);
                }
            }
        }
    }
}