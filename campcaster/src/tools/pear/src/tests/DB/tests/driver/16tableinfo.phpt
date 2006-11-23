--TEST--
DB_driver::tableInfo
--INI--
error_reporting = 2047
--SKIPIF--
<?php

/**
 * Calls tableInfo() in various ways and checks to see that the output
 * matches what's expected.
 *
 * These tests account for each DBMS using different column types and
 * reporting back different information.  These differences are accounted
 * for in the <var>$quirks</var> array, which has the following format:
 *
 * <pre>
 * 'driver' => array(
 *     'clob' => DBMS's column type for creating CLOB fields
 *     'date' => DBMS's column type for creating DATE fields
 *     'dateliteral' => The SQL keyword necessary for defining dates
 *     'finds_table' => Can this DBMS determine table names from queries?
 *     'size_from_table' => Does this DBMS know the column size via table name?
 *     'handles_results' => Can the DBMS get info from query results?
 *     'commands' => array(
 *         Extra commands to be passed to PHP's eval() function
 *     )
 *     0 => array(
 *         //  Info expected to be reported for phptest_fk.a
 *         //  (INTEGER NOT NULL) (UNIQUE KEY with d)
 *         'type' => Column type reported by the DBMS
 *         'len' => Column size reported by the DBMS
 *         'flags' => Flags reported by the DBMS
 *     )
 *     1 => array()  Info expected to be reported for phptest_fk.fk
 *                   (INTEGER NOT NULL) (PRIMARY KEY)
 *     2 => array()  Info expected to be reported for phptest_fk.c
 *                   (CLOB/CHAR/VARCHAR NULL)
 *     3 => array()  Info expected to be reported for phptest_fk.d
 *                   (DATE NOT NULL) (UNIQUE KEY with a)
 *     4 => array()  Info expected to be reported for phptest_fk.e
 *                   (CHAR(2) DEFAULT ' e' NOT NULL)
 *     5 => array()  Info expected to be reported for phptest_fk.f
 *                   (DECIMAL(2,1) NULL)
 *     9 => array()  Info expected to be reported for phptest.d
 *                   (VARCHAR(20) NULL)
 * )
 * </pre>
 *
 * @category Database
 * @package  DB
 * @version  $Id: 16tableinfo.phpt,v 1.32 2005/02/14 17:04:17 danielc Exp $
 * @author   Daniel Convissor <danielc@analysisandsolutions.com>
 * @see      DB_common::tableInfo()
 */

error_reporting(E_ALL);
chdir(dirname(__FILE__));
require_once './skipif.inc';
$tableInfo = $db->tableInfo('ajkdslfajoijkadie');
if (DB::isError($tableInfo) && $tableInfo->code == DB_ERROR_NOT_CAPABLE) {
    die("skip $tableInfo->message");
}

?>
--FILE--
<?php

//  $Id: 16tableinfo.phpt,v 1.32 2005/02/14 17:04:17 danielc Exp $

/**
 * Connect to the database and make the phptest table.
 */
require_once './mktable.inc';

/**
 * Local error callback handler.
 *
 * In general, it prints out an error message and kills the process.
 * But some errors are expected and allowed to exist.
 *
 * @param object  $o  PEAR error object automatically passed to this method
 * @return void
 * @see PEAR::setErrorHandling()
 */
function pe($o){
    global $dbh, $quirks;

    if ($o->getMessage() == "DB Error: can't distinguish duplicate field names") {
        print "NOTICE: $dbh->phptype can't distinguish duplicate field names";
        return;
    }

    if ($o->getCode() == DB_ERROR_NOT_CAPABLE &&
        !$quirks[$dbh->phptype . ':' . $dbh->dbsyntax]['handles_results'])
    {
        return;
    }

    $dbh->setErrorHandling(PEAR_ERROR_RETURN);
    drop_table($dbh, 'phptest');
    drop_table($dbh, 'phptest_fk');

    die($o->toString());
}

/**
 * Loop through an array returned from tableInfo(), compare the actual
 * contents to the expected contents.  If the actual results match the
 * expectations, say so.  If not, say so and show the information.
 *
 * @param array   $array     the array to be examined
 * @param string  $expected  the expected contents of the array
 * @param string  $field     field index number of $quriks and table
 * @param boolean $query     true if array is from a query or false if array
 *                           is tableInfo()
 * @return void
 */
