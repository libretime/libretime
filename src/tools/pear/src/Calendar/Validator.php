<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */

/**
 * Contains the Calendar_Validator class
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
 * @version   CVS: $Id: Validator.php,v 1.6 2007/11/28 19:42:33 quipo Exp $
 * @link      http://pear.php.net/package/Calendar
 */

/**
 * Validation Error Messages
 */
if (!defined('CALENDAR_VALUE_TOOSMALL')) {
    define('CALENDAR_VALUE_TOOSMALL', 'Too small: min = ');
}
if (!defined('CALENDAR_VALUE_TOOLARGE')) {
    define('CALENDAR_VALUE_TOOLARGE', 'Too large: max = ');
}

/**
 * Used to validate any given Calendar date object. Instances of this class
 * can be obtained from any data object using the getValidator method
 *
 * @category  Date and Time
 * @package   Calendar
 * @author    Harry Fuecks <hfuecks@phppatterns.com>
 * @copyright 2003-2007 Harry Fuecks
 * @license   http://www.debian.org/misc/bsd.license  BSD License (3 Clause)
 * @link      http://pear.php.net/package/Calendar
 * @see       Calendar::getValidator()
 * @access    public
 */
class Calendar_Validator
{
    /**
     * Instance of the Calendar date object to validate
     * @var object
     * @access private
     */
    var $calendar;

    /**
     * Instance of the Calendar_Engine
     * @var object
     * @access private
     */
    var $cE;

    /**
     * Array of errors for validation failures
     * @var array
     * @access private
     */
    var $errors = array();

    /**
     * Constructs Calendar_Validator
     *
     * @param object &$calendar subclass of Calendar
     *
     * @access public
     */
    function Calendar_Validator(&$calendar)
    {
        $this->calendar = & $calendar;
        $this->cE       = & $calendar->getEngine();
    }

    /**
     * Calls all the other isValidXXX() methods in the validator
     *
     * @return boolean
     * @access public
     */
    function isValid()
    {
        $checks = array('isValidYear', 'isValidMonth', 'isValidDay',
            'isValidHour', 'isValidMinute', 'isValidSecond');
        $valid  = true;
        foreach ($checks as $check) {
            if (!$this->{$check}()) {
                $valid = false;
            }
        }
        return $valid;
    }

    /**
     * Check whether this is a valid year
     *
     * @return boolean
     * @access public
     */
    function isValidYear()
    {
        $y   = $this->calendar->thisYear();
        $min = $this->cE->getMinYears();
        if ($min > $y) {
            $this->errors[] = new Calendar_Validation_Error(
                'Year', $y, CALENDAR_VALUE_TOOSMALL.$min);
            return false;
        }
        $max = $this->cE->getMaxYears();
        if ($y > $max) {
            $this->errors[] = new Calendar_Validation_Error(
                'Year', $y, CALENDAR_VALUE_TOOLARGE.$max);
            return false;
        }
        return true;
    }

    /**
     * Check whether this is a valid month
     *
     * @return boolean
     * @access public
     */
    function isValidMonth()
    {
        $m   = $this->calendar->thisMonth();
        $min = 1;
        if ($min > $m) {
            $this->errors[] = new Calendar_Validation_Error(
                'Month', $m, CALENDAR_VALUE_TOOSMALL.$min);
            return false;
        }
        $max = $this->cE->getMonthsInYear($this->calendar->thisYear());
        if ($m > $max) {
            $this->errors[] = new Calendar_Validation_Error(
                'Month', $m, CALENDAR_VALUE_TOOLARGE.$max);
            return false;
        }
        return true;
    }

    /**
     * Check whether this is a valid day
     *
     * @return boolean
     * @access public
     */
    function isValidDay()
    {
        $d   = $this->calendar->thisDay();
        $min = 1;
        if ($min > $d) {
            $this->errors[] = new Calendar_Validation_Error(
                'Day', $d, CALENDAR_VALUE_TOOSMALL.$min);
            return false;
        }
        $max = $this->cE->getDaysInMonth(
            $this->calendar->thisYear(), 
            $this->calendar->thisMonth()
        );
        if ($d > $max) {
            $this->errors[] = new Calendar_Validation_Error(
                'Day', $d, CALENDAR_VALUE_TOOLARGE.$max);
            return false;
        }
        return true;
    }

