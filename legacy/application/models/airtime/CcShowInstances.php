<?php

declare(strict_types=1);

/**
 * Skeleton subclass for representing a row from the 'cc_show_instances' table.
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 */
class CcShowInstances extends BaseCcShowInstances
{
    /**
     * Get the [optionally formatted] temporal [starts] column value.
     *
     * @param string $format The date/time format string (either date()-style or strftime()-style).
     *                       If format is NULL, then the raw DateTime object will be returned.
     *
     * @return mixed Formatted date/time value as string or DateTime object (if format is NULL), NULL if column is NULL
     *
     * @throws propelException - if unable to parse/validate the date/time value
     */
    public function getDbStarts($format = 'Y-m-d H:i:s')
    {
        if ($this->starts === null) {
            return null;
        }

        try {
            $dt = new DateTime($this->starts, new DateTimeZone('UTC'));
        } catch (Exception $x) {
            throw new PropelException('Internally stored date/time/timestamp value could not be converted to DateTime: ' . var_export($this->starts, true), $x);
        }

        if ($format === null) {
            // Because propel.useDateTimeClass is TRUE, we return a DateTime object.
            return $dt;
        }
        if (strpos($format, '%') !== false) {
            return strftime($format, $dt->format('U'));
        }

        return $dt->format($format);
    }

    /**
     * Get the [optionally formatted] temporal [ends] column value.
     *
     * @param string $format The date/time format string (either date()-style or strftime()-style).
     *                       If format is NULL, then the raw DateTime object will be returned.
     *
     * @return mixed Formatted date/time value as string or DateTime object (if format is NULL), NULL if column is NULL
     *
     * @throws propelException - if unable to parse/validate the date/time value
     */
    public function getDbEnds($format = 'Y-m-d H:i:s')
    {
        if ($this->ends === null) {
            return null;
        }

        try {
            $dt = new DateTime($this->ends, new DateTimeZone('UTC'));
        } catch (Exception $x) {
            throw new PropelException('Internally stored date/time/timestamp value could not be converted to DateTime: ' . var_export($this->ends, true), $x);
        }

        if ($format === null) {
            // Because propel.useDateTimeClass is TRUE, we return a DateTime object.
            return $dt;
        }
        if (strpos($format, '%') !== false) {
            return strftime($format, $dt->format('U'));
        }

        return $dt->format($format);
    }

    /**
     * Get the [optionally formatted] temporal [last_scheduled] column value.
     *
     * @param string $format The date/time format string (either date()-style or strftime()-style).
     *                       If format is NULL, then the raw DateTime object will be returned.
     *
     * @return mixed Formatted date/time value as string or DateTime object (if format is NULL), NULL if column is NULL
     *
     * @throws propelException - if unable to parse/validate the date/time value
     */
    public function getDbLastScheduled($format = 'Y-m-d H:i:s')
    {
        if ($this->last_scheduled === null) {
            return null;
        }

        try {
            $dt = new DateTime($this->last_scheduled, new DateTimeZone('UTC'));
        } catch (Exception $x) {
            throw new PropelException('Internally stored date/time/timestamp value could not be converted to DateTime: ' . var_export($this->last_scheduled, true), $x);
        }

        if ($format === null) {
            // Because propel.useDateTimeClass is TRUE, we return a DateTime object.
            return $dt;
        }
        if (strpos($format, '%') !== false) {
            return strftime($format, $dt->format('U'));
        }

        return $dt->format($format);
    }

    // post save hook to update the cc_schedule status column for the tracks in the show.
    public function updateScheduleStatus(PropelPDO $con)
    {
        $this->updateDbTimeFilled($con);

        // scheduled track is in the show
        CcScheduleQuery::create()
            ->filterByDbInstanceId($this->id)
            ->filterByDbPlayoutStatus(0, Criteria::GREATER_EQUAL)
            ->filterByDbEnds($this->ends, Criteria::LESS_EQUAL)
            ->update(['DbPlayoutStatus' => 1], $con);

        // scheduled track is a boundary track
        CcScheduleQuery::create()
            ->filterByDbInstanceId($this->id)
            ->filterByDbPlayoutStatus(0, Criteria::GREATER_EQUAL)
            ->filterByDbStarts($this->ends, Criteria::LESS_THAN)
            ->filterByDbEnds($this->ends, Criteria::GREATER_THAN)
            ->update(['DbPlayoutStatus' => 2], $con);

        // scheduled track is overbooked.
        CcScheduleQuery::create()
            ->filterByDbInstanceId($this->id)
            ->filterByDbPlayoutStatus(0, Criteria::GREATER_EQUAL)
            ->filterByDbStarts($this->ends, Criteria::GREATER_THAN)
            ->update(['DbPlayoutStatus' => 0], $con);

        $this->setDbLastScheduled(gmdate('Y-m-d H:i:s'));
        $this->save($con);
    }

    /**
     * This function resets the cc_schedule table's position numbers so that
     * tracks for each cc_show_instances start at position 1.
     *
     * The position numbers can become out of sync when the user deletes items
     * from linekd shows filled with dyanmic smart blocks, where each instance
     * has a different amount of scheduled items
     */
    public function correctSchedulePositions()
    {
        $schedule = CcScheduleQuery::create()
            ->filterByDbInstanceId($this->id)
            ->orderByDbStarts()
            ->find();

        $pos = 0;
        foreach ($schedule as $item) {
            $item->setDbPosition($pos)->save();
            ++$pos;
        }
    }

    /**
     * Computes the value of the aggregate column time_filled.
     *
     * @param PropelPDO $con A connection object
     *
     * @return mixed The scalar result from the aggregate query
     */
    public function computeDbTimeFilled(PropelPDO $con)
    {
        $stmt = $con->prepare('SELECT SUM(clip_length) FROM "cc_schedule" WHERE cc_schedule.INSTANCE_ID = :p1');
        $stmt->bindValue(':p1', $this->getDbId());
        $stmt->execute();

        return $stmt->fetchColumn();
    }

    /**
     * Updates the aggregate column time_filled.
     *
     * @param PropelPDO $con A connection object
     */
    public function updateDbTimeFilled(PropelPDO $con)
    {
        $timefilled = $this->computeDbTimeFilled($con);
        if (is_null($timefilled)) {
            $timefilled = '00:00:00';
        }
        $this->setDbTimeFilled($timefilled);
        $this->save($con);
    }

    public function preInsert(PropelPDO $con = null)
    {
        $now = new DateTime('now', new DateTimeZone('UTC'));
        $this->setDbCreated($now);

        return true;
    }

    public function isRecorded()
    {
        return $this->getDbRecord() == 1 ? true : false;
    }

    public function isRebroadcast()
    {
        return $this->getDbRebroadcast() == 1 ? true : false;
    }

    public function getLocalStartDateTime()
    {
        $startDT = $this->getDbStarts(null);

        return $startDT->setTimezone(new DateTimeZone(Application_Model_Preference::GetTimezone()));
    }
} // CcShowInstances