function examineArrayData($array, $expected, $field = false, $query = true) {
    global $dbh, $quirks;

    $quirk_key = $dbh->phptype . ':' . $dbh->dbsyntax;

    if (DB::isError($array) && $array->getCode() == DB_ERROR_NOT_CAPABLE) {
        print "matched expected result\n";
        return;
    }

    if (!is_array($array)) {
        print "This DMBS didn't produce proper results\n";
        return;
    }

    if (is_int($field)) {
        $array = $array[$field];
    }

    $actual = '';
    foreach ($array as $key => $value) {
        if ($field !== false &&
            isset($quirks[$quirk_key][$field][$key]))
        {
            if ($key == 'flags' && $value == '' && $query &&
                !$quirks[$quirk_key]['finds_table'])
            {
                $actual .= "$key ... matched expected value\n";
            } else {
                if ($quirks[$quirk_key][$field][$key] == $value) {
                    $actual .= "$key ... matched expected value\n";
                } else {
                    if ($value == 0
                        && !$quirks[$quirk_key]['size_from_table'])
                    {
                        $actual .= "$key ... matched expected value\n";
                    } else {
                        $actual .= "$key ... was '$value' but we expected ";
                        $actual .= "'{$quirks[$quirk_key][$field][$key]}'\n";
                    }
                }
            }
        } else {
            if ($key == 'table') {
                if ($field <= 5) {
                    if ($value == 'phptest_fk') {
                        $actual .= "$key ... matched expected value\n";
                    } else {
                        if ($value == '' && $query &&
                            !$quirks[$quirk_key]['finds_table'])
                        {
                            $actual .= "$key ... matched expected value\n";
                        } else {
                            $actual .= "$key ... was '$value' but we expected 'phptest_fk'\n";
                        }
                    }
                } else {
                    if ($value == 'phptest') {
                        $actual .= "$key ... matched expected value\n";
                    } else {
                        if ($value == '' && $query &&
                            !$quirks[$quirk_key]['finds_table'])
                        {
                            $actual .= "$key ... matched expected value\n";
                        } else {
                            $actual .= "$key ... was '$value' but we expected 'phptest_fk'\n";
                        }
                    }
                }
            } else {
                $actual .= "$key => $value\n";
            }
        }
    }
    if ($actual == $expected) {
        print "matched expected result\n";
    } else {
        print "DIDN'T match expected values...\n";
        print "~~~~~~~~\nExpected:\n$expected\n";
        print "~~~~\nActual:\n$actual\n~~~~~~~~\n\n";
    }
}

/**
 * Loop through an array of table info data and return the results.
 *
 * @param array  $array  the array to be examined
 * @return string
 */
function returnArrayData($array) {
    global $dbh, $quirks;

    $quirk_key = $dbh->phptype . ':' . $dbh->dbsyntax;

    if (!$quirks[$quirk_key]['handles_results']) {
        return "\n";
    }

    $out = '';
    foreach ($array as $key => $value) {
        $out .= "$key => $value\n";
    }
    return $out;
}


$dbh->setErrorHandling(PEAR_ERROR_CALLBACK, 'pe');


