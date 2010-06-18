<?php
// $Id: all_tests.php,v 1.2 2004/08/16 08:55:24 hfuecks Exp $

require_once('simple_include.php');
require_once('calendar_include.php');

define("TEST_RUNNING", true);

require_once('./calendar_tests.php');
require_once('./calendar_tabular_tests.php');
require_once('./validator_tests.php');
require_once('./calendar_engine_tests.php');
require_once('./calendar_engine_tests.php');
require_once('./table_helper_tests.php');
require_once('./decorator_tests.php');
require_once('./util_tests.php');


class AllTests extends GroupTest {
    function AllTests() {
        $this->GroupTest('All PEAR::Calendar Tests');
        $this->AddTestCase(new CalendarTests());
        $this->AddTestCase(new CalendarTabularTests());
        $this->AddTestCase(new ValidatorTests());
        $this->AddTestCase(new CalendarEngineTests());
        $this->AddTestCase(new TableHelperTests());
        $this->AddTestCase(new DecoratorTests());
        $this->AddTestCase(new UtilTests());
    }
}

$test = &new AllTests();
$test->run(new HtmlReporter());
?>