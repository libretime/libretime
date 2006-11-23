--TEST--
DB_driver::getListOf
--INI--
error_reporting = 2047
--SKIPIF--
<?php chdir(dirname(__FILE__)); require_once './skipif.inc'; ?>
--FILE--
<?php

require_once './mktable.inc';

/*
 * An array with keys containing the $type to be passed to the getListOf()
 * method.  The values of the main array are a sub-array that has keys
 * listing the phptype and dbsyntax of each driver and values of the
 * result expected from the combination of all these factors.
 */
$tests = array(
    'tables' => array(
        'dbase:dbase' => DB_ERROR_UNSUPPORTED,
        'fbsql:fbsql' => 'array',
        'ibase:ibase' => 'array',
        'ibase:firebird' => 'array',
        'ifx:ifx' => 'array',
        'msql:msql' => 'array',
        'mssql:mssql' => 'array',
        'mysql:mysql' => 'array',
        'mysqli:mysqli' => 'array',
        'oci8:oci8' => 'array',
        'odbc:access' => 'array',
        'odbc:db2' => 'array',
        'pgsql:pgsql' => 'array',
        'sqlite:sqlite' => 'array',
        'sybase:sybase' => 'array',
    ),
    'views' => array(
        'dbase:dbase' => DB_ERROR_UNSUPPORTED,
        'fbsql:fbsql' => 'array',
        'ibase:ibase' => 'array',
        'ibase:firebird' => 'array',
        'ifx:ifx' => DB_ERROR_UNSUPPORTED,
        'msql:msql' => DB_ERROR_UNSUPPORTED,
        'mssql:mssql' => 'array',
        'mysql:mysql' => DB_ERROR_UNSUPPORTED,
        'mysqli:mysqli' => DB_ERROR_UNSUPPORTED,
        'oci8:oci8' => DB_ERROR_UNSUPPORTED,
        'odbc:access' => 'array',
        'odbc:db2' => 'array',
        'pgsql:pgsql' => 'array',
        'sqlite:sqlite' => DB_ERROR_UNSUPPORTED,
        'sybase:sybase' => 'array',
    ),
    'users' => array(
        'dbase:dbase' => DB_ERROR_UNSUPPORTED,
        'fbsql:fbsql' => 'array',
        'ibase:ibase' => 'array',
        'ibase:firebird' => 'array',
        'ifx:ifx' => DB_ERROR_UNSUPPORTED,
        'msql:msql' => DB_ERROR_UNSUPPORTED,
        'mssql:mssql' => DB_ERROR_UNSUPPORTED,
        'mysql:mysql' => DB_ERROR_ACCESS_VIOLATION,
        'mysqli:mysqli' => DB_ERROR_ACCESS_VIOLATION,
        'oci8:oci8' => DB_ERROR_UNSUPPORTED,
        'odbc:access' => DB_ERROR_UNSUPPORTED,
        'odbc:db2' => DB_ERROR_UNSUPPORTED,
        'pgsql:pgsql' => 'array',
        'sqlite:sqlite' => DB_ERROR_UNSUPPORTED,
        'sybase:sybase' => DB_ERROR_UNSUPPORTED,
    ),
    'databases' => array(
        'dbase:dbase' => DB_ERROR_UNSUPPORTED,
        'fbsql:fbsql' => DB_ERROR_UNSUPPORTED,
        'ibase:ibase' => DB_ERROR_UNSUPPORTED,
        'ibase:firebird' => DB_ERROR_UNSUPPORTED,
        'ifx:ifx' => DB_ERROR_UNSUPPORTED,
        'msql:msql' => 'array',
        'mssql:mssql' => DB_ERROR_UNSUPPORTED,
        'mysql:mysql' => 'array',
        'mysqli:mysqli' => 'array',
        'oci8:oci8' => DB_ERROR_UNSUPPORTED,
        'odbc:access' => 'array',
        'odbc:db2' => 'array',
        'pgsql:pgsql' => 'array',
        'sqlite:sqlite' => DB_ERROR_UNSUPPORTED,
        'sybase:sybase' => DB_ERROR_UNSUPPORTED,
    ),
    'functions' => array(
        'dbase:dbase' => DB_ERROR_UNSUPPORTED,
        'fbsql:fbsql' => 'array',
        'ibase:ibase' => DB_ERROR_UNSUPPORTED,
        'ibase:firebird' => DB_ERROR_UNSUPPORTED,
        'ifx:ifx' => DB_ERROR_UNSUPPORTED,
        'msql:msql' => DB_ERROR_UNSUPPORTED,
        'mssql:mssql' => DB_ERROR_UNSUPPORTED,
        'mysql:mysql' => DB_ERROR_UNSUPPORTED,
        'mysqli:mysqli' => DB_ERROR_UNSUPPORTED,
        'oci8:oci8' => DB_ERROR_UNSUPPORTED,
        'odbc:access' => DB_ERROR_UNSUPPORTED,
        'odbc:db2' => DB_ERROR_UNSUPPORTED,
        'pgsql:pgsql' => 'array',
        'sqlite:sqlite' => DB_ERROR_UNSUPPORTED,
        'sybase:sybase' => DB_ERROR_UNSUPPORTED,
    ),
    'procedures' => array(
        'dbase:dbase' => DB_ERROR_UNSUPPORTED,
        'fbsql:fbsql' => 'array',
        'ibase:ibase' => DB_ERROR_UNSUPPORTED,
        'ibase:firebird' => DB_ERROR_UNSUPPORTED,
        'ifx:ifx' => DB_ERROR_UNSUPPORTED,
        'msql:msql' => DB_ERROR_UNSUPPORTED,
        'mssql:mssql' => DB_ERROR_UNSUPPORTED,
        'mysql:mysql' => DB_ERROR_UNSUPPORTED,
        'mysqli:mysqli' => DB_ERROR_UNSUPPORTED,
        'oci8:oci8' => DB_ERROR_UNSUPPORTED,
        'odbc:access' => DB_ERROR_UNSUPPORTED,
        'odbc:db2' => DB_ERROR_UNSUPPORTED,
        'pgsql:pgsql' => 'array',
        'sqlite:sqlite' => DB_ERROR_UNSUPPORTED,
        'sybase:sybase' => DB_ERROR_UNSUPPORTED,
    ),
    'schema.tables' => array(
        'dbase:dbase' => DB_ERROR_UNSUPPORTED,
        'fbsql:fbsql' => DB_ERROR_UNSUPPORTED,
        'ibase:ibase' => DB_ERROR_UNSUPPORTED,
        'ibase:firebird' => DB_ERROR_UNSUPPORTED,
        'ifx:ifx' => DB_ERROR_UNSUPPORTED,
        'msql:msql' => DB_ERROR_UNSUPPORTED,
        'mssql:mssql' => DB_ERROR_UNSUPPORTED,
        'mysql:mysql' => DB_ERROR_UNSUPPORTED,
        'mysqli:mysqli' => DB_ERROR_UNSUPPORTED,
        'oci8:oci8' => DB_ERROR_UNSUPPORTED,
        'odbc:access' => 'array',
        'odbc:db2' => 'array',
        'pgsql:pgsql' => 'array',
        'sqlite:sqlite' => DB_ERROR_UNSUPPORTED,
        'sybase:sybase' => DB_ERROR_UNSUPPORTED,
    ),
    'synonyms' => array(
        'dbase:dbase' => DB_ERROR_UNSUPPORTED,
        'fbsql:fbsql' => DB_ERROR_UNSUPPORTED,
        'ibase:ibase' => DB_ERROR_UNSUPPORTED,
        'ibase:firebird' => DB_ERROR_UNSUPPORTED,
        'ifx:ifx' => DB_ERROR_UNSUPPORTED,
        'msql:msql' => DB_ERROR_UNSUPPORTED,
        'mssql:mssql' => DB_ERROR_UNSUPPORTED,
        'mysql:mysql' => DB_ERROR_UNSUPPORTED,
        'mysqli:mysqli' => DB_ERROR_UNSUPPORTED,
        'oci8:oci8' => 'array',
        'odbc:access' => DB_ERROR_UNSUPPORTED,
        'odbc:db2' => DB_ERROR_UNSUPPORTED,
        'pgsql:pgsql' => DB_ERROR_UNSUPPORTED,
        'sqlite:sqlite' => DB_ERROR_UNSUPPORTED,
        'sybase:sybase' => DB_ERROR_UNSUPPORTED,
    ),
);