$quirks = array(
    'fbsql:fbsql' => array(
        'clob' => 'CHAR(29)',
        'date' => 'DATE',
        'dateliteral' => ' DATE ',
        'finds_table' => false,
        'size_from_table' => false,
        'handles_results' => true,
        'commands' => array(
        ),
        0 => array(
            'type' => 'INTEGER',
            'len' => 0,
            'flags' => '',
        ),
        1 => array(
            'type' => 'INTEGER',
            'len' => 0,
            'flags' => 'not_null',
        ),
        2 => array(
            'type' => 'CHARACTER',
            'len' => 29,
            'flags' => '',
        ),
        3 => array(
            'type' => 'DATE',
            'len' => 0,
            'flags' => '',
        ),
        4 => array(
            'type' => 'CHARACTER',
            'len' => 2,
            'flags' => '',
        ),
        5 => array(
            'type' => 'DECIMAL',
            'len' => 0,
            'flags' => '',
        ),
        9 => array(
            'type' => 'CHARACTER',
            'len' => 20,
            'flags' => '',
        ),
    ),

    'ibase:ibase' => array(
        'clob' => 'VARCHAR(50)',
        'date' => 'DATE',
        'dateliteral' => '',
        'finds_table' => false,
        'size_from_table' => false,
        'handles_results' => true,
        'commands' => array(
        ),
        0 => array(
            'type' => 'INTEGER',
            'len' => 4,
            'flags' => 'unique_key not_null',
        ),
        1 => array(
            'type' => 'INTEGER',
            'len' => 4,
            'flags' => 'primary_key not_null',
        ),
        2 => array(
            'type' => 'VARCHAR',
            'len' => 50,
            'flags' => '',
        ),
        3 => array(
            'type' => 'DATE',
            'len' => 4,
            'flags' => 'unique_key not_null',
        ),
        4 => array(
            'type' => 'CHAR',
            'len' => 2,
            'flags' => 'not_null default',
        ),
        5 => array(
            'type' => 'NUMERIC(9,1)',
            'len' => 4,
            'flags' => '',
        ),
        9 => array(
            'type' => 'VARCHAR',
            'len' => 20,
            'flags' => '',
        ),
    ),

    'ibase:firebird' => array(
        'clob' => 'VARCHAR(50)',
        'date' => 'DATE',
        'dateliteral' => '',
        'finds_table' => false,
        'size_from_table' => false,
        'handles_results' => true,
        'commands' => array(
        ),
        0 => array(
            'type' => 'INTEGER',
            'len' => 4,
            'flags' => 'unique_key not_null',
        ),
        1 => array(
            'type' => 'INTEGER',
            'len' => 4,
            'flags' => 'primary_key not_null',
        ),
        2 => array(
            'type' => 'VARCHAR',
            'len' => 50,
            'flags' => '',
        ),
        3 => array(
            'type' => 'DATE',
            'len' => 4,
            'flags' => 'unique_key not_null',
        ),
        4 => array(
            'type' => 'CHAR',
            'len' => 2,
            'flags' => 'not_null default',
        ),
        5 => array(
            'type' => 'NUMERIC(9,1)',
            'len' => 4,
            'flags' => '',
        ),
        9 => array(
            'type' => 'VARCHAR',
            'len' => 20,
            'flags' => '',
        ),
    ),

    'ifx:ifx' => array(
        'clob' => 'CHAR(29)',
        'date' => 'CHAR(10)',
        'dateliteral' => '',
        'finds_table' => false,
        'size_from_table' => false,
        'handles_results' => true,
        'commands' => array(
        ),
        0 => array(
            'type' => 'SQLINT',
            'len' => 4,
            'flags' => 'not_null',
        ),
        1 => array(
            'type' => 'SQLINT',
            'len' => 4,
            'flags' => 'not_null',
        ),
        2 => array(
            'type' => 'SQLCHAR',
            'len' => 29,
            'flags' => '',
        ),
        3 => array(
            'type' => 'SQLCHAR',
            'len' => 10,
            'flags' => 'not_null',
        ),
        4 => array(
            'type' => 'SQLCHAR',
            'len' => 2,
            'flags' => 'not_null',
        ),
        5 => array(
            'type' => 'SQLDECIMAL',
            'len' => 513,
            'flags' => '',
        ),
        9 => array(
            'type' => 'SQLCHAR',
            'len' => 20,
            'flags' => '',
        ),
    ),

    'msql:msql' => array(
        'clob' => 'TEXT(255)',
        'date' => 'CHAR(10)',
        'dateliteral' => '',
        'finds_table' => true,
        'size_from_table' => true,
        'handles_results' => true,
        'commands' => array(
        ),
        0 => array(
            'type' => 'int',
            'len' => 4,
            'flags' => 'not_null',
        ),
        1 => array(
            'type' => 'int',
            'len' => 4,
            // for some reason, the unique property always contains 0
            // 'flags' => 'unique_key not_null',
            'flags' => 'not_null',
        ),
        2 => array(
            'type' => 'text',
            'len' => 255,
            'flags' => '',
        ),
        3 => array(
            'type' => 'char',
            'len' => 10,
            'flags' => 'not_null',
        ),
        4 => array(
            'type' => 'char',
            'len' => 2,
            'flags' => 'not_null',
        ),
        5 => array(
            'type' => 'real',
            'len' => 8,
            'flags' => '',
        ),
        9 => array(
            'type' => 'char',
            'len' => 20,
            'flags' => '',
        ),
    ),

    'mssql:mssql' => array(
        'clob' => 'TEXT',
        'date' => 'SMALLDATETIME',
        'dateliteral' => '',
        'finds_table' => false,
        'size_from_table' => false,
        'handles_results' => true,
        'commands' => array(
            'ini_set("mssql.datetimeconvert", "Off");',
            '$dbh->query("SET DATEFORMAT ymd");',
        ),
        0 => array(
            'type' => 'int',
            'len' => 4,
            'flags' => 'multiple_key unique_key not_null',
        ),
        1 => array(
            'type' => 'int',
            'len' => 4,
            'flags' => 'primary_key not_null',
        ),
        2 => array(
            'type' => 'text',
            'len' => 4096,
            'flags' => '',
        ),
        3 => array(
            'type' => 'datetime',
            'len' => 4,
            'flags' => 'multiple_key unique_key not_null',
        ),
        4 => array(
            'type' => 'char',
            'len' => 2,
            'flags' => 'not_null',
        ),
        5 => array(
            'type' => 'real',
            'len' => 19,
            'flags' => '',
        ),
        9 => array(
            'type' => 'char',
            'len' => 20,
            'flags' => '',
        ),
    ),

    'mysql:mysql' => array(
        'clob' => 'TEXT',
        'date' => 'DATE',
        'dateliteral' => '',
        'finds_table' => true,
        'size_from_table' => true,
        'handles_results' => true,
        'commands' => array(
        ),
        0 => array(
            'type' => 'int',
            'len' => 11,
            'flags' => 'not_null multiple_key',
        ),
        1 => array(
            'type' => 'int',
            'len' => 11,
            'flags' => 'not_null primary_key',
        ),
        2 => array(
            'type' => 'blob',
            'len' => 65535,
            'flags' => 'blob',
        ),
        3 => array(
            'type' => 'date',
            'len' => 10,
            'flags' => 'not_null multiple_key',
        ),
        4 => array(
            'type' => 'string',
            'len' => 2,
            'flags' => 'not_null',
        ),
        5 => array(
            'type' => 'real',
            'len' => 4,
            'flags' => '',
        ),
        9 => array(
            'type' => 'string',
            'len' => 20,
            'flags' => '',
        ),
    ),

    'mysqli:mysqli' => array(
        'clob' => 'TEXT',
        'date' => 'DATE',
        'dateliteral' => '',
        'finds_table' => true,
        'size_from_table' => false,
        'handles_results' => true,
        'commands' => array(
        ),
        0 => array(
            'type' => 'int',
            'len' => 2,
            'flags' => 'not_null multiple_key group_by',
        ),
        1 => array(
            'type' => 'int',
            'len' => 0,
            'flags' => 'not_null primary_key group_by',
        ),
        2 => array(
            'type' => 'blob',
            'len' => 0,
            'flags' => 'blob',
        ),
        3 => array(
            'type' => 'date',
            'len' => 10,
            'flags' => 'not_null multiple_key',
        ),
        4 => array(
            'type' => 'char',
            'len' => 0,
            'flags' => 'not_null',
        ),
        5 => array(
            'type' => 'decimal',
            'len' => 0,
            'flags' => 'group_by',
        ),
        9 => array(
            'type' => 'varchar',
            'len' => 10,
            'flags' => '',
        ),
    ),

    'oci8:oci8' => array(
        'clob' => 'CLOB',
        'date' => 'DATE',
        'dateliteral' => '',
        'finds_table' => false,
        'size_from_table' => false,
        'handles_results' => true,
        'commands' => array(
            '$dbh->query("ALTER SESSION SET NLS_DATE_FORMAT = \'YYYY-MM-DD\'");',
        ),
        0 => array(
            'type' => 'NUMBER',
            'len' => 22,
            'flags' => 'not_null',
        ),
        1 => array(
            'type' => 'NUMBER',
            'len' => 22,
            'flags' => 'not_null',
        ),
        2 => array(
            'type' => 'CLOB',
            'len' => 4000,
            'flags' => '',
        ),
        3 => array(
            'type' => 'DATE',
            'len' => 7,
            'flags' => 'not_null',
        ),
        4 => array(
            'type' => 'CHAR',
            'len' => 2,
            'flags' => 'not_null',
        ),
        5 => array(
            'type' => 'NUMBER',
            'len' => 22,
            'flags' => '',
        ),
        9 => array(
            'type' => 'VARCHAR',
            'len' => 20,
            'flags' => '',
        ),
    ),

    'odbc:access' => array(
        'clob' => 'TEXT',
        'date' => 'DATETIME',
        'dateliteral' => '',
        'finds_table' => false,
        'size_from_table' => false,
        'handles_results' => true,
        'commands' => array(
        ),
        0 => array(
            'type' => 'INTEGER',
            'len' => 10,
            'flags' => 'not_null',
        ),
        1 => array(
            'type' => 'INTEGER',
            'len' => 10,
            'flags' => 'not_null',
        ),
        2 => array(
            'type' => 'LONGCHAR',
            'len' => 255,
            'flags' => '',
        ),
        3 => array(
            'type' => 'DATETIME',
            'len' => 19,
            'flags' => 'not_null',
        ),
        4 => array(
            'type' => 'VARCHAR',
            'len' => 2,
            'flags' => 'not_null',
        ),
        5 => array(
            'type' => 'DECIMAL',
            'len' => 15,
            'flags' => '',
        ),
        9 => array(
            'type' => 'VARCHAR',
            'len' => 20,
            'flags' => '',
        ),
    ),

    'odbc:db2' => array(
        'clob' => 'CLOB',
        'date' => 'DATE',
        'dateliteral' => '',
        'finds_table' => false,
        'size_from_table' => false,
        'handles_results' => true,
        'commands' => array(
        ),
        0 => array(
            'type' => 'INTEGER',
            'len' => 10,
            'flags' => 'not_null',
        ),
        1 => array(
            'type' => 'INTEGER',
            'len' => 10,
            'flags' => 'not_null',
        ),
        2 => array(
            'type' => 'CLOB',
            'len' => 1048576,
            'flags' => '',
        ),
        3 => array(
            'type' => 'DATE',
            'len' => 10,
            'flags' => 'not_null',
        ),
        4 => array(
            'type' => 'CHAR',
            'len' => 2,
            'flags' => 'not_null',
        ),
        5 => array(
            'type' => 'DECIMAL',
            'len' => 2,
            'flags' => '',
        ),
        9 => array(
            'type' => 'VARCHAR',
            'len' => 20,
            'flags' => '',
        ),
    ),

    'pgsql:pgsql' => array(
        'clob' => 'TEXT',
        'date' => 'DATE',
        'dateliteral' => '',
        'finds_table' => false,
        'size_from_table' => false,
        'handles_results' => true,
        'commands' => array(
            '$dbh->query("SET DATESTYLE = ISO");',
        ),
        0 => array(
            'type' => 'int4',
            'len' => 4,
            'flags' => 'not_null unique_key multiple_key',
        ),
        1 => array(
            'type' => 'int4',
            'len' => 4,
            'flags' => 'not_null primary_key',
        ),
        2 => array(
            'type' => 'text',
            'len' => -1,
            'flags' => '',
        ),
        3 => array(
            'type' => 'date',
            'len' => 4,
            'flags' => 'not_null unique_key multiple_key',
        ),
        4 => array(
            'type' => 'bpchar',
            'len' => -1,
            'flags' => 'not_null default_%20e',
        ),
        5 => array(
            'type' => 'numeric',
            'len' => -1,
            'flags' => '',
        ),
        9 => array(
            'type' => 'varchar',
            'len' => -1,
            'flags' => '',
        ),
    ),

    'sqlite:sqlite' => array(
        'clob' => 'CLOB',
        'date' => 'DATE',
        'dateliteral' => '',
        'finds_table' => false,
        'size_from_table' => false,
        'handles_results' => false,
        'commands' => array(
        ),
        0 => array(
            'type' => 'INTEGER',
            'len' => 0,
            'flags' => 'not_null',
        ),
        1 => array(
            'type' => 'INTEGER',
            'len' => 0,
            'flags' => 'primary_key not_null',
        ),
        2 => array(
            'type' => 'CLOB',
            'len' => 0,
            'flags' => '',
        ),
        3 => array(
            'type' => 'DATE',
            'len' => 0,
            'flags' => 'not_null',
        ),
        4 => array(
            'type' => 'CHAR',
            'len' => 2,
            'flags' => 'not_null default_%20e',
        ),
        5 => array(
            'type' => 'DECIMAL',
            'len' => 2,
            'flags' => '',
        ),
        9 => array(
            'type' => 'VARCHAR',
            'len' => 20,
            'flags' => '',
        ),
    ),

    'sybase:sybase' => array(
        'clob' => 'TEXT',
        'date' => 'SMALLDATETIME',
        'dateliteral' => '',
        'finds_table' => false,
        'size_from_table' => false,
        'handles_results' => true,
        'commands' => array(
            '$dbh->query("SET DATEFORMAT ymd");',
        ),
        0 => array(
            'type' => 'int',
            'len' => 11,
            'flags' => 'multiple_key unique_key',
        ),
        1 => array(
            'type' => 'int',
            'len' => 11,
            'flags' => 'unique_key',
        ),
        2 => array(
            'type' => 'string',
            'len' => 32768,
            'flags' => '',
        ),
        3 => array(
            'type' => 'datetime',
            'len' => 29,
            'flags' => 'multiple_key unique_key',
        ),
        4 => array(
            'type' => 'string',
            'len' => 2,
            'flags' => '',
        ),
        5 => array(
            'type' => 'real',
            'len' => 4,
            'flags' => '',
        ),
        9 => array(
            'type' => 'string',
            'len' => 20,
            'flags' => '',
        ),
    ),
);


