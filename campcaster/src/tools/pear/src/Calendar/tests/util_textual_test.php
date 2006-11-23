<?php
// $Id: util_textual_test.php,v 1.1 2004/08/16 12:56:10 hfuecks Exp $

require_once('simple_include.php');
require_once('calendar_include.php');

require_once('./decorator_test.php');

class TestOfUtilTextual extends UnitTestCase {
    var $mockengine;
    var $mockcal;
    function TestOfUtilTextual() {
        $this->UnitTestCase('Test of Calendar_Util_Textual');
    }
    function setUp() {
        $this->mockengine = new Mock_Calendar_Engine($this);
        $this->mockcal = new Mock_Calendar_Second($this);
        $this->mockcal->setReturnValue('prevYear',2002);
        $this->mockcal->setReturnValue('thisYear',2003);
        $this->mockcal->setReturnValue('nextYear',2004);
        $this->mockcal->setReturnValue('prevMonth',9);
        $this->mockcal->setReturnValue('thisMonth',10);
        $this->mockcal->setReturnValue('nextMonth',11);
        $this->mockcal->setReturnValue('prevDay',14);
        $this->mockcal->setReturnValue('thisDay',15);
        $this->mockcal->setReturnValue('nextDay',16);
        $this->mockcal->setReturnValue('prevHour',12);
        $this->mockcal->setReturnValue('thisHour',13);
        $this->mockcal->setReturnValue('nextHour',14);
        $this->mockcal->setReturnValue('prevMinute',29);
        $this->mockcal->setReturnValue('thisMinute',30);
        $this->mockcal->setReturnValue('nextMinute',31);
        $this->mockcal->setReturnValue('prevSecond',44);
        $this->mockcal->setReturnValue('thisSecond',45);
        $this->mockcal->setReturnValue('nextSecond',46);
        $this->mockcal->setReturnValue('getEngine',$this->mockengine);
        $this->mockcal->setReturnValue('getTimestamp',12345);
    }
    function tearDown() {
        unset ( $this->engine );
        unset ( $this->mockcal );
    }    
    function testMonthNamesLong() {
        $monthNames = array(
            1=>'January',
            2=>'February',
            3=>'March',
            4=>'April',
            5=>'May',
            6=>'June',
            7=>'July',
            8=>'August',
            9=>'September',
            10=>'October',
            11=>'November',
            12=>'December',
        );
        $this->assertEqual($monthNames,Calendar_Util_Textual::monthNames());
    }
    function testMonthNamesShort() {
        $monthNames = array(
            1=>'Jan',
            2=>'Feb',
            3=>'Mar',
            4=>'Apr',
            5=>'May',
            6=>'Jun',
            7=>'Jul',
            8=>'Aug',
            9=>'Sep',
            10=>'Oct',
            11=>'Nov',
            12=>'Dec',
        );
        $this->assertEqual($monthNames,Calendar_Util_Textual::monthNames('short'));
    }
    function testMonthNamesTwo() {
        $monthNames = array(
            1=>'Ja',
            2=>'Fe',
            3=>'Ma',
            4=>'Ap',
            5=>'Ma',
            6=>'Ju',
            7=>'Ju',
            8=>'Au',
            9=>'Se',
            10=>'Oc',
            11=>'No',
            12=>'De',
        );
        $this->assertEqual($monthNames,Calendar_Util_Textual::monthNames('two'));
    }
    function testMonthNamesOne() {
        $monthNames = array(
            1=>'J',
            2=>'F',
            3=>'M',
            4=>'A',
            5=>'M',
            6=>'J',
            7=>'J',
            8=>'A',
            9=>'S',
            10=>'O',
            11=>'N',
            12=>'D',
        );
        $this->assertEqual($monthNames,Calendar_Util_Textual::monthNames('one'));
    }
    function testWeekdayNamesLong() {
        $weekdayNames = array(
            0=>'Sunday',
            1=>'Monday',
            2=>'Tuesday',
            3=>'Wednesday',
            4=>'Thursday',
            5=>'Friday',
            6=>'Saturday',
        );
        $this->assertEqual($weekdayNames,Calendar_Util_Textual::weekdayNames());
    }
    function testWeekdayNamesShort() {
        $weekdayNames = array(
            0=>'Sun',
            1=>'Mon',
            2=>'Tue',
            3=>'Wed',
            4=>'Thu',
            5=>'Fri',
            6=>'Sat',
        );
        $this->assertEqual($weekdayNames,Calendar_Util_Textual::weekdayNames('short'));
    }
    function testWeekdayNamesTwo() {
        $weekdayNames = array(
            0=>'Su',
            1=>'Mo',
            2=>'Tu',
            3=>'We',
            4=>'Th',
            5=>'Fr',
            6=>'Sa',
        );
        $this->assertEqual($weekdayNames,Calendar_Util_Textual::weekdayNames('two'));
    }
    function testWeekdayNamesOne() {
        $weekdayNames = array(
            0=>'S',
            1=>'M',
            2=>'T',
            3=>'W',
            4=>'T',
            5=>'F',
            6=>'S',
        );
        $this->assertEqual($weekdayNames,Calendar_Util_Textual::weekdayNames('one'));
    }
    function testPrevMonthNameShort() {
        $this->assertEqual('Sep',Calendar_Util_Textual::prevMonthName($this->mockcal,'short'));
    }
    function testThisMonthNameShort() {
        $this->assertEqual('Oct',Calendar_Util_Textual::thisMonthName($this->mockcal,'short'));
    }
    function testNextMonthNameShort() {
        $this->assertEqual('Nov',Calendar_Util_Textual::nextMonthName($this->mockcal,'short'));
    }
    function testThisDayNameShort() {
        $this->assertEqual('Wed',Calendar_Util_Textual::thisDayName($this->mockcal,'short'));
    }
    function testOrderedWeekdaysShort() {
        $weekdayNames = array(
            0=>'Sun',
            1=>'Mon',
            2=>'Tue',
            3=>'Wed',
            4=>'Thu',
            5=>'Fri',
            6=>'Sat',
        );
        $this->assertEqual($weekdayNames,Calendar_Util_Textual::orderedWeekdays($this->mockcal,'short'));
    }

}

if (!defined('TEST_RUNNING')) {
    define('TEST_RUNNING', true);
    $test = &new TestOfUtilTextual();
    $test->run(new HtmlReporter());
}
?>