<?php
// $Id: decorator_test.php,v 1.1 2004/05/24 22:25:43 quipo Exp $

require_once('simple_include.php');
require_once('calendar_include.php');

Mock::generate('Calendar_Engine_Interface','Mock_Calendar_Engine');
Mock::generate('Calendar_Second','Mock_Calendar_Second');
Mock::generate('Calendar_Week','Mock_Calendar_Week');
Mock::generate('Calendar_Day','Mock_Calendar_Day');

class TestOfDecorator extends UnitTestCase {
    var $mockengine;
    var $mockcal;
    var $decorator;
    function TestOfDecorator() {
        $this->UnitTestCase('Test of Calendar_Decorator');
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
    function testPrevYear() {
        $this->mockcal->expectOnce('prevYear',array('int'));
        $Decorator =& new Calendar_Decorator($this->mockcal);
        $this->assertEqual(2002,$Decorator->prevYear());
    }
    function testThisYear() {
        $this->mockcal->expectOnce('thisYear',array('int'));
        $Decorator =& new Calendar_Decorator($this->mockcal);
        $this->assertEqual(2003,$Decorator->thisYear());
    }
    function testNextYear() {
        $this->mockcal->expectOnce('nextYear',array('int'));
        $Decorator =& new Calendar_Decorator($this->mockcal);
        $this->assertEqual(2004,$Decorator->nextYear());
    }
    function testPrevMonth() {
        $this->mockcal->expectOnce('prevMonth',array('int'));
        $Decorator =& new Calendar_Decorator($this->mockcal);
        $this->assertEqual(9,$Decorator->prevMonth());
    }
    function testThisMonth() {
        $this->mockcal->expectOnce('thisMonth',array('int'));
        $Decorator =& new Calendar_Decorator($this->mockcal);
        $this->assertEqual(10,$Decorator->thisMonth());
    }
    function testNextMonth() {
        $this->mockcal->expectOnce('nextMonth',array('int'));
        $Decorator =& new Calendar_Decorator($this->mockcal);
        $this->assertEqual(11,$Decorator->nextMonth());
    }
    function testPrevWeek() {
        $mockweek = & new Mock_Calendar_Week($this);
        $mockweek->setReturnValue('prevWeek',1);
        $mockweek->expectOnce('prevWeek',array('n_in_month'));
        $Decorator =& new Calendar_Decorator($mockweek);
        $this->assertEqual(1,$Decorator->prevWeek());
    }
    function testThisWeek() {
        $mockweek = & new Mock_Calendar_Week($this);
        $mockweek->setReturnValue('thisWeek',2);
        $mockweek->expectOnce('thisWeek',array('n_in_month'));
        $Decorator =& new Calendar_Decorator($mockweek);
        $this->assertEqual(2,$Decorator->thisWeek());
    }
    function testNextWeek() {
        $mockweek = & new Mock_Calendar_Week($this);
        $mockweek->setReturnValue('nextWeek',3);
        $mockweek->expectOnce('nextWeek',array('n_in_month'));
        $Decorator =& new Calendar_Decorator($mockweek);
        $this->assertEqual(3,$Decorator->nextWeek());
    }
    function testPrevDay() {
        $this->mockcal->expectOnce('prevDay',array('int'));
        $Decorator =& new Calendar_Decorator($this->mockcal);
        $this->assertEqual(14,$Decorator->prevDay());
    }
    function testThisDay() {
        $this->mockcal->expectOnce('thisDay',array('int'));
        $Decorator =& new Calendar_Decorator($this->mockcal);
        $this->assertEqual(15,$Decorator->thisDay());
    }
    function testNextDay() {
        $this->mockcal->expectOnce('nextDay',array('int'));
        $Decorator =& new Calendar_Decorator($this->mockcal);
        $this->assertEqual(16,$Decorator->nextDay());
    }
    function testPrevHour() {
        $this->mockcal->expectOnce('prevHour',array('int'));
        $Decorator =& new Calendar_Decorator($this->mockcal);
        $this->assertEqual(12,$Decorator->prevHour());
    }
    function testThisHour() {
        $this->mockcal->expectOnce('thisHour',array('int'));
        $Decorator =& new Calendar_Decorator($this->mockcal);
        $this->assertEqual(13,$Decorator->thisHour());
    }
    function testNextHour() {
        $this->mockcal->expectOnce('nextHour',array('int'));
        $Decorator =& new Calendar_Decorator($this->mockcal);
        $this->assertEqual(14,$Decorator->nextHour());
    }
    function testPrevMinute() {
        $this->mockcal->expectOnce('prevMinute',array('int'));
        $Decorator =& new Calendar_Decorator($this->mockcal);
        $this->assertEqual(29,$Decorator->prevMinute());
    }
    function testThisMinute() {
        $this->mockcal->expectOnce('thisMinute',array('int'));
        $Decorator =& new Calendar_Decorator($this->mockcal);
        $this->assertEqual(30,$Decorator->thisMinute());
    }
    function testNextMinute() {
        $this->mockcal->expectOnce('nextMinute',array('int'));
        $Decorator =& new Calendar_Decorator($this->mockcal);
        $this->assertEqual(31,$Decorator->nextMinute());
    }
    function testPrevSecond() {
        $this->mockcal->expectOnce('prevSecond',array('int'));
        $Decorator =& new Calendar_Decorator($this->mockcal);
        $this->assertEqual(44,$Decorator->prevSecond());
    }
    function testThisSecond() {
        $this->mockcal->expectOnce('thisSecond',array('int'));
        $Decorator =& new Calendar_Decorator($this->mockcal);
        $this->assertEqual(45,$Decorator->thisSecond());
    }
    function testNextSecond() {
        $this->mockcal->expectOnce('nextSecond',array('int'));
        $Decorator =& new Calendar_Decorator($this->mockcal);
        $this->assertEqual(46,$Decorator->nextSecond());
    }
    function testGetEngine() {
        $Decorator =& new Calendar_Decorator($this->mockcal);
        $this->assertIsA($Decorator->getEngine(),'Mock_Calendar_Engine');
    }
    function testSetTimestamp() {
        $this->mockcal->expectOnce('setTimestamp',array('12345'));
        $Decorator =& new Calendar_Decorator($this->mockcal);
        $Decorator->setTimestamp('12345');
    }
    function testGetTimestamp() {
        $Decorator =& new Calendar_Decorator($this->mockcal);
        $this->assertEqual(12345,$Decorator->getTimestamp());
    }
    function testSetSelected() {
        $this->mockcal->expectOnce('setSelected',array(true));
        $Decorator =& new Calendar_Decorator($this->mockcal);
        $Decorator->setSelected();
    }
    function testIsSelected() {
        $this->mockcal->setReturnValue('isSelected',true);
        $Decorator =& new Calendar_Decorator($this->mockcal);
        $this->assertTrue($Decorator->isSelected());
    }
    function testAdjust() {
        $this->mockcal->expectOnce('adjust',array());
        $Decorator =& new Calendar_Decorator($this->mockcal);
        $Decorator->adjust();
    }
    function testToArray() {
        $this->mockcal->expectOnce('toArray',array(12345));
        $testArray = array('foo'=>'bar');
        $this->mockcal->setReturnValue('toArray',$testArray);
        $Decorator =& new Calendar_Decorator($this->mockcal);
        $this->assertEqual($testArray,$Decorator->toArray(12345));
    }
    function testReturnValue() {
        $this->mockcal->expectOnce('returnValue',array('a','b','c','d'));
        $this->mockcal->setReturnValue('returnValue','foo');
        $Decorator =& new Calendar_Decorator($this->mockcal);
        $this->assertEqual('foo',$Decorator->returnValue('a','b','c','d'));
    }
    function testSetFirst() {
        $mockday = & new Mock_Calendar_Day($this);
        $mockday->expectOnce('setFirst',array(true));
        $Decorator =& new Calendar_Decorator($mockday);
        $Decorator->setFirst();
    }
    function testSetLast() {
        $mockday = & new Mock_Calendar_Day($this);
        $mockday->expectOnce('setLast',array(true));
        $Decorator =& new Calendar_Decorator($mockday);
        $Decorator->setLast();
    }
    function testIsFirst() {
        $mockday = & new Mock_Calendar_Day($this);
        $mockday->setReturnValue('isFirst',TRUE);
        $Decorator =& new Calendar_Decorator($mockday);
        $this->assertTrue($Decorator->isFirst());
    }
    function testIsLast() {
        $mockday = & new Mock_Calendar_Day($this);
        $mockday->setReturnValue('isLast',TRUE);
        $Decorator =& new Calendar_Decorator($mockday);
        $this->assertTrue($Decorator->isLast());
    }
    function testSetEmpty() {
        $mockday = & new Mock_Calendar_Day($this);
        $mockday->expectOnce('setEmpty',array(true));
        $Decorator =& new Calendar_Decorator($mockday);
        $Decorator->setEmpty();
    }
    function testIsEmpty() {
        $mockday = & new Mock_Calendar_Day($this);
        $mockday->setReturnValue('isEmpty',TRUE);
        $Decorator =& new Calendar_Decorator($mockday);
        $this->assertTrue($Decorator->isEmpty());
    }
    function testBuild() {
        $testArray=array('foo'=>'bar');
        $this->mockcal->expectOnce('build',array($testArray));
        $Decorator =& new Calendar_Decorator($this->mockcal);
        $Decorator->build($testArray);
    }
    function testFetch() {
        $this->mockcal->expectOnce('fetch',array());
        $Decorator =& new Calendar_Decorator($this->mockcal);
        $Decorator->fetch();
    }
    function testFetchAll() {
        $this->mockcal->expectOnce('fetchAll',array());
        $Decorator =& new Calendar_Decorator($this->mockcal);
        $Decorator->fetchAll();
    }
    function testSize() {
        $this->mockcal->expectOnce('size',array());
        $Decorator =& new Calendar_Decorator($this->mockcal);
        $Decorator->size();
    }
    function testIsValid() {
        $this->mockcal->expectOnce('isValid',array());
        $Decorator =& new Calendar_Decorator($this->mockcal);
        $Decorator->isValid();
    }
    function testGetValidator() {
        $this->mockcal->expectOnce('getValidator',array());
        $Decorator =& new Calendar_Decorator($this->mockcal);
        $Decorator->getValidator();
    }
}
?>
