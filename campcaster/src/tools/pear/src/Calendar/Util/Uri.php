<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */
//
// +----------------------------------------------------------------------+
// | PHP                                                                  |
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
// $Id: Uri.php,v 1.1 2004/08/16 09:03:55 hfuecks Exp $
//
/**
 * @package Calendar
 * @version $Id: Uri.php,v 1.1 2004/08/16 09:03:55 hfuecks Exp $
 */

/**
 * Utility to help building HTML links for navigating the calendar<br />
 * <code>
 * $Day = new Calendar_Day(2003, 10, 23);
 * $Uri = & new Calendar_Util_Uri('year', 'month', 'day');
 * echo $Uri->prev($Day,'month'); // Displays year=2003&amp;month=10
 * echo $Uri->prev($Day,'day'); // Displays year=2003&amp;month=10&amp;day=22
 * $Uri->seperator = '/';
 * $Uri->scalar = true;
 * echo $Uri->prev($Day,'month'); // Displays 2003/10
 * echo $Uri->prev($Day,'day'); // Displays 2003/10/22
 * </code>
 * @package Calendar
 * @access public
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
     * @param string URI fragment for year
     * @param string (optional) URI fragment for month
     * @param string (optional) URI fragment for day
     * @param string (optional) URI fragment for hour
     * @param string (optional) URI fragment for minute
     * @param string (optional) URI fragment for second
     * @access public
     */
    function Calendar_Util_Uri($y, $m=null, $d=null, $h=null, $i=null, $s=null)
    {
        $this->setFragments($y, $m, $d, $h, $i, $s);
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
        if (!is_null($y)) $this->uris['Year']   = $y;
        if (!is_null($m)) $this->uris['Month']  = $m;
        if (!is_null($d)) $this->uris['Day']    = $d;
        if (!is_null($h)) $this->uris['Hour']   = $h;
        if (!is_null($i)) $this->uris['Minute'] = $i;
        if (!is_null($s)) $this->uris['Second'] = $s;
    }

    /**
     * Gets the URI string for the previous calendar unit
     * @param object subclassed from Calendar e.g. Calendar_Month
     * @param string calendar unit ( must be year, month, week, day, hour, minute or second)
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
     * @param object subclassed from Calendar e.g. Calendar_Month
     * @param string calendar unit ( must be year, month, week, day, hour, minute or second)
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
     * @param object subclassed from Calendar e.g. Calendar_Month
     * @param string calendar unit ( must be year, month, week, day, hour, minute or second)
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
     * @param string method substring
     * @param int timestamp
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
            if (!$this->scalar) $uriString .= $uri.'=';
            $uriString .= $cE->{$call}($stamp);
            $separator = $this->separator;
        }
        return $uriString;
    }
}
?>