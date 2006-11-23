<?php

/**
 * Sets the include_path, error_reporting and PATH_SEPARATOR for tests
 * in the DB/tests directory
 *
 * PHP versions 4 and 5
 *
 * LICENSE: This source file is subject to version 3.0 of the PHP license
 * that is available through the world-wide-web at the following URI:
 * http://www.php.net/license/3_0.txt.  If you did not receive a copy of
 * the PHP License and are unable to obtain it through the web, please
 * send a note to license@php.net so we can mail you a copy immediately.
 *
 * @category   Database
 * @package    DB
 * @author     Daniel Convissor <danielc@php.net>
 * @copyright  1997-2005 The PHP Group
 * @license    http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version    $Id: include.inc,v 1.5 2005/02/03 05:49:44 danielc Exp $
 * @link       http://pear.php.net/package/DB
 */

error_reporting(E_ALL);

if (!defined('PATH_SEPARATOR')) {
    if (stristr(PHP_OS, 'WIN')) {
        /**
         * Define the path separator for windows
         */
        define('PATH_SEPARATOR', ';');
    } else {
        /**
         * Define the path separator for other systems
         */
        define('PATH_SEPARATOR', ':');
    }
}

/*
 * If the path to your PEAR installation is found in the left hand
 * portion of the if() expression below, that means this file has
 * come from the PEAR installer.  Therefore, let's use the
 * installed version of DB, which should be found via the
 * computer's default include_path.  Add '.' to the include_path
 * to ensure '.' is in there.
 * 
 * If the path has not been substituted in the if() expression,
 * this file has likely come from a CVS checkout or a .tar file.
 * Therefore, we'll assume the tests should use the version of
 * DB that has come from there as well.
 */
if ('/home/paul/software/campcaster/campcaster/usr/lib/pear' != '@'.'include_path'.'@') {
    ini_set('include_path', ini_get('include_path')
            . PATH_SEPARATOR . '.'
    );
} else {
    ini_set('include_path', realpath(dirname(__FILE__) . '/..')
            . PATH_SEPARATOR . '.' . PATH_SEPARATOR
            . ini_get('include_path')
    );
}
