<?php
class uiCalendar
{

    function uiCalendar()
    {
        $this->firstDayOfWeek = 1;
    }


    function buildMonth()
    {
        if (is_array($this->Month)) return FALSE;

        require_once 'Calendar/Calendar.php';
        require_once 'Calendar/Month/Weekdays.php';
        require_once 'Calendar/Day.php';

        $Month = new Calendar_Month_Weekdays($this->curr['year'], $this->curr['month'], $this->firstDayOfWeek);
        # mark today #
        $sel =   new Calendar_Day($this->curr['year'], $this->curr['month'], $this->curr['day']);
        $selection = array($sel);

        $Month->build($selection);
        while ($Day = $Month->fetch()) {
            $this->Month[] = array(
                                'day'           => $this->Base->_twoDigits($Day->thisDay()),
                                'week'          => $this->_getWeekNr($Day),
                                'month'         => $this->Base->_twoDigits($Day->thisMonth()),
                                'year'          => $Day->thisYear(),
                                'isEmpty'       => $Day->isEmpty(),
                                'isFirst'       => $Day->isFirst(),
                                'isLast'        => $Day->isLast(),
                                'isSelected'    => $Day->isSelected()
                             );
        }
    }


    function buildWeek()
    {
        if (is_array($this->Week)) return FALSE;

        require_once 'Calendar/Week.php';

        $Week = new Calendar_Week ($this->curr['year'], $this->curr['month'], $this->curr['day'], $this->firstDayOfWeek);
        $Week->build();
        while ($Day = $Week->fetch()) {
            $this->Week[] = array(
                                'day'           => $this->Base->_twoDigits($Day->thisDay()),
                                'label'         => $this->_getDayName($Day),
                            );
        }

    }


    function buildDay()
    {
        if (is_array($this->Day)) return FALSE;

        require_once 'Calendar/Day.php';

        $Day = new Calendar_Day ($this->curr['year'], $this->curr['month'], $this->curr['day']);
        $Day->build();
        while ($Hour = $Day->fetch()) {
            $this->Day[] = array('hour'         => $Hour->thisHour());
        }

    }


    function buildHour()
    {
        require_once 'Calendar/Hour.php';

        $Hour = new Calendar_Hour ($this->curr['year'], $this->curr['month'], $this->curr['day'], $this->curr['hour']);
        $Hour->build();
        while ($Min = $Hour->fetch()) {
            $this->Hour[] = $Min->thisMinute();
        }
    }

    function _getWeekNr(&$date) {
        $timestamp = mktime($date->thisHour(), $date->thisMinute(), $date->thisSecond(), $date->thisMonth(), $date->thisDay(), $date->thisYear());
        #echo $date->thisHour().$date->thisMinute().$date->thisSecond().$date->thisYear().$date->thisMonth().$date->thisDay().$timestamp."<br>";
        return date("W", $timestamp);
    }


    function _getDayName(&$date) {
        $timestamp = mktime($date->thisHour(), $date->thisMinute(), $date->thisSecond(), $date->thisMonth(), $date->thisDay(), $date->thisYear());
        #echo $date->thisHour().$date->thisMinute().$date->thisSecond().$date->thisYear().$date->thisMonth().$date->thisDay().$timestamp."<br>";
        return array('short' => strftime("%a", $timestamp),
                     'full'  => strftime("%A", $timestamp));
    }
}
?>
