<?php
// $Id: validator_unit_test.php,v 1.1 2004/05/24 22:25:43 quipo Exp $

require_once('simple_include.php');
require_once('calendar_include.php');

Mock::generate('Calendar_Engine_Interface','Mock_Calendar_Engine');
Mock::generate('Calendar_Second','Mock_Calendar_Second');

class TestOfValidator extends UnitTestCase {
    var $mockengine;
    var $mockcal;
    function TestOfValidator() {
        $this->UnitTestCase('Test of Validator');
    }
    function setUp() {
        $this->mockengine = new Mock_Calendar_Engine($this);
        $this->mockengine->setReturnValue('getMinYears',1970);
        $this->mockengine->setReturnValue('getMaxYears',2037);
        $this->mockengine->setReturnValue('getMonthsInYear',12);
        $this->mockengine->setReturnValue('getDaysInMonth',30);
        $this->mockengine->setReturnValue('getHoursInDay',24);
        $this->mockengine->setReturnValue('getMinutesInHour',60);
        $this->mockengine->setReturnValue('getSecondsInMinute',60);
        $this->mockcal = new Mock_Calendar_Second($this);
        $this->mockcal->setReturnValue('getEngine',$this->mockengine);
    }
    function tearDown() {
        unset ($this->mockengine);
        unset ($this->mocksecond);
    }
    function testIsValidYear() {
        $this->mockcal->setReturnValue('thisYear',2000);
        $Validator = & new Calendar_Validator($this->mockcal);
        $this->assertTrue($Validator->isValidYear());
    }
    function testIsValidYearTooSmall() {
        $this->mockcal->setReturnValue('thisYear',1969);
        $Validator = & new Calendar_Validator($this->mockcal);
        $this->assertFalse($Validator->isValidYear());
    }
    function testIsValidYearTooLarge() {
        $this->mockcal->setReturnValue('thisYear',2038);
        $Validator = & new Calendar_Validator($this->mockcal);
        $this->assertFalse($Validator->isValidYear());
    }
    function testIsValidMonth() {
        $this->mockcal->setReturnValue('thisMonth',10);
        $Validator = & new Calendar_Validator($this->mockcal);
        $this->assertTrue($Validator->isValidMonth());
    }
    function testIsValidMonthTooSmall() {
        $this->mockcal->setReturnValue('thisMonth',0);
        $Validator = & new Calendar_Validator($this->mockcal);
        $this->assertFalse($Validator->isValidMonth());
    }
    function testIsValidMonthTooLarge() {
        $this->mockcal->setReturnValue('thisMonth',13);
        $Validator = & new Calendar_Validator($this->mockcal);
        $this->assertFalse($Validator->isValidMonth());
    }
    function testIsValidDay() {
        $this->mockcal->setReturnValue('thisDay',10);
        $Validator = & new Calendar_Validator($this->mockcal);
        $this->assertTrue($Validator->isValidDay());
    }
    function testIsValidDayTooSmall() {
        $this->mockcal->setReturnValue('thisDay',0);
        $Validator = & new Calendar_Validator($this->mockcal);
        $this->assertFalse($Validator->isValidDay());
    }
    function testIsValidDayTooLarge() {
        $this->mockcal->setReturnValue('thisDay',31);
        $Validator = & new Calendar_Validator($this->mockcal);
        $this->assertFalse($Validator->isValidDay());
    }
    function testIsValidHour() {
        $this->mockcal->setReturnValue('thisHour',10);
        $Validator = & new Calendar_Validator($this->mockcal);
        $this->assertTrue($Validator->isValidHour());
    }
    function testIsValidHourTooSmall() {
        $this->mockcal->setReturnValue('thisHour',-1);
        $Validator = & new Calendar_Validator($this->mockcal);
        $this->assertFalse($Validator->isValidHour());
    }
    function testIsValidHourTooLarge() {
        $this->mockcal->setReturnValue('thisHour',24);
        $Validator = & new Calendar_Validator($this->mockcal);
        $this->assertFalse($Validator->isValidHour());
    }
    function testIsValidMinute() {
        $this->mockcal->setReturnValue('thisMinute',30);
        $Validator = & new Calendar_Validator($this->mockcal);
        $this->assertTrue($Validator->isValidMinute());
    }
    function testIsValidMinuteTooSmall() {
        $this->mockcal->setReturnValue('thisMinute',-1);
        $Validator = & new Calendar_Validator($this->mockcal);
        $this->assertFalse($Validator->isValidMinute());
    }
    function testIsValidMinuteTooLarge() {
        $this->mockcal->setReturnValue('thisMinute',60);
        $Validator = & new Calendar_Validator($this->mockcal);
        $this->assertFalse($Validator->isValidMinute());
    }
    function testIsValidSecond() {
        $this->mockcal->setReturnValue('thisSecond',30);
        $Validator = & new Calendar_Validator($this->mockcal);
        $this->assertTrue($Validator->isValidSecond());
    }
    function testIsValidSecondTooSmall() {
        $this->mockcal->setReturnValue('thisSecond',-1);
        $Validator = & new Calendar_Validator($this->mockcal);
        $this->assertFalse($Validator->isValidSecond());
    }
    function testIsValidSecondTooLarge() {
        $this->mockcal->setReturnValue('thisSecond',60);
        $Validator = & new Calendar_Validator($this->mockcal);
        $this->assertFalse($Validator->isValidSecond());
    }
    function testIsValid() {
        $this->mockcal->setReturnValue('thisYear',2000);
        $this->mockcal->setReturnValue('thisMonth',5);
        $this->mockcal->setReturnValue('thisDay',15);
        $this->mockcal->setReturnValue('thisHour',13);
        $this->mockcal->setReturnValue('thisMinute',30);
        $this->mockcal->setReturnValue('thisSecond',40);
        $Validator = & new Calendar_Validator($this->mockcal);
        $this->assertTrue($Validator->isValid());
    }
    function testIsValidAllWrong() {
        $this->mockcal->setReturnValue('thisYear',2038);
        $this->mockcal->setReturnValue('thisMonth',13);
        $this->mockcal->setReturnValue('thisDay',31);
        $this->mockcal->day = 31;
        $this->mockcal->setReturnValue('thisHour',24);
        $this->mockcal->setReturnValue('thisMinute',60);
        $this->mockcal->setReturnValue('thisSecond',60);
        $Validator = & new Calendar_Validator($this->mockcal);
        $this->assertFalse($Validator->isValid());
        $i = 0;
        while ( $Validator->fetch() ) {
            $i++;
        }
        $this->assertEqual($i,6);
    }
}

