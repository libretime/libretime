<?php
// $Id: decorator_uri_test.php,v 1.2 2004/07/08 10:18:48 quipo Exp $

require_once('simple_include.php');
require_once('calendar_include.php');

require_once('./decorator_test.php');

class TestOfDecoratorUri extends TestOfDecorator {
    function TestOfDecoratorUri() {
        $this->UnitTestCase('Test of Calendar_Decorator_Uri');
    }
    function testFragments() {
        $Uri = new Calendar_Decorator_Uri($this->mockcal);
        $Uri->setFragments('year','month','day','hour','minute','second');
        $this->assertEqual('year=&amp;month=&amp;day=&amp;hour=&amp;minute=&amp;second=',$Uri->this('second'));
    }
    function testScalarFragments() {
        $Uri = new Calendar_Decorator_Uri($this->mockcal);
        $Uri->setFragments('year','month','day','hour','minute','second');
        $Uri->setScalar();
        $this->assertEqual('&amp;&amp;&amp;&amp;&amp;',$Uri->this('second'));
    }
    function testSetSeperator() {
        $Uri = new Calendar_Decorator_Uri($this->mockcal);
        $Uri->setFragments('year','month','day','hour','minute','second');
        $Uri->setSeparator('/');
        $this->assertEqual('year=/month=/day=/hour=/minute=/second=',$Uri->this('second'));
    }
}

if (!defined('TEST_RUNNING')) {
    define('TEST_RUNNING', true);
    $test = &new TestOfDecoratorUri();
    $test->run(new HtmlReporter());
}
?>