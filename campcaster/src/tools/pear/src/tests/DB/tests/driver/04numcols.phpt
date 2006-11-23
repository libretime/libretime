--TEST--
DB_driver::numCols
--INI--
error_reporting = 2047
--SKIPIF--
<?php chdir(dirname(__FILE__)); require_once './skipif.inc'; ?>
--FILE--
<?php
require_once './mktable.inc';
require_once '../numcols.inc';
?>
--EXPECT--
1
2
3
4