$quirk_key = $dbh->phptype . ':' . $dbh->dbsyntax;

if (!isset($quirks[$quirk_key])) {
    die("This test does not yet support $quirk_key");
}

if (count($quirks[$quirk_key]['commands'])) {
    foreach ($quirks[$quirk_key]['commands'] as $value) {
        eval($value);
    }
}


$dbh->query('DELETE FROM phptest');
$dbh->query("INSERT INTO phptest VALUES (1, 'one', 'One', '2001-02-16')");
$dbh->query("INSERT INTO phptest VALUES (2, 'two', 'Two', '2001-02-15')");
$dbh->query("INSERT INTO phptest VALUES (3, 'three', 'Three', '2001-02-14')");


$dbh->setErrorHandling(PEAR_ERROR_RETURN);
drop_table($dbh, 'phptest_fk');
$dbh->setErrorHandling(PEAR_ERROR_CALLBACK, 'pe');


if ($quirk_key == 'odbc:access') {
    $default_e = '';
    $decimal   = 'NUMERIC';
} elseif ($quirk_key == 'msql:msql') {
    $default_e = '';
    $decimal   = 'REAL';
} else {
    $default_e = "DEFAULT ' e'";
    $decimal   = 'DECIMAL(2,1)';
}

// $null is set in mktable.inc

