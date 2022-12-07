<?php

declare(strict_types=1);

/**
 * Skeleton subclass for representing a row from the 'cc_schedule' table.
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 */
class CcSchedule extends BaseCcSchedule
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
    public function getDbStarts($format = 'Y-m-d H:i:s.u')
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
    public function getDbEnds($format = 'Y-m-d H:i:s.u')
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
     * Get the [optionally formatted] temporal [fadein] column value.
     *
     * @param mixed $format
     *
     * @return mixed Formatted date/time value as string or DateTime object (if format is NULL), NULL if column is NULL
     *
     * @throws propelException - if unable to parse/validate the date/time value
     */
    public function getDbFadeIn($format = 's.u')
    {
        return parent::getDbFadein($format);
    }

    /**
     * Get the [optionally formatted] temporal [fadein] column value.
     *
     * @param mixed $format
     *
     * @return mixed Formatted date/time value as string or DateTime object (if format is NULL), NULL if column is NULL
     *
     * @throws propelException - if unable to parse/validate the date/time value
     */
    public function getDbFadeOut($format = 's.u')
    {
        return parent::getDbFadeout($format);
    }

    /**
     * @param string in format SS.uuuuuu, Datetime, or DateTime accepted string.
     * @param mixed $v
     *
     * @return CcPlaylistcontents The current object (for fluent API support)
     */
    public function setDbFadeIn($v)
    {
        $microsecond = 0;
        if ($v instanceof DateTime) {
            $dt = $v;
        } elseif (preg_match('/^[0-9]{1,2}(\.\d{1,6})?$/', $v)) {
            // in php 5.3.2 createFromFormat() with "u" is not supported(bug)
            // Hence we need to do parsing.
            $info = explode('.', $v);
            $microsecond = $info[1];
            $dt = DateTime::createFromFormat('s', $info[0]);
        } else {
            try {
                $dt = new DateTime($v);
            } catch (Exception $x) {
                throw new PropelException('Error parsing date/time value: ' . var_export($v, true), $x);
            }
        }

        if ($microsecond == 0) {
            $this->fade_in = $dt->format('H:i:s.u');
        } else {
            $this->fade_in = $dt->format('H:i:s') . '.' . $microsecond;
        }
        $this->modifiedColumns[] = CcSchedulePeer::FADE_IN;

        return $this;
    } // setDbFadein()

    /**
     * @param string in format SS.uuuuuu, Datetime, or DateTime accepted string.
     * @param mixed $v
     *
     * @return CcPlaylistcontents The current object (for fluent API support)
     */
    public function setDbFadeOut($v)
    {
        $microsecond = 0;
        if ($v instanceof DateTime) {
            $dt = $v;
        } elseif (preg_match('/^[0-9]{1,2}(\.\d{1,6})?$/', $v)) {
            // in php 5.3.2 createFromFormat() with "u" is not supported(bug)
            // Hence we need to do parsing.
            $info = explode('.', $v);
            $microsecond = $info[1];
            $dt = DateTime::createFromFormat('s', $info[0]);
        } else {
            try {
                $dt = new DateTime($v);
            } catch (Exception $x) {
                throw new PropelException('Error parsing date/time value: ' . var_export($v, true), $x);
            }
        }

        if ($microsecond == 0) {
            $this->fade_out = $dt->format('H:i:s.u');
        } else {
            $this->fade_out = $dt->format('H:i:s') . '.' . $microsecond;
        }
        $this->modifiedColumns[] = CcSchedulePeer::FADE_OUT;

        return $this;
    } // setDbFadeout()

    /**
     * Sets the value of [starts] column to a normalized version of the date/time value specified.
     *
     * @param mixed $v string, integer (timestamp), or DateTime value.  Empty string will
     *                 be treated as NULL for temporal objects.
     *
     * @return CcSchedule The current object (for fluent API support)
     */
    public function setDbStarts($v)
    {
        $utcTimeZone = new DateTimeZone('UTC');

        if ($v instanceof DateTime) {
            $dt = $v;
            $dt->setTimezone($utcTimeZone);
        } else {
            // some string/numeric value passed; we normalize that so that we can
            // validate it.
            try {
                if (is_numeric($v)) { // if it's a unix timestamp
                    $dt = new DateTime('@' . $v, $utcTimeZone);
                } else {
                    $dt = new DateTime($v, $utcTimeZone);
                }
            } catch (Exception $x) {
                throw new PropelException('Error parsing date/time value: ' . var_export($v, true), $x);
            }
        }

        $this->starts = ($dt ? $dt->format('Y-m-d H:i:s.u') : null);
        $this->modifiedColumns[] = CcSchedulePeer::STARTS;

        return $this;
    } // setDbStarts()

    /**
     * Sets the value of [ends] column to a normalized version of the date/time value specified.
     *
     * @param mixed $v string, integer (timestamp), or DateTime value.  Empty string will
     *                 be treated as NULL for temporal objects.
     *
     * @return CcSchedule The current object (for fluent API support)
     */
    public function setDbEnds($v)
    {
        $utcTimeZone = new DateTimeZone('UTC');

        if ($v instanceof DateTime) {
            $dt = $v;
            $dt->setTimezone($utcTimeZone);
        } else {
            // some string/numeric value passed; we normalize that so that we can
            // validate it.
            try {
                if (is_numeric($v)) { // if it's a unix timestamp
                    $dt = new DateTime('@' . $v, $utcTimeZone);
                } else {
                    $dt = new DateTime($v, $utcTimeZone);
                }
            } catch (Exception $x) {
                throw new PropelException('Error parsing date/time value: ' . var_export($v, true), $x);
            }
        }

        $this->ends = ($dt ? $dt->format('Y-m-d H:i:s.u') : null);
        $this->modifiedColumns[] = CcSchedulePeer::ENDS;

        return $this;
    } // setDbEnds()

    public function isCurrentItem($epochNow = null)
    {
        if (is_null($epochNow)) {
            $epochNow = microtime(true);
        }

        $epochStart = floatval($this->getDbStarts('U.u'));
        $epochEnd = floatval($this->getDbEnds('U.u'));

        if ($epochStart < $epochNow && $epochEnd > $epochNow) {
            return true;
        }

        return false;
    }
} // CcSchedule
