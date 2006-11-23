<?php

/**
 * Tests the drivers' sequence (aka auto_increment) methods
 *
 * Executed by driver/05sequences.phpt
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
 * @version    $Id: sequences.inc,v 1.11 2005/02/18 22:38:43 danielc Exp $
 * @link       http://pear.php.net/package/DB
 */

/**
 * Local error handler
 */
function error_handler(&$obj) {
    print "sequences.inc error_handler:\n    ";
    print $obj->getDebugInfo() . "\n\n";
}
ob_implicit_flush(true);

$drop = $dbh->dropSequence('test');
if (DB::isError($drop) &&
    $drop->getCode() != DB_ERROR_NOSUCHTABLE)
{
    print "Could not drop sequence...\n";
    print $drop->getDebugInfo() . "\n\n";
    if ($dbh->phptype == 'ibase' &&
        $drop->getCode() == DB_ERROR_ACCESS_VIOLATION)
    {
        print "Use this query to provide the permissions needed:\n";
        print '   grant all on RDB$GENERATORS to <USERNAME>';
    }
    exit;
}

// 1) test that sequences are not created if "ondemand" is false

$e = $dbh->nextId("test", false);
if (DB::isError($e) && $e->getCode() == DB_ERROR_NOSUCHTABLE) {
    print "an error is the proper response here\n";
} else {
    if (DB::isError($e)) {
        if ($dbh->phptype == 'ibase' && $e->getCode() == DB_ERROR_SYNTAX) {
            print "an error is the proper response here\n";
        } else {
            print "test 1) we expected to get back 'DB Error: no such table'.\n";
            print "Here is the error we got:\n";
            print 'Code: ' . $e->getCode() . "\n";
            print 'Message: ' . $e->getMessage() . "\n";
            print 'Debug: ' . $e->getDebugInfo() . "\n\n";
        }
    } else {
        print "test 1) we expected to get back 'DB Error: no such table'.\n";
        print "But an error wasn't generated\n\n";
    }
}

// 2) test that the sequence is not created but the error is
// handled by the class error handler
$dbh->setErrorHandling(PEAR_ERROR_PRINT,
                       "an error cought by the error handler is good\n");
$e = $dbh->nextId("test", false);
if (!DB::isError($e)) {
    print "test 2) failed!\n";
}
$dbh->_default_error_mode = null;

// 3) test that sequences are created if "ondemand" is true, and that
// two successive nextIds return adjacent values
$a = $dbh->nextId("test");
$b = $dbh->nextId("test");
if (DB::isError($a)) {
    print 'a: ' . $a->getDebugInfo() . "\n\n";
} else {
    print "a=$a\n";
}
if (DB::isError($b)) {
    print 'b: ' . $b->getDebugInfo() . "\n\n";
} else {
    print "b=$b\n";
}
if (!DB::isError($a) && !DB::isError($b)) {
    print 'b-a=' . ($b-$a) . "\n";
}

// 4) test that the user-specified error handler is really disabled
// during nextId, with per-object handler as well as global handler
$dbh->dropSequence("test");

$dbh->setErrorHandling(PEAR_ERROR_CALLBACK, 'error_handler');
$c = $dbh->nextId("test");
if (!DB::isError($c)) {
    print "c=$c\n";
}
$dbh->dropSequence("test");
$dbh->_default_error_mode = null;
$d = $dbh->nextId("test");
if (!DB::isError($d)) {
    print "d=$d\n";
}

// 5) test that the sequence is handled right when the table is empty

// Backend with real sequences may don't like that
PEAR::pushErrorHandling(PEAR_ERROR_RETURN);
$dbh->query('DELETE FROM test_seq');
PEAR::popErrorHandling();
$e = $dbh->nextID('test');
if (DB::isError($d)) {
    print 'e: ' . $d->getDebugInfo() . "\n\n";
} else {
    print "e=$d\n";
}

// final clean-up
$dbh->dropSequence("test");
