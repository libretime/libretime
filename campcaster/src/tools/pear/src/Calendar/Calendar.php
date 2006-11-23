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
// $Id: Calendar.php,v 1.3 2005/10/22 10:07:11 quipo Exp $
//
/**
 * @package Calendar
 * @version $Id: Calendar.php,v 1.3 2005/10/22 10:07:11 quipo Exp $
 */

/**
 * Allows Calendar include path to be redefined
 */
if (!defined('CALENDAR_ROOT')) {
    define('CALENDAR_ROOT', 'Calendar'.DIRECTORY_SEPARATOR);
}

/**
 * Constant which defines the calculation engine to use
 */
if (!defined('CALENDAR_ENGINE')) {
    define('CALENDAR_ENGINE', 'UnixTS');
}

/**
 * Define Calendar Month states
 */
define('CALENDAR_USE_MONTH',          1);
define('CALENDAR_USE_MONTH_WEEKDAYS', 2);
define('CALENDAR_USE_MONTH_WEEKS',    3);

/**
 * Contains a factory method to return a Singleton instance of a class
 * implementing the Calendar_Engine_Interface.<br>
 * <b>Note:</b> this class must be modified to "register" alternative
 * Calendar_Engines. The engine used can be controlled with the constant
 * CALENDAR_ENGINE
 * @see Calendar_Engine_Interface
 * @package Calendar
 * @access protected
 */
class Calendar_Engine_Factory
{
    /**
     * Returns an instance of the engine
     * @return object instance of a calendar calculation engine
     * @access protected
     */
    function & getEngine()
    {
        static $engine = false;
        switch (CALENDAR_ENGINE) {
            case 'PearDate':
                $class = 'Calendar_Engine_PearDate';
                break;
            case 'UnixTS':
            default:
                $class = 'Calendar_Engine_UnixTS';
            break;
        }
        if (!$engine) {
            if (!class_exists($class)) {
                require_once CALENDAR_ROOT.'Engine'.DIRECTORY_SEPARATOR.CALENDAR_ENGINE.'.php';
            }
            $engine = new $class;
        }
        return $engine;
    }
}

/**
 * Base class for Calendar API. This class should not be instantiated
 * directly.
 * @abstract
 * @package Calendar
 */
class Calendar
{
    /**
     * Instance of class implementing calendar engine interface
     * @var object
     * @access private
     */
    var $cE;

    /**
     * Instance of Calendar_Validator (lazy initialized when isValid() or
     * getValidor() is called
     * @var Calendar_Validator
     * @access private
     */
    var $validator;

    /**
     * Year for this calendar object e.g. 2003
     * @access private
     * @var int
     */
   var $year;

    /**
     * Month for this calendar object e.g. 9
     * @access private
     * @var int
     */
    var $month;

    /**
     * Day of month for this calendar object e.g. 23
     * @access private
     * @var int
     */
    var $day;

    /**
     * Hour of day for this calendar object e.g. 13
     * @access private
     * @var int
     */
    var $hour;

    /**
     * Minute of hour this calendar object e.g. 46
     * @access private
     * @var int
     */
    var $minute;

    /**
     * Second of minute this calendar object e.g. 34
     * @access private
     * @var int
     */
    var $second;

    /**
     * Marks this calendar object as selected (e.g. 'today')
     * @access private
     * @var boolean
     */
    var $selected = false;

    /**
     * Collection of child calendar objects created from subclasses
     * of Calendar. Type depends on the object which created them.
     * @access private
     * @var array
     */
    var $children = array();

    /**
     * Constructs the Calendar
     * @param int year
     * @param int month
     * @param int day
     * @param int hour
     * @param int minute
     * @param int second
     * @access protected
     */
    function Calendar($y = 2000, $m = 1, $d = 1, $h = 0, $i = 0, $s = 0)
    {
        static $cE = null;
        if (!isset($cE)) {
            $cE = & Calendar_Engine_Factory::getEngine();
        }
        $this->cE = & $cE;
        $this->year   = (int)$y;
        $this->month  = (int)$m;
        $this->day    = (int)$d;
        $this->hour   = (int)$h;
        $this->minute = (int)$i;
        $this->second = (int)$s;
    }

