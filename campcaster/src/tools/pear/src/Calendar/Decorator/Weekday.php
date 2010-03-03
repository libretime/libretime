<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */

/**
 * Contains the Calendar_Decorator_Weekday class
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
 * @version   CVS: $Id: Weekday.php,v 1.8 2007/11/24 11:04:24 quipo Exp $
 * @link      http://pear.php.net/package/Calendar
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
 *
 * @category  Date and Time
 * @package   Calendar
 * @author    Harry Fuecks <hfuecks@phppatterns.com>
 * @author    Lorenzo Alberton <l.alberton@quipo.it>
 * @copyright 2003-2007 Harry Fuecks, Lorenzo Alberton
 * @license   http://www.debian.org/misc/bsd.license  BSD License (3 Clause)
 * @link      http://pear.php.net/package/Calendar
 * @access    public
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
     *
     * @param object &$Calendar subclass of Calendar
     *
     * @access public
     */
    function Calendar_Decorator_Weekday(&$Calendar)
    {
        parent::Calendar_Decorator($Calendar);
    }

    /**
     * Sets the first day of the week (0 = Sunday, 1 = Monday (default) etc)
     *
     * @param int $firstDay first day of week
     *
     * @return void
     * @access public
     */
    function setFirstDay($firstDay) 
    {
        $this->firstDay = (int)$firstDay;
    }

    /**
     * Returns the previous weekday
     *
     * @param string $format (default = 'int') return value format
     *
     * @return int $format numeric day of week or timestamp
     * @access public
     */
    function prevWeekDay($format = 'int')
    {
        $ts  = $this->calendar->prevDay('timestamp');
        $Day = new Calendar_Day(2000, 1, 1);
        $Day->setTimeStamp($ts);
        $day = $this->calendar->cE->getDayOfWeek(
            $Day->thisYear(),
            $Day->thisMonth(),
            $Day->thisDay()
        );
        $day = $this->adjustWeekScale($day);
        return $this->returnValue('Day', $format, $ts, $day);
    }

    /**
     * Returns the current weekday
     *
     * @param string $format (default = 'int') return value format
     *
     * @return int numeric day of week or timestamp
     * @access public
     */
    function thisWeekDay($format = 'int')
    {
        $ts  = $this->calendar->thisDay('timestamp');
        $day = $this->calendar->cE->getDayOfWeek(
            $this->calendar->year,
            $this->calendar->month,
            $this->calendar->day
        );
        $day = $this->adjustWeekScale($day);
        return $this->returnValue('Day', $format, $ts, $day);
    }

    /**
     * Returns the next weekday
     *
     * @param string $format (default = 'int') return value format
     *
     * @return int numeric day of week or timestamp
     * @access public
     */
    function nextWeekDay($format = 'int')
    {
        $ts  = $this->calendar->nextDay('timestamp');
        $Day = new Calendar_Day(2000, 1, 1);
        $Day->setTimeStamp($ts);
        $day = $this->calendar->cE->getDayOfWeek(
            $Day->thisYear(),
            $Day->thisMonth(),
            $Day->thisDay()
        );
        $day = $this->adjustWeekScale($day);
        return $this->returnValue('Day', $format, $ts, $day);
    }

    /**
     * Adjusts the day of the week relative to the first day of the week
     *
     * @param int $dayOfWeek day of week calendar from Calendar_Engine
     *
     * @return int day of week adjusted to first day
     * @access private
     */
    function adjustWeekScale($dayOfWeek) 
    {
        $dayOfWeek = $dayOfWeek - $this->firstDay;
        if ($dayOfWeek >= 0) {
            return $dayOfWeek;
        } else {
            return $this->calendar->cE->getDaysInWeek(
                $this->calendar->year,
                $this->calendar->month,
                $this->calendar->day
            ) + $dayOfWeek;
        }
    }
}
?>