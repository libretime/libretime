<?php



/**
 * Skeleton subclass for representing a row from the 'cc_blockcontents' table.
 *
 * 
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.airtime
 */
class CcBlockcontents extends BaseCcBlockcontents {
    /**
     * Get the [optionally formatted] temporal [fadein] column value.
     *
     * @return     mixed Formatted date/time value as string or DateTime object (if format is NULL), NULL if column is NULL
     * @throws     PropelException - if unable to parse/validate the date/time value.
     */
    public function getDbFadein($format = "s.u")
    {
        return parent::getDbFadein($format);
    }
    
    /**
     * Get the [optionally formatted] temporal [fadein] column value.
     *
     * @return     mixed Formatted date/time value as string or DateTime object (if format is NULL), NULL if column is NULL
     * @throws     PropelException - if unable to parse/validate the date/time value.
     */
    public function getDbFadeout($format = "s.u")
    {
        return parent::getDbFadeout($format);
    }
    
    /**
     *
     * @param String in format SS.uuuuuu, Datetime, or DateTime accepted string.
     *
     * @return CcBlockcontents The current object (for fluent API support)
     */
    public function setDbFadein($v)
    {
        if ($v instanceof DateTime) {
            $dt = $v;
        }
        else if (preg_match('/^[0-9]{1,2}(\.\d{1,6})?$/', $v)) {
            $dt = DateTime::createFromFormat("s.u", $v);
        }
        else {
            try {
                $dt = new DateTime($v);
            } catch (Exception $x) {
                throw new PropelException('Error parsing date/time value: ' . var_export($v, true), $x);
            }
        }
    
        $this->fadein = $dt->format('H:i:s.u');
        $this->modifiedColumns[] = CcBlockcontentsPeer::FADEIN;
    
        return $this;
    } // setDbFadein()
    
    /**
     *
     * @param String in format SS.uuuuuu, Datetime, or DateTime accepted string.
     *
     * @return CcBlockcontents The current object (for fluent API support)
     */
    public function setDbFadeout($v)
    {
        if ($v instanceof DateTime) {
            $dt = $v;
        }
        else if (preg_match('/^[0-9]{1,2}(\.\d{1,6})?$/', $v)) {
            $dt = DateTime::createFromFormat("s.u", $v);
        }
        else {
            try {
                $dt = new DateTime($v);
            } catch (Exception $x) {
                throw new PropelException('Error parsing date/time value: ' . var_export($v, true), $x);
            }
        }
    
        $this->fadeout = $dt->format('H:i:s.u');
        $this->modifiedColumns[] = CcBlockcontentsPeer::FADEOUT;
    
        return $this;
    } // setDbFadeout()

} // CcBlockcontents
