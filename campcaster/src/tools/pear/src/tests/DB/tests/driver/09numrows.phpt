--TEST--
DB_driver::numRows
--INI--
error_reporting = 2047
--SKIPIF--
<?php chdir(dirname(__FILE__)); require_once './skipif.inc'; ?>
--FILE--
<?php
require_once './mktable.inc';
require_once '../numrows.inc';
?>
--EXPECT--
(want 1) got 1 from first
(want 2) got 2 from 0
(want 3) got 3 from 1
(want 4) got 4 from 2
(want 5) got 5 from 3
(want 6) got 6 from 4
(want 5) got 5 from > 0 (passing params to query)
(want 4) got 4 from < 4 (doing prepare/execute)
(want 2) got 2 from 5 and 6 not deleted
(want 0) got 0 from < 0
