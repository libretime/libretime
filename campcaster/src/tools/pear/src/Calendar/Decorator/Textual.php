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
 * @author    Lorenzo Alberton <l.alberton@quipo.it>
 * @copyright 2003-2007 Harry Fuecks, Lorenzo Alberton
 * @license   http://www.debian.org/misc/bsd.license  BSD License (3 Clause)
 * @version   CVS: $Id: Textual.php,v 1.8 2007/11/24 11:04:24 quipo Exp $
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
 * Load the Uri utility
 */
require_once CALENDAR_ROOT.'Util'.DIRECTORY_SEPARATOR.'Textual.php';

/**
 * Decorator to help with fetching textual representations of months and
 * days of the week.
 * <b>Note:</b> for performance you should prefer Calendar_Util_Textual unless you
 * have a specific need to use a decorator
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
class Calendar_Decorator_Textual extends Calendar_Decorator
{
    /**
     * Constructs Calendar_Decorator_Textual
     *
     * @param object &$Calendar subclass of Calendar
     *
     * @access public
     */
    function Calendar_Decorator_Textual(&$Calendar)
    {
        parent::Calendar_Decorator($Calendar);
    }

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
        return Calendar_Util_Textual::monthNames($format);
    }

    /**
     * Returns an array of 7 week day names (first index = 0)
     *
     * @param string $format (optional) format of returned days (one|two|short|long)
     *
     * @return array
     * @access public
     * @static
     */
    function weekdayNames($format = 'long')
    {
        return Calendar_Util_Textual::weekdayNames($format);
    }

    /**
     * Returns textual representation of the previous month of the decorated calendar object
     *
     * @param string $format (optional) format of returned months (one|two|short|long)
     *
     * @return string
     * @access public
     */
    function prevMonthName($format = 'long')
    {
        return Calendar_Util_Textual::prevMonthName($this->calendar, $format);
    }

    /**
     * Returns textual representation of the month of the decorated calendar object
     *
     * @param string $format (optional) format of returned months (one|two|short|long)
     *
     * @return string
     * @access public
     */
    function thisMonthName($format = 'long')
    {
        return Calendar_Util_Textual::thisMonthName($this->calendar, $format);
    }

    /**
     * Returns textual representation of the next month of the decorated calendar object
     *
     * @param string $format (optional) format of returned months (one|two|short|long)
     *
     * @return string
     * @access public
     */
    function nextMonthName($format = 'long')
    {
        return Calendar_Util_Textual::nextMonthName($this->calendar, $format);
    }

    /**
     * Returns textual representation of the previous day of week of the decorated calendar object
     *
     * @param string $format (optional) format of returned months (one|two|short|long)
     *
     * @return string
     * @access public
     */
    function prevDayName($format = 'long')
    {
        return Calendar_Util_Textual::prevDayName($this->calendar, $format);
    }

    /**
     * Returns textual representation of the day of week of the decorated calendar object
     *
     * @param string $format (optional) format of returned months (one|two|short|long)
     *
     * @return string
     * @access public
     */
    function thisDayName($format = 'long')
    {
        return Calendar_Util_Textual::thisDayName($this->calendar, $format);
    }

    /**
     * Returns textual representation of the next day of week of the decorated calendar object
     *
     * @param string $format (optional) format of returned months (one|two|short|long)
     *
     * @return string
     * @access public
     */
    function nextDayName($format = 'long')
    {
        return Calendar_Util_Textual::nextDayName($this->calendar, $format);
    }

    /**
     * Returns the days of the week using the order defined in the decorated
     * calendar object. Only useful for Calendar_Month_Weekdays, Calendar_Month_Weeks
     * and Calendar_Week. Otherwise the returned array will begin on Sunday
     *
     * @param string $format (optional) format of returned months (one|two|short|long)
     *
     * @return array ordered array of week day names
     * @access public
     */
    function orderedWeekdays($format = 'long')
    {
        return Calendar_Util_Textual::orderedWeekdays($this->calendar, $format);
    }
}
?>