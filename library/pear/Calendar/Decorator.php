<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */

/**
 * Contains the Calendar_Decorator class
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
 * @copyright 2003-2007 Harry Fuecks
 * @license   http://www.debian.org/misc/bsd.license  BSD License (3 Clause)
 * @version   CVS: $Id: Decorator.php 300729 2010-06-24 12:05:53Z quipo $
 * @link      http://pear.php.net/package/Calendar
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
 * $Day = new Calendar_Day(2003, 10, 25);
 * $DayDecorator = new DayDecorator($Day);
 * echo $DayDecorator->thisDay(); // Outputs "Sat"
 * </code>
 *
 * @category  Date and Time
 * @package   Calendar
 * @author    Harry Fuecks <hfuecks@phppatterns.com>
 * @copyright 2003-2007 Harry Fuecks
 * @license   http://www.debian.org/misc/bsd.license  BSD License (3 Clause)
 * @link      http://pear.php.net/package/Calendar
 * @abstract
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
     *
     * @param object &$calendar subclass to Calendar to decorate
     */
    function Calendar_Decorator(&$calendar)
    {
        $this->calendar = & $calendar;
    }

    /**
     * Defines the calendar by a Unix timestamp, replacing values
     * passed to the constructor
     *
     * @param int $ts Unix timestamp
     *
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
     *
     * @return int $ts timestamp
     * @access public
     */
    function getTimestamp()
    {
        return $this->calendar->getTimeStamp();
    }

    /**
     * Defines calendar object as selected (e.g. for today)
     *
     * @param boolean $state whether Calendar subclass must be selected
     *
     * @return void
     * @access public
     */
    function setSelected($state = true)
    {
        $this->calendar->setSelected($state = true);
    }

    /**
     * True if the calendar subclass object is selected (e.g. today)
     *
     * @return boolean
     * @access public
     */
    function isSelected()
    {
        return $this->calendar->isSelected();
    }

    /**
     * Adjusts the date (helper method)
     *
     * @return void
     * @access public
     */
    function adjust()
    {
        $this->calendar->adjust();
    }

    /**
     * Returns the date as an associative array (helper method)
     *
     * @param mixed $stamp timestamp (leave empty for current timestamp)
     *
     * @return array
     * @access public
     */
    function toArray($stamp = null)
    {
        return $this->calendar->toArray($stamp);
    }

    /**
     * Returns the value as an associative array (helper method)
     *
     * @param string  $returnType type of date object that return value represents
     * @param string  $format     ['int'|'timestamp'|'object'|'array']
     * @param mixed   $stamp      timestamp (depending on Calendar engine being used)
     * @param integer $default    default value (i.e. give me the answer quick)
     *
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
     *
     * @param boolean $state whether it's first or not
     *
     * @return void
     * @access private
     */
    function setFirst($state = true)
    {
        if (method_exists($this->calendar, 'setFirst')) {
            $this->calendar->setFirst($state);
        }
    }

    /**
     * Defines Day object as last in a week
     * Used only following Calendar_Month_Weekdays::build()
     *
     * @param boolean $state whether it's last or not
     *
     * @return void
     * @access private
     */
    function setLast($state = true)
    {
        if (method_exists($this->calendar, 'setLast')) {
            $this->calendar->setLast($state);
        }
    }

    /**
     * Returns true if Day object is first in a Week
     * Only relevant when Day is created by Calendar_Month_Weekdays::build()
     *
     * @return boolean
     * @access public
     */
    function isFirst()
    {
        if (method_exists($this->calendar, 'isFirst')) {
            return $this->calendar->isFirst();
        }
    }

    /**
     * Returns true if Day object is last in a Week
     * Only relevant when Day is created by Calendar_Month_Weekdays::build()
     *
     * @return boolean
     * @access public
     */
    function isLast()
    {
        if (method_exists($this->calendar, 'isLast')) {
            return $this->calendar->isLast();
        }
    }

    /**
     * Defines Day object as empty
     * Only used by Calendar_Month_Weekdays::build()
     *
     * @param boolean $state whether it's empty or not
     *
     * @return void
     * @access private
     */
    function setEmpty ($state = true)
    {
        if (method_exists($this->calendar, 'setEmpty')) {
            $this->calendar->setEmpty($state);
        }
    }

    /**
     * Check if the current object is empty
     *
     * @return boolean
     * @access public
     */
    function isEmpty()
    {
        if (method_exists($this->calendar, 'isEmpty')) {
            return $this->calendar->isEmpty();
        }
    }

    /**
     * Build the children
     *
     * @param array $sDates array containing Calendar objects to select (optional)
     *
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
     *
     * @return mixed either an object subclass of Calendar or false
     * @access public
     */
    function fetch()
    {
        return $this->calendar->fetch();
    }

    /**
     * Fetches all child from the current collection of children
     *
     * @return array
     * @access public
     */
    function fetchAll()
    {
        return $this->calendar->fetchAll();
    }

    /**
     * Get the number Calendar subclass objects stored in the internal collection
     *
     * @return int
     * @access public
     */
    function size()
    {
        return $this->calendar->size();
    }

    /**
     * Determine whether this date is valid, with the bounds determined by
     * the Calendar_Engine. The call is passed on to Calendar_Validator::isValid
     *
     * @return boolean
     * @access public
     */
    function isValid()
    {
        return $this->calendar->isValid();
    }

    /**
     * Returns an instance of Calendar_Validator
     *
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
     *
     * @return object implementing Calendar_Engine_Inteface
     * @access private
     */
    function & getEngine()
    {
        $engine = $this->calendar->getEngine();
        return $engine;
    }

    /**
     * Returns the value for the previous year
     *
     * @param string $format return value format ['int'|'timestamp'|'object'|'array']
     *
     * @return int e.g. 2002 or timestamp
     * @access public
     */
    function prevYear($format = 'int')
    {
        return $this->calendar->prevYear($format);
    }

    /**
     * Returns the value for this year
     *
     * @param string $format return value format ['int'|'timestamp'|'object'|'array']
     *
     * @return int e.g. 2003 or timestamp
     * @access public
     */
    function thisYear($format = 'int')
    {
        return $this->calendar->thisYear($format);
    }

    /**
     * Returns the value for next year
     *
     * @param string $format return value format ['int'|'timestamp'|'object'|'array']
     *
     * @return int e.g. 2004 or timestamp
     * @access public
     */
    function nextYear($format = 'int')
    {
        return $this->calendar->nextYear($format);
    }

    /**
     * Returns the value for the previous month
     *
     * @param string $format return value format ['int'|'timestamp'|'object'|'array']
     *
     * @return int e.g. 4 or Unix timestamp
     * @access public
      */
    function prevMonth($format = 'int')
    {
        return $this->calendar->prevMonth($format);
    }

    /**
     * Returns the value for this month
     *
     * @param string $format return value format ['int'|'timestamp'|'object'|'array']
     *
     * @return int e.g. 5 or timestamp
     * @access public
     */
    function thisMonth($format = 'int')
    {
        return $this->calendar->thisMonth($format);
    }

    /**
     * Returns the value for next month
     *
     * @param string $format return value format ['int'|'timestamp'|'object'|'array']
     *
     * @return int e.g. 6 or timestamp
     * @access public
     */
    function nextMonth($format = 'int')
    {
        return $this->calendar->nextMonth($format);
    }

    /**
     * Returns the value for the previous week
     *
     * @param string $format return value format ['int'|'timestamp'|'object'|'array']
     *
     * @return int e.g. 4 or Unix timestamp
     * @access public
      */
    function prevWeek($format = 'n_in_month')
    {
        if ( method_exists($this->calendar, 'prevWeek')) {
            return $this->calendar->prevWeek($format);
        } else {
            include_once 'PEAR.php';
            PEAR::raiseError(
                'Cannot call prevWeek on Calendar object of type: '.
                get_class($this->calendar), 133, PEAR_ERROR_TRIGGER,
                E_USER_NOTICE, 'Calendar_Decorator::prevWeek()');
            return false;
        }
    }

    /**
     * Returns the value for this week
     *
     * @param string $format return value format ['int'|'timestamp'|'object'|'array']
     *
     * @return int e.g. 5 or timestamp
     * @access public
     */
    function thisWeek($format = 'n_in_month')
    {
        if ( method_exists($this->calendar, 'thisWeek')) {
            return $this->calendar->thisWeek($format);
        } else {
            include_once 'PEAR.php';
            PEAR::raiseError(
                'Cannot call thisWeek on Calendar object of type: '.
                get_class($this->calendar), 133, PEAR_ERROR_TRIGGER,
                E_USER_NOTICE, 'Calendar_Decorator::thisWeek()');
            return false;
        }
    }

    /**
     * Returns the value for next week
     *
     * @param string $format return value format ['int'|'timestamp'|'object'|'array']
     *
     * @return int e.g. 6 or timestamp
     * @access public
     */
    function nextWeek($format = 'n_in_month')
    {
        if ( method_exists($this->calendar, 'nextWeek')) {
            return $this->calendar->nextWeek($format);
        } else {
            include_once 'PEAR.php';
            PEAR::raiseError(
                'Cannot call thisWeek on Calendar object of type: '.
                get_class($this->calendar), 133, PEAR_ERROR_TRIGGER,
                E_USER_NOTICE, 'Calendar_Decorator::nextWeek()');
            return false;
        }
    }

    /**
     * Returns the value for the previous day
     *
     * @param string $format return value format ['int'|'timestamp'|'object'|'array']
     *
     * @return int e.g. 10 or timestamp
     * @access public
     */
    function prevDay($format = 'int')
    {
        return $this->calendar->prevDay($format);
    }

    /**
     * Returns the value for this day
     *
     * @param string $format return value format ['int'|'timestamp'|'object'|'array']
     *
     * @return int e.g. 11 or timestamp
     * @access public
     */
    function thisDay($format = 'int')
    {
        return $this->calendar->thisDay($format);
    }

    /**
     * Returns the value for the next day
     *
     * @param string $format return value format ['int'|'timestamp'|'object'|'array']
     *
     * @return int e.g. 12 or timestamp
     * @access public
     */
    function nextDay($format = 'int')
    {
        return $this->calendar->nextDay($format);
    }

    /**
     * Returns the value for the previous hour
     *
     * @param string $format return value format ['int'|'timestamp'|'object'|'array']
     *
     * @return int e.g. 13 or timestamp
     * @access public
     */
    function prevHour($format = 'int')
    {
        return $this->calendar->prevHour($format);
    }

    /**
     * Returns the value for this hour
     *
     * @param string $format return value format ['int'|'timestamp'|'object'|'array']
     *
     * @return int e.g. 14 or timestamp
     * @access public
     */
    function thisHour($format = 'int')
    {
        return $this->calendar->thisHour($format);
    }

    /**
     * Returns the value for the next hour
     *
     * @param string $format return value format ['int'|'timestamp'|'object'|'array']
     *
     * @return int e.g. 14 or timestamp
     * @access public
     */
    function nextHour($format = 'int')
    {
        return $this->calendar->nextHour($format);
    }

    /**
     * Returns the value for the previous minute
     *
     * @param string $format return value format ['int'|'timestamp'|'object'|'array']
     *
     * @return int e.g. 23 or timestamp
     * @access public
     */
    function prevMinute($format = 'int')
    {
        return $this->calendar->prevMinute($format);
    }

    /**
     * Returns the value for this minute
     *
     * @param string $format return value format ['int'|'timestamp'|'object'|'array']
     *
     * @return int e.g. 24 or timestamp
     * @access public
     */
    function thisMinute($format = 'int')
    {
        return $this->calendar->thisMinute($format);
    }

    /**
     * Returns the value for the next minute
     *
     * @param string $format return value format ['int'|'timestamp'|'object'|'array']
     *
     * @return int e.g. 25 or timestamp
     * @access public
     */
    function nextMinute($format = 'int')
    {
        return $this->calendar->nextMinute($format);
    }

    /**
     * Returns the value for the previous second
     *
     * @param string $format return value format ['int'|'timestamp'|'object'|'array']
     *
     * @return int e.g. 43 or timestamp
     * @access public
     */
    function prevSecond($format = 'int')
    {
        return $this->calendar->prevSecond($format);
    }

    /**
     * Returns the value for this second
     *
     * @param string $format return value format ['int'|'timestamp'|'object'|'array']
     *
     * @return int e.g. 44 or timestamp
     * @access public
     */
    function thisSecond($format = 'int')
    {
        return $this->calendar->thisSecond($format);
    }

    /**
     * Returns the value for the next second
     *
     * @param string $format return value format ['int'|'timestamp'|'object'|'array']
     *
     * @return int e.g. 45 or timestamp
     * @access public
     */
    function nextSecond($format = 'int')
    {
        return $this->calendar->nextSecond($format);
    }
}
?>