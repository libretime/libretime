<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */
//
// +----------------------------------------------------------------------+
// | PHP Version 4                                                        |
// +----------------------------------------------------------------------+
// | Copyright (c) 1997-2002 The PHP Group                                |
// +----------------------------------------------------------------------+
// | This source file is subject to version 2.02 of the PHP license,      |
// | that is bundled with this package in the file LICENSE, and is        |
// | available at through the world-wide-web at                           |
// | http://www.php.net/license/3_0.txt.                                  |
// | If you did not receive a copy of the PHP license and are unable to   |
// | obtain it through the world-wide-web, please send a note to          |
// | license@php.net so we can mail you a copy immediately.               |
// +----------------------------------------------------------------------+
// | Authors: Harry Fuecks <hfuecks@phppatterns.com>                      |
// +----------------------------------------------------------------------+
//
// $Id: Helper.php,v 1.5 2005/10/22 09:51:53 quipo Exp $
//
/**
 * @package Calendar
 * @version $Id: Helper.php,v 1.5 2005/10/22 09:51:53 quipo Exp $
 */

/**
 * Used by Calendar_Month_Weekdays, Calendar_Month_Weeks and Calendar_Week to
 * help with building the calendar in tabular form
 * @package Calendar
 * @access protected
 */
class Calendar_Table_Helper
{
    /**
     * Instance of the Calendar object being helped.
     * @var object
     * @access private
     */
    var $calendar;

    /**
     * Instance of the Calendar_Engine
     * @var object
     * @access private
     */
    var $cE;

    /**
     * First day of the week
     * @access private
     * @var string
     */
    var $firstDay;

    /**
     * The seven days of the week named
     * @access private
     * @var array
     */
    var $weekDays;

    /**
     * Days of the week ordered with $firstDay at the beginning
     * @access private
     * @var array
     */
    var $daysOfWeek = array();

    /**
     * Days of the month built from days of the week
     * @access private
     * @var array
     */
    var $daysOfMonth = array();

    /**
     * Number of weeks in month
     * @var int
     * @access private
     */
    var $numWeeks = null;

    /**
     * Number of emtpy days before real days begin in month
     * @var int
     * @access private
     */
    var $emptyBefore = 0;

    /**
     * Constructs Calendar_Table_Helper
     * @param object Calendar_Month_Weekdays, Calendar_Month_Weeks, Calendar_Week
     * @param int (optional) first day of the week e.g. 1 for Monday
     * @access protected
     */
    function Calendar_Table_Helper(& $calendar, $firstDay=null)
    {
        $this->calendar = & $calendar;
        $this->cE = & $calendar->getEngine();
        if (is_null($firstDay)) {
            $firstDay = $this->cE->getFirstDayOfWeek(
                $this->calendar->thisYear(),
                $this->calendar->thisMonth(),
                $this->calendar->thisDay()
            );
        }
        $this->firstDay = $firstDay;
        $this->setFirstDay();
        $this->setDaysOfMonth();
    }

    /**
     * Constructs $this->daysOfWeek based on $this->firstDay
     * @return void
     * @access private
     */
    function setFirstDay()
    {
        $weekDays = $this->cE->getWeekDays(
            $this->calendar->thisYear(),
            $this->calendar->thisMonth(),
            $this->calendar->thisDay()
        );
        $endDays  = array();
        $tmpDays  = array();
        $begin = false;
        foreach ($weekDays as $day) {
            if ($begin) {
                $endDays[] = $day;
            } else if ($day === $this->firstDay) {
                $begin = true;
                $endDays[] = $day;
            } else {
                $tmpDays[] = $day;
            }
        }
        $this->daysOfWeek = array_merge($endDays, $tmpDays);
    }

    /**
     * Constructs $this->daysOfMonth
     * @return void
     * @access private
     */
    function setDaysOfMonth()
    {
        $this->daysOfMonth = $this->daysOfWeek;
        $daysInMonth = $this->cE->getDaysInMonth(
            $this->calendar->thisYear(), $this->calendar->thisMonth());
        $firstDayInMonth = $this->cE->getFirstDayInMonth(
            $this->calendar->thisYear(), $this->calendar->thisMonth());
        $this->emptyBefore=0;
        foreach ($this->daysOfMonth as $dayOfWeek) {
            if ($firstDayInMonth == $dayOfWeek) {
                break;
            }
            $this->emptyBefore++;
        }
        $this->numWeeks = ceil(
            ($daysInMonth + $this->emptyBefore)
                /
            $this->cE->getDaysInWeek(
                $this->calendar->thisYear(),
                $this->calendar->thisMonth(),
                $this->calendar->thisDay()
            )
        );
        for ($i=1; $i < $this->numWeeks; $i++) {
            $this->daysOfMonth =
                array_merge($this->daysOfMonth, $this->daysOfWeek);
        }
    }

    /**
     * Returns the first day of the month
     * @see Calendar_Engine_Interface::getFirstDayOfWeek()
     * @return int
     * @access protected
     */
    function getFirstDay()
    {
        return $this->firstDay;
    }

    /**
     * Returns the order array of days in a week
     * @return int
     * @access protected
     */
    function getDaysOfWeek()
    {
        return $this->daysOfWeek;
    }

    /**
     * Returns the number of tabular weeks in a month
     * @return int
     * @access protected
     */
    function getNumWeeks()
    {
        return $this->numWeeks;
    }

    /**
     * Returns the number of real days + empty days
     * @return int
     * @access protected
     */
    function getNumTableDaysInMonth()
    {
        return count($this->daysOfMonth);
    }

    /**
     * Returns the number of empty days before the real days begin
     * @return int
     * @access protected
     */
    function getEmptyDaysBefore()
    {
        return $this->emptyBefore;
    }

    /**
     * Returns the index of the last real day in the month
     * @todo Potential performance optimization with static
     * @return int
     * @access protected
     */
    function getEmptyDaysAfter()
    {
        // Causes bug when displaying more than one month
//        static $index;
//        if (!isset($index)) {
            $index = $this->getEmptyDaysBefore() + $this->cE->getDaysInMonth(
                $this->calendar->thisYear(), $this->calendar->thisMonth());
//        }
        return $index;
    }

    /**
     * Returns the index of the last real day in the month, relative to the
     * beginning of the tabular week it is part of
     * @return int
     * @access protected
     */
    function getEmptyDaysAfterOffset()
    {
        $eAfter = $this->getEmptyDaysAfter();
        return $eAfter - (
            $this->cE->getDaysInWeek(
                $this->calendar->thisYear(),
                $this->calendar->thisMonth(),
                $this->calendar->thisDay()
            ) * ($this->numWeeks-1) );
    }

    /**
     * Returns the timestamp of the first day of the current week
     */
    function getWeekStart($y, $m, $d, $firstDay=1)
    {
        $dow = $this->cE->getDayOfWeek($y, $m, $d);
        if ($dow > $firstDay) {
            $d -= ($dow - $firstDay);
        }
        if ($dow < $firstDay) {
            $d -= (
                $this->cE->getDaysInWeek(
                    $this->calendar->thisYear(),
                    $this->calendar->thisMonth(),
                    $this->calendar->thisDay()
                ) - $firstDay + $dow);
        }
        return $this->cE->dateToStamp($y, $m, $d);
    }
}
?>