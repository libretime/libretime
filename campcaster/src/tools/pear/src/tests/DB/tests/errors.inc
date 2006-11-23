<?php

/**
 * Tests the drivers' error mapping
 *
 * Executed by driver/10errormap.phpt
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
 * @version    $Id: errors.inc,v 1.35 2005/02/28 02:02:41 danielc Exp $
 * @link       http://pear.php.net/package/DB
 */

/**
 * Determine if the error from the driver matches the error we expect
 *
 * If things are as we expect, print out "matches expected outcome"
 *
 * If things go wrong, print "UNEXPECTED OUTCOME" and display the
 * error's information.
 *
 * @param object  $e                 the DB_Error object from the query
 * @param int     $expected_db_code  the DB_ERROR* constant to expect
 * @param boolean $should_be_error   does the DBMS consider this an error?
 *
 * @return void
 */
function check_error($e, $expected_db_code, $should_be_error = true) {
    if ($should_be_error) {
        if (DB::isError($e)) {
            if ($e->getCode() == $expected_db_code) {
                print "matches expected outcome\n";
            } else {
                print "UNEXPECTED OUTCOME...\n";
                print '    PEAR::DB errorcode: ' . $e->getCode() . "\n";
                print '    ' . $e->getUserInfo() . "\n";
            }
        } else {
            print "\n    UNEXPECTED OUTCOME... expected error but it wasn't\n";
        }
    } else {
        if (DB::isError($e)) {
            print "UNEXPECTED OUTCOME... didn't expect error but it was\n";
            print '    PEAR::DB errorcode: ' . $e->getCode() . "\n";
            print '    ' . $e->getUserInfo() . "\n";
        } else {
            print "matches expected outcome\n";
        }
    }
}

/**
 * Local error callback handler
 * 
 * @param object  $o  PEAR error object automatically passed to this method
 * @return void
 * @see PEAR::setErrorHandling()
 */
function pe($o) {
    print "\n---------------\n";
    print "Having problems creating a table for testing...\n";
    print $o->getDebugInfo() . "\n";
    print "---------------\n";
}


$dbh->setErrorHandling(PEAR_ERROR_RETURN);


print 'DB_ERROR_NOSUCHTABLE for select:  ';
$res = $dbh->query('SELECT * FROM tableThatsBogus');
check_error($res, DB_ERROR_NOSUCHTABLE);

print 'DB_ERROR_NOSUCHTABLE for drop:  ';
$res = drop_table($dbh, 'tableThatsBogus');
check_error($res, DB_ERROR_NOSUCHTABLE);

print 'DB_ERROR_NOT_FOUND for drop index:  ';
switch ($dbh->phptype . ':' . $dbh->dbsyntax) {
    case 'fbsql:fbsql':
    case 'ibase:firebird':
    case 'ibase:ibase':
    case 'ifx:ifx':
    case 'odbc:db2':
    case 'oci8:oci8':
    case 'pgsql:pgsql':
    case 'sqlite:sqlite':
        $res = $dbh->query('DROP INDEX fakeindex');
        break;
    case 'mssql:mssql':
    case 'sybase:sybase':
        $res = $dbh->query('DROP INDEX phptest.fakeindex');
        break;
    case 'msql:msql':
        $res = $dbh->query('DROP INDEX fakeindex FROM phptest');
        break;
    default:
        $res = $dbh->query('DROP INDEX fakeindex ON phptest');
}
check_error($res, DB_ERROR_NOT_FOUND);


print 'DB_ERROR_ALREADY_EXISTS for create table:  ';
$res = $dbh->query($test_mktable_query);
check_error($res, DB_ERROR_ALREADY_EXISTS);

print 'DB_ERROR_ALREADY_EXISTS for create index:  ';
$res = drop_table($dbh, 'a');
$dbh->pushErrorHandling(PEAR_ERROR_CALLBACK, 'pe');
$res = $dbh->query('CREATE TABLE a (a INTEGER)');
$dbh->popErrorHandling();
$res = $dbh->query('CREATE INDEX aa_idx ON a (a)');
$res = $dbh->query('CREATE INDEX aa_idx ON a (a)');
switch ($dbh->phptype) {
    case 'fbsql':
        // FrontBase doesn't assign a specific code for this yet.
        check_error($res, DB_ERROR_ALREADY_EXISTS, false);
        break;
    default:
        check_error($res, DB_ERROR_ALREADY_EXISTS);
}
$res = drop_table($dbh, 'a');


