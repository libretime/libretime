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

    /**
     * 
     * Enter description here ...
     * @param DateTime $startDateTime first show in user's local time
     */
    public function getLocalEndDateAndTime($startDateTime)
    {
        $duration = explode(":", $this->getDbDuration());

        return $startDateTime->add(new DateInterval('PT'.$duration[0].'H'.$duration[1].'M'));
    }

    public function isShowStartInPast()
    {
        return $this->getUTCStartDateAndTime() > gmdate("Y-m-d H:i:s");
    }

    public function formatDuration()
    {
        $info = explode(':',$this->getDbDuration());

        return str_pad(intval($info[0]),2,'0',STR_PAD_LEFT).'h '.str_pad(intval($info[1]),2,'0',STR_PAD_LEFT).'m';
    }
} // CcShowDays
