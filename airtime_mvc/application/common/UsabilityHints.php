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
        $futureShow = self::getNextFutureShow();
        $currentShow = self::getCurrentShow();

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
        $futureShow = self::getNextFutureShow();
        $currentShow = self::getCurrentShow();

        if (is_null($futureShow) && is_null($currentShow)) {
            return false;
        } else {
            if ($currentShow) {
                $scheduledTracks = CcScheduleQuery::create()
                    ->filterByDbInstanceId($currentShow->getDbId())
                    ->find();
                if ($scheduledTracks->count() == 0) {
                    return true;
                }
            } else if ($futureShow) {
                $scheduledTracks = CcScheduleQuery::create()
                    ->filterByDbInstanceId($futureShow->getDbId())
                    ->find();
                if ($scheduledTracks->count() == 0) {
                    return true;
                }
            }
        }
    }

    private static function getCurrentShow()
    {
        $now = new DateTime("now", new DateTimeZone("UTC"));

        return CcShowInstancesQuery::create()
            ->filterByDbStarts($now, Criteria::LESS_THAN)
            ->filterByDbEnds($now, Criteria::GREATER_THAN)
            ->filterByDbModifiedInstance(false)
            ->findOne();
    }

    private static function getNextFutureShow()
    {
        $now = new DateTime("now", new DateTimeZone("UTC"));

        return CcShowInstancesQuery::create()
            ->filterByDbStarts($now, Criteria::GREATER_THAN)
            ->filterByDbModifiedInstance(false)
            ->orderByDbStarts()
            ->findOne();
    }
}