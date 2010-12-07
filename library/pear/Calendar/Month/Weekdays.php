<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */

/**
 * Contains the Calendar_Month_Weekdays class
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
 * @version   CVS: $Id: Weekdays.php 300729 2010-06-24 12:05:53Z quipo $
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
 * Load base month
 */
require_once CALENDAR_ROOT.'Month.php';

/**
 * Represents a Month and builds Days in tabular form<br>
 * <code>
 * require_once 'Calendar/Month/Weekdays.php';
 * $Month = new Calendar_Month_Weekdays(2003, 10); // Oct 2003
 * $Month->build(); // Build Calendar_Day objects
 * while ($Day = & $Month->fetch()) {
 *     if ($Day->isFirst()) {
 *         echo '<tr>';
 *     }
 *     if ($Day->isEmpty()) {
 *         echo '<td>&nbsp;</td>';
 *     } else {
 *         echo '<td>'.$Day->thisDay().'</td>';
 *     }
 *     if ($Day->isLast()) {
 *         echo '</tr>';
 *     }
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
class Calendar_Month_Weekdays extends Calendar_Month
{
    /**
     * Instance of Calendar_Table_Helper
     * @var Calendar_Table_Helper
     * @access private
     */
    var $tableHelper;

    /**
     * First day of the week
     * @access private
     * @var string
     */
    var $firstDay;

    /**
     * Constructs Calendar_Month_Weekdays
     *
     * @param int $y        year e.g. 2003
     * @param int $m        month e.g. 5
     * @param int $firstDay (optional) first day of week (e.g. 0 for Sunday, 2 for Tuesday etc.)
     *
     * @access public
     */
    function Calendar_Month_Weekdays($y, $m, $firstDay=null)
    {
        parent::Calendar_Month($y, $m, $firstDay);
    }

    /**
     * Builds Day objects in tabular form, to allow display of calendar month
     * with empty cells if the first day of the week does not fall on the first
     * day of the month.
     *
     * @param array $sDates (optional) Calendar_Day objects representing selected dates
     *
     * @return boolean
     * @access public
     * @see Calendar_Day::isEmpty()
     * @see Calendar_Day_Base::isFirst()
     * @see Calendar_Day_Base::isLast()
     */
    function build($sDates = array())
    {
        include_once CALENDAR_ROOT.'Table/Helper.php';
        $this->tableHelper = new Calendar_Table_Helper($this, $this->firstDay);
        Calendar_Month::build($sDates);
        $this->buildEmptyDaysBefore();
        $this->shiftDays();
        $this->buildEmptyDaysAfter();
        $this->setWeekMarkers();
        return true;
    }

    /**
     * Prepends empty days before the real days in the month
     *
     * @return void
     * @access private
     */
    function buildEmptyDaysBefore()
    {
        $eBefore = $this->tableHelper->getEmptyDaysBefore();
        for ($i=0; $i < $eBefore; $i++) {
            $stamp = $this->cE->dateToStamp($this->year, $this->month, -$i);
            $Day = new Calendar_Day(
                                $this->cE->stampToYear($stamp),
                                $this->cE->stampToMonth($stamp),
                                $this->cE->stampToDay($stamp));
            $Day->setEmpty();
            $Day->adjust();
            array_unshift($this->children, $Day);
        }
    }

    /**
     * Shifts the array of children forward, if necessary
     *
     * @return void
     * @access private
     */
    function shiftDays()
    {
        if (isset($this->children[0])) {
            array_unshift($this->children, null);
            unset($this->children[0]);
        }
    }

    /**
     * Appends empty days after the real days in the month
     *
     * @return void
     * @access private
     */
    function buildEmptyDaysAfter()
    {
        $eAfter = $this->tableHelper->getEmptyDaysAfter();
        $sDOM   = $this->tableHelper->getNumTableDaysInMonth();
        for ($i=1; $i <= $sDOM-$eAfter; $i++) {
            $Day = new Calendar_Day($this->year, $this->month+1, $i);
            $Day->setEmpty();
            $Day->adjust();
            array_push($this->children, $Day);
        }
    }

    /**
     * Sets the "markers" for the beginning and of a of week, in the
     * built Calendar_Day children
     *
     * @return void
     * @access private
     */
    function setWeekMarkers()
    {
        $dIW = $this->cE->getDaysInWeek(
            $this->thisYear(),
            $this->thisMonth(),
            $this->thisDay()
        );
        $sDOM = $this->tableHelper->getNumTableDaysInMonth();
        for ($i=1; $i <= $sDOM; $i+= $dIW) {
            $this->children[$i]->setFirst();
            $this->children[$i+($dIW-1)]->setLast();
        }
    }
}
?>