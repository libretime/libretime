<?php
/**
 * @author Sebastian Gobel <sebastian.goebel@web.de>
 * @author Paul Baranowski <paul@paulbaranowski.org>
 * @package Campcaster
 * @subpackage htmlUI
 * @version $Revision$
 * @copyright 2006 MDLF, Inc.
 * @license http://www.gnu.org/licenses/gpl.txt
 * @link http://www.campware.org
 */
class uiCalendar
{
    /**
     * An array of 10 array, one for each year, centered around the
     * current year.  Each year array consists of:
     *  ["year"] => int
     *  ["isSelected"] => boolean
     *
     * @var array
     */
    public $Decade;

    /**
     * An array of 12 arrays, each representing a month of the year.
     * Each array consists of:
     *  ["month"] => int : numeric representation of the month
     *  ["label"] => string : name of the month
     *  ["isSelected"] => boolean : TRUE if the month is selected
     *
     * @var array
     */
    public $Year;

    /**
     * An array of 30 arrays, one for each day of the month.
     *
     * @var array
     */
    public $Month;

    /**
     * An array of 7 arrays, one for each day of the week.
     *
     * @var array
     */
    public $Week;

    /**
     * An array of 24 arrays, one for each hour in the day.
     *
     * @var array
     */
    public $Day;

    /**
     * An array of 60 arrays, one for each minute in the hour.
     *
     * @var array
     */
    public $Hour;

    public function __construct() { }


    /**
     * Create the internal "Decade" array, an array of 10 arrays,
     * one for each year centered around the current year.
     *
     * @return void
     */
    function buildDecade()
    {
        // Return if already created.
        if (is_array($this->Decade)) {
        	return;
        }

        for ($Year = $this->curr['year'] - 5; $Year <= ($this->curr['year'] + 5); $Year++) {
            $this->Decade[] = array('year' => $Year,
                                    'isSelected' => ($Year==$this->curr['year']) ? TRUE : FALSE);
        }
    }


    function buildYear()
    {
        // Return if already created.
        if (is_array($this->Year)) {
        	return;
        }

        require_once('Calendar/Year.php');
        require_once('Calendar/Month.php');

        $Year = new Calendar_Year($this->curr['year']);
        // mark current month
        $sel = new Calendar_Month($this->curr['year'], $this->curr['month']);
        $selections = array($sel);

        $Year->build($selections, UI_SCHEDULER_FIRSTWEEKDAY);
        while ($Month = $Year->fetch()) {
            $this->Year[] = array('month' => sprintf('%02d', $Month->thisMonth()),
                                  'label' => uiCalendar::_getMonthName($Month),
                                  'isSelected' => $Month->isSelected());
        }
    }


    function buildMonth()
    {
        // Return if already created.
        if (is_array($this->Month)) {
        	return;
        }

        require_once('Calendar/Month/Weekdays.php');
        require_once('Calendar/Day.php');

        $Month = new Calendar_Month_Weekdays($this->curr['year'], $this->curr['month'], UI_SCHEDULER_FIRSTWEEKDAY);
        // scheduled days are selected
        $Month->build($this->_scheduledDays('month'));
        while ($Day = $Month->fetch()) {
        	// Next 2 lines are due to a bug in Calendar_Month_Weekdays
            $corrMonth = ($Day->thisMonth() <= 12) ? sprintf('%02d', $Day->thisMonth()) : '01';
            $corrYear = ($Day->thisMonth() <= 12) ? $Day->thisYear() : $Day->thisYear() + 1;
            $isCurrent = ($Day->thisDay() == $this->curr['day']
                          && $Day->thisMonth() == $this->curr['month']) ? TRUE : FALSE;
            $isToday = ($Day->thisDay() == strftime("%d")
                        && $Day->thisMonth()==strftime("%m")) ? TRUE : FALSE;
            $this->Month[] = array(
                                'day' => sprintf('%02d', $Day->thisDay()),
                                'week' => uiCalendar::_getWeekNr($Day),
                                'month' => $corrMonth,
                                'year' => $corrYear,
                                'label' => $this->_getDayName($Day),
                                'isEmpty' => $Day->isEmpty(),
                                'isFirst' => $Day->isFirst(),
                                'isLast' => $Day->isLast(),
                                'isSelected' => $Day->isSelected(),
                                'isCurrent' => $isCurrent,
                                'isToday' => $isToday,
                                'timestamp' => $Day->thisDay(TRUE));
        }
    }


