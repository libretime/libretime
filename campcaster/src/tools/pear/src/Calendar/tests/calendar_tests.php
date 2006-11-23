<?php
// $Id: calendar_tests.php,v 1.1 2004/05/24 22:25:43 quipo Exp $

require_once('simple_include.php');
require_once('calendar_include.php');

class CalendarTests extends GroupTest {
    function CalendarTests() {
        $this->GroupTest('Calendar Tests');
        $this->addTestFile('calendar_test.php');
        $this->addTestFile('year_test.php');
        $this->addTestFile('month_test.php');
        $this->addTestFile('day_test.php');
        $this->addTestFile('hour_test.php');
        $this->addTestFile('minute_test.php');
        $this->addTestFile('second_test.php');
    }
}

if (!defined('TEST_RUNNING')) {
    define('TEST_RUNNING', true);
    $test = &new CalendarTests();
    $test->run(new HtmlReporter());
}
?>