class TestOfValidatorLive extends UnitTestCase {
    function TestOfValidatorLive() {
        $this->UnitTestCase('Test of Validator Live');
    }
    function testYear() {
        $Unit = new Calendar_Year(2038);
        $Validator = & $Unit->getValidator();
        $this->assertFalse($Validator->isValidYear());
    }
    function testMonth() {
        $Unit = new Calendar_Month(2000,13);
        $Validator = & $Unit->getValidator();
        $this->assertFalse($Validator->isValidMonth());
    }
/*
    function testWeek() {
        $Unit = new Calendar_Week(2000,12,7);
        $Validator = & $Unit->getValidator();
        $this->assertFalse($Validator->isValidWeek());
    }
*/
    function testDay() {
        $Unit = new Calendar_Day(2000,12,32);
        $Validator = & $Unit->getValidator();
        $this->assertFalse($Validator->isValidDay());
    }
    function testHour() {
        $Unit = new Calendar_Hour(2000,12,20,24);
        $Validator = & $Unit->getValidator();
        $this->assertFalse($Validator->isValidHour());
    }
    function testMinute() {
        $Unit = new Calendar_Minute(2000,12,20,23,60);
        $Validator = & $Unit->getValidator();
        $this->assertFalse($Validator->isValidMinute());
    }
    function testSecond() {
        $Unit = new Calendar_Second(2000,12,20,23,59,60);
        $Validator = & $Unit->getValidator();
        $this->assertFalse($Validator->isValidSecond());
    }
    function testAllBad() {
        $Unit = new Calendar_Second(2000,13,32,24,60,60);
        $this->assertFalse($Unit->isValid());
        $Validator = & $Unit->getValidator();
        $i = 0;
        while ( $Validator->fetch() ) {
            $i++;
        }
        $this->assertEqual($i,5);
    }
}

if (!defined('TEST_RUNNING')) {
    define('TEST_RUNNING', true);
    $test = &new TestOfValidator();
    $test->run(new HtmlReporter());
    $test = &new TestOfValidatorLive();
    $test->run(new HtmlReporter());
}
?>