<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */

/**
 * Contains the Calendar_Month class
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
 * @version   CVS: $Id: Month.php,v 1.7 2007/11/16 20:03:12 quipo Exp $
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
 * Represents a Month and builds Days
 * <code>
 * require_once 'Calendar/Month.php';
 * $Month = & new Calendar_Month(2003, 10); // Oct 2003
 * $Month->build(); // Build Calendar_Day objects
 * while ($Day = & $Month->fetch()) {
 *     echo $Day->thisDay().'<br />';
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
class Calendar_Month extends Calendar
{
    /**
     * Constructs Calendar_Month
     *
     * @param int $y        year e.g. 2003
     * @param int $m        month e.g. 5
     * @param int $firstDay first day of the week [optional]
     *
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
     *
     * @param array $sDates (optional) Calendar_Day objects representing selected dates
     *
     * @return boolean
     * @access public
     */
    function build($sDates = array())
    {
        include_once CALENDAR_ROOT.'Day.php';
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
     *
     * @param array $sDates Calendar_Day objects representing selected dates
     *
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