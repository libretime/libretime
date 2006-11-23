<?php
// $Id: second_test.php,v 1.1 2004/05/24 22:25:43 quipo Exp $

require_once('simple_include.php');
require_once('calendar_include.php');

require_once('./calendar_test.php');

class TestOfSecond extends TestOfCalendar {
    function TestOfSecond() {
        $this->UnitTestCase('Test of Second');
    }
    function setUp() {
        $this->cal = new Calendar_Second(2003,10,25,13,32,43);
    }
    function testPrevDay_Array () {
        $this->assertEqual(
            array(
                'year'   => 2003,
                'month'  => 10,
                'day'    => 24,
                'hour'   => 0,
                'minute' => 0,
                'second' => 0),
            $this->cal->prevDay('array'));
    }
}

if (!defined('TEST_RUNNING')) {
    define('TEST_RUNNING', true);
    $test = &new TestOfSecond();
    $test->run(new HtmlReporter());
}
?>