--TEST--
DB::factory
--SKIPIF--
<?php chdir(dirname(__FILE__)); require_once './skipif.inc'; ?>
--FILE--
<?php // -*- C++ -*-
require_once './include.inc';
require_once 'DB.php';

$backends = array(
    'dbase',
    'fbsql',
    'ibase',
    'ifx',
    'msql',
    'mssql',
    'mysql',
    'mysqli',
    'oci8',
    'odbc',
    'pgsql',
    'sqlite',
    'sybase',
);

foreach ($backends as $name) {
    $obj = DB::factory($name);

    print "testing $name: ";
    if (DB::isError($obj)) {
	    print 'error: ' . $obj->getMessage() . "\n";
    } else {
	    print 'object: ' . $obj->toString() . "\n";
    }
}

?>
--GET--
--POST--
--EXPECT--
testing dbase: object: db_dbase: (phptype=dbase, dbsyntax=dbase)
testing fbsql: object: db_fbsql: (phptype=fbsql, dbsyntax=fbsql)
testing ibase: object: db_ibase: (phptype=ibase, dbsyntax=ibase)
testing ifx: object: db_ifx: (phptype=ifx, dbsyntax=ifx)
testing msql: object: db_msql: (phptype=msql, dbsyntax=msql)
testing mssql: object: db_mssql: (phptype=mssql, dbsyntax=mssql)
testing mysql: object: db_mysql: (phptype=mysql, dbsyntax=mysql)
testing mysqli: object: db_mysqli: (phptype=mysqli, dbsyntax=mysqli)
testing oci8: object: db_oci8: (phptype=oci8, dbsyntax=oci8)
testing odbc: object: db_odbc: (phptype=odbc, dbsyntax=sql92)
testing pgsql: object: db_pgsql: (phptype=pgsql, dbsyntax=pgsql)
testing sqlite: object: db_sqlite: (phptype=sqlite, dbsyntax=sqlite)
testing sybase: object: db_sybase: (phptype=sybase, dbsyntax=sybase)
