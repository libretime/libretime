--TEST--
DB_driver::transaction test
--INI--
error_reporting = 2047
--SKIPIF--
<?php
chdir(dirname(__FILE__)); require_once './skipif.inc';
if (!$db->features['transactions']) {
    die('skip this driver does not support transactions');
}
?>
--FILE--
<?php
$needinnodb = true;
require_once './mktable.inc';
require_once '../transactions.inc';
?>
--EXPECT--
1) after autocommit: bing one.  ops=ok
2) before commit: bing one two three.  ops=ok
3) after commit: bing one two three.  ops=ok
4) before rollback: bing one two three four five.  ops=ok
5) after rollback: bing one two three.  ops=ok
6) before autocommit+rollback: bing one two three six seven.  ops=ok
7) after autocommit+rollback: bing one two three six seven.  ops=ok
8) testing that select doesn't disturbe opcount: ok
