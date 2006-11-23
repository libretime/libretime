<?php

/**
 * Tests the drivers' numCols() method
 *
 * Executed by driver/04numcols.phpt
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
 * @version    $Id: numcols.inc,v 1.7 2005/02/03 05:49:44 danielc Exp $
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


$sth = $dbh->query("SELECT a FROM phptest");
printf("%d\n", $sth->numCols());
$sth = $dbh->query("SELECT a,b FROM phptest");
printf("%d\n", $sth->numCols());
$sth = $dbh->query("SELECT a,b,c FROM phptest");
printf("%d\n", $sth->numCols());
$sth = $dbh->query("SELECT * FROM phptest");
printf("%d\n", $sth->numCols());


switch ($dbh->phptype) {
    case 'ibase':
        /*
         * Interbase doesn't allow dropping tables that have result
         * sets still open.
         */
        $dbh->freeResult($sth->result);
        break;
}
$dbh->setErrorHandling(PEAR_ERROR_RETURN);
drop_table($dbh, 'phptest');
