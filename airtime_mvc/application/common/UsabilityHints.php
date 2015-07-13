<?php

class Application_Common_UsabilityHints
{
    /**
     * Returns true if no files have been uploaded.
     */
    public static function zeroFilesUploaded()
    {
        $fileCount = CcFilesQuery::create()
            ->filterByDbFileExists(true)
            ->filterByDbHidden(false)
            ->count();

        if ($fileCount == 0) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Returns true if there is at least one show scheduled in the future.
     */
    public static function isFutureOrCurrentShowScheduled()
    {
        $now = new DateTime("now", new DateTimeZone("UTC"));
        $futureShow = CcShowInstancesQuery::create()
            ->filterByDbStarts($now, Criteria::GREATER_THAN)
            ->filterByDbModifiedInstance(false)
            ->findOne();

        $currentShow = CcShowInstancesQuery::create()
            ->filterByDbStarts($now, Criteria::LESS_THAN)
            ->filterByDbEnds($now, Criteria::GREATER_THAN)
            ->filterByDbModifiedInstance(false)
            ->findOne();
        if (is_null($futureShow) && is_null($currentShow)) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Returns true if the current show does not have anything scheduled in it.
     *
     * Returns true if there is nothing currently scheduled and the next show
     * is empty.
     */
    public static function isCurrentOrNextShowEmpty()
    {
        $schedule = Application_Model_Schedule::GetPlayOrderRange();
        $shows = $schedule["shows"];

        if (empty($shows["current"]) && empty($shows["next"])) {
            return false;
        } else {
            if ($shows["current"]) {
                $scheduledTracks = CcScheduleQuery::create()
                    ->filterByDbInstanceId($shows["current"]["instance_id"])
                    ->find();
                if ($scheduledTracks->count() == 0) {
                    return true;
                }
            } else if ($shows["next"]) {
                $nextShow = $shows["next"][0];
                $scheduledTracks = CcScheduleQuery::create()
                    ->filterByDbInstanceId($nextShow["instance_id"])
                    ->find();
                if ($scheduledTracks->count() == 0) {
                    return true;
                }
            }
        }
    }
}