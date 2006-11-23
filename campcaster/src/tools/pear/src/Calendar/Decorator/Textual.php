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
// $Id: Textual.php,v 1.3 2004/08/16 13:02:44 hfuecks Exp $
//
/**
 * @package Calendar
 * @version $Id: Textual.php,v 1.3 2004/08/16 13:02:44 hfuecks Exp $
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
 * @package Calendar
 * @access public
 */
class Calendar_Decorator_Textual extends Calendar_Decorator
{
    /**
     * Constructs Calendar_Decorator_Textual
     * @param object subclass of Calendar
     * @access public
     */
    function Calendar_Decorator_Textual(&$Calendar)
    {
        parent::Calendar_Decorator($Calendar);
    }

    /**
     * Returns an array of 12 month names (first index = 1)
     * @param string (optional) format of returned months (one,two,short or long)
     * @return array
     * @access public
     * @static
     */
    function monthNames($format='long')
    {
        return Calendar_Util_Textual::monthNames($format);
    }

    /**
     * Returns an array of 7 week day names (first index = 0)
     * @param string (optional) format of returned days (one,two,short or long)
     * @return array
     * @access public
     * @static
     */
    function weekdayNames($format='long')
    {
        return Calendar_Util_Textual::weekdayNames($format);
    }

    /**
     * Returns textual representation of the previous month of the decorated calendar object
     * @param string (optional) format of returned months (one,two,short or long)
     * @return string
     * @access public
     */
    function prevMonthName($format='long')
    {
        return Calendar_Util_Textual::prevMonthName($this->calendar,$format);
    }

    /**
     * Returns textual representation of the month of the decorated calendar object
     * @param string (optional) format of returned months (one,two,short or long)
     * @return string
     * @access public
     */
    function thisMonthName($format='long')
    {
        return Calendar_Util_Textual::thisMonthName($this->calendar,$format);
    }

    /**
     * Returns textual representation of the next month of the decorated calendar object
     * @param string (optional) format of returned months (one,two,short or long)
     * @return string
     * @access public
     */
    function nextMonthName($format='long')
    {
        return Calendar_Util_Textual::nextMonthName($this->calendar,$format);
    }

    /**
     * Returns textual representation of the previous day of week of the decorated calendar object
     * @param string (optional) format of returned months (one,two,short or long)
     * @return string
     * @access public
     */
    function prevDayName($format='long')
    {
        return Calendar_Util_Textual::prevDayName($this->calendar,$format);
    }

    /**
     * Returns textual representation of the day of week of the decorated calendar object
     * @param string (optional) format of returned months (one,two,short or long)
     * @return string
     * @access public
     */
    function thisDayName($format='long')
    {
        return Calendar_Util_Textual::thisDayName($this->calendar,$format);
    }

    /**
     * Returns textual representation of the next day of week of the decorated calendar object
     * @param string (optional) format of returned months (one,two,short or long)
     * @return string
     * @access public
     */
    function nextDayName($format='long')
    {
        return Calendar_Util_Textual::nextDayName($this->calendar,$format);
    }

    /**
     * Returns the days of the week using the order defined in the decorated
     * calendar object. Only useful for Calendar_Month_Weekdays, Calendar_Month_Weeks
     * and Calendar_Week. Otherwise the returned array will begin on Sunday
     * @param string (optional) format of returned months (one,two,short or long)
     * @return array ordered array of week day names
     * @access public
     */
    function orderedWeekdays($format='long')
    {
        return Calendar_Util_Textual::orderedWeekdays($this->calendar,$format);
    }
}
?>