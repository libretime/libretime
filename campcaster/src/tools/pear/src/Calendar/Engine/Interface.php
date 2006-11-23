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
// $Id: Interface.php,v 1.5 2004/08/16 12:29:18 hfuecks Exp $
//
/**
 * @package Calendar
 * @version $Id: Interface.php,v 1.5 2004/08/16 12:29:18 hfuecks Exp $
 */
/**
 * The methods the classes implementing the Calendar_Engine must implement.
 * Note this class is not used but simply to help development
 * @package Calendar
 * @access protected
 */
class Calendar_Engine_Interface
{
    /**
     * Provides a mechansim to make sure parsing of timestamps
     * into human dates is only performed once per timestamp.
     * Typically called "internally" by methods like stampToYear.
     * Return value can vary, depending on the specific implementation
     * @param int timestamp (depending on implementation)
     * @return mixed
     * @access protected
     */
    function stampCollection($stamp)
    {
    }

    /**
     * Returns a numeric year given a timestamp
     * @param int timestamp (depending on implementation)
     * @return int year (e.g. 2003)
     * @access protected
     */
    function stampToYear($stamp)
    {
    }

    /**
     * Returns a numeric month given a timestamp
     * @param int timestamp (depending on implementation)
     * @return int month (e.g. 9)
     * @access protected
     */
    function stampToMonth($stamp)
    {
    }

    /**
     * Returns a numeric day given a timestamp
     * @param int timestamp (depending on implementation)
     * @return int day (e.g. 15)
     * @access protected
     */
    function stampToDay($stamp)
    {
    }

    /**
     * Returns a numeric hour given a timestamp
     * @param int timestamp (depending on implementation)
     * @return int hour (e.g. 13)
     * @access protected
     */
    function stampToHour($stamp)
    {
    }

    /**
     * Returns a numeric minute given a timestamp
     * @param int timestamp (depending on implementation)
     * @return int minute (e.g. 34)
     * @access protected
     */
    function stampToMinute($stamp)
    {
    }

    /**
     * Returns a numeric second given a timestamp
     * @param int timestamp (depending on implementation)
     * @return int second (e.g. 51)
     * @access protected
     */
    function stampToSecond($stamp)
    {
    }

    /**
     * Returns a timestamp. Can be worth "caching" generated
     * timestamps in a static variable, identified by the
     * params this method accepts, to timestamp will only
     * be calculated once.
     * @param int year (e.g. 2003)
     * @param int month (e.g. 9)
     * @param int day (e.g. 13)
     * @param int hour (e.g. 13)
     * @param int minute (e.g. 34)
     * @param int second (e.g. 53)
     * @return int (depends on implementation)
     * @access protected
     */
    function dateToStamp($y,$m,$d,$h,$i,$s)
    {
    }

    /**
     * The upper limit on years that the Calendar Engine can work with
     * @return int (e.g. 2037)
     * @access protected
     */
    function getMaxYears()
    {
    }

    /**
     * The lower limit on years that the Calendar Engine can work with
     * @return int (e.g 1902)
     * @access protected
     */
    function getMinYears()
    {
    }

    /**
     * Returns the number of months in a year
     * @param int (optional) year to get months for
     * @return int (e.g. 12)
     * @access protected
     */
    function getMonthsInYear($y=null)
    {
    }

    /**
     * Returns the number of days in a month, given year and month
     * @param int year (e.g. 2003)
     * @param int month (e.g. 9)
     * @return int days in month
     * @access protected
     */
    function getDaysInMonth($y, $m)
    {
    }

    /**
     * Returns numeric representation of the day of the week in a month,
     * given year and month
     * @param int year (e.g. 2003)
     * @param int month (e.g. 9)
     * @return int
     * @access protected
     */
    function getFirstDayInMonth ($y, $m)
    {
    }

    /**
     * Returns the number of days in a week
     * @param int year (2003)
     * @param int month (9)
     * @param int day (4)
     * @return int (e.g. 7)
     * @access protected
     */
    function getDaysInWeek($y=NULL, $m=NULL, $d=NULL)
    {
    }

    /**
     * Returns the number of the week in the year (ISO-8601), given a date
     * @param int year (2003)
     * @param int month (9)
     * @param int day (4)
     * @return int week number
     * @access protected
     */
    function getWeekNInYear($y, $m, $d)
    {
    }

    /**
     * Returns the number of the week in the month, given a date
     * @param int year (2003)
     * @param int month (9)
     * @param int day (4)
     * @param int first day of the week (default: 1 - monday)
     * @return int week number
     * @access protected
     */
    function getWeekNInMonth($y, $m, $d, $firstDay=1)
    {
    }

    /**
     * Returns the number of weeks in the month
     * @param int year (2003)
     * @param int month (9)
     * @param int first day of the week (default: 1 - monday)
     * @return int weeks number
     * @access protected
     */
    function getWeeksInMonth($y, $m)
    {
    }

    /**
     * Returns the number of the day of the week (0=sunday, 1=monday...)
     * @param int year (2003)
     * @param int month (9)
     * @param int day (4)
     * @return int weekday number
     * @access protected
     */
    function getDayOfWeek($y, $m, $d)
    {
    }

    /**
     * Returns the numeric values of the days of the week.
     * @param int year (2003)
     * @param int month (9)
     * @param int day (4)
     * @return array list of numeric values of days in week, beginning 0
     * @access protected
     */
    function getWeekDays($y=NULL, $m=NULL, $d=NULL)
    {
    }

    /**
     * Returns the default first day of the week as an integer. Must be a
     * member of the array returned from getWeekDays
     * @param int year (2003)
     * @param int month (9)
     * @param int day (4)
     * @return int (e.g. 1 for Monday)
     * @see getWeekDays
     * @access protected
     */
    function getFirstDayOfWeek($y=NULL, $m=NULL, $d=NULL)
    {
    }

    /**
     * Returns the number of hours in a day<br>
     * @param int (optional) day to get hours for
     * @return int (e.g. 24)
     * @access protected
     */
    function getHoursInDay($y=null,$m=null,$d=null)
    {
    }

    /**
     * Returns the number of minutes in an hour
     * @param int (optional) hour to get minutes for
     * @return int
     * @access protected
     */
    function getMinutesInHour($y=null,$m=null,$d=null,$h=null)
    {
    }

    /**
     * Returns the number of seconds in a minutes
     * @param int (optional) minute to get seconds for
     * @return int
     * @access protected
     */
    function getSecondsInMinute($y=null,$m=null,$d=null,$h=null,$i=null)
    {
    }
}
?>