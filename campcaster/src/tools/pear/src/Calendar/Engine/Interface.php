<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */

/**
 * Contains the Calendar_Engine_Interface class (interface)
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
 * @author    Lorenzo Alberton <l.alberton@quipo.it>
 * @copyright 2003-2007 Harry Fuecks, Lorenzo Alberton
 * @license   http://www.debian.org/misc/bsd.license  BSD License (3 Clause)
 * @version   CVS: $Id: Interface.php,v 1.10 2008/11/15 21:21:42 quipo Exp $
 * @link      http://pear.php.net/package/Calendar
 */

/**
 * The methods the classes implementing the Calendar_Engine must implement.
 * Note this class is not used but simply to help development
 *
 * @category  Date and Time
 * @package   Calendar
 * @author    Harry Fuecks <hfuecks@phppatterns.com>
 * @author    Lorenzo Alberton <l.alberton@quipo.it>
 * @copyright 2003-2007 Harry Fuecks, Lorenzo Alberton
 * @license   http://www.debian.org/misc/bsd.license  BSD License (3 Clause)
 * @link      http://pear.php.net/package/Calendar
 * @access protected
 */
class Calendar_Engine_Interface
{
    /**
     * Provides a mechansim to make sure parsing of timestamps
     * into human dates is only performed once per timestamp.
     * Typically called "internally" by methods like stampToYear.
     * Return value can vary, depending on the specific implementation
     *
     * @param int $stamp timestamp (depending on implementation)
     *
     * @return mixed
     * @access protected
     */
    function stampCollection($stamp)
    {
    }

    /**
     * Returns a numeric year given a timestamp
     *
     * @param int $stamp timestamp (depending on implementation)
     *
     * @return int year (e.g. 2003)
     * @access protected
     */
    function stampToYear($stamp)
    {
    }

    /**
     * Returns a numeric month given a timestamp
     *
     * @param int $stamp timestamp (depending on implementation)
     *
     * @return int month (e.g. 9)
     * @access protected
     */
    function stampToMonth($stamp)
    {
    }

    /**
     * Returns a numeric day given a timestamp
     *
     * @param int $stamp timestamp (depending on implementation)
     *
     * @return int day (e.g. 15)
     * @access protected
     */
    function stampToDay($stamp)
    {
    }

    /**
     * Returns a numeric hour given a timestamp
     *
     * @param int $stamp timestamp (depending on implementation)
     *
     * @return int hour (e.g. 13)
     * @access protected
     */
    function stampToHour($stamp)
    {
    }

    /**
     * Returns a numeric minute given a timestamp
     *
     * @param int $stamp timestamp (depending on implementation)
     *
     * @return int minute (e.g. 34)
     * @access protected
     */
    function stampToMinute($stamp)
    {
    }

    /**
     * Returns a numeric second given a timestamp
     *
     * @param int $stamp timestamp (depending on implementation)
     *
     * @return int second (e.g. 51)
     * @access protected
     */
    function stampToSecond($stamp)
    {
    }

    /**
     * Returns a timestamp. Can be worth "caching" generated timestamps in a
     * static variable, identified by the params this method accepts,
     * to timestamp will only be calculated once.
     *
     * @param int $y year (e.g. 2003)
     * @param int $m month (e.g. 9)
     * @param int $d day (e.g. 13)
     * @param int $h hour (e.g. 13)
     * @param int $i minute (e.g. 34)
     * @param int $s second (e.g. 53)
     *
     * @return int (depends on implementation)
     * @access protected
     */
    function dateToStamp($y, $m, $d, $h, $i, $s)
    {
    }

    /**
     * The upper limit on years that the Calendar Engine can work with
     *
     * @return int (e.g. 2037)
     * @access protected
     */
    function getMaxYears()
    {
    }

    /**
     * The lower limit on years that the Calendar Engine can work with
     *
     * @return int (e.g 1902)
     * @access protected
     */
    function getMinYears()
    {
    }

    /**
     * Returns the number of months in a year
     *
     * @param int $y (optional) year to get months for
     *
     * @return int (e.g. 12)
     * @access protected
     */
    function getMonthsInYear($y=null)
    {
    }

