<?php
// $Id: month_weeks_test.php,v 1.3 2008/11/15 21:21:42 quipo Exp $

require_once('simple_include.php');
require_once('calendar_include.php');

require_once('./calendar_test.php');

class TestOfMonthWeeks extends TestOfCalendar {
    function TestOfMonthWeeks() {
        $this->UnitTestCase('Test of Month Weeks');
    }
    function setUp() {
        $this->cal = new Calendar_Month_Weeks(2003, 10);
    }
    function testPrevDay () {
        $this->assertEqual(30, $this->cal->prevDay());
    }
    function testPrevDay_Array () {
        $this->assertEqual(
            array(
                'year'   => 2003,
                'month'  => 9,
                'day'    => 30,
                'hour'   => 0,
                'minute' => 0,
                'second' => 0),
            $this->cal->prevDay('array'));
    }
    function testThisDay () {
        $this->assertEqual(1, $this->cal->thisDay());
    }
    function testNextDay () {
        $this->assertEqual(2, $this->cal->nextDay());
    }
    function testPrevHour () {
        $this->assertEqual(23, $this->cal->prevHour());
    }
    function testThisHour () {
        $this->assertEqual(0, $this->cal->thisHour());
    }
    function testNextHour () {
        $this->assertEqual(1, $this->cal->nextHour());
    }
    function testPrevMinute () {
        $this->assertEqual(59, $this->cal->prevMinute());
    }
    function testThisMinute () {
        $this->assertEqual(0, $this->cal->thisMinute());
    }
    function testNextMinute () {
        $this->assertEqual(1, $this->cal->nextMinute());
    }
    function testPrevSecond () {
        $this->assertEqual(59, $this->cal->prevSecond());
    }
    function testThisSecond () {
        $this->assertEqual(0, $this->cal->thisSecond());
    }
    function testNextSecond () {
        $this->assertEqual(1, $this->cal->nextSecond());
    }
    function testGetTimeStamp() {
        $stamp = mktime(0,0,0,10,1,2003);
        $this->assertEqual($stamp,$this->cal->getTimeStamp());
    }
}

class TestOfMonthWeeksBuild extends TestOfMonthWeeks {
    function TestOfMonthWeeksBuild() {
        $this->UnitTestCase('Test of Month_Weeks::build()');
    }
    function testSize() {
        $this->cal->build();
        $this->assertEqual(5,$this->cal->size());
    }

    function testFetch() {
        $this->cal->build();
        $i=0;
        while ($Child = $this->cal->fetch()) {
            $i++;
        }
        $this->assertEqual(5,$i);
    }
/* Recusive dependency issue with SimpleTest
    function testFetchAll() {
        $this->cal->build();
        $children = array();
        $i = 1;
        while ( $Child = $this->cal->fetch() ) {
            $children[$i]=$Child;
            $i++;
        }
        $this->assertEqual($children,$this->cal->fetchAll());
    }
*/
    function testSelection() {
        include_once CALENDAR_ROOT . 'Week.php';
        $selection = array(new Calendar_Week(2003, 10, 12));
        $this->cal->build($selection);
        $i = 1;
        $expected = (CALENDAR_FIRST_DAY_OF_WEEK == 0) ? 3 : 2;
        while ($Child = $this->cal->fetch()) {
            if ($i == $expected) {
                //12-10-2003 is in the 2nd week of the month if firstDay is Monday,
                //in the 3rd if firstDay is Sunday
                break;
            }
            $i++;
        }
        $this->assertTrue($Child->isSelected());
    }
    function testEmptyDaysBefore_AfterAdjust() {
        $this->cal = new Calendar_Month_Weeks(2004, 0);
        $this->cal->build();
        $expected = (CALENDAR_FIRST_DAY_OF_WEEK == 0) ? 1 : 0;
        $this->assertEqual($expected, $this->cal->tableHelper->getEmptyDaysBefore());
    }
}

if (!defined('TEST_RUNNING')) {
    define('TEST_RUNNING', true);
    $test = &new TestOfMonthWeeks();
    $test->run(new HtmlReporter());
    $test = &new TestOfMonthWeeksBuild();
    $test->run(new HtmlReporter());
}
?>