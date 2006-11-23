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
// $Id: Weekdays.php,v 1.4 2005/10/22 10:28:49 quipo Exp $
//
/**
 * @package Calendar
 * @version $Id: Weekdays.php,v 1.4 2005/10/22 10:28:49 quipo Exp $
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
 * $Month = & new Calendar_Month_Weekdays(2003, 10); // Oct 2003
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
 * @package Calendar
 * @access public
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
     * @param int year e.g. 2003
     * @param int month e.g. 5
     * @param int (optional) first day of week (e.g. 0 for Sunday, 2 for Tuesday etc.)
     * @access public
     */
    function Calendar_Month_Weekdays($y, $m, $firstDay=null)
    {
        Calendar_Month::Calendar_Month($y, $m, $firstDay);
    }

    /**
     * Builds Day objects in tabular form, to allow display of calendar month
     * with empty cells if the first day of the week does not fall on the first
     * day of the month.
     * @see Calendar_Day::isEmpty()
     * @see Calendar_Day_Base::isFirst()
     * @see Calendar_Day_Base::isLast()
     * @param array (optional) Calendar_Day objects representing selected dates
     * @return boolean
     * @access public
     */
    function build($sDates=array())
    {
        require_once CALENDAR_ROOT.'Table/Helper.php';
        $this->tableHelper = & new Calendar_Table_Helper($this, $this->firstDay);
        Calendar_Month::build($sDates);
        $this->buildEmptyDaysBefore();
        $this->shiftDays();
        $this->buildEmptyDaysAfter();
        $this->setWeekMarkers();
        return true;
    }

    /**
     * Prepends empty days before the real days in the month
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
     * @return void
     * @access private
     */
    function shiftDays()
    {
        if (isset ($this->children[0])) {
            array_unshift($this->children, null);
            unset($this->children[0]);
        }
    }

    /**
     * Appends empty days after the real days in the month
     * @return void
     * @access private
     */
    function buildEmptyDaysAfter()
    {
        $eAfter = $this->tableHelper->getEmptyDaysAfter();
        $sDOM = $this->tableHelper->getNumTableDaysInMonth();
        for ($i = 1; $i <= $sDOM-$eAfter; $i++) {
            $Day = new Calendar_Day($this->year, $this->month+1, $i);
            $Day->setEmpty();
            $Day->adjust();
            array_push($this->children, $Day);
        }
    }

    /**
     * Sets the "markers" for the beginning and of a of week, in the
     * built Calendar_Day children
     * @return void
     * @access private
     */
    function setWeekMarkers()
    {
        $dIW  = $this->cE->getDaysInWeek(
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