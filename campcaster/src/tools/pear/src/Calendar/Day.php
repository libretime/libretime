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
// $Id: Day.php,v 1.1 2004/05/24 22:25:42 quipo Exp $
//
/**
 * @package Calendar
 * @version $Id: Day.php,v 1.1 2004/05/24 22:25:42 quipo Exp $
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
 * require_once 'Calendar'.DIRECTORY_SEPARATOR.'Day.php';
 * $Day = & new Calendar_Day(2003, 10, 21); // Oct 21st 2003
 * while ($Hour = & $Day->fetch()) {
 *    echo $Hour->thisHour().'<br />';
 * }
 * </code>
 * @package Calendar
 * @access public
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
     * @param int year e.g. 2003
     * @param int month e.g. 8
     * @param int day e.g. 15
     * @access public
     */
    function Calendar_Day($y, $m, $d)
    {
        Calendar::Calendar($y, $m, $d);
    }

    /**
     * Builds the Hours of the Day
     * @param array (optional) Caledar_Hour objects representing selected dates
     * @return boolean
     * @access public
     */
    function build($sDates = array())
    {
        require_once CALENDAR_ROOT.'Hour.php';

        $hID = $this->cE->getHoursInDay($this->year, $this->month, $this->day);
        for ($i=0; $i < $hID; $i++) {
            $this->children[$i]=
                new Calendar_Hour($this->year, $this->month, $this->day, $i);
        }
        if (count($sDates) > 0) {
            $this->setSelection($sDates);
        }
        return true;
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
     * @param boolean state
     * @return void
     * @access private
     */
    function setFirst ($state = true)
    {
        $this->first = $state;
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
        $this->last = $state;
    }

    /**
     * Returns true if Day object is first in a Week
     * Only relevant when Day is created by Calendar_Month_Weekdays::build()
     * @return boolean
     * @access public
     */
    function isFirst() {
        return $this->first;
    }

    /**
     * Returns true if Day object is last in a Week
     * Only relevant when Day is created by Calendar_Month_Weekdays::build()
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
     * @param boolean state
     * @return void
     * @access private
     */
    function setEmpty ($state = true)
    {
        $this->empty = $state;
    }

    /**
     * @return boolean
     * @access public
     */
    function isEmpty()
    {
        return $this->empty;
    }
}
?>