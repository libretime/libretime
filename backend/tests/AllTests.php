<?php
require_once(dirname(__FILE__).'/../../conf.php');
require_once('DB.php');
require_once('PHPUnit.php');
require_once 'BasicStorTests.php';
require_once 'SchedulerTests.php';
require_once 'SchedulerExportTests.php';
require_once 'PlayListTests.php';

$suite  = new PHPUnit_TestSuite("BasicStorTest");
//$suite = new PHPUnit_TestSuite("SchedulerTests");
$suite->addTestSuite("SchedulerTests");
$suite->addTestSuite("SchedulerExportTests");
$suite->addTestSuite("PlayListTests");
$result = PHPUnit::run($suite);

echo $result->toString();

?>