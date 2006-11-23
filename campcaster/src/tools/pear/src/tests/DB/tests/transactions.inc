<?php

/**
 * Tests the drivers' transaction handling methods
 *
 * Executed by driver/11transactions.phpt
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
 * @version    $Id: transactions.inc,v 1.9 2005/02/03 05:49:44 danielc Exp $
 * @link       http://pear.php.net/package/DB
 */

// Testing here due to skip not working currently in head
if (!$dbh->features['transactions']) {
    die('this driver does not support transactions');
}

// View the table from a separate connection so we don't disturb
// the transaction.
$dbh2 = DB::connect($dbh->dsn);

function error_handler(&$obj) {
    print "\n" . $obj->getDebugInfo() . "\n";
}

function dumptable($expected) {
    global $dbh, $dbh2;
    print implode(' ', $dbh->getCol('SELECT b FROM phptest'));

    if (isset($dbh->transaction_opcount)) {
        if ($expected == $dbh->transaction_opcount) {
            print ".  ops=ok\n";
        } else {
            print ".  ops=$dbh->transaction_opcount\n";
        }
    } else {
        print ".  ops=ok\n";
    }
}

$dbh->setErrorHandling(PEAR_ERROR_CALLBACK, 'error_handler');


$dbh->autoCommit(true);
$dbh->query("INSERT INTO phptest VALUES(1, 'one', 'One', '2001-02-19')");

print '1) after autocommit: ';
dumptable(0);

$dbh->autoCommit(false);
$dbh->query("INSERT INTO phptest VALUES(2, 'two', 'Two', '2001-02-20')");
$dbh->query("INSERT INTO phptest VALUES(3, 'three', 'Three', '2001-02-21')");
print '2) before commit: ';
dumptable(2);

$dbh->commit();
print '3) after commit: ';
dumptable(0);

$dbh->query("INSERT INTO phptest VALUES(4, 'four', 'Four', '2001-02-22')");
$dbh->query("INSERT INTO phptest VALUES(5, 'five', 'Five', '2001-02-23')");
print '4) before rollback: ';
dumptable(2);

$dbh->rollback();
print '5) after rollback: ';
dumptable(0);
$dbh->rollback();

$dbh->autoCommit(true);
$dbh->query("INSERT INTO phptest VALUES(6, 'six', 'Six', '2001-02-24')");
$dbh->query("INSERT INTO phptest VALUES(7, 'seven', 'Seven', '2001-02-25')");
print '6) before autocommit+rollback: ';
dumptable(0);

$dbh->rollback();
print '7) after autocommit+rollback: ';
dumptable(0);

print '8) testing that select doesn\'t disturbe opcount: ';
$dbh->autoCommit(false);
$dbh->simpleQuery("SELECT * FROM phptest");
$dbh->simpleQuery("SELECT a,c FROM phptest");
$dbh->simpleQuery("SELECT b,d FROM phptest");
if (empty($dbh->transaction_opcount)) {
    print "ok\n";
} else {
    print "failed (count=$dbh->transaction_opcount)\n";
}
