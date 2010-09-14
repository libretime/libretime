<?php

require_once 'UnitTests.php';

$suite  = new PHPUnit_TestSuite("BasicStorTest");
$result = PHPUnit::run($suite);

echo $result->toString();
?>