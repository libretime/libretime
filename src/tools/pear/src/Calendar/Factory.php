<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */

/**
 * Contains the Calendar_Factory class
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
 * @version   CVS: $Id: Factory.php,v 1.8 2007/11/18 21:46:42 quipo Exp $
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
 * Load Calendar base class
 */
require_once CALENDAR_ROOT.'Calendar.php';

/**
 * Contains a factory method to return a Singleton instance of a class
 * implementing the Calendar_Engine_Interface.<br>
 * For Month objects, to control type of month returned, use CALENDAR_MONTH_STATE
 * constact e.g.;
 * <code>
 * require_once 'Calendar/Factory.php';
 * define ('CALENDAR_MONTH_STATE',CALENDAR_USE_MONTH_WEEKDAYS); // Use Calendar_Month_Weekdays
 * // define ('CALENDAR_MONTH_STATE',CALENDAR_USE_MONTH_WEEKS); // Use Calendar_Month_Weeks
 * // define ('CALENDAR_MONTH_STATE',CALENDAR_USE_MONTH); // Use Calendar_Month
 * </code>
 * It defaults to building Calendar_Month objects.<br>
 * Use the constract CALENDAR_FIRST_DAY_OF_WEEK to control the first day of the week
 * for Month or Week objects (e.g. 0 = Sunday, 6 = Saturday)
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
class Calendar_Factory
{
    /**
     * Creates a calendar object given the type and units
     *
     * @param string $type class of calendar object to create
     * @param int    $y    year
     * @param int    $m    month
     * @param int    $d    day
     * @param int    $h    hour
     * @param int    $i    minute
     * @param int    $s    second
     *
     * @return object subclass of Calendar
     * @access public
     * @static
     */
    function create($type, $y = 2000, $m = 1, $d = 1, $h = 0, $i = 0, $s = 0)
    {
        $firstDay = defined('CALENDAR_FIRST_DAY_OF_WEEK') ? CALENDAR_FIRST_DAY_OF_WEEK : 1;
        switch ($type) {
        case 'Day':
            include_once CALENDAR_ROOT.'Day.php';
            return new Calendar_Day($y, $m, $d);
        case 'Month':
            // Set default state for which month type to build
            if (!defined('CALENDAR_MONTH_STATE')) {
                define('CALENDAR_MONTH_STATE', CALENDAR_USE_MONTH);
            }
            switch (CALENDAR_MONTH_STATE) {
            case CALENDAR_USE_MONTH_WEEKDAYS:
                include_once CALENDAR_ROOT.'Month/Weekdays.php';
                $class = 'Calendar_Month_Weekdays';
                break;
            case CALENDAR_USE_MONTH_WEEKS:
                include_once CALENDAR_ROOT.'Month/Weeks.php';
                $class = 'Calendar_Month_Weeks';
                break;
            case CALENDAR_USE_MONTH:
            default:
                include_once CALENDAR_ROOT.'Month.php';
                $class = 'Calendar_Month';
                break;
            }
            return new $class($y, $m, $firstDay);
        case 'Week':
            include_once CALENDAR_ROOT.'Week.php';
            return new Calendar_Week($y, $m, $d, $firstDay);
        case 'Hour':
            include_once CALENDAR_ROOT.'Hour.php';
            return new Calendar_Hour($y, $m, $d, $h);
        case 'Minute':
            include_once CALENDAR_ROOT.'Minute.php';
            return new Calendar_Minute($y, $m, $d, $h, $i);
        case 'Second':
            include_once CALENDAR_ROOT.'Second.php';
            return new Calendar_Second($y, $m, $d, $h, $i, $s);
        case 'Year':
            include_once CALENDAR_ROOT.'Year.php';
            return new Calendar_Year($y);
        default:
            include_once 'PEAR.php';
            PEAR::raiseError('Calendar_Factory::create() unrecognised type: '.$type,
                null, PEAR_ERROR_TRIGGER, E_USER_NOTICE, 'Calendar_Factory::create()');
            return false;
        }
    }

    /**
     * Creates an instance of a calendar object, given a type and timestamp
     *
     * @param string $type  type of object to create
     * @param mixed  $stamp timestamp (depending on Calendar engine being used)
     *
     * @return object subclass of Calendar
     * @access public
     * @static
     */
    function & createByTimestamp($type, $stamp)
    {
        $cE  = & Calendar_Engine_Factory::getEngine();
        $y   = $cE->stampToYear($stamp);
        $m   = $cE->stampToMonth($stamp);
        $d   = $cE->stampToDay($stamp);
        $h   = $cE->stampToHour($stamp);
        $i   = $cE->stampToMinute($stamp);
        $s   = $cE->stampToSecond($stamp);
        $cal = Calendar_Factory::create($type, $y, $m, $d, $h, $i, $s);
        return $cal;
    }
}
?>