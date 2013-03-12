<?php



/**
 * Skeleton subclass for representing a row from the 'cc_show_days' table.
 *
 * 
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.airtime
 */
class CcShowDays extends BaseCcShowDays {

    public function isRepeating()
    {
        return $this->getDbRepeatType() != -1;
    }

    public function getUTCStartDateAndTime()
    {
        $dt = new DateTime(
            "{$this->getDbFirstShow()} {$this->getDbStartTime()}",
            new DateTimeZone($this->getDbTimezone())
        );
        $dt->setTimezone(new DateTimeZone("UTC"));

        return $dt;
    }

    public function getLocalStartDateAndTime()
    {
        $dt = new DateTime(
            "{$this->getDbFirstShow()} {$this->getDbStartTime()}",
            new DateTimeZone($this->getDbTimezone())
        );

        return $dt;
    }
} // CcShowDays
