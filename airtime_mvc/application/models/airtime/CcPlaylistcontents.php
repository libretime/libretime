<?php

/**
 * Skeleton subclass for representing a row from the 'cc_playlistcontents' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.campcaster
 */
class CcPlaylistcontents extends BaseCcPlaylistcontents {

    /**
     * Get the [optionally formatted] temporal [fadein] column value.
     *
     * @return     mixed Formatted date/time value as string or DateTime object (if format is NULL), NULL if column is NULL
     * @throws     PropelException - if unable to parse/validate the date/time value.
     */
    public function getDbFadein()
    {
        if ($this->fadein === null) {
            return null;
        }

        try {
            $dt = new DateTime($this->fadein);
        } catch (Exception $x) {
            throw new PropelException("Internally stored date/time/timestamp value could not be converted to DateTime: " . var_export($this->fadein, true), $x);
        }

        return $dt->format("s.u");
    }

    /**
     * Get the [optionally formatted] temporal [fadein] column value.
     *
     * @return     mixed Formatted date/time value as string or DateTime object (if format is NULL), NULL if column is NULL
     * @throws     PropelException - if unable to parse/validate the date/time value.
     */
    public function getDbFadeout()
    {
        if ($this->fadeout === null) {
            return null;
        }

        try {
            $dt = new DateTime($this->fadeout);
        } catch (Exception $x) {
            throw new PropelException("Internally stored date/time/timestamp value could not be converted to DateTime: " . var_export($this->fadein, true), $x);
        }

        return $dt->format("s.u");
    }

    /**
     * Just changing the default format to return subseconds
     *
     * @return     mixed Formatted date/time value as string or DateTime object (if format is NULL), NULL if column is NULL
     * @throws     PropelException - if unable to parse/validate the date/time value.
     */
    public function getDbCuein($format = 'H:i:s.u')
    {
       return parent::getDbCuein($format);
    }

    /**
     * Just changing the default format to return subseconds
     *
     * @return     mixed Formatted date/time value as string or DateTime object (if format is NULL), NULL if column is NULL
     * @throws     PropelException - if unable to parse/validate the date/time value.
     */
    public function getDbCueout($format = 'H:i:s.u')
    {
       return parent::getDbCueout($format);
    }

    /**
     * Just changing the default format to return subseconds
     *
     * @return     mixed Formatted date/time value as string or DateTime object (if format is NULL), NULL if column is NULL
     * @throws     PropelException - if unable to parse/validate the date/time value.
     */
    public function getDbCliplength($format = 'H:i:s.u')
    {
       return parent::getDbCliplength($format);
    }

    /**
     *
     * @param String in format SS.uuuuuu, Datetime, or DateTime accepted string.
     *
     * @return CcPlaylistcontents The current object (for fluent API support)
     */
    public function setDbFadein($v)
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

        $this->fadein = ($dt ? $dt->format('H:i:s.u') : null);
        $this->modifiedColumns[] = CcPlaylistcontentsPeer::FADEIN;

        return $this;
    } // setDbFadein()

    /**
    *
    * @param String in format SS.uuuuuu, Datetime, or DateTime accepted string.
    *
    * @return CcPlaylistcontents The current object (for fluent API support)
    */
    public function setDbFadeout($v)
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

        $this->fadeout = ($dt ? $dt->format('H:i:s.u') : null);
        $this->modifiedColumns[] = CcPlaylistcontentsPeer::FADEOUT;

        return $this;
    } // setDbFadeout()

    /**
     * Sets the value of [cuein] column to a normalized version of the date/time value specified.
     *
     * @param      mixed $v string, integer (timestamp), or DateTime value.  Empty string will
     *                      be treated as NULL for temporal objects.
     * @return     CcPlaylistcontents The current object (for fluent API support)
     */
    public function setDbCuein($v)
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

        $this->cuein = $dt->format('H:i:s.u');
        $this->modifiedColumns[] = CcPlaylistcontentsPeer::CUEIN;

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

        $this->cueout = $dt->format('H:i:s.u');
        $this->modifiedColumns[] = CcPlaylistcontentsPeer::CUEOUT;

        return $this;
    } // setDbCueout()

    /**
     * Sets the value of [cliplength] column to a normalized version of the date/time value specified.
     *
     * @param      mixed $v string, integer (timestamp), or DateTime value.  Empty string will
     *                      be treated as NULL for temporal objects.
     * @return     CcPlaylistcontents The current object (for fluent API support)
     */
    public function setDbCliplength($v)
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

        $this->cliplength = $dt->format('H:i:s.u');
        $this->modifiedColumns[] = CcPlaylistcontentsPeer::CLIPLENGTH;

        return $this;
    } // setDbCliplength()


} // CcPlaylistcontents
