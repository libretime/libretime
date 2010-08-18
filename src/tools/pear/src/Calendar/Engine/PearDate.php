<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */

/**
 * Contains the Calendar_Engine_PearDate class
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
 * @author    Lorenzo Alberton <l.alberton@quipo.it>
 * @copyright 2003-2007 Lorenzo Alberton
 * @license   http://www.debian.org/misc/bsd.license  BSD License (3 Clause)
 * @version   CVS: $Id: PearDate.php 269076 2008-11-15 21:41:38Z quipo $
 * @link      http://pear.php.net/package/Calendar
 */

/**
 * Load PEAR::Date class
 */
require_once 'Date.php';

/**
 * Performs calendar calculations based on the PEAR::Date class
 * Timestamps are in the ISO-8601 format (YYYY-MM-DD HH:MM:SS)
 *
 * @category  Date and Time
 * @package   Calendar
 * @author    Lorenzo Alberton <l.alberton@quipo.it>
 * @copyright 2003-2007 Lorenzo Alberton
 * @license   http://www.debian.org/misc/bsd.license  BSD License (3 Clause)
 * @link      http://pear.php.net/package/Calendar
 * @access protected
 */
class Calendar_Engine_PearDate /* implements Calendar_Engine_Interface */
{
    /**
     * Makes sure a given timestamp is only ever parsed once
     * Uses a static variable to prevent date() being used twice
     * for a date which is already known
     *
     * @param mixed $stamp Any timestamp format recognized by Pear::Date
     *
     * @return object Pear::Date object
     * @access protected
     */
    function stampCollection($stamp)
    {
        static $stamps = array();
        if (!isset($stamps[$stamp])) {
            $stamps[$stamp] = new Date($stamp);
        }
        return $stamps[$stamp];
    }

    /**
     * Returns a numeric year given a iso-8601 datetime
     *
     * @param string $stamp iso-8601 datetime (YYYY-MM-DD HH:MM:SS)
     *
     * @return int year (e.g. 2003)
     * @access protected
     */
    function stampToYear($stamp)
    {
        $date = Calendar_Engine_PearDate::stampCollection($stamp);
        return (int)$date->year;
    }

    /**
     * Returns a numeric month given a iso-8601 datetime
     *
     * @param string $stamp iso-8601 datetime (YYYY-MM-DD HH:MM:SS)
     *
     * @return int month (e.g. 9)
     * @access protected
     */
    function stampToMonth($stamp)
    {
        $date = Calendar_Engine_PearDate::stampCollection($stamp);
        return (int)$date->month;
    }

    /**
     * Returns a numeric day given a iso-8601 datetime
     *
     * @param string $stamp iso-8601 datetime (YYYY-MM-DD HH:MM:SS)
     *
     * @return int day (e.g. 15)
     * @access protected
     */
    function stampToDay($stamp)
    {
        $date = Calendar_Engine_PearDate::stampCollection($stamp);
        return (int)$date->day;
    }

    /**
     * Returns a numeric hour given a iso-8601 datetime
     *
     * @param string $stamp iso-8601 datetime (YYYY-MM-DD HH:MM:SS)
     *
     * @return int hour (e.g. 13)
     * @access protected
     */
    function stampToHour($stamp)
    {
        $date = Calendar_Engine_PearDate::stampCollection($stamp);
        return (int)$date->hour;
    }

    /**
     * Returns a numeric minute given a iso-8601 datetime
     *
     * @param string $stamp iso-8601 datetime (YYYY-MM-DD HH:MM:SS)
     *
     * @return int minute (e.g. 34)
     * @access protected
     */
    function stampToMinute($stamp)
    {
        $date = Calendar_Engine_PearDate::stampCollection($stamp);
        return (int)$date->minute;
    }

    /**
     * Returns a numeric second given a iso-8601 datetime
     *
     * @param string $stamp iso-8601 datetime (YYYY-MM-DD HH:MM:SS)
     *
     * @return int second (e.g. 51)
     * @access protected
     */
    function stampToSecond($stamp)
    {
        $date = Calendar_Engine_PearDate::stampCollection($stamp);
        return (int)$date->second;
    }

