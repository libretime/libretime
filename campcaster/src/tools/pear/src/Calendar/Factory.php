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
// $Id: Factory.php,v 1.3 2005/10/22 10:08:47 quipo Exp $
//
/**
 * @package Calendar
 * @version $Id: Factory.php,v 1.3 2005/10/22 10:08:47 quipo Exp $
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
 * @package Calendar
 * @access protected
 */
class Calendar_Factory
{
    /**
     * Creates a calendar object given the type and units
     * @param string class of calendar object to create
     * @param int year
     * @param int month
     * @param int day
     * @param int hour
     * @param int minute
     * @param int second
     * @return object subclass of Calendar
     * @access public
     * @static
     */
    function create($type, $y = 2000, $m = 1, $d = 1, $h = 0, $i = 0, $s = 0)
    {
        $firstDay = defined('CALENDAR_FIRST_DAY_OF_WEEK') ? CALENDAR_FIRST_DAY_OF_WEEK : 1;
        switch ($type) {
            case 'Day':
                require_once CALENDAR_ROOT.'Day.php';
                return new Calendar_Day($y,$m,$d);
            case 'Month':
                // Set default state for which month type to build
                if (!defined('CALENDAR_MONTH_STATE')) {
                    define('CALENDAR_MONTH_STATE', CALENDAR_USE_MONTH);
                }
                switch (CALENDAR_MONTH_STATE) {
                    case CALENDAR_USE_MONTH_WEEKDAYS:
                        require_once CALENDAR_ROOT.'Month/Weekdays.php';
                        $class = 'Calendar_Month_Weekdays';
                        break;
                    case CALENDAR_USE_MONTH_WEEKS:
                        require_once CALENDAR_ROOT.'Month/Weeks.php';
                        $class = 'Calendar_Month_Weeks';
                        break;
                    case CALENDAR_USE_MONTH:
                    default:
                        require_once CALENDAR_ROOT.'Month.php';
                        $class = 'Calendar_Month';
                        break;
                }
                return new $class($y, $m, $firstDay);
            case 'Week':
                require_once CALENDAR_ROOT.'Week.php';
                return new Calendar_Week($y, $m, $d, $firstDay);
            case 'Hour':
                require_once CALENDAR_ROOT.'Hour.php';
                return new Calendar_Hour($y, $m, $d, $h);
            case 'Minute':
                require_once CALENDAR_ROOT.'Minute.php';
                return new Calendar_Minute($y, $m, $d, $h, $i);
            case 'Second':
                require_once CALENDAR_ROOT.'Second.php';
                return new Calendar_Second($y,$m,$d,$h,$i,$s);
            case 'Year':
                require_once CALENDAR_ROOT.'Year.php';
                return new Calendar_Year($y);
            default:
                require_once 'PEAR.php';
                PEAR::raiseError(
                    'Calendar_Factory::create() unrecognised type: '.$type, null, PEAR_ERROR_TRIGGER,
                    E_USER_NOTICE, 'Calendar_Factory::create()');
                return false;
        }
    }
    /**
     * Creates an instance of a calendar object, given a type and timestamp
     * @param string type of object to create
     * @param mixed timestamp (depending on Calendar engine being used)
     * @return object subclass of Calendar
     * @access public
     * @static
     */
    function & createByTimestamp($type, $stamp)
    {
        $cE = & Calendar_Engine_Factory::getEngine();
        $y = $cE->stampToYear($stamp);
        $m = $cE->stampToMonth($stamp);
        $d = $cE->stampToDay($stamp);
        $h = $cE->stampToHour($stamp);
        $i = $cE->stampToMinute($stamp);
        $s = $cE->stampToSecond($stamp);
        $cal = Calendar_Factory::create($type, $y, $m, $d, $h, $i, $s);
        return $cal;
    }
}
?>