    /**
     * Defines the calendar by a timestamp (Unix or ISO-8601), replacing values
     * passed to the constructor
     * @param int|string Unix or ISO-8601 timestamp
     * @return void
     * @access public
     */
    function setTimestamp($ts)
    {
        $this->year   = $this->cE->stampToYear($ts);
        $this->month  = $this->cE->stampToMonth($ts);
        $this->day    = $this->cE->stampToDay($ts);
        $this->hour   = $this->cE->stampToHour($ts);
        $this->minute = $this->cE->stampToMinute($ts);
        $this->second = $this->cE->stampToSecond($ts);
    }

    /**
     * Returns a timestamp from the current date / time values. Format of
     * timestamp depends on Calendar_Engine implementation being used
     * @return int|string timestamp
     * @access public
     */
    function getTimestamp()
    {
        return $this->cE->dateToStamp(
            $this->year, $this->month, $this->day,
            $this->hour, $this->minute, $this->second);
    }

    /**
     * Defines calendar object as selected (e.g. for today)
     * @param boolean state whether Calendar subclass
     * @return void
     * @access public
     */
    function setSelected($state = true)
    {
        $this->selected = $state;
    }

    /**
     * True if the calendar subclass object is selected (e.g. today)
     * @return boolean
     * @access public
     */
    function isSelected()
    {
        return $this->selected;
    }

    /**
     * Adjusts the date (helper method)
     * @return void
     * @access public
     */
    function adjust()
    {
        $stamp = $this->getTimeStamp();
        $this->year   = $this->cE->stampToYear($stamp);
        $this->month  = $this->cE->stampToMonth($stamp);
        $this->day    = $this->cE->stampToDay($stamp);
        $this->hour   = $this->cE->stampToHour($stamp);
        $this->minute = $this->cE->stampToMinute($stamp);
        $this->second = $this->cE->stampToSecond($stamp);
    }

    /**
     * Returns the date as an associative array (helper method)
     * @param mixed timestamp (leave empty for current timestamp)
     * @return array
     * @access public
     */
    function toArray($stamp=null)
    {
        if (is_null($stamp)) {
            $stamp = $this->getTimeStamp();
        }
        return array(
            'year'   => $this->cE->stampToYear($stamp),
            'month'  => $this->cE->stampToMonth($stamp),
            'day'    => $this->cE->stampToDay($stamp),
            'hour'   => $this->cE->stampToHour($stamp),
            'minute' => $this->cE->stampToMinute($stamp),
            'second' => $this->cE->stampToSecond($stamp)
        );
    }

    /**
     * Returns the value as an associative array (helper method)
     * @param string type of date object that return value represents
     * @param string $format ['int' | 'array' | 'timestamp' | 'object']
     * @param mixed timestamp (depending on Calendar engine being used)
     * @param int integer default value (i.e. give me the answer quick)
     * @return mixed
     * @access private
     */
    function returnValue($returnType, $format, $stamp, $default)
    {
        switch (strtolower($format)) {
            case 'int':
                return $default;
            case 'array':
                return $this->toArray($stamp);
                break;
            case 'object':
                require_once CALENDAR_ROOT.'Factory.php';
                return Calendar_Factory::createByTimestamp($returnType,$stamp);
                break;
            case 'timestamp':
            default:
                return $stamp;
                break;
        }
    }

    /**
     * Abstract method for building the children of a calendar object.
     * Implemented by Calendar subclasses
     * @param array containing Calendar objects to select (optional)
     * @return boolean
     * @access public
     * @abstract
     */
    function build($sDates = array())
    {
        require_once 'PEAR.php';
        PEAR::raiseError(
            'Calendar::build is abstract', null, PEAR_ERROR_TRIGGER,
            E_USER_NOTICE, 'Calendar::build()');
        return false;
    }

