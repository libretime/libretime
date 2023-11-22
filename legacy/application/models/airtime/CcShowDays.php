<?php

/**
 * Skeleton subclass for representing a row from the 'cc_show_days' table.
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 */
class CcShowDays extends BaseCcShowDays
{
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
        $dt->setTimezone(new DateTimeZone('UTC'));

        return $dt;
    }

    // Returns the start of a show in the timezone it was created in
    public function getLocalStartDateAndTime()
    {
            Logging::info('getLocalStartDateAndTime');
            Logging::info($this->getDbTimezone()); 
           return new DateTime("{$this->getDbFirstShow()} {$this->getDbStartTime()}",new DateTimeZone($this->getDbTimezone()));//BUG?
        return DateTime::createFromFormat("U.u",microtime(true)
//            '{$this->getDbFirstShow()} {$this->getDbStartTime()}'
         //   , new DateTimeZone($this->getDbTimezone())
        );

        // set timezone to that of the show
        // $dt->setTimezone(new DateTimeZone($this->getDbTimezone()));
    }

    /**
     * Returns the end of a show in the timezone it was created in.
     */
    public function getLocalEndDateAndTime()
    {
        $startDateTime = $this->getLocalStartDateAndTime();
        $duration = explode(':', $this->getDbDuration());

        return $startDateTime->add(new DateInterval('PT' . $duration[0] . 'H' . $duration[1] . 'M'));
    }

    public function isShowStartInPast()
    {
        return $this->getUTCStartDateAndTime()->format('Y-m-d H:i:s') < gmdate('Y-m-d H:i:s');
    }

    public function formatDuration()
    {
        $info = explode(':', $this->getDbDuration());

        return str_pad(intval($info[0]), 2, '0', STR_PAD_LEFT) . 'h ' . str_pad(intval($info[1]), 2, '0', STR_PAD_LEFT) . 'm';
    }
} // CcShowDays
