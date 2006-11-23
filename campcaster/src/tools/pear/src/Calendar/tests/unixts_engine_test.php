<?php
// $Id: unixts_engine_test.php,v 1.2 2004/08/16 11:36:51 hfuecks Exp $

require_once('simple_include.php');
require_once('calendar_include.php');

class TestOfUnixTsEngine extends UnitTestCase {
    var $engine;
    function TestOfUnixTsEngine() {
        $this->UnitTestCase('Test of Calendar_Engine_UnixTs');
    }
    function setUp() {
        $this->engine = new Calendar_Engine_UnixTs();
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
        $test = strpos(PHP_OS, 'WIN') >= 0 ? 1970 : 1902;
        $this->assertEqual($this->engine->getMinYears(),$test);
    }
    function testGetMaxYears() {
        $this->assertEqual($this->engine->getMaxYears(),2037);
    }
    function testDateToStamp() {
        $stamp = mktime(0,0,0,10,15,2003);
        $this->assertEqual($this->engine->dateToStamp(2003,10,15,0,0,0),$stamp);
    }
    function testStampToSecond() {
        $stamp = mktime(13,30,45,10,15,2003);
        $this->assertEqual($this->engine->stampToSecond($stamp),45);
    }
    function testStampToMinute() {
        $stamp = mktime(13,30,45,10,15,2003);
        $this->assertEqual($this->engine->stampToMinute($stamp),30);
    }
    function testStampToHour() {
        $stamp = mktime(13,30,45,10,15,2003);
        $this->assertEqual($this->engine->stampToHour($stamp),13);
    }
    function testStampToDay() {
        $stamp = mktime(13,30,45,10,15,2003);
        $this->assertEqual($this->engine->stampToDay($stamp),15);
    }
    function testStampToMonth() {
        $stamp = mktime(13,30,45,10,15,2003);
        $this->assertEqual($this->engine->stampToMonth($stamp),10);
    }
    function testStampToYear() {
        $stamp = mktime(13,30,45,10,15,2003);
        $this->assertEqual($this->engine->stampToYear($stamp),2003);
    }
}

if (!defined('TEST_RUNNING')) {
    define('TEST_RUNNING', true);
    $test = &new TestOfUnixTsEngine();
    $test->run(new HtmlReporter());
}
?>