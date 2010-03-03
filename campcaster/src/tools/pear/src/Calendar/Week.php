<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Contains the Calendar_Week class
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
 * @version   CVS: $Id: Week.php,v 1.14 2007/11/18 21:46:42 quipo Exp $
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
 * Represents a Week and builds Days in tabular format<br>
 * <code>
 * require_once 'Calendar/Week.php';
 * $Week = & new Calendar_Week(2003, 10, 1); Oct 2003, 1st tabular week
 * echo '<tr>';
 * while ($Day = & $Week->fetch()) {
 *     if ($Day->isEmpty()) {
 *         echo '<td>&nbsp;</td>';
 *     } else {
 *         echo '<td>'.$Day->thisDay().'</td>';
 *      }
 * }
 * echo '</tr>';
 * </code>
 *
 * @category  Date and Time
 * @package   Calendar
 * @author    Harry Fuecks <hfuecks@phppatterns.com>
 * @author    Lorenzo Alberton <l.alberton@quipo.it>
 * @copyright 2003-2007 Harry Fuecks, Lorenzo Alberton
 * @license   http://www.debian.org/misc/bsd.license  BSD License (3 Clause)
 * @link      http://pear.php.net/package/Calendar
 */
class Calendar_Week extends Calendar
{
    /**
     * Instance of Calendar_Table_Helper
     * @var Calendar_Table_Helper
     * @access private
     */
    var $tableHelper;

    /**
     * Stores the timestamp of the first day of this week
     * @access private
     * @var object
     */
    var $thisWeek;

    /**
     * Stores the timestamp of first day of previous week
     * @access private
     * @var object
     */
    var $prevWeek;

    /**
     * Stores the timestamp of first day of next week
     * @access private
     * @var object
     */
    var $nextWeek;

    /**
     * Used by build() to set empty days
     * @access private
     * @var boolean
     */
    var $firstWeek = false;

    /**
     * Used by build() to set empty days
     * @access private
     * @var boolean
     */
    var $lastWeek = false;

    /**
     * First day of the week (0=sunday, 1=monday...)
     * @access private
     * @var boolean
     */
    var $firstDay = 1;

    /**
     * Constructs Week
     *
     * @param int $y        year e.g. 2003
     * @param int $m        month e.g. 5
     * @param int $d        a day of the desired week
     * @param int $firstDay (optional) first day of week (e.g. 0 for Sunday, 2 for Tuesday etc.)
     *
     * @access public
     */
    function Calendar_Week($y, $m, $d, $firstDay = null)
    {
        include_once CALENDAR_ROOT.'Table/Helper.php';
        Calendar::Calendar($y, $m, $d);
        $this->firstDay    = $this->defineFirstDayOfWeek($firstDay);
        $this->tableHelper = & new Calendar_Table_Helper($this, $this->firstDay);
        $this->thisWeek    = $this->tableHelper->getWeekStart($y, $m, $d, $this->firstDay);
        $this->prevWeek    = $this->tableHelper->getWeekStart(
            $y, 
            $m, 
            $d - $this->cE->getDaysInWeek(
                $this->thisYear(),
                $this->thisMonth(),
                $this->thisDay()
            ), 
            $this->firstDay
        );
        $this->nextWeek = $this->tableHelper->getWeekStart(
            $y, 
            $m, 
            $d + $this->cE->getDaysInWeek(
                $this->thisYear(),
                $this->thisMonth(),
                $this->thisDay()
            ), 
            $this->firstDay
        );
    }

    /**
     * Defines the calendar by a timestamp (Unix or ISO-8601), replacing values
     * passed to the constructor
     *
     * @param int|string $ts Unix or ISO-8601 timestamp
     *
     * @return void
     * @access public
     */
    function setTimestamp($ts)
    {
        parent::setTimestamp($ts);
        $this->thisWeek = $this->tableHelper->getWeekStart(
            $this->year, $this->month, $this->day, $this->firstDay
        );
        $this->prevWeek = $this->tableHelper->getWeekStart(
            $this->year, 
            $this->month, 
            $this->day - $this->cE->getDaysInWeek(
                $this->thisYear(),
                $this->thisMonth(),
                $this->thisDay()
            ), 
            $this->firstDay
        );
        $this->nextWeek = $this->tableHelper->getWeekStart(
            $this->year, 
            $this->month, 
            $this->day + $this->cE->getDaysInWeek(
                $this->thisYear(),
                $this->thisMonth(),
                $this->thisDay()
            ), 
            $this->firstDay
        );
    }