    /**
     * Check whether this is a valid hour
     *
     * @return boolean
     * @access public
     */
    function isValidHour()
    {
        $h   = $this->calendar->thisHour();
        $min = 0;
        if ($min > $h) {
            $this->errors[] = new Calendar_Validation_Error(
                'Hour', $h, CALENDAR_VALUE_TOOSMALL.$min);
            return false;
        }
        $max = ($this->cE->getHoursInDay($this->calendar->thisDay())-1);
        if ($h > $max) {
            $this->errors[] = new Calendar_Validation_Error(
                'Hour', $h, CALENDAR_VALUE_TOOLARGE.$max);
            return false;
        }
        return true;
    }

    /**
     * Check whether this is a valid minute
     *
     * @return boolean
     * @access public
     */
    function isValidMinute()
    {
        $i   = $this->calendar->thisMinute();
        $min = 0;
        if ($min > $i) {
            $this->errors[] = new Calendar_Validation_Error(
                'Minute', $i, CALENDAR_VALUE_TOOSMALL.$min);
            return false;
        }
        $max = ($this->cE->getMinutesInHour($this->calendar->thisHour())-1);
        if ($i > $max) {
            $this->errors[] = new Calendar_Validation_Error(
                'Minute', $i, CALENDAR_VALUE_TOOLARGE.$max);
            return false;
        }
        return true;
    }

    /**
     * Check whether this is a valid second
     *
     * @return boolean
     * @access public
     */
    function isValidSecond()
    {
        $s   = $this->calendar->thisSecond();
        $min = 0;
        if ($min > $s) {
            $this->errors[] = new Calendar_Validation_Error(
                'Second', $s, CALENDAR_VALUE_TOOSMALL.$min);
            return false;
        }
        $max = ($this->cE->getSecondsInMinute($this->calendar->thisMinute())-1);
        if ($s > $max) {
            $this->errors[] = new Calendar_Validation_Error(
                'Second', $s, CALENDAR_VALUE_TOOLARGE.$max);
            return false;
        }
        return true;
    }

    /**
     * Iterates over any validation errors
     *
     * @return mixed either Calendar_Validation_Error or false
     * @access public
     */
    function fetch()
    {
        $error = each($this->errors);
        if ($error) {
            return $error['value'];
        } else {
            reset($this->errors);
            return false;
        }
    }
}

/**
 * For Validation Error messages
 *
 * @category  Date and Time
 * @package   Calendar
 * @author    Harry Fuecks <hfuecks@phppatterns.com>
 * @copyright 2003-2007 Harry Fuecks
 * @license   http://www.debian.org/misc/bsd.license  BSD License (3 Clause)
 * @link      http://pear.php.net/package/Calendar
 * @see       Calendar::fetch()
 * @access    public
 */
class Calendar_Validation_Error
{
    /**
     * Date unit (e.g. month,hour,second) which failed test
     * @var string
     * @access private
     */
    var $unit;

    /**
     * Value of unit which failed test
     * @var int
     * @access private
     */
    var $value;

    /**
     * Validation error message
     * @var string
     * @access private
     */
    var $message;

    /**
     * Constructs Calendar_Validation_Error
     *
     * @param string $unit    Date unit (e.g. month,hour,second)
     * @param int    $value   Value of unit which failed test
     * @param string $message Validation error message
     *
     * @access protected
     */
    function Calendar_Validation_Error($unit, $value, $message)
    {
        $this->unit    = $unit;
        $this->value   = $value;
        $this->message = $message;
    }

    /**
     * Returns the Date unit
     *
     * @return string
     * @access public
     */
    function getUnit()
    {
        return $this->unit;
    }

    /**
     * Returns the value of the unit
     *
     * @return int
     * @access public
     */
    function getValue()
    {
        return $this->value;
    }

    /**
     * Returns the validation error message
     *
     * @return string
     * @access public
     */
    function getMessage()
    {
        return $this->message;
    }

    /**
     * Returns a string containing the unit, value and error message
     *
     * @return string
     * @access public
     */
    function toString ()
    {
        return $this->unit.' = '.$this->value.' ['.$this->message.']';
    }
}
?>