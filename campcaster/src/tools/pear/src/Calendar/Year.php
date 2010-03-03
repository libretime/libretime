<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */

/**
 * Contains the Calendar_Minute class
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
 * @version   CVS: $Id: Year.php,v 1.9 2007/11/18 21:46:42 quipo Exp $
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
 * Represents a Year and builds Months<br>
 * <code>
 * require_once 'Calendar'.DIRECTORY_SEPARATOR.'Year.php';
 * $Year = & new Calendar_Year(2003, 10, 21); // 21st Oct 2003
 * $Year->build(); // Build Calendar_Month objects
 * while ($Month = & $Year->fetch()) {
 *     echo $Month->thisMonth().'<br />';
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
class Calendar_Year extends Calendar
{
    /**
     * Constructs Calendar_Year
     *
     * @param int $y year e.g. 2003
     *
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
     *
     * @param array $sDates   (optional) array of Calendar_Month objects
     *                        representing selected dates
     * @param int   $firstDay (optional) first day of week
     *                        (e.g. 0 for Sunday, 2 for Tuesday etc.)
     *
     * @return boolean
     * @access public
     */
    function build($sDates = array(), $firstDay = null)
    {
        include_once CALENDAR_ROOT.'Factory.php';
        $this->firstDay = $this->defineFirstDayOfWeek($firstDay);
        $monthsInYear   = $this->cE->getMonthsInYear($this->thisYear());
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
     *
     * @param array $sDates array of Calendar_Month objects representing selected dates
     *
     * @return void
     * @access private
     */
    function setSelection($sDates) 
    {
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