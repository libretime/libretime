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
// $Id: Decorator.php,v 1.3 2005/10/22 10:29:46 quipo Exp $
//
/**
 * @package Calendar
 * @version $Id: Decorator.php,v 1.3 2005/10/22 10:29:46 quipo Exp $
 */
/**
 * Decorates any calendar class.
 * Create a subclass of this class for your own "decoration".
 * Used for "selections"
 * <code>
 * class DayDecorator extends Calendar_Decorator
 * {
 *     function thisDay($format = 'int')
 *     {
.*         $day = parent::thisDay('timestamp');
.*         return date('D', $day);
 *     }
 * }
 * $Day = & new Calendar_Day(2003, 10, 25);
 * $DayDecorator = & new DayDecorator($Day);
 * echo $DayDecorator->thisDay(); // Outputs "Sat"
 * </code>
 * @abstract
 * @package Calendar
 */
class Calendar_Decorator
{
    /**
     * Subclass of Calendar being decorated
     * @var object
     * @access private
     */
    var $calendar;

    /**
     * Constructs the Calendar_Decorator
     * @param object subclass to Calendar to decorate
     */
    function Calendar_Decorator(& $calendar)
    {
        $this->calendar = & $calendar;
    }

    /**
     * Defines the calendar by a Unix timestamp, replacing values
     * passed to the constructor
     * @param int Unix timestamp
     * @return void
     * @access public
     */
    function setTimestamp($ts)
    {
        $this->calendar->setTimestamp($ts);
    }

    /**
     * Returns a timestamp from the current date / time values. Format of
     * timestamp depends on Calendar_Engine implementation being used
     * @return int timestamp
     * @access public
     */
    function getTimestamp()
    {
        return $this->calendar->getTimeStamp();
    }

    /**
     * Defines calendar object as selected (e.g. for today)
     * @param boolean state whether Calendar subclass
     * @return void
     * @access public
     */
    function setSelected($state = true)
    {
        $this->calendar->setSelected($state = true);
    }

    /**
     * True if the calendar subclass object is selected (e.g. today)
     * @return boolean
     * @access public
     */
    function isSelected()
    {
        return $this->calendar->isSelected();
    }

    /**
     * Adjusts the date (helper method)
     * @return void
     * @access public
     */
    function adjust()
    {
        $this->calendar->adjust();
    }

    /**
     * Returns the date as an associative array (helper method)
     * @param mixed timestamp (leave empty for current timestamp)
     * @return array
     * @access public
     */
    function toArray($stamp=null)
    {
        return $this->calendar->toArray($stamp);
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
        return $this->calendar->returnValue($returnType, $format, $stamp, $default);
    }

    /**
     * Defines Day object as first in a week
     * Only used by Calendar_Month_Weekdays::build()
     * @param boolean state
     * @return void
     * @access private
     */
    function setFirst ($state = true)
    {
        if ( method_exists($this->calendar,'setFirst') ) {
            $this->calendar->setFirst($state);
        }
    }

    /**
     * Defines Day object as last in a week
     * Used only following Calendar_Month_Weekdays::build()
     * @param boolean state
     * @return void
     * @access private
     */
    function setLast($state = true)
    {
        if ( method_exists($this->calendar,'setLast') ) {
            $this->calendar->setLast($state);
        }
    }

    /**
     * Returns true if Day object is first in a Week
     * Only relevant when Day is created by Calendar_Month_Weekdays::build()
     * @return boolean
     * @access public
     */
    function isFirst() {
        if ( method_exists($this->calendar,'isFirst') ) {
            return $this->calendar->isFirst();
        }
    }

    /**
     * Returns true if Day object is last in a Week
     * Only relevant when Day is created by Calendar_Month_Weekdays::build()
     * @return boolean
     * @access public
     */
    function isLast()
    {
        if ( method_exists($this->calendar,'isLast') ) {
            return $this->calendar->isLast();
        }
    }

    /**
     * Defines Day object as empty
     * Only used by Calendar_Month_Weekdays::build()
     * @param boolean state
     * @return void
     * @access private
     */
    function setEmpty ($state = true)
    {
        if ( method_exists($this->calendar,'setEmpty') ) {
            $this->calendar->setEmpty($state);
        }
    }

