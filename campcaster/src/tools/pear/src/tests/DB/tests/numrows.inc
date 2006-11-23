<?php

/**
 * Tests the drivers' numRows() method
 *
 * Executed by driver/09numrows.phpt
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
 * @version    $Id: numrows.inc,v 1.12 2005/02/09 07:07:24 danielc Exp $
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

    print "\n------------\n";
    if ($o->getCode() == DB_ERROR_NOT_CAPABLE) {
        print "This DBMS does not support numRows().";
    } elseif ($o->getCode() == DB_ERROR_MISMATCH) {
        print "Mismatch between the number of placeholders and parameters.\n";
        foreach ($o->backtrace as $item => $detail) {
            if ($detail['function'] == 'query') {
                echo 'QUERY: ' . $detail['args'][0] . "\n";
                echo "PARAMETERS:\n";
                print_r($detail['args'][1]);
            }
        }
    } else {
        print $o->getDebugInfo() . "\n";
    }
    exit;
}

$dbh->setErrorHandling(PEAR_ERROR_CALLBACK, 'pe');


$res = $dbh->query("SELECT a FROM phptest");
if (!DB::isError($rows = $res->numRows())) {
    print "(want 1) got $rows from first\n";
} else {
    print "\n";
}
for ($i = 0; $i < 5; $i++) {
    $dbh->query("INSERT INTO phptest (a) VALUES ($i)");
    $res = $dbh->query("SELECT a FROM phptest");
    if (!DB::isError($rows = $res->numRows())) {
        print '(want ' . ($i + 2) . ") got $rows from $i\n";
    } else {
        print "\n";
    }
}

$res = $dbh->query('SELECT a FROM phptest WHERE a > ?', 0);
if (!DB::isError($rows = $res->numRows())) {
    print "(want 5) got $rows from > 0 (passing params to query)\n";
} else {
    print "\n";
}

$sth = $dbh->prepare('SELECT a FROM phptest WHERE a < ?');
$res = $dbh->execute($sth, array(4));
if (!DB::isError($rows = $res->numRows())) {
    print "(want 4) got $rows from < 4 (doing prepare/execute)\n";
} else {
    print "\n";
}

$dbh->query("DELETE FROM phptest WHERE a < 4");
$res = $dbh->query("SELECT a FROM phptest");
if (!DB::isError($rows = $res->numRows())) {
    print "(want 2) got $rows from 5 and 6 not deleted\n";
} else {
    print "\n";
}
$res = $dbh->query("SELECT a FROM phptest where a < 0");
if (!DB::isError($rows = $res->numRows())) {
    print "(want 0) got $rows from < 0\n";
} else {
    print "\n";
}


$res->free();  // keep ibase happy
$dbh->setErrorHandling(PEAR_ERROR_RETURN);
drop_table($dbh, 'phptest');
