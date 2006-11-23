<?php

/**
 * A test to make sure the database can be connected to
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
 * @version    $Id: skipif.inc,v 1.7 2005/02/02 00:40:23 danielc Exp $
 * @link       http://pear.php.net/package/DB
 */

/**
 * Establish the include_path, DSN's and connection $options
 */
require_once './setup.inc';

if (empty($dsns)) {
    die('skip At least one element of $dsns must be defined in setup.inc');
}

list($dbms, $dsn) = each($dsns);

$dbh =& DB::connect($dsn, $options);
if (DB::isError($dbh)) {
    die('skip ' . $dbh->message);
}
