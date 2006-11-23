<?php
// $Id: calendar_tabular_tests.php,v 1.1 2004/05/24 22:25:43 quipo Exp $

require_once('simple_include.php');
require_once('calendar_include.php');

class CalendarTabularTests extends GroupTest {
    function CalendarTabularTests() {
        $this->GroupTest('Calendar Tabular Tests');
        $this->addTestFile('month_weekdays_test.php');
        $this->addTestFile('month_weeks_test.php');
        $this->addTestFile('week_test.php');
    }
}

if (!defined('TEST_RUNNING')) {
    define('TEST_RUNNING', true);
    $test = &new CalendarTabularTests();
    $test->run(new HtmlReporter());
}
?>