print 'DB_ERROR_CONSTRAINT for primary key insert duplicate:  ';
$res = drop_table($dbh, 'a');
$dbh->pushErrorHandling(PEAR_ERROR_CALLBACK, 'pe');
switch ($dbh->phptype) {
    case 'msql':
        $res = $dbh->query('CREATE TABLE a (a INTEGER NOT NULL)');
        $res = $dbh->query('CREATE UNIQUE INDEX apk ON a (a)');
        break;
    default:
        $res = $dbh->query('CREATE TABLE a (a INTEGER NOT NULL, PRIMARY KEY (a))');
}
$dbh->popErrorHandling();
$res = $dbh->query('INSERT INTO a VALUES (1)');
$res = $dbh->query('INSERT INTO a VALUES (1)');
check_error($res, DB_ERROR_CONSTRAINT);


print 'DB_ERROR_CONSTRAINT for primary key update duplicate:  ';
$res = $dbh->query('INSERT INTO a VALUES (2)');
$res = $dbh->query('UPDATE a SET a=1 WHERE a=2');
check_error($res, DB_ERROR_CONSTRAINT);


print 'DB_ERROR_CONSTRAINT for unique key insert duplicate:  ';
$res = drop_table($dbh, 'a');
$dbh->pushErrorHandling(PEAR_ERROR_CALLBACK, 'pe');
switch ($dbh->phptype) {
    case 'msql':
        $res = $dbh->query('CREATE TABLE a (a INTEGER NOT NULL)');
        $res = $dbh->query('CREATE UNIQUE INDEX auk ON a (a)');
        break;
    default:
        $res = $dbh->query('CREATE TABLE a (a INTEGER NOT NULL, UNIQUE (a))');
}
$dbh->popErrorHandling();
$res = $dbh->query('INSERT INTO a VALUES (1)');
$res = $dbh->query('INSERT INTO a VALUES (1)');
check_error($res, DB_ERROR_CONSTRAINT);


print 'DB_ERROR_CONSTRAINT for unique key update duplicate:  ';
$res = $dbh->query('INSERT INTO a VALUES (2)');
$res = $dbh->query('UPDATE a SET a=1 WHERE a=2');
check_error($res, DB_ERROR_CONSTRAINT);


print 'DB_ERROR_CONSTRAINT for foreign key on insert:  ';
$res = drop_table($dbh, 'b');
$res = drop_table($dbh, 'a');
$dbh->pushErrorHandling(PEAR_ERROR_CALLBACK, 'pe');
switch ($dbh->phptype) {
    case 'mysql':
    case 'mysqli':
        $res = $dbh->query('CREATE TABLE a (a INT NOT NULL, '
                    . 'PRIMARY KEY (a)) '
                    . 'TYPE=INNODB');
        $res = $dbh->query('CREATE TABLE b (b INT, '
                    . 'INDEX par_ind (b), '
                    . 'FOREIGN KEY (b) REFERENCES a (a)) '
                    . 'TYPE=INNODB');
        $dbh->popErrorHandling();
        break;

    case 'msql':
        // msql does not support foreign keys
        $res = $dbh->query('CREATE TABLE a (a INTEGER NOT NULL)');
        $res = $dbh->query('CREATE UNIQUE INDEX auk ON a (a)');
        $dbh->popErrorHandling();
        $res = $dbh->query('CREATE TABLE b (b INTEGER REFERENCES a (a))');
        if (DB::isError($res)) {
            print "matches expected outcome\n";
            print "DB_ERROR_CONSTRAINT for foreign key on delete:  matches expected outcome\n";
        } else {
            print "WOW, it seems mSQL now supports references\n";
            print "WOW, it seems mSQL now supports references\n";
        }
        break;

    default:
        $res = $dbh->query('CREATE TABLE a (a INTEGER NOT NULL, PRIMARY KEY (a))');
        $res = $dbh->query('CREATE TABLE b (b INTEGER REFERENCES a (a))');
        $dbh->popErrorHandling();
}

