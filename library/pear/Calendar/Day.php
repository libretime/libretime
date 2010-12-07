<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */

/**
 * Contains the Calendar_Day class
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
 * @version   CVS: $Id: Day.php 300729 2010-06-24 12:05:53Z quipo $
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
 * Represents a Day and builds Hours.
 * <code>
 * require_once 'Calendar/Day.php';
 * $Day = new Calendar_Day(2003, 10, 21); // Oct 21st 2003
 * while ($Hour = & $Day->fetch()) {
 *    echo $Hour->thisHour().'<br />';
 * }
 * </code>
 *
 * @category  Date and Time
 * @package   Calendar
 * @author    Harry Fuecks <hfuecks@phppatterns.com>
 * @copyright 2003-2007 Harry Fuecks
 * @license   http://www.debian.org/misc/bsd.license  BSD License (3 Clause)
 * @link      http://pear.php.net/package/Calendar
 * @access    public
 */
class Calendar_Day extends Calendar
{
    /**
     * Marks the Day at the beginning of a week
     * @access private
     * @var boolean
     */
    var $first = false;

    /**
     * Marks the Day at the end of a week
     * @access private
     * @var boolean
     */
    var $last = false;


    /**
     * Used for tabular calendars
     * @access private
     * @var boolean
     */
    var $empty = false;

    /**
     * Constructs Calendar_Day
     *
     * @param int $y year e.g. 2003
     * @param int $m month e.g. 8
     * @param int $d day e.g. 15
     *
     * @access public
     */
    function Calendar_Day($y, $m, $d)
    {
        parent::Calendar($y, $m, $d);
    }

    /**
     * Builds the Hours of the Day
     *
     * @param array $sDates (optional) Caledar_Hour objects representing selected dates
     *
     * @return boolean
     * @access public
     */
    function build($sDates = array())
    {
        include_once CALENDAR_ROOT.'Hour.php';

        $hID = $this->cE->getHoursInDay($this->year, $this->month, $this->day);
        for ($i=0; $i < $hID; $i++) {
            $this->children[$i] =
                new Calendar_Hour($this->year, $this->month, $this->day, $i);
        }
        if (count($sDates) > 0) {
            $this->setSelection($sDates);
        }
        return true;
    }

    /**
     * Called from build()
     *
     * @param array $sDates dates to be selected
     *
     * @return void
     * @access private
     */
    function setSelection($sDates)
    {
        foreach ($sDates as $sDate) {
            if ($this->year == $sDate->thisYear()
                && $this->month == $sDate->thisMonth()
                && $this->day == $sDate->thisDay())
            {
                $key = (int)$sDate->thisHour();
                if (isset($this->children[$key])) {
                    $sDate->setSelected();
                    $this->children[$key] = $sDate;
                }
            }
        }
    }

    /**
     * Defines Day object as first in a week
     * Only used by Calendar_Month_Weekdays::build()
     *
     * @param boolean $state set this day as first in week
     *
     * @return void
     * @access private
     */
    function setFirst($state = true)
    {
        $this->first = $state;
    }

    /**
     * Defines Day object as last in a week
     * Used only following Calendar_Month_Weekdays::build()
     *
     * @param boolean $state set this day as last in week
     *
     * @return void
     * @access private
     */
    function setLast($state = true)
    {
        $this->last = $state;
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
        return $this->first;
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
        return $this->last;
    }

    /**
     * Defines Day object as empty
     * Only used by Calendar_Month_Weekdays::build()
     *
     * @param boolean $state set this day as empty
     *
     * @return void
     * @access private
     */
    function setEmpty ($state = true)
    {
        $this->empty = $state;
    }

    /**
     * Check if this day is empty
     *
     * @return boolean
     * @access public
     */
    function isEmpty()
    {
        return $this->empty;
    }
}
?>