    /**
     * Builds Calendar_Day objects for this Week
     *
     * @param array $sDates (optional) Calendar_Day objects representing selected dates
     *
     * @return boolean
     * @access public
     */
    function build($sDates = array())
    {
        include_once CALENDAR_ROOT.'Day.php';
        $year  = $this->cE->stampToYear($this->thisWeek);
        $month = $this->cE->stampToMonth($this->thisWeek);
        $day   = $this->cE->stampToDay($this->thisWeek);
        $end   = $this->cE->getDaysInWeek(
            $this->thisYear(),
            $this->thisMonth(),
            $this->thisDay()
        );

        for ($i=1; $i <= $end; $i++) {
            $stamp = $this->cE->dateToStamp($year, $month, $day++);
            $this->children[$i] = new Calendar_Day(
                $this->cE->stampToYear($stamp),
                $this->cE->stampToMonth($stamp),
                $this->cE->stampToDay($stamp)
            );
        }

        //set empty days (@see Calendar_Month_Weeks::build())
        if ($this->firstWeek) {
            $eBefore = $this->tableHelper->getEmptyDaysBefore();
            for ($i=1; $i <= $eBefore; $i++) {
                $this->children[$i]->setEmpty();
            }
        }
        if ($this->lastWeek) {
            $eAfter = $this->tableHelper->getEmptyDaysAfterOffset();
            for ($i = $eAfter+1; $i <= $end; $i++) {
                $this->children[$i]->setEmpty();
            }
        }

        if (count($sDates) > 0) {
            $this->setSelection($sDates);
        }
        return true;
    }

    /**
     * Set as first week of the month
     *
     * @param boolean $state whether it's first or not
     *
     * @return void
     * @access private
     */
    function setFirst($state = true)
    {
        $this->firstWeek = $state;
    }

    /**
     * Set as last week of the month
     *
     * @param boolean $state whether it's lasst or not
     *
     * @return void
     * @access private
     */
    function setLast($state = true)
    {
        $this->lastWeek = $state;
    }

    /**
     * Called from build()
     *
     * @param array $sDates Calendar_Day objects representing selected dates
     *
     * @return void
     * @access private
     */
    function setSelection($sDates)
    {
        foreach ($sDates as $sDate) {
            foreach ($this->children as $key => $child) {
                if ($child->thisDay() == $sDate->thisDay() &&
                    $child->thisMonth() == $sDate->thisMonth() &&
                    $child->thisYear() == $sDate->thisYear()
                ) {
                    $this->children[$key] = $sDate;
                    $this->children[$key]->setSelected();
                }
            }
        }
        reset($this->children);
    }

    /**
     * Returns the value for this year
     *
     * When a on the first/last week of the year, the year of the week is
     * calculated according to ISO-8601
     *
     * @param string $format return value format ['int' | 'timestamp' | 'object' | 'array']
     *
     * @return int e.g. 2003 or timestamp
     * @access public
     */
    function thisYear($format = 'int')
    {
        $tmp_cal = new Calendar();
        $tmp_cal->setTimestamp($this->thisWeek);
        $first_dow = $tmp_cal->thisDay('array');
        $days_in_week = $tmp_cal->cE->getDaysInWeek($tmp_cal->year, $tmp_cal->month, $tmp_cal->day);
        $tmp_cal->day += $days_in_week;
        $last_dow  = $tmp_cal->thisDay('array');

        if ($first_dow['year'] == $last_dow['year']) {
            return $first_dow['year'];
        }

        if ($last_dow['day'] > floor($days_in_week / 2)) {
            return $last_dow['year'];
        }
        return $first_dow['year'];
    }

