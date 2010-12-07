<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */

/**
 * Contains the Calendar_Month_Weeks class
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
 * @author    Lorenzo Alberton <l.alberton@quipo.it>
 * @copyright 2003-2007 Harry Fuecks, Lorenzo Alberton
 * @license   http://www.debian.org/misc/bsd.license  BSD License (3 Clause)
 * @version   CVS: $Id: Weeks.php 300729 2010-06-24 12:05:53Z quipo $
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
 * Represents a Month and builds Weeks
 * <code>
 * require_once 'Calendar'.DIRECTORY_SEPARATOR.'Month'.DIRECTORY_SEPARATOR.'Weeks.php';
 * $Month = new Calendar_Month_Weeks(2003, 10); // Oct 2003
 * $Month->build(); // Build Calendar_Day objects
 * while ($Week = & $Month->fetch()) {
 *     echo $Week->thisWeek().'<br />';
 * }
 * </code>
 *
 * @category  Date and Time
 * @package   Calendar
 * @author    Harry Fuecks <hfuecks@phppatterns.com>
 * @author    Lorenzo Alberton <l.alberton@quipo.it>
 * @copyright 2003-2007 Harry Fuecks, Lorenzo Alberton
 * @license   http://www.debian.org/misc/bsd.license  BSD License (3 Clause)
 * @link      http://pear.php.net/package/Calendar
 * @access    public
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
     *
     * @param int $y        year e.g. 2003
     * @param int $m        month e.g. 5
     * @param int $firstDay (optional) first day of week (e.g. 0 for Sunday, 2 for Tuesday etc.)
     *
     * @access public
     */
    function Calendar_Month_Weeks($y, $m, $firstDay=null)
    {
        parent::Calendar_Month($y, $m, $firstDay);
    }

    /**
     * Builds Calendar_Week objects for the Month. Note that Calendar_Week
     * builds Calendar_Day object in tabular form (with Calendar_Day->empty)
     *
     * @param array $sDates (optional) Calendar_Week objects representing selected dates
     *
     * @return boolean
     * @access public
     */
    function build($sDates = array())
    {
        include_once CALENDAR_ROOT.'Table/Helper.php';
        $this->tableHelper = new Calendar_Table_Helper($this, $this->firstDay);
        include_once CALENDAR_ROOT.'Week.php';
        $numWeeks = $this->tableHelper->getNumWeeks();
        for ($i=1, $d=1; $i<=$numWeeks; $i++,
            $d+=$this->cE->getDaysInWeek(
                $this->thisYear(),
                $this->thisMonth(),
                $this->thisDay()
            )
        ) {
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
     *
     * @param array $sDates Calendar_Week objects representing selected dates
     *
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