<?php
// $Id: year_test.php,v 1.1 2004/05/24 22:25:43 quipo Exp $

require_once('simple_include.php');
require_once('calendar_include.php');

require_once('./calendar_test.php');

class TestOfYear extends TestOfCalendar {
    function TestOfYear() {
        $this->UnitTestCase('Test of Year');
    }
    function setUp() {
        $this->cal = new Calendar_Year(2003);
    }
    function testPrevYear_Object() {
        $this->assertEqual(new Calendar_Year(2002), $this->cal->prevYear('object'));
    }
    function testThisYear_Object() {
        $this->assertEqual(new Calendar_Year(2003), $this->cal->thisYear('object'));
    }
    function testPrevMonth () {
        $this->assertEqual(12,$this->cal->prevMonth());
    }
    function testPrevMonth_Array () {
        $this->assertEqual(
            array(
                'year'   => 2002,
                'month'  => 12,
                'day'    => 1,
                'hour'   => 0,
                'minute' => 0,
                'second' => 0),
            $this->cal->prevMonth('array'));
    }
    function testThisMonth () {
        $this->assertEqual(1,$this->cal->thisMonth());
    }
    function testNextMonth () {
        $this->assertEqual(2,$this->cal->nextMonth());
    }
    function testPrevDay () {
        $this->assertEqual(31,$this->cal->prevDay());
    }
    function testPrevDay_Array () {
        $this->assertEqual(
            array(
                'year'   => 2002,
                'month'  => 12,
                'day'    => 31,
                'hour'   => 0,
                'minute' => 0,
                'second' => 0),
            $this->cal->prevDay('array'));
    }
    function testThisDay () {
        $this->assertEqual(1,$this->cal->thisDay());
    }
    function testNextDay () {
        $this->assertEqual(2,$this->cal->nextDay());
    }
    function testPrevHour () {
        $this->assertEqual(23,$this->cal->prevHour());
    }
    function testThisHour () {
        $this->assertEqual(0,$this->cal->thisHour());
    }
    function testNextHour () {
        $this->assertEqual(1,$this->cal->nextHour());
    }
    function testPrevMinute () {
        $this->assertEqual(59,$this->cal->prevMinute());
    }
    function testThisMinute () {
        $this->assertEqual(0,$this->cal->thisMinute());
    }
    function testNextMinute () {
        $this->assertEqual(1,$this->cal->nextMinute());
    }
    function testPrevSecond () {
        $this->assertEqual(59,$this->cal->prevSecond());
    }
    function testThisSecond () {
        $this->assertEqual(0,$this->cal->thisSecond());
    }
    function testNextSecond () {
        $this->assertEqual(1,$this->cal->nextSecond());
    }
    function testGetTimeStamp() {
        $stamp = mktime(0,0,0,1,1,2003);
        $this->assertEqual($stamp,$this->cal->getTimeStamp());
    }
}

class TestOfYearBuild extends TestOfYear {
    function TestOfYearBuild() {
        $this->UnitTestCase('Test of Year::build()');
    }
    function testSize() {
        $this->cal->build();
        $this->assertEqual(12,$this->cal->size());
    }
    function testFetch() {
        $this->cal->build();
        $i=0;
        while ( $Child = $this->cal->fetch() ) {
            $i++;
        }
        $this->assertEqual(12,$i);
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
        require_once(CALENDAR_ROOT . 'Month.php');
        $selection = array(new Calendar_Month(2003,10));
        $this->cal->build($selection);
        $i = 1;
        while ( $Child = $this->cal->fetch() ) {
            if ( $i == 10 )
                break;
            $i++;
        }
        $this->assertTrue($Child->isSelected());
    }
}

if (!defined('TEST_RUNNING')) {
    define('TEST_RUNNING', true);
    $test = &new TestOfYear();
    $test->run(new HtmlReporter());
    $test = &new TestOfYearBuild();
    $test->run(new HtmlReporter());
}
?>