    /**
     * Returns a iso-8601 datetime
     *
     * @param int $y year (2003)
     * @param int $m month (9)
     * @param int $d day (13)
     * @param int $h hour (13)
     * @param int $i minute (34)
     * @param int $s second (53)
     *
     * @return string iso-8601 datetime
     * @access protected
     */
    function dateToStamp($y, $m, $d, $h=0, $i=0, $s=0)
    {
        $r = array();
        Calendar_Engine_PearDate::adjustDate($y, $m, $d, $h, $i, $s);
        $key = $y.$m.$d.$h.$i.$s;
        if (!isset($r[$key])) {
            $r[$key] = sprintf("%04d-%02d-%02d %02d:%02d:%02d",
                                $y, $m, $d, $h, $i, $s);
        }
        return $r[$key];
    }

    /**
     * Set the correct date values (useful for math operations on dates)
     *
     * @param int &$y year   (2003)
     * @param int &$m month  (9)
     * @param int &$d day    (13)
     * @param int &$h hour   (13)
     * @param int &$i minute (34)
     * @param int &$s second (53)
     *
     * @return void
     * @access protected
     */
    function adjustDate(&$y, &$m, &$d, &$h, &$i, &$s)
    {
        if ($s < 0) {
            $m -= floor($s / 60);
            $s = -$s % 60;
        }
        if ($s > 60) {
            $m += floor($s / 60);
            $s %= 60;
        }
        if ($i < 0) {
            $h -= floor($i / 60);
            $i = -$i % 60;
        }
        if ($i > 60) {
            $h += floor($i / 60);
            $i %= 60;
        }
        if ($h < 0) {
            $d -= floor($h / 24);
            $h = -$h % 24;
        }
        if ($h > 24) {
            $d += floor($h / 24);
            $h %= 24;
        }
        for(; $m < 1; $y--, $m+=12);
        for(; $m > 12; $y++, $m-=12);

        while ($d < 1) {
            if ($m > 1) {
                $m--;
            } else {
                $m = 12;
                $y--;
            }
            $d += Date_Calc::daysInMonth($m, $y);
        }
        for ($max_days = Date_Calc::daysInMonth($m, $y); $d > $max_days; ) {
            $d -= $max_days;
            if ($m < 12) {
                $m++;
            } else {
                $m = 1;
                $y++;
            }
        }
    }

    /**
     * The upper limit on years that the Calendar Engine can work with
     *
     * @return int 9999
     * @access protected
     */
    function getMaxYears()
    {
        return 9999;
    }

    /**
     * The lower limit on years that the Calendar Engine can work with
     *
     * @return int 0
     * @access protected
     */
    function getMinYears()
    {
        return 0;
    }

    /**
     * Returns the number of months in a year
     *
     * @param int $y year
     *
     * @return int (12)
     * @access protected
     */
    function getMonthsInYear($y=null)
    {
        return 12;
    }

    /**
     * Returns the number of days in a month, given year and month
     *
     * @param int $y year (2003)
     * @param int $m month (9)
     *
     * @return int days in month
     * @access protected
     */
    function getDaysInMonth($y, $m)
    {
        return (int)Date_Calc::daysInMonth($m, $y);
    }

    /**
     * Returns numeric representation of the day of the week in a month,
     * given year and month
     *
     * @param int $y year (2003)
     * @param int $m month (9)
     *
     * @return int from 0 to 7
     * @access protected
     */
    function getFirstDayInMonth($y, $m)
    {
        return (int)Date_Calc::dayOfWeek(1, $m, $y);
    }

    /**
     * Returns the number of days in a week
     *
     * @param int $y year (2003)
     * @param int $m month (9)
     * @param int $d day (4)
     *
     * @return int (7)
     * @access protected
     */
    function getDaysInWeek($y=null, $m=null, $d=null)
    {
        return 7;
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
        //return Date_Calc::weekOfYear($d, $m, $y); //beware, Date_Calc doesn't follow ISO-8601 standard!
        list($nYear, $nWeek) = Date_Calc::weekOfYear4th($d, $m, $y);
        return $nWeek;
    }

