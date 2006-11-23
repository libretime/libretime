<?php

/**
 * Tests the drivers' limitQuery() method
 *
 * Executed by driver/13limit.phpt
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
 * @version    $Id: limit.inc,v 1.11 2005/02/03 05:49:44 danielc Exp $
 * @link       http://pear.php.net/package/DB
 */

error_reporting(E_ALL);

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


$dbh->setErrorHandling(PEAR_ERROR_RETURN);
drop_table($dbh, 'php_limit');

$dbh->setErrorHandling(PEAR_ERROR_CALLBACK, 'pe');

$dbh->query('CREATE TABLE php_limit (a CHAR(20))');


$from    = 0;
$count   = 10;
$numrows = 30;

for ($i=0; $i<=$numrows+2; $i++) {
    $dbh->query("INSERT INTO php_limit VALUES('result $i')");
}
for ($i = 0; $i <= 3; $i++) {
    $from = 10 * $i;
    $res = $dbh->limitQuery("select * from php_limit", $from, $count);
    echo "======= From: $from || Number of rows to fetch: $count =======\n";
    while ($row = $res->fetchRow(DB_FETCHMODE_ASSOC)) {
        echo $res->getRowCounter() . '.- ' . $row['a'] . "\n";
    }
    $res->free();  // keep fbsql happy.
}


$from  = 11;
$count = 3;

echo "======= Passing \$params || From: $from || Number of rows to fetch: $count =======\n";
$res = $dbh->limitQuery('SELECT * FROM php_limit WHERE a < ?', $from, $count, array('result 99'));
while ($row = $res->fetchRow(DB_FETCHMODE_ASSOC)) {
    echo $res->getRowCounter() . '.- ' . $row['a'] . "\n";
}

$res->free();  // keep fbsql happy.
               // keep ibase happy: can't drop tbl that has results open against it.

$dbh->setErrorHandling(PEAR_ERROR_RETURN);
drop_table($dbh, 'php_limit');