    /**
     * Returns the number of days in a month, given year and month
     *
     * @param int $y year (e.g. 2003)
     * @param int $m month (e.g. 9)
     *
     * @return int days in month
     * @access protected
     */
    function getDaysInMonth($y, $m)
    {
    }

    /**
     * Returns numeric representation of the day of the week in a month,
     * given year and month
     *
     * @param int $y year (e.g. 2003)
     * @param int $m month (e.g. 9)
     *
     * @return int
     * @access protected
     */
    function getFirstDayInMonth ($y, $m)
    {
    }

    /**
     * Returns the number of days in a week
     *
     * @param int $y year (2003)
     * @param int $m month (9)
     * @param int $d day (4)
     *
     * @return int (e.g. 7)
     * @access protected
     */
    function getDaysInWeek($y=null, $m=null, $d=null)
    {
    }

    /**
     * Returns the number of the week in the year (ISO-8601), given a date
     *
     * @param int $y year (2003)
     * @param int $m month (9)
     * @param int $d day (4)
     *
     * @return int week number
     * @access protected
     */
    function getWeekNInYear($y, $m, $d)
    {
    }

    /**
     * Returns the number of the week in the month, given a date
     *
     * @param int $y        year (2003)
     * @param int $m        month (9)
     * @param int $d        day (4)
     * @param int $firstDay first day of the week (default: 1 - monday)
     *
     * @return int week number
     * @access protected
     */
    function getWeekNInMonth($y, $m, $d, $firstDay=1)
    {
    }

    /**
     * Returns the number of weeks in the month
     *
     * @param int $y year (2003)
     * @param int $m month (9)
     *
     * @return int weeks number
     * @access protected
     */
    function getWeeksInMonth($y, $m)
    {
    }

    /**
     * Returns the number of the day of the week (0=sunday, 1=monday...)
     *
     * @param int $y year (2003)
     * @param int $m month (9)
     * @param int $d day (4)
     *
     * @return int weekday number
     * @access protected
     */
    function getDayOfWeek($y, $m, $d)
    {
    }

    /**
     * Returns the numeric values of the days of the week.
     *
     * @param int $y year (2003)
     * @param int $m month (9)
     * @param int $d day (4)
     *
     * @return array list of numeric values of days in week, beginning 0
     * @access protected
     */
    function getWeekDays($y=null, $m=null, $d=null)
    {
    }

    /**
     * Returns the default first day of the week as an integer. Must be a
     * member of the array returned from getWeekDays
     *
     * @param int $y year (2003)
     * @param int $m month (9)
     * @param int $d day (4)
     *
     * @return int (e.g. 1 for Monday)
     * @see getWeekDays
     * @access protected
     */
    function getFirstDayOfWeek($y=null, $m=null, $d=null)
    {
    }

    /**
     * Returns the number of hours in a day
     *
     * @param int $y year (2003)
     * @param int $m month (9)
     * @param int $d day (4)
     *
     * @return int (e.g. 24)
     * @access protected
     */
    function getHoursInDay($y=null,$m=null,$d=null)
    {
    }

    /**
     * Returns the number of minutes in an hour
     *
     * @param int $y year (2003)
     * @param int $m month (9)
     * @param int $d day (4)
     * @param int $h hour
     *
     * @return int
     * @access protected
     */
    function getMinutesInHour($y=null,$m=null,$d=null,$h=null)
    {
    }

    /**
     * Returns the number of seconds in a minutes
     *
     * @param int $y year (2003)
     * @param int $m month (9)
     * @param int $d day (4)
     * @param int $h hour
     * @param int $i minute
     *
     * @return int
     * @access protected
     */
    function getSecondsInMinute($y=null,$m=null,$d=null,$h=null,$i=null)
    {
    }

    /**
     * Checks if the given day is the current day
     *
     * @param int timestamp (depending on implementation)
     *
     * @return boolean
     * @access protected
     */
    function isToday($stamp)
    {
    }
}
?>