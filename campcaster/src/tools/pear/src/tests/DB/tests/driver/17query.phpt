--TEST--
DB_driver::query
--INI--
error_reporting = 2047
--SKIPIF--
<?php

/**
 * Calls the query() method in various ways against any DBMS.
 *
 * @see      DB_common::query()
 * 
 * @package  DB
 * @version  $Id: 17query.phpt,v 1.13 2005/02/16 13:54:52 danielc Exp $
 * @category Database
 * @author   Daniel Convissor <danielc@analysisandsolutions.com>
 * @internal
 */

chdir(dirname(__FILE__));
require_once './skipif.inc';

?>
--FILE--
<?php

// $Id: 17query.phpt,v 1.13 2005/02/16 13:54:52 danielc Exp $

/**
 * Connect to the database and make the phptest table.
 */
require_once './mktable.inc';


/**
 * Local error callback handler.
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


$dbh->setFetchMode(DB_FETCHMODE_ASSOC);


$res =& $dbh->query('DELETE FROM phptest WHERE a = 17');
print '1) delete: ' . ($res === DB_OK ? 'okay' : 'error') . "\n";

$res =& $dbh->query("INSERT INTO phptest (a, b, c) VALUES (17, 'one', 'One')");
print '2) insert: ' . ($res === DB_OK ? 'okay' : 'error') . "\n";

$res =& $dbh->query('INSERT INTO phptest (a, b, c) VALUES (?, ?, ?)', array(17, 'two', 'Two'));
print '3) insert: ' . ($res === DB_OK ? 'okay' : 'error') . "\n";


$res =& $dbh->query('SELECT a, b FROM phptest WHERE a = 17');
$row = $res->fetchRow();
print "4) a = {$row['a']}, b = {$row['b']}\n";
$res->free();  // keep fbsql happy.

$res =& $dbh->query('SELECT a, b FROM phptest WHERE c = ?', array('Two'));
$row = $res->fetchRow();
print "5) a = {$row['a']}, b = {$row['b']}\n";


$array = array(
    'foo' => 11,
    'bar' => 'three',
    'baz' => null,
);
$res =& $dbh->query('INSERT INTO phptest (a, b, d) VALUES (?, ?, ?)', $array);
print '6) insert: ' . ($res === DB_OK ? 'okay' : 'error') . "\n";

$res =& $dbh->query('SELECT a, b, d FROM phptest WHERE a = ?', 11);
$row = $res->fetchRow();
print "7) a = {$row['a']}, b = {$row['b']}, d = ";
if ($dbh->phptype == 'msql') {
    if (array_key_exists('d', $row)) {
        $type = gettype($row['d']);
        if ($type == 'NULL' || $row['d'] == '') {
            print "got expected value\n";
        } else {
            print "ERR: expected d's type to be NULL but it's $type and the value is ";
            print $row['d'] . "\n";
        }
    } else {
        // http://bugs.php.net/?id=31960
        print "Prior to PHP 4.3.11 or 5.0.4, PHP's msql extension silently"
              . " dropped columns with null values. You need to upgrade.\n";
    }
} else {
    $type = gettype($row['d']);
    if ($type == 'NULL' || $row['d'] == '') {
        print "got expected value\n";
    } else {
        print "ERR: expected d's type to be NULL but it's $type and the value is ";
        print $row['d'] . "\n";
    }
}


$res =& $dbh->query('DELETE FROM phptest WHERE a = ?', array(17));
print '8) delete: ' . ($res === DB_OK ? 'okay' : 'error') . "\n";

$res =& $dbh->query('DELETE FROM phptest WHERE a = ?', array(0));
print '9) delete with array(0) as param: ' . ($res === DB_OK ? 'okay' : 'error') . "\n";

$res =& $dbh->query('DELETE FROM phptest WHERE a = ?', 0);
print '10) delete with 0 as param: ' . ($res === DB_OK ? 'okay' : 'error') . "\n";


$dbh->setErrorHandling(PEAR_ERROR_RETURN);
drop_table($dbh, 'phptest');

?>
--EXPECT--
1) delete: okay
2) insert: okay
3) insert: okay
4) a = 17, b = one
5) a = 17, b = two
6) insert: okay
7) a = 11, b = three, d = got expected value
8) delete: okay
9) delete with array(0) as param: okay
10) delete with 0 as param: okay