    /**
     * Returns the number of the week in the month, given a date
     *
     * @param int $y        year (2003)
     * @param int $m        month (9)
     * @param int $d        day (4)
     * @param int $firstDay first day of the week (default: monday)
     *
     * @return int week number
     * @access protected
     */
    function getWeekNInMonth($y, $m, $d, $firstDay=1)
    {
        $weekEnd = ($firstDay == 0) ? $this->getDaysInWeek()-1 : $firstDay-1;
        $end_of_week = (int)Date_Calc::nextDayOfWeek($weekEnd, 1, $m, $y, '%e', true);
        $w = 1;
        while ($d > $end_of_week) {
            ++$w;
            $end_of_week += $this->getDaysInWeek();
        }
        return $w;
    }

    /**
     * Returns the number of weeks in the month
     *
     * @param int $y        year (2003)
     * @param int $m        month (9)
     * @param int $firstDay first day of the week (default: monday)
     *
     * @return int weeks number
     * @access protected
     */
    function getWeeksInMonth($y, $m, $firstDay=1)
    {
        $FDOM = Date_Calc::firstOfMonthWeekday($m, $y);
        if ($FDOM == 0) {
            $FDOM = $this->getDaysInWeek();
        }
        if ($FDOM > $firstDay) {
            $daysInTheFirstWeek = $this->getDaysInWeek() - $FDOM + $firstDay;
            $weeks = 1;
        } else {
            $daysInTheFirstWeek = $firstDay - $FDOM;
            $weeks = 0;
        }
        $daysInTheFirstWeek %= $this->getDaysInWeek();
        return (int)(ceil(($this->getDaysInMonth($y, $m) - $daysInTheFirstWeek) /
                           $this->getDaysInWeek()) + $weeks);
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
        return Date_Calc::dayOfWeek($d, $m, $y);
    }

    /**
     * Returns a list of integer days of the week beginning 0
     *
     * @param int $y year (2003)
     * @param int $m month (9)
     * @param int $d day (4)
     *
     * @return array (0, 1, 2, 3, 4, 5, 6) 1 = Monday
     * @access protected
     */
    function getWeekDays($y=null, $m=null, $d=null)
    {
        return array(0, 1, 2, 3, 4, 5, 6);
    }

    /**
     * Returns the default first day of the week
     *
     * @param int $y year (2003)
     * @param int $m month (9)
     * @param int $d day (4)
     *
     * @return int (default 1 = Monday)
     * @access protected
     */
    function getFirstDayOfWeek($y=null, $m=null, $d=null)
    {
        return 1;
    }

    /**
     * Returns the number of hours in a day
     *
     * @param int $y year (2003)
     * @param int $m month (9)
     * @param int $d day (4)
     *
     * @return int (24)
     * @access protected
     */
    function getHoursInDay($y=null,$m=null,$d=null)
    {
        return 24;
    }

    /**
     * Returns the number of minutes in an hour
     *
     * @param int $y year (2003)
     * @param int $m month (9)
     * @param int $d day (4)
     * @param int $h hour
     *
     * @return int (60)
     * @access protected
     */
    function getMinutesInHour($y=null,$m=null,$d=null,$h=null)
    {
        return 60;
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
     * @return int (60)
     * @access protected
     */
    function getSecondsInMinute($y=null,$m=null,$d=null,$h=null,$i=null)
    {
        return 60;
    }

    /**
     * Checks if the given day is the current day
     *
     * @param mixed $stamp Any timestamp format recognized by Pear::Date
     *
     * @return boolean
     * @access protected
     */
    function isToday($stamp)
    {
        static $today = null;
        if (is_null($today)) {
            $today = new Date();
        }
        $date = Calendar_Engine_PearDate::stampCollection($stamp);
        return (   $date->day == $today->getDay()
                && $date->month == $today->getMonth()
                && $date->year == $today->getYear()
        );
    }
}
?>