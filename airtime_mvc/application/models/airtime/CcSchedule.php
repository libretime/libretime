<?php



/**
 * Skeleton subclass for representing a row from the 'cc_schedule' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.airtime
 */
class CcSchedule extends BaseCcSchedule {

    public function getDbClipLength($format = 'H:i:s.u')
    {
        return parent::getDbClipLength($format);
    }

    /**
     * Get the [optionally formatted] temporal [starts] column value.
     *
     *
     * @param      string $format The date/time format string (either date()-style or strftime()-style).
     *                          If format is NULL, then the raw DateTime object will be returned.
     * @return     mixed Formatted date/time value as string or DateTime object (if format is NULL), NULL if column is NULL
     * @throws     PropelException - if unable to parse/validate the date/time value.
     */
    public function getDbStarts($format = 'Y-m-d H:i:s.u')
    {
        return parent::getDbStarts($format);
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
    public function getDbEnds($format = 'Y-m-d H:i:s.u')
    {
        return parent::getDbEnds($format);
    }

 /**
     * Get the [optionally formatted] temporal [fadein] column value.
     *
     * @return     mixed Formatted date/time value as string or DateTime object (if format is NULL), NULL if column is NULL
     * @throws     PropelException - if unable to parse/validate the date/time value.
     */
    public function getDbFadeIn($format = "s.u")
    {
        parent::getDbFadein($format);
    }

    /**
     * Get the [optionally formatted] temporal [fadein] column value.
     *
     * @return     mixed Formatted date/time value as string or DateTime object (if format is NULL), NULL if column is NULL
     * @throws     PropelException - if unable to parse/validate the date/time value.
     */
    public function getDbFadeOut($format = "s.u")
    {
       parent::getDbFadeout($format);
    }

    /**
     * Just changing the default format to return subseconds
     *
     * @return     mixed Formatted date/time value as string or DateTime object (if format is NULL), NULL if column is NULL
     * @throws     PropelException - if unable to parse/validate the date/time value.
     */
    public function getDbCueIn($format = 'H:i:s.u')
    {
       return parent::getDbCuein($format);
    }

    /**
     * Just changing the default format to return subseconds
     *
     * @return     mixed Formatted date/time value as string or DateTime object (if format is NULL), NULL if column is NULL
     * @throws     PropelException - if unable to parse/validate the date/time value.
     */
    public function getDbCueOut($format = 'H:i:s.u')
    {
       return parent::getDbCueout($format);
    }

    /**
     *
     * @param String in format SS.uuuuuu, Datetime, or DateTime accepted string.
     *
     * @return CcPlaylistcontents The current object (for fluent API support)
     */
    public function setDbFadeIn($v)
    {
        if ($v instanceof DateTime) {
            $dt = $v;
        }
        else if (preg_match('/^[0-5][0-9](\.\d{1,6})?$/', $v)) {
            $dt = DateTime::createFromFormat("s.u", $v);
        }
        else {
            try {
                $dt = new DateTime($v);
            } catch (Exception $x) {
                throw new PropelException('Error parsing date/time value: ' . var_export($v, true), $x);
            }
        }

        $this->fade_in = $dt->format('H:i:s.u');
        $this->modifiedColumns[] = CcSchedulePeer::FADE_IN;

        return $this;
    } // setDbFadein()

    /**
    *
    * @param String in format SS.uuuuuu, Datetime, or DateTime accepted string.
    *
    * @return CcPlaylistcontents The current object (for fluent API support)
    */
    public function setDbFadeOut($v)
    {
        if ($v instanceof DateTime) {
            $dt = $v;
        }
        else if (preg_match('/^[0-5][0-9](\.\d{1,6})?$/', $v)) {
            $dt = DateTime::createFromFormat("s.u", $v);
        }
        else {
            try {
                $dt = new DateTime($v);
            } catch (Exception $x) {
                throw new PropelException('Error parsing date/time value: ' . var_export($v, true), $x);
            }
        }

        $this->fadeIout = $dt->format('H:i:s.u');
        $this->modifiedColumns[] = CcPlaylistcontentsPeer::FADE_OUT;

        return $this;
    } // setDbFadeout()

    /**
     * Sets the value of [cuein] column to a normalized version of the date/time value specified.
     *
     * @param      mixed $v string, integer (timestamp), or DateTime value.  Empty string will
     *                      be treated as NULL for temporal objects.
     * @return     CcPlaylistcontents The current object (for fluent API support)
     */
    public function setDbCueIn($v)
    {
        if ($v instanceof DateTime) {
            $dt = $v;
        }
        else {
            try {
                $dt = new DateTime($v);
            }
            catch (Exception $x) {
                throw new PropelException('Error parsing date/time value: ' . var_export($v, true), $x);
            }
        }

        $this->cue_in = $dt->format('H:i:s.u');
        $this->modifiedColumns[] = CcPlaylistcontentsPeer::CUE_IN;

        return $this;
    } // setDbCuein()

    /**
     * Sets the value of [cueout] column to a normalized version of the date/time value specified.
     *
     * @param      mixed $v string, integer (timestamp), or DateTime value.  Empty string will
     *                      be treated as NULL for temporal objects.
     * @return     CcPlaylistcontents The current object (for fluent API support)
     */
    public function setDbCueout($v)
    {
        if ($v instanceof DateTime) {
            $dt = $v;
        }
        else {
            try {
                $dt = new DateTime($v);
            }
            catch (Exception $x) {
                throw new PropelException('Error parsing date/time value: ' . var_export($v, true), $x);
            }
        }

        $this->cue_out = $dt->format('H:i:s.u');
        $this->modifiedColumns[] = CcPlaylistcontentsPeer::CUE_OUT;

        return $this;
    } // setDbCueout()

    /**
     * Sets the value of [cliplength] column to a normalized version of the date/time value specified.
     *
     * @param      mixed $v string, integer (timestamp), or DateTime value.  Empty string will
     *                      be treated as NULL for temporal objects.
     * @return     CcPlaylistcontents The current object (for fluent API support)
     */
    public function setDbClipLength($v)
    {
        if ($v instanceof DateTime) {
            $dt = $v;
        }
        else {

            try {

                $dt = new DateTime($v);

            } catch (Exception $x) {
                throw new PropelException('Error parsing date/time value: ' . var_export($v, true), $x);
            }
        }

        $this->clip_length = $dt->format('H:i:s.u');
        $this->modifiedColumns[] = CcPlaylistcontentsPeer::CLIP_LENGTH;

        return $this;
    } // setDbCliplength()

} // CcSchedule