    /**
     * Abstract method for selected data objects called from build
     * @param array
     * @return boolean
     * @access public
     * @abstract
     */
    function setSelection($sDates)
    {
        require_once 'PEAR.php';
        PEAR::raiseError(
            'Calendar::setSelection is abstract', null, PEAR_ERROR_TRIGGER,
            E_USER_NOTICE, 'Calendar::setSelection()');
        return false;
    }

    /**
     * Iterator method for fetching child Calendar subclass objects
     * (e.g. a minute from an hour object). On reaching the end of
     * the collection, returns false and resets the collection for
     * further iteratations.
     * @return mixed either an object subclass of Calendar or false
     * @access public
     */
    function fetch()
    {
        $child = each($this->children);
        if ($child) {
            return $child['value'];
        } else {
            reset($this->children);
            return false;
        }
    }

    /**
     * Fetches all child from the current collection of children
     * @return array
     * @access public
     */
    function fetchAll()
    {
        return $this->children;
    }

    /**
     * Get the number Calendar subclass objects stored in the internal
     * collection.
     * @return int
     * @access public
     */
    function size()
    {
        return count($this->children);
    }

    /**
     * Determine whether this date is valid, with the bounds determined by
     * the Calendar_Engine. The call is passed on to
     * Calendar_Validator::isValid
     * @return boolean
     * @access public
     */
    function isValid()
    {
        $validator = & $this->getValidator();
        return $validator->isValid();
    }

    /**
     * Returns an instance of Calendar_Validator
     * @return Calendar_Validator
     * @access public
     */
    function & getValidator()
    {
        if (!isset($this->validator)) {
            require_once CALENDAR_ROOT.'Validator.php';
            $this->validator = & new Calendar_Validator($this);
        }
        return $this->validator;
    }

    /**
     * Returns a reference to the current Calendar_Engine being used. Useful
     * for Calendar_Table_Helper and Calendar_Validator
     * @return object implementing Calendar_Engine_Inteface
     * @access protected
     */
    function & getEngine()
    {
        return $this->cE;
    }

    /**
     * Set the CALENDAR_FIRST_DAY_OF_WEEK constant to the $firstDay value
     * if the constant is not set yet.
     * @throws E_USER_WARNING this method throws a WARNING if the
     *    CALENDAR_FIRST_DAY_OF_WEEK constant is already defined and
     *    the $firstDay parameter is set to a different value
     * @param integer $firstDay first day of the week (0=sunday, 1=monday, ...)
     * @return integer
     * @access protected
     */
    function defineFirstDayOfWeek($firstDay = null)
    {
        if (defined('CALENDAR_FIRST_DAY_OF_WEEK')) {
            if (!is_null($firstDay) && ($firstDay != CALENDAR_FIRST_DAY_OF_WEEK)) {
                $msg = 'CALENDAR_FIRST_DAY_OF_WEEK constant already defined.'
                  .' The $firstDay parameter will be ignored.';
                trigger_error($msg, E_USER_WARNING);
            }
            return CALENDAR_FIRST_DAY_OF_WEEK;
        }
        if (is_null($firstDay)) {
            $firstDay = $this->cE->getFirstDayOfWeek(
                $this->thisYear(),
                $this->thisMonth(),
                $this->thisDay()
            );
        }
        define ('CALENDAR_FIRST_DAY_OF_WEEK', $firstDay);
        return CALENDAR_FIRST_DAY_OF_WEEK;
    }

    /**
     * Returns the value for the previous year
     * @param string return value format ['int' | 'timestamp' | 'object' | 'array']
     * @return int e.g. 2002 or timestamp
     * @access public
     */
    function prevYear($format = 'int')
    {
        $ts = $this->cE->dateToStamp($this->year-1, 1, 1, 0, 0, 0);
        return $this->returnValue('Year', $format, $ts, $this->year-1);
    }

