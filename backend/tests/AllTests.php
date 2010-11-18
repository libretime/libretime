<?php
$path = dirname(__FILE__).'/../../3rd_party/php/pear';
set_include_path(get_include_path() . PATH_SEPARATOR . $path);
$WHITE_SCREEN_OF_DEATH = true;

require_once(dirname(__FILE__).'/../../conf.php');
require_once('DB.php');
require_once('PHPUnit.php');
require_once 'StoredFileTests.php';
require_once 'SchedulerTests.php';
//require_once 'SchedulerExportTests.php';
require_once 'PlaylistTests.php';

//$suite  = new PHPUnit_TestSuite("PlayListTests");
//$suite = new PHPUnit_TestSuite("SchedulerTests");
$suite  = new PHPUnit_TestSuite("StoredFileTest");
$suite->addTestSuite("PlaylistTests");
$suite->addTestSuite("SchedulerTests");
//$suite->addTestSuite("SchedulerExportTests");
$result = PHPUnit::run($suite);

echo $result->toString();

?>
