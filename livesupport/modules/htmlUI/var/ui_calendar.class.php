<?php
class uiCalendar
{

    function uiCalendar()
    {

    }

    function buildDecade()
    {
        if (is_array($this->Decade)) return TRUE;

        for ($Year = $this->curr['year']-5; $Year<=$this->curr['year']+5; $Year++) {
            $this->Decade[] = array(
                                'year'          => $Year,
                                'isSelected'    => $Year==$this->curr['year'] ? TRUE : FALSE
                              );
        }

    }


    function buildYear()
    {
        require_once 'Calendar/Year.php';
        require_once 'Calendar/Month.php';

        $Year = new Calendar_Year($this->curr['year']);
        # mark current month
        $sel =   new Calendar_Month($this->curr['year'], $this->curr['month']);
        $selections = array($sel);

        $Year->build($selections, UI_SCHEDULER_FIRSTWEEKDAY);
        while ($Month = $Year->fetch()) {
            $this->Year[] = array(
                                'month'         => sprintf('%02d', $Month->thisMonth()),
                                'label'         => $this->_getMonthName($Month),
                                'isSelected'    => $Month->isSelected()
                            );
        }
    }


    function buildMonth()
    {
        if (is_array($this->Month)) return TRUE;

        require_once 'Calendar/Month/Weekdays.php';
        require_once 'Calendar/Day.php';

        $Month = new Calendar_Month_Weekdays($this->curr['year'], $this->curr['month'], UI_SCHEDULER_FIRSTWEEKDAY);
        $Month->build($this->_scheduledDays('month'));                                                       ## scheduled days are selected
        while ($Day = $Month->fetch()) {
            $corrMonth = $Day->thisMonth()<=12 ? sprintf('%02d', $Day->thisMonth()) : '01';           ## due to bug in
            $corrYear  = $Day->thisMonth()<=12 ? $Day->thisYear() : $Day->thisYear()+1;               ## Calendar_Month_Weekdays
            $this->Month[] = array(
                                'day'           => sprintf('%02d', $Day->thisDay()),
                                'week'          => $this->_getWeekNr($Day),
                                'month'         => $corrMonth,
                                'year'          => $corrYear,
                                'label'         => $this->_getDayName($Day),
                                'isEmpty'       => $Day->isEmpty(),
                                'isFirst'       => $Day->isFirst(),
                                'isLast'        => $Day->isLast(),
                                'isSelected'    => $Day->isSelected(),
                                'isCurrent'     => $Day->thisDay()==$this->curr['day'] && $Day->thisMonth()==$this->curr['month'] ? TRUE : FALSE,
                                'isToday'       => $Day->thisDay()==strftime("%d") && $Day->thisMonth()==strftime("%m") ? TRUE : FALSE,
                                'timestamp'     => $Day->thisDay(TRUE)
                             );
        }
    }


    function buildWeek()
    {
        if (is_array($this->Week)) return FALSE;

        require_once 'Calendar/Week.php';

        $Week = new Calendar_Week ($this->curr['year'], $this->curr['month'], $this->curr['day'], UI_SCHEDULER_FIRSTWEEKDAY);
        $Week->build($this->_scheduledDays('week'));
        while ($Day = $Week->fetch()) {
            $corrMonth = $Day->thisMonth()<=12 ? sprintf('%02d', $Day->thisMonth()) : '01';           ## due to bug in
            $corrYear  = $Day->thisMonth()<=12 ? $Day->thisYear() : $Day->thisYear()+1;               ## Calendar_Month_Weekdays
            $this->Week[] = array(
                                'day'           => sprintf('%02d', $Day->thisDay()),
                                'week'          => $this->_getWeekNr($Day),
                                'month'         => $corrMonth,
                                'year'          => $corrYear,
                                'label'         => $this->_getDayName($Day),
                                'isSelected'    => $Day->isSelected(),
                                'isCurrent'     => $Day->thisDay()==$this->curr['day'] ? TRUE : FALSE,
                                'isToday'       => $Day->thisDay()==strftime("%d") && $Day->thisMonth()==strftime("%m") ? TRUE : FALSE,
                                'timestamp'     => $Day->thisDay(TRUE)
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
            $corrMonth = $Hour->thisMonth()<=12 ? sprintf('%02d', $Hour->thisMonth()) : '01';           ## due to bug in
            $corrYear  = $Hour->thisMonth()<=12 ? $Day->thisYear() : $Hour->thisYear()+1;               ## Calendar_Month_Weekdays
            $this->Day[] = array(
                                'day'          => sprintf('%02d', $Hour->thisDay()),
                                'week'         => $this->_getWeekNr($Hour),
                                'month'        => $corrMonth,
                                'year'         => $corrYear,
                                'hour'         => $Hour->thisHour(),
                                'isSelected'   => $Hour->isSelected(),
                                'isCurrent'    => $Hour->thisDay()==$this->curr['day'] ? TRUE : FALSE,
                                'timestamp'    => $Hour->thisHour(TRUE)
                           );
        }

    }


    function buildHour()
    {
        if (is_array($this->Hour)) return FALSE;

        require_once 'Calendar/Hour.php';

        $Hour = new Calendar_Hour ($this->curr['year'], $this->curr['month'], $this->curr['day'], $this->curr['hour']);
        $Hour->build();
        while ($Min = $Hour->fetch()) {
            $this->Hour[] = array(
                                'day'          => sprintf('%02d', $Min->thisDay()),
                                'week'         => $this->_getWeekNr($Min),
                                'month'        => sprintf('%02d', $Min->thisMonth()),
                                'year'         => $Min->thisYear(),
                                'hour'         => $Min->thisHour(),
                                'minute'       => $Min->thisMinute(),
                                'isSelected'   => $Min->isSelected(),
                                'isCurrent'    => $Min->thisDay()==$this->curr['hour'] ? TRUE : FALSE
                            );
        }
    }


    ## some data which PEAR::Calendar does not provide ##########################################################################################
    function _getMonthName(&$date) {
        $timestamp = mktime($date->thisHour(), $date->thisMinute(), $date->thisSecond(), $date->thisMonth(), $date->thisDay(), $date->thisYear());
        #echo $date->thisHour().$date->thisMinute().$date->thisSecond().$date->thisYear().$date->thisMonth().$date->thisDay().$timestamp."<br>";
        return array('short' => strftime("%b", $timestamp),
                     'full'  => strftime("%B", $timestamp));
    }


    function _getWeekNr(&$date) {
        $timestamp = mktime($date->thisHour(), $date->thisMinute(), $date->thisSecond(), $date->thisMonth(), $date->thisDay(), $date->thisYear());
        #echo $date->thisHour().$date->thisMinute().$date->thisSecond().$date->thisYear().$date->thisMonth().$date->thisDay().$timestamp."<br>";
        return strftime("%V", $timestamp);
    }


    function _getDayName(&$date) {
        $timestamp = mktime($date->thisHour(), $date->thisMinute(), $date->thisSecond(), $date->thisMonth(), $date->thisDay(), $date->thisYear());
        #echo $date->thisHour().$date->thisMinute().$date->thisSecond().$date->thisYear().$date->thisMonth().$date->thisDay().$timestamp."<br>";
        return array('short' => strftime("%a", $timestamp),
                     'full'  => strftime("%A", $timestamp));
    }
}
?>