    /**
     * Returns the value for this year
     * @param string return value format ['int' | 'timestamp' | 'object' | 'array']
     * @return int e.g. 2003 or timestamp
     * @access public
     */
    function thisYear($format = 'int')
    {
        $ts = $this->cE->dateToStamp($this->year, 1, 1, 0, 0, 0);
        return $this->returnValue('Year', $format, $ts, $this->year);
    }

    /**
     * Returns the value for next year
     * @param string return value format ['int' | 'timestamp' | 'object' | 'array']
     * @return int e.g. 2004 or timestamp
     * @access public
     */
    function nextYear($format = 'int')
    {
        $ts = $this->cE->dateToStamp($this->year+1, 1, 1, 0, 0, 0);
        return $this->returnValue('Year', $format, $ts, $this->year+1);
    }

    /**
     * Returns the value for the previous month
     * @param string return value format ['int' | 'timestamp' | 'object' | 'array']
     * @return int e.g. 4 or Unix timestamp
     * @access public
     */
    function prevMonth($format = 'int')
    {
        $ts = $this->cE->dateToStamp($this->year, $this->month-1, 1, 0, 0, 0);
        return $this->returnValue('Month', $format, $ts, $this->cE->stampToMonth($ts));
    }

    /**
     * Returns the value for this month
     * @param string return value format ['int' | 'timestamp' | 'object' | 'array']
     * @return int e.g. 5 or timestamp
     * @access public
     */
    function thisMonth($format = 'int')
    {
        $ts = $this->cE->dateToStamp($this->year, $this->month, 1, 0, 0, 0);
        return $this->returnValue('Month', $format, $ts, $this->month);
    }

    /**
     * Returns the value for next month
     * @param string return value format ['int' | 'timestamp' | 'object' | 'array']
     * @return int e.g. 6 or timestamp
     * @access public
     */
    function nextMonth($format = 'int')
    {
        $ts = $this->cE->dateToStamp($this->year, $this->month+1, 1, 0, 0, 0);
        return $this->returnValue('Month', $format, $ts, $this->cE->stampToMonth($ts));
    }

    /**
     * Returns the value for the previous day
     * @param string return value format ['int' | 'timestamp' | 'object' | 'array']
     * @return int e.g. 10 or timestamp
     * @access public
     */
    function prevDay($format = 'int')
    {
        $ts = $this->cE->dateToStamp(
            $this->year, $this->month, $this->day-1, 0, 0, 0);
        return $this->returnValue('Day', $format, $ts, $this->cE->stampToDay($ts));
    }

    /**
     * Returns the value for this day
     * @param string return value format ['int' | 'timestamp' | 'object' | 'array']
     * @return int e.g. 11 or timestamp
     * @access public
     */
    function thisDay($format = 'int')
    {
        $ts = $this->cE->dateToStamp(
            $this->year, $this->month, $this->day, 0, 0, 0);
        return $this->returnValue('Day', $format, $ts, $this->day);
    }

    /**
     * Returns the value for the next day
     * @param string return value format ['int' | 'timestamp' | 'object' | 'array']
     * @return int e.g. 12 or timestamp
     * @access public
     */
    function nextDay($format = 'int')
    {
        $ts = $this->cE->dateToStamp(
            $this->year, $this->month, $this->day+1, 0, 0, 0);
        return $this->returnValue('Day', $format, $ts, $this->cE->stampToDay($ts));
    }

    /**
     * Returns the value for the previous hour
     * @param string return value format ['int' | 'timestamp' | 'object' | 'array']
     * @return int e.g. 13 or timestamp
     * @access public
     */
    function prevHour($format = 'int')
    {
        $ts = $this->cE->dateToStamp(
            $this->year, $this->month, $this->day, $this->hour-1, 0, 0);
        return $this->returnValue('Hour', $format, $ts, $this->cE->stampToHour($ts));
    }

