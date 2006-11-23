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
// $Id: Month.php,v 1.3 2005/10/22 10:10:26 quipo Exp $
//
/**
 * @package Calendar
 * @version $Id: Month.php,v 1.3 2005/10/22 10:10:26 quipo Exp $
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
 * Represents a Month and builds Days
 * <code>
 * require_once 'Calendar/Month.php';
 * $Month = & new Calendar_Month(2003, 10); // Oct 2003
 * $Month->build(); // Build Calendar_Day objects
 * while ($Day = & $Month->fetch()) {
 *     echo $Day->thisDay().'<br />';
 * }
 * </code>
 * @package Calendar
 * @access public
 */
class Calendar_Month extends Calendar
{
    /**
     * Constructs Calendar_Month
     * @param int $y year e.g. 2003
     * @param int $m month e.g. 5
     * @param int $firstDay first day of the week [optional]
     * @access public
     */
    function Calendar_Month($y, $m, $firstDay=null)
    {
        Calendar::Calendar($y, $m);
        $this->firstDay = $this->defineFirstDayOfWeek($firstDay);
    }

    /**
     * Builds Day objects for this Month. Creates as many Calendar_Day objects
     * as there are days in the month
     * @param array (optional) Calendar_Day objects representing selected dates
     * @return boolean
     * @access public
     */
    function build($sDates=array())
    {
        require_once CALENDAR_ROOT.'Day.php';
        $daysInMonth = $this->cE->getDaysInMonth($this->year, $this->month);
        for ($i=1; $i<=$daysInMonth; $i++) {
            $this->children[$i] = new Calendar_Day($this->year, $this->month, $i);
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
            ) {
                $key = $sDate->thisDay();
                if (isset($this->children[$key])) {
                    $sDate->setSelected();
                    $class = strtolower(get_class($sDate));
                    if ($class == 'calendar_day' || $class == 'calendar_decorator') {
                        $sDate->setFirst($this->children[$key]->isFirst());
                        $sDate->setLast($this->children[$key]->isLast());
                    }
                    $this->children[$key] = $sDate;
                }
            }
        }
    }
}
?>