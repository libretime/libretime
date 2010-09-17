<?php

require_once 'UnitTests.php';
require_once 'PlayListTests.php';

/*
$suite  = new PHPUnit_TestSuite("BasicStorTest");
$result = PHPUnit::run($suite);

echo $result->toString();
*/

$suite  = new PHPUnit_TestSuite("PlayListTest");
$result = PHPUnit::run($suite);

echo $result->toString();
?>