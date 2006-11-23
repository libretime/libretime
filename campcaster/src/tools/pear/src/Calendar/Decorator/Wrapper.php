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
// $Id: Wrapper.php,v 1.2 2005/11/03 20:35:03 quipo Exp $
//
/**
 * @package Calendar
 * @version $Id: Wrapper.php,v 1.2 2005/11/03 20:35:03 quipo Exp $
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
 * Decorator to help with wrapping built children in another decorator
 * @package Calendar
 * @access public
 */
class Calendar_Decorator_Wrapper extends Calendar_Decorator
{
    /**
     * Constructs Calendar_Decorator_Wrapper
     * @param object subclass of Calendar
     * @access public
     */
    function Calendar_Decorator_Wrapper(&$Calendar)
    {
        parent::Calendar_Decorator($Calendar);
    }

    /**
     * Wraps objects returned from fetch in the named Decorator class
     * @param string name of Decorator class to wrap with
     * @return object instance of named decorator
     * @access public
     */
    function & fetch($decorator)
    {
        $Calendar = parent::fetch();
        if ($Calendar) {
            $ret =& new $decorator($Calendar);
        } else {
            $ret = false;
        }
        return $ret;
    }

    /**
     * Wraps the returned calendar objects from fetchAll in the named decorator
     * @param string name of Decorator class to wrap with
     * @return array
     * @access public
     */
    function fetchAll($decorator)
    {
        $children = parent::fetchAll();
        foreach ($children as $key => $Calendar) {
            $children[$key] = & new $decorator($Calendar);
        }
        return $children;
    }
}
?>