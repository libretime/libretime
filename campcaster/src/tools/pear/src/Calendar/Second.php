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
// $Id: Second.php,v 1.1 2004/05/24 22:25:42 quipo Exp $
//
/**
 * @package Calendar
 * @version $Id: Second.php,v 1.1 2004/05/24 22:25:42 quipo Exp $
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
 * Represents a Second<br />
 * <b>Note:</b> Seconds do not build other objects
 * so related methods are overridden to return NULL
 * @package Calendar
 */
class Calendar_Second extends Calendar
{
    /**
     * Constructs Second
     * @param int year e.g. 2003
     * @param int month e.g. 5
     * @param int day e.g. 11
     * @param int hour e.g. 13
     * @param int minute e.g. 31
     * @param int second e.g. 45
     */
    function Calendar_Second($y, $m, $d, $h, $i, $s)
    {
        Calendar::Calendar($y, $m, $d, $h, $i, $s);
    }

    /**
     * Overwrite build
     * @return NULL
     */
    function build()
    {
        return null;
    }

    /**
     * Overwrite fetch
     * @return NULL
     */
    function fetch()
    {
        return null;
    }

    /**
     * Overwrite fetchAll
     * @return NULL
     */
    function fetchAll()
    {
        return null;
    }

    /**
     * Overwrite size
     * @return NULL
     */
    function size()
    {
        return null;
    }
}
?>