/**
 * Determine if the output from the driver matches what we expect
 *
 * If things are as we expect, nothing is printed out.
 *
 * If things go wrong, print "UNEXPECTED OUTCOME" and display
 * what happened.
 *
 * @param mixed  $result    the result from getListOf
 * @param mixed  $expected  the expected result
 * @param string $name      the name of the current test
 *
 * @return void
 */
function check_output($result, $expected, $name) {
    if (is_object($result)) {
        if ($result->getCode() !== $expected) {
            echo "UNEXPECTED OUTCOME FOR $name...\n";
            echo $result->getDebugInfo() . "\n";
        }
    } else {
        $type = gettype($result);
        if ($type != $expected) {
            if ($expected === DB_ERROR_ACCESS_VIOLATION
                && $type == 'array')
            {
                // This user has access to the mysql table.
                // Not a problem
            } else {
                echo "UNEXPECTED OUTCOME FOR $name...\n";
                echo "  Expected: $expected\n";
                echo '  Result: ';
                print_r($result);
                echo "\n";
            }
        }
    }
}


$dbh->setErrorHandling(PEAR_ERROR_RETURN);
foreach ($tests as $test => $dbms) {
    check_output($dbh->getListOf($test),
                 $dbms[$dbh->phptype . ':' . $dbh->dbsyntax],
                 $test);
}


drop_table($dbh, 'phptest');

?>
--EXPECT--