    function buildWeek()
    {
        if (is_array($this->Week)) {
        	return;
        }

        require_once('Calendar/Week.php');

        $Week = new Calendar_Week($this->curr['year'], $this->curr['month'], $this->curr['day'], UI_SCHEDULER_FIRSTWEEKDAY);
        $Week->build($this->_scheduledDays('week'));
        while ($Day = $Week->fetch()) {
        	// Next 2 lines are due to a bug in Calendar_Month_Weekdays
            $corrMonth = ($Day->thisMonth() <= 12) ? sprintf('%02d', $Day->thisMonth()) : '01';
            $corrYear  = ($Day->thisMonth() <= 12) ? $Day->thisYear() : $Day->thisYear()+1;
            $isToday = ($Day->thisDay()==strftime("%d")
                        && $Day->thisMonth()==strftime("%m")) ? TRUE : FALSE;
            $this->Week[] = array(
                                'day' => sprintf('%02d', $Day->thisDay()),
                                'week' => uiCalendar::_getWeekNr($Day),
                                'month' => $corrMonth,
                                'year' => $corrYear,
                                'label' => $this->_getDayName($Day),
                                'isSelected' => $Day->isSelected(),
                                'isCurrent' => $Day->thisDay()==$this->curr['day'] ? TRUE : FALSE,
                                'isToday' => $isToday,
                                'timestamp' => $Day->thisDay(TRUE));
        }
    }


    function buildDay()
    {
        if (is_array($this->Day)) {
            return;
        }

        require_once('Calendar/Day.php');

        $Day = new Calendar_Day ($this->curr['year'], $this->curr['month'], $this->curr['day']);
        $Day->build();
        while ($Hour = $Day->fetch()) {
        	// Next two lines are due to a bug in Calendar_Month_Weekdays
            $corrMonth = ($Hour->thisMonth() <= 12) ? sprintf('%02d', $Hour->thisMonth()) : '01';
            $corrYear  = ($Hour->thisMonth() <= 12) ? $Day->thisYear() : $Hour->thisYear()+1;
            $this->Day[] = array(
                                'day' => sprintf('%02d', $Hour->thisDay()),
                                'week' => uiCalendar::_getWeekNr($Hour),
                                'month' => $corrMonth,
                                'year' => $corrYear,
                                'hour' => $Hour->thisHour(),
                                'isSelected' => $Hour->isSelected(),
                                'isCurrent' => $Hour->thisDay()==$this->curr['day'] ? TRUE : FALSE,
                                'timestamp' => $Hour->thisHour(TRUE));
        }
    }


    function buildHour()
    {
        if (is_array($this->Hour)) {
        	return;
        }

        require_once('Calendar/Hour.php');

        $Hour = new Calendar_Hour($this->curr['year'], $this->curr['month'], $this->curr['day'], $this->curr['hour']);
        $Hour->build();
        while ($Min = $Hour->fetch()) {
            $isCurrent = ($Min->thisDay() == $this->curr['hour']) ? TRUE : FALSE;
            $this->Hour[] = array(
                                'day' => sprintf('%02d', $Min->thisDay()),
                                'week' => uiCalendar::_getWeekNr($Min),
                                'month' => sprintf('%02d', $Min->thisMonth()),
                                'year' => $Min->thisYear(),
                                'hour' => $Min->thisHour(),
                                'minute' => $Min->thisMinute(),
                                'isSelected' => $Min->isSelected(),
                                'isCurrent' => $isCurrent);
        }
    }


    /**
     * Get the name of the month.
     *
     * @param Calendar_Month $date
     * @return array
     *      With keys:
     *      ['short'] => short name of the month
     *      ['full'] => complete name of the month
     */
    private static function _getMonthName(&$date) {
        $timestamp = mktime($date->thisHour(), $date->thisMinute(), $date->thisSecond(), $date->thisMonth(), $date->thisDay(), $date->thisYear());
        return array('short' => strftime("%b", $timestamp),
                     'full'  => strftime("%B", $timestamp));
    }


    /**
     * Get the week number (1 to 53)
     *
     * @param Calendar_Day|Calendar_Hour|Calendar_Minute $date
     * @return int
     */
    private static function _getWeekNr(&$date) {
        $timestamp = mktime($date->thisHour(), $date->thisMinute(), $date->thisSecond(), $date->thisMonth(), $date->thisDay(), $date->thisYear());
        return strftime("%V", $timestamp);
    }


    /**
     * Get the name of the day.
     *
     * @param Calendar_Day $date
     * @return array
     *      With keys:
     *      ['short'] => short version of day name
     *      ['full'] => day name
     */
    private static function _getDayName(&$date) {
        $timestamp = mktime($date->thisHour(), $date->thisMinute(), $date->thisSecond(), $date->thisMonth(), $date->thisDay(), $date->thisYear());
        return array('short' => strftime("%a", $timestamp),
                     'full'  => strftime("%A", $timestamp));
    }
} // class uiCalendar
?>