    /**
     * Returns the value for this hour
     * @param string return value format ['int' | 'timestamp' | 'object' | 'array']
     * @return int e.g. 14 or timestamp
     * @access public
     */
    function thisHour($format = 'int')
    {
        $ts = $this->cE->dateToStamp(
            $this->year, $this->month, $this->day, $this->hour, 0, 0);
        return $this->returnValue('Hour', $format, $ts, $this->hour);
    }

    /**
     * Returns the value for the next hour
     * @param string return value format ['int' | 'timestamp' | 'object' | 'array']
     * @return int e.g. 14 or timestamp
     * @access public
     */
    function nextHour($format = 'int')
    {
        $ts = $this->cE->dateToStamp(
            $this->year, $this->month, $this->day, $this->hour+1, 0, 0);
        return $this->returnValue('Hour', $format, $ts, $this->cE->stampToHour($ts));
    }

    /**
     * Returns the value for the previous minute
     * @param string return value format ['int' | 'timestamp' | 'object' | 'array']
     * @return int e.g. 23 or timestamp
     * @access public
     */
    function prevMinute($format = 'int')
    {
        $ts = $this->cE->dateToStamp(
            $this->year, $this->month, $this->day,
            $this->hour, $this->minute-1, 0);
        return $this->returnValue('Minute', $format, $ts, $this->cE->stampToMinute($ts));
    }

    /**
     * Returns the value for this minute
     * @param string return value format ['int' | 'timestamp' | 'object' | 'array']
     * @return int e.g. 24 or timestamp
     * @access public
     */
    function thisMinute($format = 'int')
    {
        $ts = $this->cE->dateToStamp(
            $this->year, $this->month, $this->day,
            $this->hour, $this->minute, 0);
        return $this->returnValue('Minute', $format, $ts, $this->minute);
    }

    /**
    * Returns the value for the next minute
     * @param string return value format ['int' | 'timestamp' | 'object' | 'array']
     * @return int e.g. 25 or timestamp
     * @access public
     */
    function nextMinute($format = 'int')
    {
        $ts = $this->cE->dateToStamp(
            $this->year, $this->month, $this->day,
            $this->hour, $this->minute+1, 0);
        return $this->returnValue('Minute', $format, $ts, $this->cE->stampToMinute($ts));
    }

    /**
     * Returns the value for the previous second
     * @param string return value format ['int' | 'timestamp' | 'object' | 'array']
     * @return int e.g. 43 or timestamp
     * @access public
     */
    function prevSecond($format = 'int')
    {
        $ts = $this->cE->dateToStamp(
            $this->year, $this->month, $this->day,
            $this->hour, $this->minute, $this->second-1);
        return $this->returnValue('Second', $format, $ts, $this->cE->stampToSecond($ts));
    }

    /**
     * Returns the value for this second
    * @param string return value format ['int' | 'timestamp' | 'object' | 'array']
     * @return int e.g. 44 or timestamp
     * @access public
     */
    function thisSecond($format = 'int')
    {
        $ts = $this->cE->dateToStamp(
            $this->year, $this->month, $this->day,
            $this->hour, $this->minute, $this->second);
        return $this->returnValue('Second', $format, $ts, $this->second);
    }

    /**
     * Returns the value for the next second
     * @param string return value format ['int' | 'timestamp' | 'object' | 'array']
     * @return int e.g. 45 or timestamp
     * @access public
     */
    function nextSecond($format = 'int')
    {
        $ts = $this->cE->dateToStamp(
            $this->year, $this->month, $this->day,
            $this->hour, $this->minute, $this->second+1);
        return $this->returnValue('Second', $format, $ts, $this->cE->stampToSecond($ts));
    }
}
?>