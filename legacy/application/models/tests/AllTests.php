<?php

declare(strict_types=1);

set_include_path(get_include_path() . PATH_SEPARATOR . $path);
$WHITE_SCREEN_OF_DEATH = false;

require_once dirname(__FILE__) . '/../../configs/conf.php';

// $suite  = new PHPUnit_TestSuite("PlayListTests");
// $suite = new PHPUnit_TestSuite("SchedulerTests");
$suite = new PHPUnit_TestSuite('StoredFileTest');
$suite->addTestSuite('PlaylistTests');
$suite->addTestSuite('SchedulerTests');
// $suite->addTestSuite("SchedulerExportTests");
$result = PHPUnit::run($suite);

echo $result->toString();
