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
} // CcShowInstances