switch ($dbh->phptype) {
    case 'msql':
        $dbh->query("
            CREATE TABLE phptest_fk (
                a INTEGER NOT NULL,
                fk INTEGER NOT NULL,
                c {$quirks[$quirk_key]['clob']} $null,
                d {$quirks[$quirk_key]['date']} NOT NULL,
                e CHAR(2) $default_e NOT NULL,
                f $decimal $null
            )
        ");
        $dbh->query('CREATE UNIQUE INDEX fkpk ON phptest_fk (fk)');
        $dbh->query('CREATE UNIQUE INDEX fkuk ON phptest_fk (a, d)');
        break;
    default:
        $dbh->query("
            CREATE TABLE phptest_fk (
                a INTEGER NOT NULL,
                fk INTEGER NOT NULL,
                c {$quirks[$quirk_key]['clob']} $null,
                d {$quirks[$quirk_key]['date']} NOT NULL,
                e CHAR(2) $default_e NOT NULL,
                f $decimal $null,
                PRIMARY KEY (fk),
                UNIQUE (a, d)
            )
        ");
}

$dbh->query("CREATE INDEX thedidx ON phptest_fk (d)");
$dbh->query("INSERT INTO phptest_fk VALUES (10, 1, 'One',"
            . $quirks[$quirk_key]['dateliteral'] . "'2001-02-16',  'c1', 1.1)");
$dbh->query("INSERT INTO phptest_fk VALUES (20, 2, 'Two',"
            . $quirks[$quirk_key]['dateliteral'] . "'2001-02-15', 'c2', 2.2)");
$dbh->query("INSERT INTO phptest_fk VALUES (30, 3, 'Three',"
            . $quirks[$quirk_key]['dateliteral'] . "'2001-02-14', 'c3', 3.3)");

function &runQuery() {
    global $dbh, $resultobj;

    $quirk_key = $dbh->phptype . ':' . $dbh->dbsyntax;

    switch ($quirk_key) {
        case 'odbc:db2':
            // can't extract blob's this way so make a fake column
            $query = "SELECT phptest_fk.a, phptest_fk.fk, 'tempxyz' AS c,"
                   . ' phptest_fk.d, phptest_fk.e, phptest_fk.f,'
                   . ' phptest.a, phptest.b, phptest.c, phptest.d'
                   . ' FROM phptest_fk, phptest'
                   . ' WHERE phptest.a = phptest_fk.fk';
            break;
        case 'msql:msql':
            $query = 'SELECT phptest_fk.a, phptest_fk.fk, phptest_fk.c,'
                   . ' phptest_fk.d, phptest_fk.e, phptest_fk.f,'
                   . ' phptest.a, phptest.b, phptest.c, phptest.d'
                   . ' FROM phptest_fk, phptest'
                   . ' WHERE phptest.a = phptest_fk.fk';
            break;
        default:
            $query = 'SELECT phptest_fk.a, phptest_fk.fk, phptest_fk.c,'
                   . ' phptest_fk.d, phptest_fk.e, phptest_fk.f,'
                   . ' phptest.a, phptest.b, phptest.c, phptest.d'
                   . ' FROM phptest_fk, phptest'
                   . ' WHERE phptest.a = phptest_fk.fk';
    }
    $resultobj =& $dbh->query($query);
    return $resultobj;
}


$expected01 = 'table ... matched expected value
name => a
type ... matched expected value
len ... matched expected value
flags ... matched expected value
';

$expected02 = 'table ... matched expected value
name => fk
type ... matched expected value
len ... matched expected value
flags ... matched expected value
';

$expected03 = 'table ... matched expected value
name => c
type ... matched expected value
len ... matched expected value
flags ... matched expected value
';

$expected04 = 'table ... matched expected value
name => d
type ... matched expected value
len ... matched expected value
flags ... matched expected value
';

$expected05 = 'table ... matched expected value
name => e
type ... matched expected value
len ... matched expected value
flags ... matched expected value
';

$expected06 = 'table ... matched expected value
name => f
type ... matched expected value
len ... matched expected value
flags ... matched expected value
';

$expected10 = 'table ... matched expected value
name => d
type ... matched expected value
len ... matched expected value
flags ... matched expected value
';



print "\n==========================================\n";
print "Passing result OBJECT to method in DB_<type>.\n";
print "Output = default.\n";
print "------------------------------------------\n";
$resultobj =& runQuery();
$array = $dbh->tableInfo($resultobj);

print "\ncolumn 0:\n";
examineArrayData($array, $expected01, 0);

print "\ncolumn 9:\n";
examineArrayData($array, $expected10, 9);


print "\n==========================================\n";
print "Passing result ID to method in DB_<type>.\n";
print "Output = DB_TABLEINFO_ORDER.\n";
print "------------------------------------------\n";
$resultobj =& runQuery();
$array = $dbh->tableInfo($resultobj->result, DB_TABLEINFO_ORDER);

print "\ncolumn 0:\n";
examineArrayData($array, $expected01, 0);

print "\ncolumn 3:\n";
examineArrayData($array, $expected04, 3);

print "\nnum_fields: ";
if ($quirks[$quirk_key]['handles_results'] && $array['num_fields'] == 10) {
    print "matched expected result\n";
} elseif (DB::isError($array) && $array->getCode() == DB_ERROR_NOT_CAPABLE) {
    print "matched expected result\n";
} else {
    print "This DMBS didn't produce proper results\n";
}

print "\norder:\n";
if ($quirks[$quirk_key]['handles_results'] && is_array($array['order'])) {
    $expected = 'a => 6
b => 7
c => 8
d => 9
e => 4
f => 5
fk => 1
';
    ksort($array['order']);
    examineArrayData($array['order'], $expected);
} elseif (DB::isError($array) && $array->getCode() == DB_ERROR_NOT_CAPABLE) {
    print "matched expected result\n";
} else {
    print "This DMBS didn't produce proper results\n";
}



print "\n==========================================\n";
print "Passing DB_TABLEINFO_ORDERTABLE to method in DB_result.\n";
print "Output = DB_TABLEINFO_ORDERTABLE.\n";
print "------------------------------------------\n";
$resultobj =& runQuery();
$array = $resultobj->tableInfo(DB_TABLEINFO_ORDERTABLE);
// Free this to keep interbase happy.
$resultobj->free();

print "\ncolumn 0:\n";
examineArrayData($array, $expected01, 0);

print "\ncolumn 3:\n";
examineArrayData($array, $expected04, 3);

print "\nnum_fields: ";
if ($quirks[$quirk_key]['handles_results'] && $array['num_fields'] == 10) {
    print "matched expected result\n";
} elseif (DB::isError($array) && $array->getCode() == DB_ERROR_NOT_CAPABLE) {
    print "matched expected result\n";
} else {
    print "This DMBS didn't produce proper results\n";
}


print 'ordertable[phptest]: ';
$expected = 'a => 6
b => 7
c => 8
d => 9
';
if ($quirks[$quirk_key]['handles_results']
    && isset($array['ordertable']['phptest'])) {
    $actual = returnArrayData($array['ordertable']['phptest']);
} else {
    $actual = '';
}
if ($actual == $expected) {
    print "matched expected result\n";
} else {
    if (($quirks[$quirk_key]['finds_table'] === false 
        || DB::isError($array) && $array->getCode() == DB_ERROR_NOT_CAPABLE)
        && $actual == '') {
        print "matched expected result\n";
    } else {
        print "DIDN'T match expected values...\n";
        print "~~~~~~~~\nExpected:\n$expected\n";
        print "~~~~\nActual:\n$actual\n~~~~~~~~\n\n";
    }
}


print 'ordertable[phptest_fk]: ';
$expected = 'a => 0
fk => 1
c => 2
d => 3
e => 4
f => 5
';
if ($quirks[$quirk_key]['handles_results']
    && isset($array['ordertable']['phptest_fk'])) {
    $actual = returnArrayData($array['ordertable']['phptest_fk']);
} else {
    $actual = '';
}
if ($actual == $expected) {
    print "matched expected result\n";
} else {
    if (($quirks[$quirk_key]['finds_table'] === false 
        || DB::isError($array) && $array->getCode() == DB_ERROR_NOT_CAPABLE)
        && $actual == '') {
        print "matched expected result\n";
    } else {
        print "DIDN'T match expected values...\n";
        print "~~~~~~~~\nExpected:\n$expected\n";
        print "~~~~\nActual:\n$actual\n~~~~~~~~\n\n";
    }
}


print "\n==========================================\n";
print "Passing TABLE NAME 'phptest_fk' to method in DB_<driver>.\n";
print "Output = default.\n";
print "------------------------------------------\n";
$array = $dbh->tableInfo('phptest_fk');

print "\ncolumn 0:\n";
examineArrayData($array, $expected01, 0, false);

print "\ncolumn 1:\n";
examineArrayData($array, $expected02, 1, false);

print "\ncolumn 2:\n";
examineArrayData($array, $expected03, 2, false);

print "\ncolumn 3:\n";
examineArrayData($array, $expected04, 3, false);

print "\ncolumn 4:\n";
examineArrayData($array, $expected05, 4, false);

print "\ncolumn 5:\n";
examineArrayData($array, $expected06, 5, false);


print "\n==========================================\n";
print "Passing TABLE NAME 'phptest_fk' to method in DB_<driver>.\n";
print "Output = DB_TABLEINFO_FULL.\n";
print "------------------------------------------\n";
$array = $dbh->tableInfo('phptest_fk', DB_TABLEINFO_FULL);

print "\ncolumn 0:\n";
examineArrayData($array, $expected01, 0, false);

print "\norder:\n";
$expect ='a => 0
fk => 1
c => 2
d => 3
e => 4
f => 5
';
examineArrayData($array['order'], $expect, false, false);

print "\nordertable[phptest_fk]:\n";
$expect ='a => 0
fk => 1
c => 2
d => 3
e => 4
f => 5
';
examineArrayData($array['ordertable']['phptest_fk'], $expect);



print "\n==========================================\n";
print "Passing TABLE NAME 'phptest_fk' to method in DB_<driver> AGAIN.\n";
print "Output = DB_TABLEINFO_FULL, lowercasing turned off.\n";
print "------------------------------------------\n";
$dbh->setOption('portability', DB_PORTABILITY_ALL ^ DB_PORTABILITY_LOWERCASE);
$array = $dbh->tableInfo('phptest_fk', DB_TABLEINFO_FULL);

// testing non-lowercasing above to ensure program doesn't die.
// lowercase the names here to ensure test uniformity.
$array[0]['table'] = strtolower($array[0]['table']);
$array[0]['name'] = strtolower($array[0]['name']);

print "\ncolumn 0:\n";
examineArrayData($array, 0, false);


$dbh->setErrorHandling(PEAR_ERROR_RETURN);
drop_table($dbh, 'phptest');
drop_table($dbh, 'phptest_fk');

?>
--EXPECT--
==========================================
Passing result OBJECT to method in DB_<type>.
Output = default.
------------------------------------------

column 0:
matched expected result

column 9:
matched expected result

==========================================
Passing result ID to method in DB_<type>.
Output = DB_TABLEINFO_ORDER.
------------------------------------------

column 0:
matched expected result

column 3:
matched expected result

num_fields: matched expected result

order:
matched expected result

==========================================
Passing DB_TABLEINFO_ORDERTABLE to method in DB_result.
Output = DB_TABLEINFO_ORDERTABLE.
------------------------------------------

column 0:
matched expected result

column 3:
matched expected result

num_fields: matched expected result
ordertable[phptest]: matched expected result
ordertable[phptest_fk]: matched expected result

==========================================
Passing TABLE NAME 'phptest_fk' to method in DB_<driver>.
Output = default.
------------------------------------------

column 0:
matched expected result

column 1:
matched expected result

column 2:
matched expected result

column 3:
matched expected result

column 4:
matched expected result

column 5:
matched expected result

==========================================
Passing TABLE NAME 'phptest_fk' to method in DB_<driver>.
Output = DB_TABLEINFO_FULL.
------------------------------------------

column 0:
matched expected result

order:
matched expected result

ordertable[phptest_fk]:
matched expected result

==========================================
Passing TABLE NAME 'phptest_fk' to method in DB_<driver> AGAIN.
Output = DB_TABLEINFO_FULL, lowercasing turned off.
------------------------------------------

column 0:
matched expected result
