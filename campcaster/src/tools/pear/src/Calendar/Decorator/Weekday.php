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
// |          Lorenzo Alberton <l dot alberton at quipo dot it>           |
// +----------------------------------------------------------------------+
//
// $Id: Weekday.php,v 1.3 2004/08/16 12:25:15 hfuecks Exp $
//
/**
 * @package Calendar
 * @version $Id: Weekday.php,v 1.3 2004/08/16 12:25:15 hfuecks Exp $
 */

/**
 * Allows Calendar include path to be redefined
 * @ignore
 */
if (!defined('CALENDAR_ROOT')) {
    define('CALENDAR_ROOT', 'Calendar'.DIRECTORY_SEPARATOR);
}

/**
 * Load Calendar decorator base class
 */
require_once CALENDAR_ROOT.'Decorator.php';

/**
 * Load a Calendar_Day
 */
require_once CALENDAR_ROOT.'Day.php';
/**
 * Decorator for fetching the day of the week
 * <code>
 * $Day = new Calendar_Day(2003, 10, 23);
 * $Weekday = & new Calendar_Decorator_Weekday($Day);
 * $Weekday->setFirstDay(0); // Set first day of week to Sunday (default Mon)
 * echo $Weekday->thisWeekDay(); // Displays 5 - fifth day of week relative to Sun
 * </code>
 * @package Calendar
 * @access public
 */
class Calendar_Decorator_Weekday extends Calendar_Decorator
{
    /**
     * First day of week
     * @var int (default = 1 for Monday)
     * @access private
     */
    var $firstDay = 1;

    /**
     * Constructs Calendar_Decorator_Weekday
     * @param object subclass of Calendar
     * @access public
     */
    function Calendar_Decorator_Weekday(& $Calendar)
    {
        parent::Calendar_Decorator($Calendar);
    }

    /**
     * Sets the first day of the week (0 = Sunday, 1 = Monday (default) etc)
     * @param int first day of week
     * @return void
     * @access public
     */
    function setFirstDay($firstDay) {
        $this->firstDay = (int)$firstDay;
    }

    /**
     * Returns the previous weekday
     * @param string (default = 'int') return value format
     * @return int numeric day of week or timestamp
     * @access public
     */
    function prevWeekDay($format = 'int')
    {
        $ts = $this->calendar->prevDay('timestamp');
        $Day = new Calendar_Day(2000,1,1);
        $Day->setTimeStamp($ts);
        $day = $this->calendar->cE->getDayOfWeek($Day->thisYear(),$Day->thisMonth(),$Day->thisDay());
        $day = $this->adjustWeekScale($day);
        return $this->returnValue('Day', $format, $ts, $day);
    }

    /**
     * Returns the current weekday
     * @param string (default = 'int') return value format
     * @return int numeric day of week or timestamp
     * @access public
     */
    function thisWeekDay($format = 'int')
    {
        $ts = $this->calendar->thisDay('timestamp');
        $day = $this->calendar->cE->getDayOfWeek($this->calendar->year,$this->calendar->month,$this->calendar->day);
        $day = $this->adjustWeekScale($day);
        return $this->returnValue('Day', $format, $ts, $day);
    }

    /**
     * Returns the next weekday
     * @param string (default = 'int') return value format
     * @return int numeric day of week or timestamp
     * @access public
     */
    function nextWeekDay($format = 'int')
    {
        $ts = $this->calendar->nextDay('timestamp');
        $Day = new Calendar_Day(2000,1,1);
        $Day->setTimeStamp($ts);
        $day = $this->calendar->cE->getDayOfWeek($Day->thisYear(),$Day->thisMonth(),$Day->thisDay());
        $day = $this->adjustWeekScale($day);
        return $this->returnValue('Day', $format, $ts, $day);
    }

    /**
     * Adjusts the day of the week relative to the first day of the week
     * @param int day of week calendar from Calendar_Engine
     * @return int day of week adjusted to first day
     * @access private
     */
    function adjustWeekScale($dayOfWeek) {
        $dayOfWeek = $dayOfWeek - $this->firstDay;
        if ( $dayOfWeek >= 0 ) {
            return $dayOfWeek;
        } else {
            return $this->calendar->cE->getDaysInWeek(
                $this->calendar->year,$this->calendar->month,$this->calendar->day
                ) + $dayOfWeek;
        }
    }
}
?>