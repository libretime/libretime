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
// $Id: Weeks.php,v 1.3 2005/10/22 10:28:49 quipo Exp $
//
/**
 * @package Calendar
 * @version $Id: Weeks.php,v 1.3 2005/10/22 10:28:49 quipo Exp $
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
 * Represents a Month and builds Weeks
 * <code>
 * require_once 'Calendar'.DIRECTORY_SEPARATOR.'Month'.DIRECTORY_SEPARATOR.'Weeks.php';
 * $Month = & new Calendar_Month_Weeks(2003, 10); // Oct 2003
 * $Month->build(); // Build Calendar_Day objects
 * while ($Week = & $Month->fetch()) {
 *     echo $Week->thisWeek().'<br />';
 * }
 * </code>
 * @package Calendar
 * @access public
 */
class Calendar_Month_Weeks extends Calendar_Month
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
     * Constructs Calendar_Month_Weeks
     * @param int year e.g. 2003
     * @param int month e.g. 5
     * @param int (optional) first day of week (e.g. 0 for Sunday, 2 for Tuesday etc.)
     * @access public
     */
    function Calendar_Month_Weeks($y, $m, $firstDay=null)
    {
        Calendar_Month::Calendar_Month($y, $m, $firstDay);
    }

    /**
     * Builds Calendar_Week objects for the Month. Note that Calendar_Week
     * builds Calendar_Day object in tabular form (with Calendar_Day->empty)
     * @param array (optional) Calendar_Week objects representing selected dates
     * @return boolean
     * @access public
     */
    function build($sDates=array())
    {
        require_once CALENDAR_ROOT.'Table/Helper.php';
        $this->tableHelper = & new Calendar_Table_Helper($this, $this->firstDay);
        require_once CALENDAR_ROOT.'Week.php';
        $numWeeks = $this->tableHelper->getNumWeeks();
        for ($i=1, $d=1; $i<=$numWeeks; $i++,
            $d+=$this->cE->getDaysInWeek(
                $this->thisYear(),
                $this->thisMonth(),
                $this->thisDay()) ) {
            $this->children[$i] = new Calendar_Week(
                $this->year, $this->month, $d, $this->tableHelper->getFirstDay());
        }
        //used to set empty days
        $this->children[1]->setFirst(true);
        $this->children[$numWeeks]->setLast(true);

        // Handle selected weeks here
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
                && $this->month == $sDate->thisMonth())
            {
                $key = $sDate->thisWeek('n_in_month');
                if (isset($this->children[$key])) {
                    $this->children[$key]->setSelected();
                }
            }
        }
    }
}
?>