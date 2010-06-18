<?php
// $Id: day_test.php,v 1.1 2004/05/24 22:25:43 quipo Exp $

require_once('simple_include.php');
require_once('calendar_include.php');

require_once('./calendar_test.php');

class TestOfDay extends TestOfCalendar {
    function TestOfDay() {
        $this->UnitTestCase('Test of Day');
    }
    function setUp() {
        $this->cal = new Calendar_Day(2003,10,25);
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
        $stamp = mktime(0,0,0,10,25,2003);
        $this->assertEqual($stamp,$this->cal->getTimeStamp());
    }
}

class TestOfDayBuild extends TestOfDay {
    function TestOfDayBuild() {
        $this->UnitTestCase('Test of Day::build()');
    }
    function testSize() {
        $this->cal->build();
        $this->assertEqual(24,$this->cal->size());
    }
    function testFetch() {
        $this->cal->build();
        $i=0;
        while ( $Child = $this->cal->fetch() ) {
            $i++;
        }
        $this->assertEqual(24,$i);
    }
    function testFetchAll() {
        $this->cal->build();
        $children = array();
        $i = 0;
        while ( $Child = $this->cal->fetch() ) {
            $children[$i]=$Child;
            $i++;
        }
        $this->assertEqual($children,$this->cal->fetchAll());
    }
    function testSelection() {
        require_once(CALENDAR_ROOT . 'Hour.php');
        $selection = array(new Calendar_Hour(2003,10,25,13));
        $this->cal->build($selection);
        $i = 0;
        while ( $Child = $this->cal->fetch() ) {
            if ( $i == 13 )
                break;
            $i++;
        }
        $this->assertTrue($Child->isSelected());
    }
}

if (!defined('TEST_RUNNING')) {
    define('TEST_RUNNING', true);
    $test = &new TestOfDay();
    $test->run(new HtmlReporter());
    $test = &new TestOfDayBuild();
    $test->run(new HtmlReporter());
}
?>