<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */

/**
 * Contains the Calendar_Hour class
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
 * @version   CVS: $Id: Hour.php 300729 2010-06-24 12:05:53Z quipo $
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
 * Represents an Hour and builds Minutes
 * <code>
 * require_once 'Calendar'.DIRECTORY_SEPARATOR.'Hour.php';
 * $Hour = new Calendar_Hour(2003, 10, 21, 15); // Oct 21st 2003, 3pm
 * $Hour->build(); // Build Calendar_Minute objects
 * while ($Minute = & $Hour->fetch()) {
 *     echo $Minute->thisMinute().'<br />';
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
class Calendar_Hour extends Calendar
{
    /**
     * Constructs Calendar_Hour
     *
     * @param int $y year e.g. 2003
     * @param int $m month e.g. 5
     * @param int $d day e.g. 11
     * @param int $h hour e.g. 13
     *
     * @access public
     */
    function Calendar_Hour($y, $m, $d, $h)
    {
        parent::Calendar($y, $m, $d, $h);
    }

    /**
     * Builds the Minutes in the Hour
     *
     * @param array $sDates (optional) Calendar_Minute objects representing selected dates
     *
     * @return boolean
     * @access public
     */
    function build($sDates = array())
    {
        include_once CALENDAR_ROOT.'Minute.php';
        $mIH = $this->cE->getMinutesInHour($this->year, $this->month, $this->day,
                           $this->hour);
        for ($i=0; $i < $mIH; $i++) {
            $this->children[$i] =
                new Calendar_Minute($this->year, $this->month, $this->day,
                           $this->hour, $i);
        }
        if (count($sDates) > 0) {
            $this->setSelection($sDates);
        }
        return true;
    }

    /**
     * Called from build()
     *
     * @param array $sDates Calendar_Minute objects representing selected dates
     *
     * @return void
     * @access private
     */
    function setSelection($sDates)
    {
        foreach ($sDates as $sDate) {
            if ($this->year == $sDate->thisYear()
                && $this->month == $sDate->thisMonth()
                && $this->day == $sDate->thisDay()
                && $this->hour == $sDate->thisHour())
            {
                $key = (int)$sDate->thisMinute();
                if (isset($this->children[$key])) {
                    $sDate->setSelected();
                    $this->children[$key] = $sDate;
                }
            }
        }
    }
}
?>