    /**
     * @return boolean
     * @access public
     */
    function isEmpty()
    {
        if ( method_exists($this->calendar,'isEmpty') ) {
            return $this->calendar->isEmpty();
        }
    }

    /**
     * Build the children
     * @param array containing Calendar objects to select (optional)
     * @return boolean
     * @access public
     * @abstract
     */
    function build($sDates = array())
    {
        $this->calendar->build($sDates);
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
        return $this->calendar->fetch();
    }

    /**
     * Fetches all child from the current collection of children
     * @return array
     * @access public
     */
    function fetchAll()
    {
        return $this->calendar->fetchAll();
    }

    /**
     * Get the number Calendar subclass objects stored in the internal
     * collection.
     * @return int
     * @access public
     */
    function size()
    {
        return $this->calendar->size();
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
        return $this->calendar->isValid();
    }

    /**
     * Returns an instance of Calendar_Validator
     * @return Calendar_Validator
     * @access public
     */
    function & getValidator()
    {
        $validator = $this->calendar->getValidator();
        return $validator;
    }

    /**
     * Returns a reference to the current Calendar_Engine being used. Useful
     * for Calendar_Table_Helper and Calendar_Validator
     * @return object implementing Calendar_Engine_Inteface
     * @access private
     */
    function & getEngine()
    {
        return $this->calendar->getEngine();
    }

    /**
     * Returns the value for the previous year
     * @param string return value format ['int' | 'timestamp' | 'object' | 'array']
     * @return int e.g. 2002 or timestamp
     * @access public
     */
    function prevYear($format = 'int')
    {
        return $this->calendar->prevYear($format);
    }

    /**
     * Returns the value for this year
     * @param string return value format ['int' | 'timestamp' | 'object' | 'array']
     * @return int e.g. 2003 or timestamp
     * @access public
     */
    function thisYear($format = 'int')
    {
        return $this->calendar->thisYear($format);
    }

    /**
     * Returns the value for next year
     * @param string return value format ['int' | 'timestamp' | 'object' | 'array']
     * @return int e.g. 2004 or timestamp
     * @access public
     */
    function nextYear($format = 'int')
    {
        return $this->calendar->nextYear($format);
    }

    /**
     * Returns the value for the previous month
     * @param string return value format ['int' | 'timestamp' | 'object' | 'array']
     * @return int e.g. 4 or Unix timestamp
     * @access public
      */
    function prevMonth($format = 'int')
    {
        return $this->calendar->prevMonth($format);
    }

    /**
     * Returns the value for this month
     * @param string return value format ['int' | 'timestamp' | 'object' | 'array']
     * @return int e.g. 5 or timestamp
     * @access public
     */
    function thisMonth($format = 'int')
    {
        return $this->calendar->thisMonth($format);
    }

    /**
     * Returns the value for next month
     * @param string return value format ['int' | 'timestamp' | 'object' | 'array']
     * @return int e.g. 6 or timestamp
     * @access public
     */
    function nextMonth($format = 'int')
    {
        return $this->calendar->nextMonth($format);
    }

    /**
     * Returns the value for the previous week
     * @param string return value format ['int' | 'timestamp' | 'object' | 'array']
     * @return int e.g. 4 or Unix timestamp
     * @access public
      */
    function prevWeek($format = 'n_in_month')
    {
        if ( method_exists($this->calendar,'prevWeek') ) {
            return $this->calendar->prevWeek($format);
        } else {
            require_once 'PEAR.php';
            PEAR::raiseError(
                'Cannot call prevWeek on Calendar object of type: '.
                get_class($this->calendar), 133, PEAR_ERROR_TRIGGER,
                E_USER_NOTICE, 'Calendar_Decorator::prevWeek()');
            return false;
        }
    }

    /**
     * Returns the value for this week
     * @param string return value format ['int' | 'timestamp' | 'object' | 'array']
     * @return int e.g. 5 or timestamp
     * @access public
     */
    function thisWeek($format = 'n_in_month')
    {
        if ( method_exists($this->calendar,'thisWeek') ) {
            return $this->calendar->thisWeek($format);
        } else {
            require_once 'PEAR.php';
            PEAR::raiseError(
                'Cannot call thisWeek on Calendar object of type: '.
                get_class($this->calendar), 133, PEAR_ERROR_TRIGGER,
                E_USER_NOTICE, 'Calendar_Decorator::thisWeek()');
            return false;
        }
    }

