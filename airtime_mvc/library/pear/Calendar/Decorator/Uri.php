<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */

/**
 * Contains the Calendar_Decorator_Uri class
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
 * Allows Calendar include path to be redefined
 * @ignore
 */
if (!defined('CALENDAR_ROOT')) {
    define('CALENDAR_ROOT', 'Calendar'.DIRECTORY_SEPARATOR);
}

/**
 * Load Calendar decorator base class
 */
require_once CALENDAR_ROOT.'Decorator.php';

/**
 * Load the Uri utility
 */
require_once CALENDAR_ROOT.'Util'.DIRECTORY_SEPARATOR.'Uri.php';

/**
 * Decorator to help with building HTML links for navigating the calendar<br />
 * <b>Note:</b> for performance you should prefer Calendar_Util_Uri unless you
 * have a specific need to use a decorator
 * <code>
 * $Day = new Calendar_Day(2003, 10, 23);
 * $Uri = new Calendar_Decorator_Uri($Day);
 * $Uri->setFragments('year', 'month', 'day');
 * echo $Uri->getPrev(); // Displays year=2003&month=10&day=22
 * </code>
 *
 * @category  Date and Time
 * @package   Calendar
 * @author    Harry Fuecks <hfuecks@phppatterns.com>
 * @author    Lorenzo Alberton <l.alberton@quipo.it>
 * @copyright 2003-2007 Harry Fuecks, Lorenzo Alberton
 * @license   http://www.debian.org/misc/bsd.license  BSD License (3 Clause)
 * @link      http://pear.php.net/package/Calendar
 * @see       Calendar_Util_Uri
 * @access    public
 */
class Calendar_Decorator_Uri extends Calendar_Decorator
{

    /**
     * @var Calendar_Util_Uri
     * @access private
     */
    var $Uri;

    /**
     * Constructs Calendar_Decorator_Uri
     *
     * @param object &$Calendar subclass of Calendar
     *
     * @access public
     */
    function Calendar_Decorator_Uri(&$Calendar)
    {
        parent::Calendar_Decorator($Calendar);
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
    function setFragments($y, $m = null, $d = null, $h = null, $i = null, $s = null)
    {
        $this->Uri = new Calendar_Util_Uri($y, $m, $d, $h, $i, $s);
    }

    /**
     * Sets the separator string between fragments
     *
     * @param string $separator url fragment separator e.g. /
     *
     * @return void
     * @access public
     */
    function setSeparator($separator)
    {
        $this->Uri->separator = $separator;
    }

    /**
     * Puts Uri decorator into "scalar mode" - URI variable names are not returned
     *
     * @param boolean $state (optional)
     *
     * @return void
     * @access public
     */
    function setScalar($state = true)
    {
        $this->Uri->scalar = $state;
    }

    /**
     * Gets the URI string for the previous calendar unit
     *
     * @param string $method calendar unit to fetch uri for (year, month, week or day etc)
     *
     * @return string
     * @access public
     */
    function prev($method)
    {
        return $this->Uri->prev($this, $method);
    }

    /**
     * Gets the URI string for the current calendar unit
     *
     * @param string $method calendar unit to fetch uri for (year,month,week or day etc)
     *
     * @return string
     * @access public
     */
    function this($method)
    {
        return $this->Uri->this($this, $method);
    }

    /**
     * Gets the URI string for the next calendar unit
     *
     * @param string $method calendar unit to fetch uri for (year,month,week or day etc)
     *
     * @return string
     * @access public
     */
    function next($method)
    {
        return $this->Uri->next($this, $method);
    }
}
?>