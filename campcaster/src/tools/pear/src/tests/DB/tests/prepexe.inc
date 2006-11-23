<?php

/**
 * Tests the drivers' prepare and execute methods
 *
 * Executed by driver/06prepexec.phpt
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
 * @version    $Id: prepexe.inc,v 1.25 2005/02/16 13:54:51 danielc Exp $
 * @link       http://pear.php.net/package/DB
 */

$tmpfile = tempnam("/tmp", "phptmp");
register_shutdown_function("my_shutdown");
$fp = fopen($tmpfile, "w");
$filedata = "opaque placeholder's test";
fwrite($fp, $filedata);
fclose($fp);


/**
 * Local error callback handler
 *
 * Prints out an error message and kills the process.
 *
 * @param object  $o  PEAR error object automatically passed to this method
 * @return void
 * @see PEAR::setErrorHandling()
 */
function pe($o) {
    print "\n" . $o->toString();
    exit;
}

$dbh->setErrorHandling(PEAR_ERROR_CALLBACK, 'pe');


// 1) Multiple prepare/exec INSERT queries
echo "------------1------------\n";

$sth1 = $dbh->prepare("INSERT INTO phptest (a, b) VALUES (?, 'a')");
$sth2 = $dbh->prepare("INSERT INTO phptest (a,b) VALUES (!,?)");
$sth3 = $dbh->prepare("INSERT INTO phptest (a,b,c) VALUES (?,!,&)");
$sth4 = $dbh->prepare("INSERT INTO phptest (a, b) VALUES (72, 'direct')");
print "sth1,sth2,sth3,sth4 created\n";
print 'sth1: ? as param, passing as array... ';
if (($res = $dbh->execute($sth1, array(72))) === DB_OK) {
    print "sth1 executed\n";
} else {
    print "sth1 failed\n";
}

print 'sth2: ! and ? as params, passing as array... ';
if (($res = $dbh->execute($sth2, array(72, "that's right"))) === DB_OK) {
    print "sth2 executed\n";
} else {
    print "sth2 failed\n";
}

print 'sth3: ?, ! and & as params, passing as array... ';
switch ($dbh->phptype) {
    case 'msql':
        $res = $dbh->execute($sth3, array(72, "'it\\'s good'", $tmpfile));
        break;
    default:
        $res = $dbh->execute($sth3, array(72, "'it''s good'", $tmpfile));
}
if ($res === DB_OK) {
    print "sth3 executed\n";
} else {
    print "sth3 failed\n";
}

print 'sth4: no params... ';
if (($res = $dbh->execute($sth4)) === DB_OK) {
    print "sth4 executed\n";
} else {
    print "sth4 failed\n";
}
print_results();


// 2) One prepared, multiple time executed
echo "\n------------2------------\n";

$dbh->query('DELETE FROM phptest');
$sth = $dbh->prepare('INSERT INTO phptest (a, b, c, d) VALUES (?, ?, &, ?)');
$data = array(
    0 => array(72, 'set1', $tmpfile, '1234-56-78'),
    1 => array(72, 'set2', $tmpfile, null),
    2 => array(72, 'set3', $tmpfile, null)
);
$res = $dbh->executeMultiple($sth, $data);
print_results();


// 3) freePrepared() test
echo "\n------------3------------\n";

if ($dbh->freePrepared($sth)) {
    echo 'TRUE';
} else {
    echo 'FALSE';
}
echo "\n";
if ($dbh->freePrepared(666)) {
    echo 'TRUE';
} else {
    echo 'FALSE';
}
echo "\n";


// 4) SELECTs tests
echo "\n------------4------------\n";
$sth1 = $dbh->prepare("SELECT * FROM phptest WHERE a = ? ORDER BY b");
print_4($sth1, 72);
print_4($sth1, 71);
$sth2 = $dbh->prepare("SELECT * FROM phptest WHERE d = ? ORDER BY b");
print_4($sth2, '1234-56-78');
$sth3 = $dbh->prepare("SELECT * FROM phptest WHERE c = & ORDER BY b");
print_4($sth3, $tmpfile);


// 5) ASSOCIATIVE ARRAY queries
echo "\n------------5------------\n";

$sth5 = $dbh->prepare('INSERT INTO phptest (a, b, d) VALUES (?, ?, ?)');
$array = array(
    'foo' => 11,
    'bar' => 'three',
    'baz' => null,
);
$res = $dbh->execute($sth5, $array);
print 'insert: ' . ($res === DB_OK ? 'okay' : 'error') . "\n";

$sth6 = $dbh->prepare('SELECT a, b, d FROM phptest WHERE a = ?');
$res = $dbh->execute($sth6, array(11));
$row = $res->fetchRow(DB_FETCHMODE_ASSOC);
print "a = {$row['a']}, b = {$row['b']}, d = ";
if ($dbh->phptype == 'msql') {
    if (array_key_exists('d', $row)) {
        $type = gettype($row['d']);
        if ($type == 'NULL' || $row['d'] == '') {
            print "got expected outcome\n";
        } else {
            $type = gettype($row['d']);
            print "UN-expected outcome: $type\n";
        }
    } else {
        // http://bugs.php.net/?id=31960
        print "Prior to PHP 4.3.11 or 5.0.4, PHP's msql extension silently"
              . " dropped columns with null values. You need to upgrade.\n";
    }
} else {
    $type = gettype($row['d']);
    if ($type == 'string') {
        print "got expected outcome\n";
    } else {
        print "UN-expected outcome: $type\n";
    }
}

/**
 * Automatically free the prepared statements and results when the script
 * terminates
 *
 * @return void
 */
function my_shutdown() {
    global $tmpfile, $dbh, $sth1, $sth2, $sth3, $sth4, $sth5, $sth6, $res;

    switch ($dbh->phptype) {
        case 'ibase':
            /*
             * Interbase doesn't allow dropping tables that have result
             * sets still open.
             */
            $dbh->freePrepared($sth1);
            $dbh->freePrepared($sth2);
            $dbh->freePrepared($sth3);
            $dbh->freePrepared($sth4);
            $dbh->freePrepared($sth5);
            $dbh->freePrepared($sth6);
            $dbh->freeResult($res->result);
            break;
    }

    $dbh->setErrorHandling(PEAR_ERROR_RETURN);
    drop_table($dbh, 'phptest');

    unlink($tmpfile);
}

/**
 * Print out the data in test table
 *
 * @return void
 */
function print_results() {
    global $dbh;
    print "results:\n";
    $res = $dbh->query("SELECT * FROM phptest WHERE a = 72 ORDER BY b");
    $i = 0;
    while ($row = $res->fetchRow(DB_FETCHMODE_ORDERED)) {
        print '|' . implode(" - ", $row) . "|\n";
        $i++;
    }
    if (!$i) {
        print "The records were not found.  Did they get inserted?\n";
    }
}

/**
 * Execute the prepared statement and print out the data in the result
 *
 * @param resource     $sth   the statement handle to process
 * @param string|array $bind  the data that will replace the placeholders
 *
 * @return void
 */
function print_4($sth, $bind) {
    global $dbh;
    $res = $dbh->execute($sth, $bind);
    while ($row = $res->fetchRow(DB_FETCHMODE_ORDERED)) {
        print '|' . implode(" - ", $row) . "|\n";
    }
    echo "~~\n";
}
