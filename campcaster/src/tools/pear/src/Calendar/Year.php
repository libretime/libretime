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
// $Id: Year.php,v 1.4 2005/10/22 10:25:39 quipo Exp $
//
/**
 * @package Calendar
 * @version $Id: Year.php,v 1.4 2005/10/22 10:25:39 quipo Exp $
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
 * Represents a Year and builds Months<br>
 * <code>
 * require_once 'Calendar'.DIRECTORY_SEPARATOR.'Year.php';
 * $Year = & new Calendar_Year(2003, 10, 21); // 21st Oct 2003
 * $Year->build(); // Build Calendar_Month objects
 * while ($Month = & $Year->fetch()) {
 *     echo $Month->thisMonth().'<br />';
 * }
 * </code>
 * @package Calendar
 * @access public
 */
class Calendar_Year extends Calendar
{
    /**
     * Constructs Calendar_Year
     * @param int year e.g. 2003
     * @access public
     */
    function Calendar_Year($y)
    {
        Calendar::Calendar($y);
    }

    /**
     * Builds the Months of the Year.<br>
     * <b>Note:</b> by defining the constant CALENDAR_MONTH_STATE you can
     * control what class of Calendar_Month is built e.g.;
     * <code>
     * require_once 'Calendar/Calendar_Year.php';
     * define ('CALENDAR_MONTH_STATE',CALENDAR_USE_MONTH_WEEKDAYS); // Use Calendar_Month_Weekdays
     * // define ('CALENDAR_MONTH_STATE',CALENDAR_USE_MONTH_WEEKS); // Use Calendar_Month_Weeks
     * // define ('CALENDAR_MONTH_STATE',CALENDAR_USE_MONTH); // Use Calendar_Month
     * </code>
     * It defaults to building Calendar_Month objects.
     * @param array (optional) array of Calendar_Month objects representing selected dates
     * @param int (optional) first day of week (e.g. 0 for Sunday, 2 for Tuesday etc.)
     * @return boolean
     * @access public
     */
    function build($sDates = array(), $firstDay = null)
    {
        require_once CALENDAR_ROOT.'Factory.php';
        $this->firstDay = $this->defineFirstDayOfWeek($firstDay);
        $monthsInYear = $this->cE->getMonthsInYear($this->thisYear());
        for ($i=1; $i <= $monthsInYear; $i++) {
            $this->children[$i] = Calendar_Factory::create('Month', $this->year, $i);
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
    function setSelection($sDates) {
        foreach ($sDates as $sDate) {
            if ($this->year == $sDate->thisYear()) {
                $key = $sDate->thisMonth();
                if (isset($this->children[$key])) {
                    $sDate->setSelected();
                    $this->children[$key] = $sDate;
                }
            }
        }
    }
}
?>