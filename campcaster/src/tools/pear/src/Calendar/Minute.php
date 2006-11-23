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
// $Id: Minute.php,v 1.1 2004/05/24 22:25:42 quipo Exp $
//
/**
 * @package Calendar
 * @version $Id: Minute.php,v 1.1 2004/05/24 22:25:42 quipo Exp $
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
 * Represents a Minute and builds Seconds
 * <code>
 * require_once 'Calendar'.DIRECTORY_SEPARATOR.'Minute.php';
 * $Minute = & new Calendar_Minute(2003, 10, 21, 15, 31); // Oct 21st 2003, 3:31pm
 * $Minute->build(); // Build Calendar_Second objects
 * while ($Second = & $Minute->fetch()) {
 *     echo $Second->thisSecond().'<br />';
 * }
 * </code>
 * @package Calendar
 * @access public
 */
class Calendar_Minute extends Calendar
{
    /**
     * Constructs Minute
     * @param int year e.g. 2003
     * @param int month e.g. 5
     * @param int day e.g. 11
     * @param int hour e.g. 13
     * @param int minute e.g. 31
     * @access public
     */
    function Calendar_Minute($y, $m, $d, $h, $i)
    {
        Calendar::Calendar($y, $m, $d, $h, $i);
    }

    /**
     * Builds the Calendar_Second objects
     * @param array (optional) Calendar_Second objects representing selected dates
     * @return boolean
     * @access public
     */
    function build($sDates=array())
    {
        require_once CALENDAR_ROOT.'Second.php';
        $sIM = $this->cE->getSecondsInMinute($this->year, $this->month,
                $this->day, $this->hour, $this->minute);
        for ($i=0; $i < $sIM; $i++) {
            $this->children[$i] = new Calendar_Second($this->year, $this->month,
                $this->day, $this->hour, $this->minute, $i);
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
                && $this->day == $sDate->thisDay()
                && $this->hour == $sDate->thisHour()
                && $this->minute == $sDate->thisMinute())
            {
                $key = (int)$sDate->thisSecond();
                if (isset($this->children[$key])) {
                    $sDate->setSelected();
                    $this->children[$key] = $sDate;
                }
            }
        }
    }
}
?>