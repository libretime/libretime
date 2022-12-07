<?php

declare(strict_types=1);

class Application_Model_Dashboard
{
    public static function GetPreviousItem($p_timeNow)
    {
        // get previous show and previous item in the schedule table.
        // Compare the two and if the last show was recorded and started
        // after the last item in the schedule table, then return the show's
        // name. Else return the last item from the schedule.

        $showInstance = Application_Model_ShowInstance::GetLastShowInstance($p_timeNow);
        $row = Application_Model_Schedule::GetLastScheduleItem($p_timeNow);

        if (is_null($showInstance)) {
            if (count($row) == 0) {
                return null;
            }

            return [
                'name' => $row[0]['artist_name'] . ' - ' . $row[0]['track_title'],
                'starts' => $row[0]['starts'],
                'ends' => $row[0]['ends'],
            ];
        }
        if (count($row) == 0) {
            if ($showInstance->isRecorded()) {
                // last item is a show instance
                return [
                    'name' => $showInstance->getName(),
                    'starts' => $showInstance->getShowInstanceStart(),
                    'ends' => $showInstance->getShowInstanceEnd(),
                ];
            }

            return null;
        }
        // return the one that started later.
        if ($row[0]['starts'] >= $showInstance->getShowInstanceStart()) {
            return [
                'name' => $row[0]['artist_name'] . ' - ' . $row[0]['track_title'],
                'starts' => $row[0]['starts'],
                'ends' => $row[0]['ends'],
            ];
        }

        return [
            'name' => $showInstance->getName(),
            'starts' => $showInstance->getShowInstanceStart(),
            'ends' => $showInstance->getShowInstanceEnd(),
        ];
    }

    public static function GetCurrentItem($p_timeNow)
    {
        // get previous show and previous item in the schedule table.
        // Compare the two and if the last show was recorded and started
        // after the last item in the schedule table, then return the show's
        // name. Else return the last item from the schedule.

        $row = [];
        $showInstance = Application_Model_ShowInstance::GetCurrentShowInstance($p_timeNow);
        if (!is_null($showInstance)) {
            $instanceId = $showInstance->getShowInstanceId();
            $row = Application_Model_Schedule::GetCurrentScheduleItem($p_timeNow, $instanceId);
        }
        if (is_null($showInstance)) {
            if (count($row) == 0) {
                return null;
            }
            /* Should never reach here, but lets return the track information
             * just in case we allow tracks to be scheduled without a show
             * in the future.
             */

            return [
                'name' => $row[0]['artist_name'] . ' - ' . $row[0]['track_title'],
                'artwork_data' => $row[0]['artwork_data'],
                'starts' => $row[0]['starts'],
                'ends' => $row[0]['ends'],
            ];
        }
        if (count($row) == 0) {
            // last item is a show instance
            if ($showInstance->isRecorded()) {
                return [
                    'name' => $showInstance->getName(),
                    'starts' => $showInstance->getShowInstanceStart(),
                    'ends' => $showInstance->getShowInstanceEnd(),
                    'media_item_played' => false,
                    'record' => true,
                ];
            }

            return null;
        }

        return [
            'name' => $row[0]['artist_name'] . ' - ' . $row[0]['track_title'],
            'artwork_data' => $row[0]['artwork_data'],
            'starts' => $row[0]['starts'],
            'ends' => $row[0]['ends'],
            'media_item_played' => $row[0]['media_item_played'],
            'record' => 0,
        ];
    }

    public static function GetNextItem($p_timeNow)
    {
        // get previous show and previous item in the schedule table.
        // Compare the two and if the last show was recorded and started
        // after the last item in the schedule table, then return the show's
        // name. Else return the last item from the schedule.

        $showInstance = Application_Model_ShowInstance::GetNextShowInstance($p_timeNow);
        $row = Application_Model_Schedule::GetNextScheduleItem($p_timeNow);

        if (is_null($showInstance)) {
            if (count($row) == 0) {
                return null;
            }

            return [
                'name' => $row[0]['artist_name'] . ' - ' . $row[0]['track_title'],
                'artwork_data' => $row[0]['artwork_data'],
                'starts' => $row[0]['starts'],
                'ends' => $row[0]['ends'],
            ];
        }
        if (count($row) == 0) {
            if ($showInstance->isRecorded()) {
                // last item is a show instance
                return [
                    'name' => $showInstance->getName(),
                    'starts' => $showInstance->getShowInstanceStart(),
                    'ends' => $showInstance->getShowInstanceEnd(),
                ];
            }

            return null;
        }
        // return the one that starts sooner.

        if ($row[0]['starts'] <= $showInstance->getShowInstanceStart()) {
            return [
                'name' => $row[0]['artist_name'] . ' - ' . $row[0]['track_title'],
                'artwork_data' => $row[0]['artwork_data'],
                'starts' => $row[0]['starts'],
                'ends' => $row[0]['ends'],
            ];
        }

        return [
            'name' => $showInstance->getName(),
            'starts' => $showInstance->getShowInstanceStart(),
            'ends' => $showInstance->getShowInstanceEnd(),
        ];
    }
}
