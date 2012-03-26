<?php



/**
 * Skeleton subclass for representing a row from the 'cc_show_instances' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.airtime
 */
class CcShowInstances extends BaseCcShowInstances {

 /**
     * Get the [optionally formatted] temporal [starts] column value.
     *
     *
     * @param      string $format The date/time format string (either date()-style or strftime()-style).
     *                          If format is NULL, then the raw DateTime object will be returned.
     * @return     mixed Formatted date/time value as string or DateTime object (if format is NULL), NULL if column is NULL
     * @throws     PropelException - if unable to parse/validate the date/time value.
     */
    public function getDbStarts($format = 'Y-m-d H:i:s')
    {
        if ($this->starts === null) {
            return null;
        }

        try {
            $dt = new DateTime($this->starts, new DateTimeZone("UTC"));
        } catch (Exception $x) {
            throw new PropelException("Internally stored date/time/timestamp value could not be converted to DateTime: " . var_export($this->starts, true), $x);
        }

        if ($format === null) {
            // Because propel.useDateTimeClass is TRUE, we return a DateTime object.
            return $dt;
        } elseif (strpos($format, '%') !== false) {
            return strftime($format, $dt->format('U'));
        } else {
            return $dt->format($format);
        }
    }

    /**
     * Get the [optionally formatted] temporal [ends] column value.
     *
     *
     * @param      string $format The date/time format string (either date()-style or strftime()-style).
     *                          If format is NULL, then the raw DateTime object will be returned.
     * @return     mixed Formatted date/time value as string or DateTime object (if format is NULL), NULL if column is NULL
     * @throws     PropelException - if unable to parse/validate the date/time value.
     */
    public function getDbEnds($format = 'Y-m-d H:i:s')
    {
        if ($this->ends === null) {
            return null;
        }

        try {
            $dt = new DateTime($this->ends, new DateTimeZone("UTC"));
        } catch (Exception $x) {
            throw new PropelException("Internally stored date/time/timestamp value could not be converted to DateTime: " . var_export($this->ends, true), $x);
        }

        if ($format === null) {
            // Because propel.useDateTimeClass is TRUE, we return a DateTime object.
            return $dt;
        } elseif (strpos($format, '%') !== false) {
            return strftime($format, $dt->format('U'));
        } else {
            return $dt->format($format);
        }
    }

    /**
     * Get the [optionally formatted] temporal [last_scheduled] column value.
     *
     *
     * @param      string $format The date/time format string (either date()-style or strftime()-style).
     *                          If format is NULL, then the raw DateTime object will be returned.
     * @return     mixed Formatted date/time value as string or DateTime object (if format is NULL), NULL if column is NULL
     * @throws     PropelException - if unable to parse/validate the date/time value.
     */
    public function getDbLastScheduled($format = 'Y-m-d H:i:s')
    {
        if ($this->last_scheduled === null) {
            return null;
        }

        try {
            $dt = new DateTime($this->last_scheduled, new DateTimeZone("UTC"));
        } catch (Exception $x) {
            throw new PropelException("Internally stored date/time/timestamp value could not be converted to DateTime: " . var_export($this->last_scheduled, true), $x);
        }

        if ($format === null) {
            // Because propel.useDateTimeClass is TRUE, we return a DateTime object.
            return $dt;
        } elseif (strpos($format, '%') !== false) {
            return strftime($format, $dt->format('U'));
        } else {
            return $dt->format($format);
        }
    }

    //post save hook to update the cc_schedule status column for the tracks in the show.
    public function updateScheduleStatus(PropelPDO $con) {

        Logging::log("in post save for showinstances");
        
        $now = time();

        //scheduled track is in the show
        CcScheduleQuery::create()
            ->filterByDbInstanceId($this->id)
            ->filterByDbPlayoutStatus(0, Criteria::GREATER_EQUAL)
            ->filterByDbEnds($this->ends, Criteria::LESS_EQUAL)
            ->update(array('DbPlayoutStatus' => 1), $con);

        Logging::log("updating status for in show items.");

        //scheduled track is a boundary track
        CcScheduleQuery::create()
            ->filterByDbInstanceId($this->id)
            ->filterByDbPlayoutStatus(0, Criteria::GREATER_EQUAL)
            ->filterByDbStarts($this->ends, Criteria::LESS_THAN)
            ->filterByDbEnds($this->ends, Criteria::GREATER_THAN)
            ->update(array('DbPlayoutStatus' => 2), $con);

        //scheduled track is overbooked.
        CcScheduleQuery::create()
            ->filterByDbInstanceId($this->id)
            ->filterByDbPlayoutStatus(0, Criteria::GREATER_EQUAL)
            ->filterByDbStarts($this->ends, Criteria::GREATER_THAN)
            ->update(array('DbPlayoutStatus' => 0), $con);

    }

} // CcShowInstances
