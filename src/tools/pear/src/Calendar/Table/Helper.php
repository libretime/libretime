<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */

/**
 * Contains the Calendar_Decorator_Wrapper class
 *
 * PHP versions 4 and 5
 *
 * LICENSE: Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 * 1. Redistributions of source code must retain the above copyright
 *    notice, this list of conditions and the following disclaimer.
 * 2. Redistributions in binary form must reproduce the above copyright
 *    notice, this list of conditions and the following disclaimer in the
 *    documentation and/or other materials provided with the distribution.
 * 3. The name of the author may not be used to endorse or promote products
 *    derived from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE AUTHOR "AS IS" AND ANY EXPRESS OR IMPLIED
 * WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF
 * MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED.
 * IN NO EVENT SHALL THE FREEBSD PROJECT OR CONTRIBUTORS BE LIABLE FOR ANY
 * DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
 * ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF
 * THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * @category  Date and Time
 * @package   Calendar
 * @author    Harry Fuecks <hfuecks@phppatterns.com>
 * @copyright 2003-2007 Harry Fuecks
 * @license   http://www.debian.org/misc/bsd.license  BSD License (3 Clause)
 * @version   CVS: $Id: Helper.php,v 1.9 2007/11/16 20:05:05 quipo Exp $
 * @link      http://pear.php.net/package/Calendar
 */

/**
 * Used by Calendar_Month_Weekdays, Calendar_Month_Weeks and Calendar_Week to
 * help with building the calendar in tabular form
 *
 * @category  Date and Time
 * @package   Calendar
 * @author    Harry Fuecks <hfuecks@phppatterns.com>
 * @copyright 2003-2007 Harry Fuecks
 * @license   http://www.debian.org/misc/bsd.license  BSD License (3 Clause)
 * @link      http://pear.php.net/package/Calendar
 * @access public
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
     *
     * @param object &$calendar Calendar_Month_Weekdays, Calendar_Month_Weeks, Calendar_Week
     * @param int    $firstDay  (optional) first day of the week e.g. 1 for Monday
     *
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
     *
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
     *
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
     *
     * @return int
     * @access protected
     * @see Calendar_Engine_Interface::getFirstDayOfWeek()
     */
    function getFirstDay()
    {
        return $this->firstDay;
    }

    /**
     * Returns the order array of days in a week
     *
     * @return int
     * @access protected
     */
    function getDaysOfWeek()
    {
        return $this->daysOfWeek;
    }

    /**
     * Returns the number of tabular weeks in a month
     *
     * @return int
     * @access protected
     */
    function getNumWeeks()
    {
        return $this->numWeeks;
    }

    /**
     * Returns the number of real days + empty days
     *
     * @return int
     * @access protected
     */
    function getNumTableDaysInMonth()
    {
        return count($this->daysOfMonth);
    }

    /**
     * Returns the number of empty days before the real days begin
     *
     * @return int
     * @access protected
     */
    function getEmptyDaysBefore()
    {
        return $this->emptyBefore;
    }

    /**
     * Returns the index of the last real day in the month
     *
     * @todo Potential performance optimization with static
     * @return int
     * @access protected
     */
    function getEmptyDaysAfter()
    {
        // Causes bug when displaying more than one month
        //static $index;
        //if (!isset($index)) {
            $index = $this->getEmptyDaysBefore() + $this->cE->getDaysInMonth(
                $this->calendar->thisYear(), $this->calendar->thisMonth());
        //}
        return $index;
    }

    /**
     * Returns the index of the last real day in the month, relative to the
     * beginning of the tabular week it is part of
     *
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
            ) * ($this->numWeeks-1));
    }

    /**
     * Returns the timestamp of the first day of the current week
     *
     * @param int $y        year
     * @param int $m        month
     * @param int $d        day
     * @param int $firstDay first day of the week (default 1 = Monday)
     *
     * @return int timestamp
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