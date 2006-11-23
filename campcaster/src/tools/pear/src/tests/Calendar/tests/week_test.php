<?php
// $Id: week_test.php,v 1.3 2004/07/28 09:44:41 quipo Exp $

require_once('simple_include.php');
require_once('calendar_include.php');

require_once('./calendar_test.php');

class TestOfWeek extends TestOfCalendar {
    function TestOfWeek() {
        $this->UnitTestCase('Test of Week');
    }
    function setUp() {
        $this->cal = new Calendar_Week(2003, 10, 9, 1); //force firstDay = monday
        //print_r($this->cal);
    }
    function testPrevDay () {
        $this->assertEqual(8, $this->cal->prevDay());
    }
    function testPrevDay_Array () {
        $this->assertEqual(
            array(
                'year'   => 2003,
                'month'  => 10,
                'day'    => 8,
                'hour'   => 0,
                'minute' => 0,
                'second' => 0),
            $this->cal->prevDay('array'));
    }
    function testThisDay () {
        $this->assertEqual(9, $this->cal->thisDay());
    }
    function testNextDay () {
        $this->assertEqual(10, $this->cal->nextDay());
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
        $stamp = mktime(0,0,0,10,9,2003);
        $this->assertEqual($stamp,$this->cal->getTimeStamp());
    }
    function testNewTimeStamp() {
        $stamp = mktime(0,0,0,7,28,2004);
        $this->cal->setTimestamp($stamp);
        $this->assertEqual('30 2004', date('W Y', $this->cal->prevWeek(true)));
        $this->assertEqual('31 2004', date('W Y', $this->cal->thisWeek(true)));
        $this->assertEqual('32 2004', date('W Y', $this->cal->nextWeek(true)));
    }
    function testPrevWeekInMonth() {
        $this->assertEqual(1, $this->cal->prevWeek());
    }
    function testThisWeekInMonth() {
        $this->assertEqual(2, $this->cal->thisWeek());
    }
    function testNextWeekInMonth() {
        $this->assertEqual(3, $this->cal->nextWeek());
    }
    function testPrevWeekInYear() {
        $this->assertEqual(40, $this->cal->prevWeek('n_in_year'));
    }
    function testThisWeekInYear() {
        $this->assertEqual(41, $this->cal->thisWeek('n_in_year'));
    }
    function testNextWeekInYear() {
        $this->assertEqual(42, $this->cal->nextWeek('n_in_year'));
    }
    function testPrevWeekArray() {
        $testArray = array(
            'year'=>2003,
            'month'=>9,
            'day'=>29,
            'hour'=>0,
            'minute'=>0,
            'second'=>0
            );
        $this->assertEqual($testArray, $this->cal->prevWeek('array'));
    }
    function testThisWeekArray() {
        $testArray = array(
            'year'=>2003,
            'month'=>10,
            'day'=>6,
            'hour'=>0,
            'minute'=>0,
            'second'=>0
            );
        $this->assertEqual($testArray, $this->cal->thisWeek('array'));
    }
    function testNextWeekArray() {
        $testArray = array(
            'year'=>2003,
            'month'=>10,
            'day'=>13,
            'hour'=>0,
            'minute'=>0,
            'second'=>0
            );
        $this->assertEqual($testArray, $this->cal->nextWeek('array'));
    }
    function testPrevWeekObject() {
        $testWeek = new Calendar_Week(2003,9,29);
        $Week = $this->cal->prevWeek('object');
        $this->assertEqual($testWeek->getTimeStamp(),$Week->getTimeStamp());
    }
    function testThisWeekObject() {
        $testWeek = new Calendar_Week(2003,10,6);
        $Week = $this->cal->thisWeek('object');
        $this->assertEqual($testWeek->getTimeStamp(),$Week->getTimeStamp());
    }
    function testNextWeekObject() {
        $testWeek = new Calendar_Week(2003,10,13);
        $Week = $this->cal->nextWeek('object');
        $this->assertEqual($testWeek->getTimeStamp(),$Week->getTimeStamp());
    }
}

class TestOfWeekBuild extends TestOfWeek {
    function TestOfWeekBuild() {
        $this->UnitTestCase('Test of Week::build()');
    }
    function testSize() {
        $this->cal->build();
        $this->assertEqual(7, $this->cal->size());
    }

    function testFetch() {
        $this->cal->build();
        $i=0;
        while ($Child = $this->cal->fetch()) {
            $i++;
        }
        $this->assertEqual(7, $i);
    }
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

    function testSelection() {
        require_once(CALENDAR_ROOT . 'Day.php');
        $selection = array(new Calendar_Day(2003, 10, 7));
        $this->cal->build($selection);
        $i = 1;
        while ($Child = $this->cal->fetch()) {
            if ($i == 2) {
                break; //07-10-2003 is the 2nd day of the week
            }
            $i++;
        }
        $this->assertTrue($Child->isSelected());
    }
    function testSelectionCornerCase() {
        require_once(CALENDAR_ROOT . 'Day.php');
        $selectedDays = array(
            new Calendar_Day(2003, 12, 28),
            new Calendar_Day(2003, 12, 29),
            new Calendar_Day(2003, 12, 30),
            new Calendar_Day(2003, 12, 31),
            new Calendar_Day(2004, 01, 01),
            new Calendar_Day(2004, 01, 02),
            new Calendar_Day(2004, 01, 03)
        );
        $this->cal = new Calendar_Week(2003, 12, 31, 0);
        $this->cal->build($selectedDays);
        while ($Day = $this->cal->fetch()) {
            $this->assertTrue($Day->isSelected());
        }
        $this->cal = new Calendar_Week(2004, 1, 1, 0);
        $this->cal->build($selectedDays);
        while ($Day = $this->cal->fetch()) {
            $this->assertTrue($Day->isSelected());
        }
    }
}
if (!defined('TEST_RUNNING')) {
    define('TEST_RUNNING', true);
    $test = &new TestOfWeek();
    $test->run(new HtmlReporter());
    $test = &new TestOfWeekBuild();
    $test->run(new HtmlReporter());
}
?>