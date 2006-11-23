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
// $Id: Week.php,v 1.7 2005/10/22 10:26:49 quipo Exp $
//
/**
 * @package Calendar
 * @version $Id: Week.php,v 1.7 2005/10/22 10:26:49 quipo Exp $
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
 * require_once 'Calendar'.DIRECTORY_SEPARATOR.'Week.php';
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
 * @package Calendar
 * @access public
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
     * @param int year e.g. 2003
     * @param int month e.g. 5
     * @param int a day of the desired week
     * @param int (optional) first day of week (e.g. 0 for Sunday, 2 for Tuesday etc.)
     * @access public
     */
    function Calendar_Week($y, $m, $d, $firstDay=null)
    {
        require_once CALENDAR_ROOT.'Table/Helper.php';
        Calendar::Calendar($y, $m, $d);
        $this->firstDay = $this->defineFirstDayOfWeek($firstDay);
        $this->tableHelper = & new Calendar_Table_Helper($this, $this->firstDay);
        $this->thisWeek = $this->tableHelper->getWeekStart($y, $m, $d, $this->firstDay);
        $this->prevWeek = $this->tableHelper->getWeekStart($y, $m, $d - $this->cE->getDaysInWeek(
            $this->thisYear(),
            $this->thisMonth(),
            $this->thisDay()), $this->firstDay);
        $this->nextWeek = $this->tableHelper->getWeekStart($y, $m, $d + $this->cE->getDaysInWeek(
            $this->thisYear(),
            $this->thisMonth(),
            $this->thisDay()), $this->firstDay);
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
        parent::setTimestamp($ts);
        $this->thisWeek = $this->tableHelper->getWeekStart(
            $this->year, $this->month, $this->day, $this->firstDay
        );
        $this->prevWeek = $this->tableHelper->getWeekStart(
            $this->year, $this->month, $this->day - $this->cE->getDaysInWeek(
                $this->thisYear(),
                $this->thisMonth(),
                $this->thisDay()), $this->firstDay
        );
        $this->nextWeek = $this->tableHelper->getWeekStart(
            $this->year, $this->month, $this->day + $this->cE->getDaysInWeek(
                $this->thisYear(),
                $this->thisMonth(),
                $this->thisDay()), $this->firstDay
        );
    }

    /**
     * Builds Calendar_Day objects for this Week
     * @param array (optional) Calendar_Day objects representing selected dates
     * @return boolean
     * @access public
     */
    function build($sDates = array())
    {
        require_once CALENDAR_ROOT.'Day.php';
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
                                $this->cE->stampToDay($stamp));
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
     * @param boolean
     * @return void
     * @access private
     */
    function setFirst($state=true)
    {
        $this->firstWeek = $state;
    }

    /**
     * @param boolean
     * @return void
     * @access private
     */
    function setLast($state=true)
    {
        $this->lastWeek = $state;
    }

    /**
     * Called from build()
     * @param array
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
     * Gets the value of the previous week, according to the requested format
     *
     * @param string $format ['timestamp' | 'n_in_month' | 'n_in_year' | 'array']
     * @return mixed
     * @access public
     */
    function prevWeek($format = 'n_in_month')
    {
        switch (strtolower($format)) {
            case 'int':
            case 'n_in_month':
                return ($this->firstWeek) ? null : $this->thisWeek('n_in_month') -1;
                break;
            case 'n_in_year':
                return $this->cE->getWeekNInYear(
                    $this->cE->stampToYear($this->prevWeek),
                    $this->cE->stampToMonth($this->prevWeek),
                    $this->cE->stampToDay($this->prevWeek));
                break;
            case 'array':
                return $this->toArray($this->prevWeek);
                break;
            case 'object':
                require_once CALENDAR_ROOT.'Factory.php';
                return Calendar_Factory::createByTimestamp('Week', $this->prevWeek);
                break;
            case 'timestamp':
            default:
                return $this->prevWeek;
                break;
        }
    }

    /**
     * Gets the value of the current week, according to the requested format
     *
     * @param string $format ['timestamp' | 'n_in_month' | 'n_in_year' | 'array']
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
                break;
            case 'n_in_year':
                return $this->cE->getWeekNInYear(
                    $this->cE->stampToYear($this->thisWeek),
                    $this->cE->stampToMonth($this->thisWeek),
                    $this->cE->stampToDay($this->thisWeek));
                break;
            case 'array':
                return $this->toArray($this->thisWeek);
                break;
            case 'object':
                require_once CALENDAR_ROOT.'Factory.php';
                return Calendar_Factory::createByTimestamp('Week', $this->thisWeek);
                break;
            case 'timestamp':
            default:
                return $this->thisWeek;
                break;
        }
    }

    /**
     * Gets the value of the following week, according to the requested format
     *
     * @param string $format ['timestamp' | 'n_in_month' | 'n_in_year' | 'array']
     * @return mixed
     * @access public
     */
    function nextWeek($format = 'n_in_month')
    {
        switch (strtolower($format)) {
            case 'int':
            case 'n_in_month':
                return ($this->lastWeek) ? null : $this->thisWeek('n_in_month') +1;
                break;
            case 'n_in_year':
                return $this->cE->getWeekNInYear(
                    $this->cE->stampToYear($this->nextWeek),
                    $this->cE->stampToMonth($this->nextWeek),
                    $this->cE->stampToDay($this->nextWeek));
                break;
            case 'array':
                return $this->toArray($this->nextWeek);
                break;
            case 'object':
                require_once CALENDAR_ROOT.'Factory.php';
                return Calendar_Factory::createByTimestamp('Week', $this->nextWeek);
                break;
            case 'timestamp':
            default:
                    return $this->nextWeek;
                    break;
        }
    }

    /**
     * Returns the instance of Calendar_Table_Helper.
     * Called from Calendar_Validator::isValidWeek
     * @return Calendar_Table_Helper
     * @access protected
     */
    function & getHelper()
    {
        return $this->tableHelper;
    }

    /**
     * Makes sure theres a value for $this->day
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