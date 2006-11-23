<?php

/**
 * A test to make sure PEAR DB can be found
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
 * @version    $Id: skipif.inc,v 1.2 2005/02/03 05:49:44 danielc Exp $
 * @link       http://pear.php.net/package/DB
 */

/**
 * Set up the include_path, error_reporting and PATH_SEPARATOR
 */
require_once './include.inc';

if (!include_once 'DB.php') {
    print 'skip could not find DB.php';
}
