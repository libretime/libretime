<?php
// $Id: peardate_engine_test.php,v 1.2 2004/08/16 11:36:51 hfuecks Exp $

require_once('simple_include.php');
require_once('calendar_include.php');

class TestOfPearDateEngine extends UnitTestCase {
    var $engine;
    function TestOfPearDateEngine() {
        $this->UnitTestCase('Test of Calendar_Engine_PearDate');
    }
    function setUp() {
        $this->engine = new Calendar_Engine_PearDate();
    }
    function testGetSecondsInMinute() {
        $this->assertEqual($this->engine->getSecondsInMinute(),60);
    }
    function testGetMinutesInHour() {
        $this->assertEqual($this->engine->getMinutesInHour(),60);
    }
    function testGetHoursInDay() {
        $this->assertEqual($this->engine->getHoursInDay(),24);
    }
    function testGetFirstDayOfWeek() {
        $this->assertEqual($this->engine->getFirstDayOfWeek(),1);
    }
    function testGetWeekDays() {
        $this->assertEqual($this->engine->getWeekDays(),array(0,1,2,3,4,5,6));
    }
    function testGetDaysInWeek() {
        $this->assertEqual($this->engine->getDaysInWeek(),7);
    }
    function testGetWeekNInYear() {
        $this->assertEqual($this->engine->getWeekNInYear(2003, 11, 3), 45);
    }
    function testGetWeekNInMonth() {
        $this->assertEqual($this->engine->getWeekNInMonth(2003, 11, 3), 2);
    }
    function testGetWeeksInMonth0() {
        $this->assertEqual($this->engine->getWeeksInMonth(2003, 11, 0), 6); //week starts on sunday
    }
    function testGetWeeksInMonth1() {
        $this->assertEqual($this->engine->getWeeksInMonth(2003, 11, 1), 5); //week starts on monday
    }
    function testGetWeeksInMonth2() {
        $this->assertEqual($this->engine->getWeeksInMonth(2003, 2, 6), 4); //week starts on saturday
    }
    function testGetWeeksInMonth3() {
        // Unusual cases that can cause fails (shows up with example 21.php)
        $this->assertEqual($this->engine->getWeeksInMonth(2004,2,1),5);
        $this->assertEqual($this->engine->getWeeksInMonth(2004,8,1),6);
    }
    function testGetDayOfWeek() {
        $this->assertEqual($this->engine->getDayOfWeek(2003, 11, 18), 2);
    }
    function testGetFirstDayInMonth() {
        $this->assertEqual($this->engine->getFirstDayInMonth(2003,10),3);
    }
    function testGetDaysInMonth() {
        $this->assertEqual($this->engine->getDaysInMonth(2003,10),31);
    }
    function testGetMinYears() {
        $this->assertEqual($this->engine->getMinYears(),0);
    }
    function testGetMaxYears() {
        $this->assertEqual($this->engine->getMaxYears(),9999);
    }
    function testDateToStamp() {
        $stamp = '2003-10-15 13:30:45';
        $this->assertEqual($this->engine->dateToStamp(2003,10,15,13,30,45),$stamp);
    }
    function testStampToSecond() {
        $stamp = '2003-10-15 13:30:45';
        $this->assertEqual($this->engine->stampToSecond($stamp),45);
    }
    function testStampToMinute() {
        $stamp = '2003-10-15 13:30:45';
        $this->assertEqual($this->engine->stampToMinute($stamp),30);
    }
    function testStampToHour() {
        $stamp = '2003-10-15 13:30:45';
        $this->assertEqual($this->engine->stampToHour($stamp),13);
    }
    function testStampToDay() {
        $stamp = '2003-10-15 13:30:45';
        $this->assertEqual($this->engine->stampToDay($stamp),15);
    }
    function testStampToMonth() {
        $stamp = '2003-10-15 13:30:45';
        $this->assertEqual($this->engine->stampToMonth($stamp),10);
    }
    function testStampToYear() {
        $stamp = '2003-10-15 13:30:45';
        $this->assertEqual($this->engine->stampToYear($stamp),2003);
    }
    function testAdjustDate() {
        $stamp = '2004-01-01 13:30:45';
        $y = $this->engine->stampToYear($stamp);
        $m = $this->engine->stampToMonth($stamp);
        $d = $this->engine->stampToDay($stamp);

        //the first day of the month should be thursday
        $this->assertEqual($this->engine->getDayOfWeek($y, $m, $d), 4);

        $m--; // 2004-00-01 => 2003-12-01
        $this->engine->adjustDate($y, $m, $d, $dummy, $dummy, $dummy);

        $this->assertEqual($y, 2003);
        $this->assertEqual($m, 12);
        $this->assertEqual($d, 1);

        // get last day and check if it's wednesday
        $d = $this->engine->getDaysInMonth($y, $m);

        $this->assertEqual($this->engine->getDayOfWeek($y, $m, $d), 3);
    }
}

if (!defined('TEST_RUNNING')) {
    define('TEST_RUNNING', true);
    $test = &new TestOfPearDateEngine();
    $test->run(new HtmlReporter());
}
?>