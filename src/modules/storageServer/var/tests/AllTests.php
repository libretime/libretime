<?php
require_once(dirname(__FILE__).'/../conf.php');
require_once('DB.php');
require_once('PHPUnit.php');
require_once 'BasicStorTests.php';
require_once 'SchedulerTests.php';

$suite  = new PHPUnit_TestSuite("BasicStorTest");
$suite->addTestSuite("SchedulerTests");
$result = PHPUnit::run($suite);

echo $result->toString();

?>