if ($dbh->phptype != 'msql') {
    $res = $dbh->query('INSERT INTO a (a) values (1)');
    $res = $dbh->query('INSERT INTO b (b) values (2)');
    switch ($dbh->phptype) {
        case 'sqlite':
            check_error($res, DB_ERROR_CONSTRAINT, false);
            break;
        default:
            check_error($res, DB_ERROR_CONSTRAINT);
    }   

    print 'DB_ERROR_CONSTRAINT for foreign key on delete:  ';
    $res = $dbh->query('INSERT INTO b (b) values (1)');
    $res = $dbh->query('DELETE FROM a WHERE a = 1');
    switch ($dbh->phptype) {
        case 'sqlite':
            check_error($res, DB_ERROR_CONSTRAINT, false);
            break;
        default:
            check_error($res, DB_ERROR_CONSTRAINT);
    }
}


print 'DB_ERROR_CONSTRAINT_NOT_NULL on insert:  ';
$res = drop_table($dbh, 'peartestnull');
$dbh->pushErrorHandling(PEAR_ERROR_CALLBACK, 'pe');
$res = $dbh->query('CREATE TABLE peartestnull (a CHAR(3) NOT NULL)');
$dbh->popErrorHandling();
$res = $dbh->query('INSERT INTO peartestnull VALUES (NULL)');
check_error($res, DB_ERROR_CONSTRAINT_NOT_NULL);


print 'DB_ERROR_CONSTRAINT_NOT_NULL on update:  ';
$res = $dbh->query("INSERT INTO peartestnull VALUES ('one')");
$res = $dbh->query("UPDATE peartestnull SET a = NULL WHERE a = 'one'");
switch ($dbh->phptype) {
    case 'mysql':
    case 'mysqli':
        check_error($res, DB_ERROR_CONSTRAINT_NOT_NULL, false);
        break;
    default:
        check_error($res, DB_ERROR_CONSTRAINT_NOT_NULL);
}


print 'DB_ERROR_NOSUCHFIELD joining ON bogus column:  ';
$res = $dbh->query('SELECT * FROM phptest JOIN a ON (phptest.a = a.b)');
switch ($dbh->phptype . ':' . $dbh->dbsyntax) {
    case 'msql:msql':
    case 'odbc:access':
        check_error($res, DB_ERROR_SYNTAX);
        break;
    default:
        check_error($res, DB_ERROR_NOSUCHFIELD);
}


print 'DB_ERROR_NOSUCHFIELD joining USING bogus column:  ';
$res = $dbh->query('SELECT * FROM phptest JOIN a USING (b)');
switch ($dbh->phptype . ':' . $dbh->dbsyntax) {
    case 'ibase:ibase':
    case 'ibase:firebird':
    case 'msql:msql':
    case 'odbc:access':
    case 'odbc:db2':
    case 'sybase:sybase':
        check_error($res, DB_ERROR_SYNTAX);
        break;
    default:
        check_error($res, DB_ERROR_NOSUCHFIELD);
}


print 'DB_ERROR_DIVZERO:  ';
// Interbase detects the error on fetching
$res = $dbh->getAll('SELECT 0/0 FROM phptest');
switch ($dbh->phptype) {
    case 'odbc':
        switch ($dbh->dbsyntax) {
            case 'access':
                check_error($res, DB_ERROR_DIVZERO, false);
                break;
            case 'db2':
                check_error($res, DB_ERROR_DIVZERO);
                break;
        }
        break;
    case 'ibase':
    case 'ifx':
    case 'fbsql':
    case 'mssql':
    case 'mysql':
    case 'mysqli':
    case 'sqlite':
    case 'sybase':
        check_error($res, DB_ERROR_DIVZERO, false);
        break;
    case 'msql':
        check_error($res, DB_ERROR_SYNTAX);
        break;
    default:
        check_error($res, DB_ERROR_DIVZERO);
}


print 'DB_ERROR_INVALID_NUMBER putting chars in INT column:  ';
$res = $dbh->query("UPDATE phptest SET a = 'abc' WHERE a = 42");
switch ($dbh->phptype) {
    case 'mysql':
    case 'mysqli':
    case 'sqlite':
        check_error($res, DB_ERROR_INVALID_NUMBER, false);
        break;
    default:
        check_error($res, DB_ERROR_INVALID_NUMBER);
}


