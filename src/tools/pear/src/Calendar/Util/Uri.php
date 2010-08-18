<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */

/**
 * Contains the Calendar_Util_Uri class
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
 * @version   CVS: $Id: Uri.php 300729 2010-06-24 12:05:53Z quipo $
 * @link      http://pear.php.net/package/Calendar
 */

/**
 * Utility to help building HTML links for navigating the calendar<br />
 * <code>
 * $Day = new Calendar_Day(2003, 10, 23);
 * $Uri = new Calendar_Util_Uri('year', 'month', 'day');
 * echo $Uri->prev($Day,'month'); // Displays year=2003&amp;month=10
 * echo $Uri->prev($Day,'day'); // Displays year=2003&amp;month=10&amp;day=22
 * $Uri->seperator = '/';
 * $Uri->scalar = true;
 * echo $Uri->prev($Day,'month'); // Displays 2003/10
 * echo $Uri->prev($Day,'day'); // Displays 2003/10/22
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
class Calendar_Util_Uri
{
    /**
     * Uri fragments for year, month, day etc.
     * @var array
     * @access private
     */
    var $uris = array();

    /**
     * String to separate fragments with.
     * Set to just & for HTML.
     * For a scalar URL you might use / as the seperator
     * @var string (default XHTML &amp;)
     * @access public
     */
    var $separator = '&amp;';

    /**
     * To output a "scalar" string - variable names omitted.
     * Used for urls like index.php/2004/8/12
     * @var boolean (default false)
     * @access public
     */
    var $scalar = false;

    /**
     * Constructs Calendar_Decorator_Uri
     * The term "fragment" means <i>name</i> of a calendar GET variables in the URL
     *
     * @param string $y URI fragment for year
     * @param string $m (optional) URI fragment for month
     * @param string $d (optional) URI fragment for day
     * @param string $h (optional) URI fragment for hour
     * @param string $i (optional) URI fragment for minute
     * @param string $s (optional) URI fragment for second
     *
     * @access public
     */
    function Calendar_Util_Uri($y, $m=null, $d=null, $h=null, $i=null, $s=null)
    {
        $this->setFragments($y, $m, $d, $h, $i, $s);
    }

    /**
     * Sets the URI fragment names
     *
     * @param string $y URI fragment for year
     * @param string $m (optional) URI fragment for month
     * @param string $d (optional) URI fragment for day
     * @param string $h (optional) URI fragment for hour
     * @param string $i (optional) URI fragment for minute
     * @param string $s (optional) URI fragment for second
     *
     * @return void
     * @access public
     */
    function setFragments($y, $m=null, $d=null, $h=null, $i=null, $s=null) 
    {
        if (!is_null($y)) $this->uris['Year']   = $y;
        if (!is_null($m)) $this->uris['Month']  = $m;
        if (!is_null($d)) $this->uris['Day']    = $d;
        if (!is_null($h)) $this->uris['Hour']   = $h;
        if (!is_null($i)) $this->uris['Minute'] = $i;
        if (!is_null($s)) $this->uris['Second'] = $s;
    }

    /**
     * Gets the URI string for the previous calendar unit
     *
     * @param object $Calendar subclassed from Calendar e.g. Calendar_Month
     * @param string $unit     calendar  unit (year|month|week|day|hour|minute|second)
     *
     * @return string
     * @access public
     */
    function prev($Calendar, $unit)
    {
        $method = 'prev'.$unit;
        $stamp  = $Calendar->{$method}('timestamp');
        return $this->buildUriString($Calendar, $method, $stamp);
    }

    /**
     * Gets the URI string for the current calendar unit
     *
     * @param object $Calendar subclassed from Calendar e.g. Calendar_Month
     * @param string $unit     calendar  unit (year|month|week|day|hour|minute|second)
     *
     * @return string
     * @access public
     */
    function this($Calendar, $unit)
    {
        $method = 'this'.$unit;
        $stamp  = $Calendar->{$method}('timestamp');
        return $this->buildUriString($Calendar, $method, $stamp);
    }

    /**
     * Gets the URI string for the next calendar unit
     *
     * @param object $Calendar subclassed from Calendar e.g. Calendar_Month
     * @param string $unit     calendar unit (year|month|week|day|hour|minute|second)
     *
     * @return string
     * @access public
     */
    function next($Calendar, $unit)
    {
        $method = 'next'.$unit;
        $stamp  = $Calendar->{$method}('timestamp');
        return $this->buildUriString($Calendar, $method, $stamp);
    }

    /**
     * Build the URI string
     *
     * @param object $Calendar subclassed from Calendar e.g. Calendar_Month
     * @param string $method   method substring
     * @param int    $stamp    timestamp
     *
     * @return string build uri string
     * @access private
     */
    function buildUriString($Calendar, $method, $stamp)
    {
        $uriString = '';
        $cE = & $Calendar->getEngine();
        $separator = '';
        foreach ($this->uris as $unit => $uri) {
            $call = 'stampTo'.$unit;
            $uriString .= $separator;
            if (!$this->scalar) {
                $uriString .= $uri.'=';
            }
            $uriString .= $cE->{$call}($stamp);
            $separator = $this->separator;
        }
        return $uriString;
    }
}
?>