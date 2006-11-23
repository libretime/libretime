<?php

/**
 * Tests the drivers' simpleQuery() method
 *
 * Executed by driver/03simplequery.phpt
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
 * @version    $Id: simplequery.inc,v 1.8 2005/02/03 05:49:44 danielc Exp $
 * @link       http://pear.php.net/package/DB
 */

/**
 * Local error callback handler
 *
 * Drops the phptest table, prints out an error message and kills the
 * process.
 *
 * @param object  $o  PEAR error object automatically passed to this method
 * @return void
 * @see PEAR::setErrorHandling()
 */
function pe($o) {
    global $dbh;

    $dbh->setErrorHandling(PEAR_ERROR_RETURN);
    drop_table($dbh, 'phptest');

    die($o->toString());
}

$dbh->setErrorHandling(PEAR_ERROR_CALLBACK, 'pe');


$sth = $dbh->simpleQuery("SELECT * FROM phptest");

switch ($dbh->phptype) {
    case 'mysqli':
        if (is_a($sth, 'mysqli_result')) {
            print "passed\n";
        } else {
            print "PROBLEM\n";
        }
        break;
    default:
        if (gettype($sth) == 'resource') {
            print "passed\n";
        } else {
            print "PROBLEM\n";
        }
}


switch ($dbh->phptype) {
    case 'ibase':
        /*
         * Interbase doesn't allow dropping tables that have result
         * sets still open.
         */
        $dbh->freeResult($sth);
        break;
}
$dbh->setErrorHandling(PEAR_ERROR_RETURN);
drop_table($dbh, 'phptest');