print 'DB_ERROR_INVALID_NUMBER putting float in INT column:  ';
$res = $dbh->query("UPDATE phptest SET a = 8.9 WHERE a = 42");
switch ($dbh->phptype) {
    case 'fbsql':
    case 'ibase':
    case 'ifx':
    case 'mssql':
    case 'mysql':
    case 'mysqli':
    case 'oci8':
    case 'odbc':
    case 'pgsql':
    case 'sqlite':
        check_error($res, DB_ERROR_INVALID_NUMBER, false);
        break;
    default:
        check_error($res, DB_ERROR_INVALID_NUMBER);
}


print 'DB_ERROR_INVALID_NUMBER putting excessive int in INT column:  ';
$res = $dbh->query("UPDATE phptest SET a = 18446744073709551616 WHERE a = 42");
switch ($dbh->phptype . ':' . $dbh->dbsyntax) {
    case 'ibase:ibase':
    case 'ibase:firebird':
        check_error($res, DB_ERROR_SYNTAX);
        break;
    case 'fbsql:fbsql':
    case 'msql:msql':
    case 'mssql:mssql':
    case 'mysql:mysql':
    case 'mysqli:mysqli':
    case 'oci8:oci8':
    case 'odbc:access':
    case 'sqlite:sqlite':
        check_error($res, DB_ERROR_INVALID_NUMBER, false);
        break;
    default:
        check_error($res, DB_ERROR_INVALID_NUMBER);
}


print 'DB_ERROR_INVALID_NUMBER putting int in CHAR column:  ';
$res = $dbh->query("UPDATE phptest SET b = 8 WHERE a = 42");
switch ($dbh->phptype . ':' . $dbh->dbsyntax) {
    case 'ibase:ibase':
    case 'ibase:firebird':
    case 'ifx:ifx':
    case 'mssql:mssql':
    case 'mysql:mysql':
    case 'mysqli:mysqli':
    case 'oci8:oci8':
    case 'odbc:access':
    case 'pgsql:pgsql':
    case 'sqlite:sqlite':
        check_error($res, DB_ERROR_INVALID_NUMBER, false);
        break;
    default:
        check_error($res, DB_ERROR_INVALID_NUMBER);
}


print 'DB_ERROR_NOSUCHFIELD:  ';
$res = $dbh->query('SELECT e FROM phptest');
check_error($res, DB_ERROR_NOSUCHFIELD);


print 'DB_ERROR_SYNTAX:  ';
$res = $dbh->query('CREATE');
check_error($res, DB_ERROR_SYNTAX);


print 'DB_ERROR_VALUE_COUNT_ON_ROW:  ';
$res = $dbh->query('INSERT INTO phptest (a) VALUES (678, 2)');
switch ($dbh->phptype) {
    case 'msql':
        check_error($res, DB_ERROR_VALUE_COUNT_ON_ROW, false);
        break;
    default:
        check_error($res, DB_ERROR_VALUE_COUNT_ON_ROW);
}


print 'DB_ERROR_INVALID on CHAR column data too long:  ';
$res = $dbh->query("INSERT INTO phptest (b) VALUES ('123456789.123456789.123456789.123456789.1')");
switch ($dbh->phptype . ':' . $dbh->dbsyntax) {
    case 'msql:msql':
    case 'mssql:mssql':
    case 'mysql:mysql':
    case 'mysqli:mysqli':
    case 'odbc:access':
    case 'sqlite:sqlite':
    case 'sybase:sybase':
        check_error($res, DB_ERROR_INVALID, false);
        break;
    case 'fbsql:fbsql':
        check_error($res, DB_ERROR_TRUNCATED);
        break;
    default:
        check_error($res, DB_ERROR_INVALID);
}


print 'DB_ERROR_INVALID on VARCHAR column data too long:  ';
$res = $dbh->query("INSERT INTO phptest (d) VALUES ('123456789.123456789.1')");
switch ($dbh->phptype . ':' . $dbh->dbsyntax) {
    case 'msql:msql':
    case 'mssql:mssql':
    case 'mysql:mysql':
    case 'mysqli:mysqli':
    case 'odbc:access':
    case 'sqlite:sqlite':
    case 'sybase:sybase':
        check_error($res, DB_ERROR_INVALID, false);
        break;
    case 'fbsql:fbsql':
        check_error($res, DB_ERROR_TRUNCATED);
        break;
    default:
        check_error($res, DB_ERROR_INVALID);
}



drop_table($dbh, 'phptest');
drop_table($dbh, 'b');
drop_table($dbh, 'a');
drop_table($dbh, 'peartestnull');
