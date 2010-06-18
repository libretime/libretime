<?php
// $Id: util_uri_test.php,v 1.1 2004/08/16 08:55:24 hfuecks Exp $

require_once('simple_include.php');
require_once('calendar_include.php');

Mock::generate('Calendar_Day','Mock_Calendar_Day');
Mock::generate('Calendar_Engine_Interface','Mock_Calendar_Engine');

class TestOfUtilUri extends UnitTestCase {

    var $MockCal;
    
    function TestOfUtilUri() {
        $this->UnitTestCase('Test of Calendar_Util_Uri');
    }
    
    function setUp() {
        $this->MockCal = & new Mock_Calendar_Day($this);
        $this->MockCal->setReturnValue('getEngine',new Mock_Calendar_Engine($this));
    }
    
    function testFragments() {
        $Uri = new Calendar_Util_Uri('y','m','d','h','m','s');
        $Uri->setFragments('year','month','day','hour','minute','second');
        $this->assertEqual(
            'year=&amp;month=&amp;day=&amp;hour=&amp;minute=&amp;second=',
            $Uri->this($this->MockCal, 'second')
        );
    }
    function testScalarFragments() {
        $Uri = new Calendar_Util_Uri('year','month','day','hour','minute','second');
        $Uri->scalar = true;
        $this->assertEqual(
            '&amp;&amp;&amp;&amp;&amp;',
            $Uri->this($this->MockCal, 'second')
        );
    }
    function testSetSeperator() {
        $Uri = new Calendar_Util_Uri('year','month','day','hour','minute','second');
        $Uri->separator = '/';
        $this->assertEqual(
            'year=/month=/day=/hour=/minute=/second=',
            $Uri->this($this->MockCal, 'second')
        );
    }
}

if (!defined('TEST_RUNNING')) {
    define('TEST_RUNNING', true);
    $test = &new TestOfUtilUri();
    $test->run(new HtmlReporter());
}
?>