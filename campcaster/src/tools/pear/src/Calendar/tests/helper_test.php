<?php
// $Id: helper_test.php,v 1.1 2004/05/24 22:25:43 quipo Exp $

require_once('simple_include.php');
require_once('calendar_include.php');

Mock::generate('Calendar_Engine_Interface','Mock_Calendar_Engine');
Mock::generate('Calendar_Second','Mock_Calendar_Second');

class TestOfTableHelper extends UnitTestCase {
    var $mockengine;
    var $mockcal;
    function TestOfTableHelper() {
        $this->UnitTestCase('Test of Calendar_Table_Helper');
    }
    function setUp() {
        $this->mockengine = new Mock_Calendar_Engine($this);
        $this->mockengine->setReturnValue('getMinYears',1970);
        $this->mockengine->setReturnValue('getMaxYears',2037);
        $this->mockengine->setReturnValue('getMonthsInYear',12);
        $this->mockengine->setReturnValue('getDaysInMonth',31);
        $this->mockengine->setReturnValue('getHoursInDay',24);
        $this->mockengine->setReturnValue('getMinutesInHour',60);
        $this->mockengine->setReturnValue('getSecondsInMinute',60);
        $this->mockengine->setReturnValue('getWeekDays',array(0,1,2,3,4,5,6));
        $this->mockengine->setReturnValue('getDaysInWeek',7);
        $this->mockengine->setReturnValue('getFirstDayOfWeek',1);
        $this->mockengine->setReturnValue('getFirstDayInMonth',3);
        $this->mockcal = new Mock_Calendar_Second($this);
        $this->mockcal->setReturnValue('thisYear',2003);
        $this->mockcal->setReturnValue('thisMonth',10);
        $this->mockcal->setReturnValue('thisDay',15);
        $this->mockcal->setReturnValue('thisHour',13);
        $this->mockcal->setReturnValue('thisMinute',30);
        $this->mockcal->setReturnValue('thisSecond',45);
        $this->mockcal->setReturnValue('getEngine',$this->mockengine);
    }
    function testGetFirstDay() {
        for ( $i = 0; $i <= 7; $i++ ) {
            $Helper = & new Calendar_Table_Helper($this->mockcal,$i);
            $this->assertEqual($Helper->getFirstDay(),$i);
        }
    }
    function testGetDaysOfWeekMonday() {
        $Helper = & new Calendar_Table_Helper($this->mockcal);
        $this->assertEqual($Helper->getDaysOfWeek(),array(1,2,3,4,5,6,0));
    }
    function testGetDaysOfWeekSunday() {
        $Helper = & new Calendar_Table_Helper($this->mockcal,0);
        $this->assertEqual($Helper->getDaysOfWeek(),array(0,1,2,3,4,5,6));
    }
    function testGetDaysOfWeekThursday() {
        $Helper = & new Calendar_Table_Helper($this->mockcal,4);
        $this->assertEqual($Helper->getDaysOfWeek(),array(4,5,6,0,1,2,3));
    }
    function testGetNumWeeks() {
        $Helper = & new Calendar_Table_Helper($this->mockcal);
        $this->assertEqual($Helper->getNumWeeks(),5);
    }
    function testGetNumTableDaysInMonth() {
        $Helper = & new Calendar_Table_Helper($this->mockcal);
        $this->assertEqual($Helper->getNumTableDaysInMonth(),35);
    }
    function testGetEmptyDaysBefore() {
        $Helper = & new Calendar_Table_Helper($this->mockcal);
        $this->assertEqual($Helper->getEmptyDaysBefore(),2);
    }
    function testGetEmptyDaysAfter() {
        $Helper = & new Calendar_Table_Helper($this->mockcal);
        $this->assertEqual($Helper->getEmptyDaysAfter(),33);
    }
    function testGetEmptyDaysAfterOffset() {
        $Helper = & new Calendar_Table_Helper($this->mockcal);
        $this->assertEqual($Helper->getEmptyDaysAfterOffset(),5);
    }
}

if (!defined('TEST_RUNNING')) {
    define('TEST_RUNNING', true);
    $test = &new TestOfTableHelper();
    $test->run(new HtmlReporter());
}
?>