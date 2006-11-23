<?php
// $Id: calendar_test.php,v 1.1 2004/05/24 22:25:43 quipo Exp $

require_once('simple_include.php');
require_once('calendar_include.php');

class TestOfCalendar extends UnitTestCase {
    var $cal;
    function TestOfCalendar($name='Test of Calendar') {
        $this->UnitTestCase($name);
    }
    function setUp() {
        $this->cal = new Calendar(2003,10,25,13,32,43);
    }
    function tearDown() {
        unset($this->cal);
    }
    function testPrevYear () {
        $this->assertEqual(2002,$this->cal->prevYear());
    }
    function testPrevYear_Array () {
        $this->assertEqual(
            array(
                'year'   => 2002,
                'month'  => 1,
                'day'    => 1,
                'hour'   => 0,
                'minute' => 0,
                'second' => 0),
            $this->cal->prevYear('array'));
    }
    function testThisYear () {
        $this->assertEqual(2003,$this->cal->thisYear());
    }
    function testNextYear () {
        $this->assertEqual(2004,$this->cal->nextYear());
    }
    function testPrevMonth () {
        $this->assertEqual(9,$this->cal->prevMonth());
    }
    function testPrevMonth_Array () {
        $this->assertEqual(
            array(
                'year'   => 2003,
                'month'  => 9,
                'day'    => 1,
                'hour'   => 0,
                'minute' => 0,
                'second' => 0),
            $this->cal->prevMonth('array'));
    }
    function testThisMonth () {
        $this->assertEqual(10,$this->cal->thisMonth());
    }
    function testNextMonth () {
        $this->assertEqual(11,$this->cal->nextMonth());
    }
    function testPrevDay () {
        $this->assertEqual(24,$this->cal->prevDay());
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
    function testThisDay () {
        $this->assertEqual(25,$this->cal->thisDay());
    }
    function testNextDay () {
        $this->assertEqual(26,$this->cal->nextDay());
    }
    function testPrevHour () {
        $this->assertEqual(12,$this->cal->prevHour());
    }
    function testThisHour () {
        $this->assertEqual(13,$this->cal->thisHour());
    }
    function testNextHour () {
        $this->assertEqual(14,$this->cal->nextHour());
    }
    function testPrevMinute () {
        $this->assertEqual(31,$this->cal->prevMinute());
    }
    function testThisMinute () {
        $this->assertEqual(32,$this->cal->thisMinute());
    }
    function testNextMinute () {
        $this->assertEqual(33,$this->cal->nextMinute());
    }
    function testPrevSecond () {
        $this->assertEqual(42,$this->cal->prevSecond());
    }
    function testThisSecond () {
        $this->assertEqual(43,$this->cal->thisSecond());
    }
    function testNextSecond () {
        $this->assertEqual(44,$this->cal->nextSecond());
    }
    function testSetTimeStamp() {
        $stamp = mktime(13,32,43,10,25,2003);
        $this->cal->setTimeStamp($stamp);
        $this->assertEqual($stamp,$this->cal->getTimeStamp());
    }
    function testGetTimeStamp() {
        $stamp = mktime(13,32,43,10,25,2003);
        $this->assertEqual($stamp,$this->cal->getTimeStamp());
    }
}
?>