--TEST--
DB_driver::connect
--INI--
error_reporting = 2047
--SKIPIF--
<?php chdir(dirname(__FILE__)); require_once './skipif.inc'; ?>
--FILE--
<?php
require_once './connect.inc';

/**
 * Determine if the database connection matches what's expected
 *
 * @param object $dbh   the PEAR DB object
 * @param string $name  the name of the current test
 *
 * @return void
 */
function check_dbh($dbh, $name) {
    if (DB::isError($dbh)) {
        die('connect.inc: ' . $dbh->toString());
    }
    if (is_object($dbh)) {
        print "$name is an object\n";
    }
    switch ($dbh->phptype) {
        case 'dbase':
            if (is_int($dbh->connection)) {
                print "$name is connected\n";
            } else {
                print "$name NOT connected\n";
            }
            break;
        case 'mysqli':
            if (is_a($dbh->connection, 'mysqli')) {
                print "$name is connected\n";
            } else {
                print "$name NOT connected\n";
            }
            break;
        default:
            if (gettype($dbh->connection) == 'resource') {
                print "$name is connected\n";
            } else {
                print "$name NOT connected\n";
            }
    }
}


check_dbh($dbh, '$dbh');


$test_array_dsn = DB::parseDSN($dsn);
foreach ($test_array_dsn as $key => $value) {
    if ($value === false) {
        unset($test_array_dsn[$key]);
    }
}

$dbha =& DB::connect($test_array_dsn, $options);
check_dbh($dbha, '$dbha');


$tmp  = serialize($dbha);
$dbhu = unserialize($tmp);
check_dbh($dbhu, '$dbhu');

?>
--EXPECT--
$dbh is an object
$dbh is connected
$dbha is an object
$dbha is connected
$dbhu is an object
$dbhu is connected
