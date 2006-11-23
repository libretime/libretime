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
// $Id: Uri.php,v 1.3 2004/08/16 09:04:20 hfuecks Exp $
//
/**
 * @package Calendar
 * @version $Id: Uri.php,v 1.3 2004/08/16 09:04:20 hfuecks Exp $
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
 * $Uri = & new Calendar_Decorator_Uri($Day);
 * $Uri->setFragments('year', 'month', 'day');
 * echo $Uri->getPrev(); // Displays year=2003&month=10&day=22
 * </code>
 * @see Calendar_Util_Uri
 * @package Calendar
 * @access public
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
     * @param object subclass of Calendar
     * @access public
     */
    function Calendar_Decorator_Uri(&$Calendar)
    {
        parent::Calendar_Decorator($Calendar);
    }

    /**
     * Sets the URI fragment names
     * @param string URI fragment for year
     * @param string (optional) URI fragment for month
     * @param string (optional) URI fragment for day
     * @param string (optional) URI fragment for hour
     * @param string (optional) URI fragment for minute
     * @param string (optional) URI fragment for second
     * @return void
     * @access public
     */
    function setFragments($y, $m=null, $d=null, $h=null, $i=null, $s=null) {
        $this->Uri = & new Calendar_Util_Uri($y, $m, $d, $h, $i, $s);
    }

    /**
     * Sets the separator string between fragments
     * @param string separator e.g. /
     * @return void
     * @access public
     */
    function setSeparator($separator)
    {
        $this->Uri->separator = $separator;
    }

    /**
     * Puts Uri decorator into "scalar mode" - URI variable names are not
     * returned
     * @param boolean (optional)
     * @return void
     * @access public
     */
    function setScalar($state=true)
    {
        $this->Uri->scalar = $state;
    }

    /**
     * Gets the URI string for the previous calendar unit
     * @param string calendar unit to fetch uri for (year,month,week or day etc)
     * @return string
     * @access public
     */
    function prev($method)
    {
        return $this->Uri->prev($this, $method);
    }

    /**
     * Gets the URI string for the current calendar unit
     * @param string calendar unit to fetch uri for (year,month,week or day etc)
     * @return string
     * @access public
     */
    function this($method)
    {
        return $this->Uri->this($this, $method);
    }

    /**
     * Gets the URI string for the next calendar unit
     * @param string calendar unit to fetch uri for (year,month,week or day etc)
     * @return string
     * @access public
     */
    function next($method)
    {
        return $this->Uri->next($this, $method);
    }

}
?>