<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */

/**
 * Contains the Calendar_Util_Textual class
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
 * @version   CVS: $Id: Textual.php 247250 2007-11-28 19:42:01Z quipo $
 * @link      http://pear.php.net/package/Calendar
 */

/**
 * @package Calendar
 * @version $Id: Textual.php 247250 2007-11-28 19:42:01Z quipo $
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
 * Static utlities to help with fetching textual representations of months and
 * days of the week.
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
class Calendar_Util_Textual
{

    /**
     * Returns an array of 12 month names (first index = 1)
     *
     * @param string $format (optional) format of returned months (one|two|short|long)
     *
     * @return array
     * @access public
     * @static
     */
    function monthNames($format = 'long')
    {
        $formats = array(
            'one'   => '%b', 
            'two'   => '%b', 
            'short' => '%b', 
            'long'  => '%B',
        );
        if (!array_key_exists($format, $formats)) {
            $format = 'long';
        }
        $months = array();
        for ($i=1; $i<=12; $i++) {
            $stamp = mktime(0, 0, 0, $i, 1, 2003);
            $month = strftime($formats[$format], $stamp);
            switch($format) {
            case 'one':
                $month = substr($month, 0, 1);
                break;
            case 'two':
                $month = substr($month, 0, 2);
                break;
            }
            $months[$i] = $month;
        }
        return $months;
    }

    /**
     * Returns an array of 7 week day names (first index = 0)
     *
     * @param string $format (optional) format of returned days (one,two,short or long)
     *
     * @return array
     * @access public
     * @static
     */
    function weekdayNames($format = 'long')
    {
        $formats = array(
            'one'   => '%a', 
            'two'   => '%a', 
            'short' => '%a', 
            'long'  => '%A',
        );
        if (!array_key_exists($format, $formats)) {
            $format = 'long';
        }
        $days = array();
        for ($i=0; $i<=6; $i++) {
            $stamp = mktime(0, 0, 0, 11, $i+2, 2003);
            $day = strftime($formats[$format], $stamp);
            switch($format) {
            case 'one':
                $day = substr($day, 0, 1);
                break;
            case 'two':
                $day = substr($day, 0, 2);
                break;
            }
            $days[$i] = $day;
        }
        return $days;
    }

    /**
     * Returns textual representation of the previous month of the decorated calendar object
     *
     * @param object $Calendar subclass of Calendar e.g. Calendar_Month
     * @param string $format   (optional) format of returned months (one,two,short or long)
     *
     * @return string
     * @access public
     * @static
     */
    function prevMonthName($Calendar, $format = 'long')
    {
        $months = Calendar_Util_Textual::monthNames($format);
        return $months[$Calendar->prevMonth()];
    }

    /**
     * Returns textual representation of the month of the decorated calendar object
     *
     * @param object $Calendar subclass of Calendar e.g. Calendar_Month
     * @param string $format   (optional) format of returned months (one,two,short or long)
     *
     * @return string
     * @access public
     * @static
     */
    function thisMonthName($Calendar, $format = 'long')
    {
        $months = Calendar_Util_Textual::monthNames($format);
        return $months[$Calendar->thisMonth()];
    }

    /**
     * Returns textual representation of the next month of the decorated calendar object
     *
     * @param object $Calendar subclass of Calendar e.g. Calendar_Month
     * @param string $format   (optional) format of returned months (one,two,short or long)
     *
     * @return string
     * @access public
     * @static
     */
    function nextMonthName($Calendar, $format = 'long')
    {
        $months = Calendar_Util_Textual::monthNames($format);
        return $months[$Calendar->nextMonth()];
    }

    /**
     * Returns textual representation of the previous day of week of the decorated calendar object
     * <b>Note:</b> Requires PEAR::Date
     *
     * @param object $Calendar subclass of Calendar e.g. Calendar_Month
     * @param string $format   (optional) format of returned months (one,two,short or long)
     *
     * @return string
     * @access public
     * @static
     */
    function prevDayName($Calendar, $format = 'long')
    {
        $days = Calendar_Util_Textual::weekdayNames($format);
        $stamp = $Calendar->prevDay('timestamp');
        $cE = $Calendar->getEngine();
        include_once 'Date/Calc.php';
        $day = Date_Calc::dayOfWeek($cE->stampToDay($stamp),
            $cE->stampToMonth($stamp), $cE->stampToYear($stamp));
        return $days[$day];
    }

    /**
     * Returns textual representation of the day of week of the decorated calendar object
     * <b>Note:</b> Requires PEAR::Date
     *
     * @param object $Calendar subclass of Calendar e.g. Calendar_Month
     * @param string $format   (optional) format of returned months (one,two,short or long)
     *
     * @return string
     * @access public
     * @static
     */
    function thisDayName($Calendar, $format='long')
    {
        $days = Calendar_Util_Textual::weekdayNames($format);
        include_once 'Date/Calc.php';
        $day = Date_Calc::dayOfWeek($Calendar->thisDay(), $Calendar->thisMonth(), $Calendar->thisYear());
        return $days[$day];
    }

    /**
     * Returns textual representation of the next day of week of the decorated calendar object
     *
     * @param object $Calendar subclass of Calendar e.g. Calendar_Month
     * @param string $format   (optional) format of returned months (one,two,short or long)
     *
     * @return string
     * @access public
     * @static
     */
    function nextDayName($Calendar, $format='long')
    {
        $days = Calendar_Util_Textual::weekdayNames($format);
        $stamp = $Calendar->nextDay('timestamp');
        $cE = $Calendar->getEngine();
        include_once 'Date/Calc.php';
        $day = Date_Calc::dayOfWeek($cE->stampToDay($stamp),
            $cE->stampToMonth($stamp), $cE->stampToYear($stamp));
        return $days[$day];
    }

    /**
     * Returns the days of the week using the order defined in the decorated
     * calendar object. Only useful for Calendar_Month_Weekdays, Calendar_Month_Weeks
     * and Calendar_Week. Otherwise the returned array will begin on Sunday
     *
     * @param object $Calendar subclass of Calendar e.g. Calendar_Month
     * @param string $format   (optional) format of returned months (one,two,short or long)
     *
     * @return array ordered array of week day names
     * @access public
     * @static
     */
    function orderedWeekdays($Calendar, $format = 'long')
    {
        $days = Calendar_Util_Textual::weekdayNames($format);
        
        if (isset($Calendar->tableHelper)) {
            $ordereddays = $Calendar->tableHelper->getDaysOfWeek();
        } else {
            //default: start from Sunday
            $firstDay = 0;
            //check if defined / set
            if (defined('CALENDAR_FIRST_DAY_OF_WEEK')) {
                $firstDay = CALENDAR_FIRST_DAY_OF_WEEK;
            } elseif(isset($Calendar->firstDay)) {
                $firstDay = $Calendar->firstDay;
            }
            $ordereddays = array();
            for ($i = $firstDay; $i < 7; $i++) {
                $ordereddays[] = $i;
            }
            for ($i = 0; $i < $firstDay; $i++) {
                $ordereddays[] = $i;
            }
        }
        
        $ordereddays = array_flip($ordereddays);
        $i = 0;
        $returndays = array();
        foreach ($ordereddays as $key => $value) {
            $returndays[$i] = $days[$key];
            $i++;
        }
        return $returndays;
    }
}
?>