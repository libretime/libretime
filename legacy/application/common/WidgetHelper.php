<?php

define('DAYS_PER_WEEK', 7);

class WidgetHelper
{
    public static function getWeekInfo($userDefinedTimezone)
    {
        // weekStart is in station time.
        $weekStartDateTime = Application_Common_DateHelper::getWeekStartDateTime();

        $dow = [
            'monday', 'tuesday', 'wednesday', 'thursday', 'friday',
            'saturday', 'sunday', 'nextmonday', 'nexttuesday', 'nextwednesday',
            'nextthursday', 'nextfriday', 'nextsaturday', 'nextsunday',
        ];

        $result = [];

        // default to the station timezone
        $timezone = Application_Model_Preference::GetDefaultTimezone();
        if ($userDefinedTimezone) {
            $userDefinedTimezone = strtolower($userDefinedTimezone);
            // if the timezone defined by the user exists, use that
            if (array_key_exists($userDefinedTimezone, timezone_abbreviations_list())) {
                $timezone = $userDefinedTimezone;
            }
        }
        $utcTimezone = new DateTimeZone('UTC');

        $weekStartDateTime->setTimezone($utcTimezone);
        $utcDayStart = $weekStartDateTime->format(DEFAULT_TIMESTAMP_FORMAT);
        for ($i = 0; $i < 14; ++$i) {
            // have to be in station timezone when adding 1 day for daylight savings.
            $weekStartDateTime->setTimezone(new DateTimeZone($timezone));
            $weekStartDateTime->add(new DateInterval('P1D'));

            // convert back to UTC to get the actual timestamp used for search.
            $weekStartDateTime->setTimezone($utcTimezone);

            $utcDayEnd = $weekStartDateTime->format(DEFAULT_TIMESTAMP_FORMAT);
            $shows = Application_Model_Show::getNextShows($utcDayStart, 'ALL', $utcDayEnd);
            $utcDayStart = $utcDayEnd;

            // convert to user-defined timezone, or default to station
            Application_Common_DateHelper::convertTimestampsToTimezone(
                $shows,
                ['starts', 'ends', 'start_timestamp', 'end_timestamp'],
                $timezone
            );

            $result[$dow[$i]] = $shows;
        }

        // XSS exploit prevention
        SecurityHelper::htmlescape_recursive($result);

        // convert image paths to point to api endpoints
        self::findAndConvertPaths($result);

        return $result;
    }

    /**
     * Returns a weeks worth of shows in UTC, and an info array of the current week's days.
     * Returns an array of two arrays:.
     *
     * The first array is 7 consecutive week days, starting with the current day.
     *
     * The second array contains shows scheduled during the 7 week days in the first array.
     * The shows returned in this array are not in any order and are in UTC.
     *
     * We don't do any timezone conversion in this function on purpose. All timezone conversion
     * and show time ordering should be done on the frontend.
     *
     * *** This function does no HTML encoding. It is up to the caller to escape or encode the data appropriately.
     *
     * @return array
     */
    public static function getWeekInfoV2()
    {
        $weekStartDateTime = new DateTime('now', new DateTimeZone(Application_Model_Preference::GetTimezone()));

        $result = [];

        $utcTimezone = new DateTimeZone('UTC');

        $weekStartDateTime->setTimezone($utcTimezone);

        // Use this variable as the start date/time range when querying
        // for shows. We set it to 1 day prior to the beginning of the
        // schedule widget data to account for show date changes when
        // converting their start day/time to the client's local timezone.
        $showQueryDateRangeStart = clone $weekStartDateTime;
        $showQueryDateRangeStart->sub(new DateInterval('P1D'));
        $showQueryDateRangeStart->setTime(0, 0, 0);

        for ($dayOfWeekCounter = 0; $dayOfWeekCounter < DAYS_PER_WEEK; ++$dayOfWeekCounter) {
            $dateParse = date_parse($weekStartDateTime->format('Y-m-d H:i:s'));

            // Associate data to its date so that when we convert this array
            // to json the order remains the same - in chronological order.
            // We also format the key to be for example: "2015-6-1" to match
            // javascript date formats so it's easier to sort the shows by day.
            $result['weekDays'][$weekStartDateTime->format('Y-n-j')] = [];
            $result['weekDays'][$weekStartDateTime->format('Y-n-j')]['dayOfMonth'] = $dateParse['day'];
            $result['weekDays'][$weekStartDateTime->format('Y-n-j')]['dayOfWeek'] = strtoupper(_(date('D', $weekStartDateTime->getTimestamp())));

            // Shows scheduled for this day will get added to this array when
            // we convert the show times to the client's local timezone in weekly-program.phtml
            $result['weekDays'][$weekStartDateTime->format('Y-n-j')]['shows'] = [];

            // $weekStartDateTime has to be in station timezone when adding 1 day for daylight savings.
            // TODO: is this necessary since we set the time to "00:00" ?
            $stationTimezone = Application_Model_Preference::GetDefaultTimezone();
            $weekStartDateTime->setTimezone(new DateTimeZone($stationTimezone));

            $weekStartDateTime->add(new DateInterval('P1D'));

            // convert back to UTC to get the actual timestamp used for search.
            $weekStartDateTime->setTimezone($utcTimezone);
        }

        // Use this variable as the end date/time range when querying
        // for shows. We set it to 1 day after the end of the schedule
        // widget data to account for show date changes when converting
        // their start day/time to the client's local timezone.
        $showQueryDateRangeEnd = clone $weekStartDateTime;
        $showQueryDateRangeEnd->setTime(23, 59, 0);

        $shows = Application_Model_Show::getNextShows(
            $showQueryDateRangeStart->format('Y-m-d H:i:s'),
            'ALL',
            $showQueryDateRangeEnd->format('Y-m-d H:i:s')
        );

        // Convert each start and end time string to DateTime objects
        // so we can get a real timestamp. The timestamps will be used
        // to convert into javascript Date objects.
        foreach ($shows as &$show) {
            $dtStarts = new DateTime($show['starts'], new DateTimeZone('UTC'));
            $show['starts_timestamp'] = $dtStarts->getTimestamp();

            $dtEnds = new DateTime($show['ends'], new DateTimeZone('UTC'));
            $show['ends_timestamp'] = $dtEnds->getTimestamp();
        }
        $result['shows'] = $shows;

        // convert image paths to point to api endpoints
        // TODO: do we need this here?
        self::findAndConvertPaths($result);

        return $result;
    }

    /**
     * Recursively find image_path keys in the various $result subarrays,
     * and convert them to point to the show-logo endpoint.
     *
     * @param unknown $arr the array to search
     */
    public static function findAndConvertPaths(&$arr)
    {
        foreach ($arr as &$a) {
            if (is_array($a)) {
                if (array_key_exists('image_path', $a)) {
                    $a['image_path'] = $a['image_path'] && $a['image_path'] !== ''
                        ? Config::getPublicUrl() . 'api/show-logo?id=' . $a['id']
                        : '';
                } else {
                    self::findAndConvertPaths($a);
                }
            }
        }
    }
}