    /**
     * Returns the value for next week
     * @param string return value format ['int' | 'timestamp' | 'object' | 'array']
     * @return int e.g. 6 or timestamp
     * @access public
     */
    function nextWeek($format = 'n_in_month')
    {
        if ( method_exists($this->calendar,'nextWeek') ) {
            return $this->calendar->nextWeek($format);
        } else {
            require_once 'PEAR.php';
            PEAR::raiseError(
                'Cannot call thisWeek on Calendar object of type: '.
                get_class($this->calendar), 133, PEAR_ERROR_TRIGGER,
                E_USER_NOTICE, 'Calendar_Decorator::nextWeek()');
            return false;
        }
    }

    /**
     * Returns the value for the previous day
     * @param string return value format ['int' | 'timestamp' | 'object' | 'array']
     * @return int e.g. 10 or timestamp
     * @access public
     */
    function prevDay($format = 'int') {
        return $this->calendar->prevDay($format);
    }

    /**
     * Returns the value for this day
     * @param string return value format ['int' | 'timestamp' | 'object' | 'array']
     * @return int e.g. 11 or timestamp
     * @access public
     */
    function thisDay($format = 'int')
    {
        return $this->calendar->thisDay($format);
    }

    /**
     * Returns the value for the next day
     * @param string return value format ['int' | 'timestamp' | 'object' | 'array']
     * @return int e.g. 12 or timestamp
     * @access public
     */
    function nextDay($format = 'int')
    {
        return $this->calendar->nextDay($format);
    }

    /**
     * Returns the value for the previous hour
     * @param string return value format ['int' | 'timestamp' | 'object' | 'array']
     * @return int e.g. 13 or timestamp
     * @access public
     */
    function prevHour($format = 'int')
    {
        return $this->calendar->prevHour($format);
    }

    /**
     * Returns the value for this hour
     * @param string return value format ['int' | 'timestamp' | 'object' | 'array']
     * @return int e.g. 14 or timestamp
     * @access public
     */
    function thisHour($format = 'int')
    {
        return $this->calendar->thisHour($format);
    }

    /**
     * Returns the value for the next hour
     * @param string return value format ['int' | 'timestamp' | 'object' | 'array']
     * @return int e.g. 14 or timestamp
     * @access public
     */
    function nextHour($format = 'int')
    {
        return $this->calendar->nextHour($format);
    }

    /**
     * Returns the value for the previous minute
     * @param string return value format ['int' | 'timestamp' | 'object' | 'array']
     * @return int e.g. 23 or timestamp
     * @access public
     */
    function prevMinute($format = 'int')
    {
        return $this->calendar->prevMinute($format);
    }

    /**
     * Returns the value for this minute
     * @param string return value format ['int' | 'timestamp' | 'object' | 'array']
     * @return int e.g. 24 or timestamp
     * @access public
     */
    function thisMinute($format = 'int')
    {
        return $this->calendar->thisMinute($format);
    }

    /**
     * Returns the value for the next minute
     * @param string return value format ['int' | 'timestamp' | 'object' | 'array']
     * @return int e.g. 25 or timestamp
     * @access public
     */
   function nextMinute($format = 'int')
    {
        return $this->calendar->nextMinute($format);
    }

    /**
     * Returns the value for the previous second
     * @param string return value format ['int' | 'timestamp' | 'object' | 'array']
     * @return int e.g. 43 or timestamp
     * @access public
     */
    function prevSecond($format = 'int')
    {
        return $this->calendar->prevSecond($format);
    }

    /**
     * Returns the value for this second
     * @param string return value format ['int' | 'timestamp' | 'object' | 'array']
     * @return int e.g. 44 or timestamp
     * @access public
     */
    function thisSecond($format = 'int')
    {
        return $this->calendar->thisSecond($format);
    }

    /**
     * Returns the value for the next second
     * @param string return value format ['int' | 'timestamp' | 'object' | 'array']
     * @return int e.g. 45 or timestamp
     * @access public
     */
    function nextSecond($format = 'int')
    {
        return $this->calendar->nextSecond($format);
    }
}
?>