    /**
     * Gets the value of the previous week, according to the requested format
     *
     * @param string $format ['timestamp' | 'n_in_month' | 'n_in_year' | 'array']
     *
     * @return mixed
     * @access public
     */
    function prevWeek($format = 'n_in_month')
    {
        switch (strtolower($format)) {
        case 'int':
        case 'n_in_month':
            return ($this->firstWeek) ? null : $this->thisWeek('n_in_month') -1;
        case 'n_in_year':
            return $this->cE->getWeekNInYear(
                $this->cE->stampToYear($this->prevWeek),
                $this->cE->stampToMonth($this->prevWeek),
                $this->cE->stampToDay($this->prevWeek));
        case 'array':
            return $this->toArray($this->prevWeek);
        case 'object':
            include_once CALENDAR_ROOT.'Factory.php';
            return Calendar_Factory::createByTimestamp('Week', $this->prevWeek);
        case 'timestamp':
        default:
            return $this->prevWeek;
        }
    }

    /**
     * Gets the value of the current week, according to the requested format
     *
     * @param string $format ['timestamp' | 'n_in_month' | 'n_in_year' | 'array']
     *
     * @return mixed
     * @access public
     */
    function thisWeek($format = 'n_in_month')
    {
        switch (strtolower($format)) {
        case 'int':
        case 'n_in_month':
            if ($this->firstWeek) {
                return 1;
            }
            if ($this->lastWeek) {
                return $this->cE->getWeeksInMonth(
                    $this->thisYear(),
                    $this->thisMonth(),
                    $this->firstDay);
            }
            return $this->cE->getWeekNInMonth(
                $this->thisYear(),
                $this->thisMonth(),
                $this->thisDay(),
                $this->firstDay);
        case 'n_in_year':
            return $this->cE->getWeekNInYear(
                $this->cE->stampToYear($this->thisWeek),
                $this->cE->stampToMonth($this->thisWeek),
                $this->cE->stampToDay($this->thisWeek));
        case 'array':
            return $this->toArray($this->thisWeek);
        case 'object':
            include_once CALENDAR_ROOT.'Factory.php';
            return Calendar_Factory::createByTimestamp('Week', $this->thisWeek);
        case 'timestamp':
        default:
            return $this->thisWeek;
        }
    }

    /**
     * Gets the value of the following week, according to the requested format
     *
     * @param string $format ['timestamp' | 'n_in_month' | 'n_in_year' | 'array']
     *
     * @return mixed
     * @access public
     */
    function nextWeek($format = 'n_in_month')
    {
        switch (strtolower($format)) {
        case 'int':
        case 'n_in_month':
            return ($this->lastWeek) ? null : $this->thisWeek('n_in_month') +1;
        case 'n_in_year':
            return $this->cE->getWeekNInYear(
                $this->cE->stampToYear($this->nextWeek),
                $this->cE->stampToMonth($this->nextWeek),
                $this->cE->stampToDay($this->nextWeek));
        case 'array':
            return $this->toArray($this->nextWeek);
        case 'object':
            include_once CALENDAR_ROOT.'Factory.php';
            return Calendar_Factory::createByTimestamp('Week', $this->nextWeek);
        case 'timestamp':
        default:
            return $this->nextWeek;
        }
    }

    /**
     * Returns the instance of Calendar_Table_Helper.
     * Called from Calendar_Validator::isValidWeek
     *
     * @return Calendar_Table_Helper
     * @access protected
     */
    function & getHelper()
    {
        return $this->tableHelper;
    }

    /**
     * Makes sure theres a value for $this->day
     *
     * @return void
     * @access private
     */
    function findFirstDay()
    {
        if (!count($this->children) > 0) {
            $this->build();
            foreach ($this->children as $Day) {
                if (!$Day->isEmpty()) {
                    $this->day = $Day->thisDay();
                    break;
                }
            }
        }
    }
}
?>