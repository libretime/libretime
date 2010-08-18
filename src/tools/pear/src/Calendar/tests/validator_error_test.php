<?php
// $Id: validator_error_test.php 159563 2004-05-24 22:25:43Z quipo $

require_once('simple_include.php');
require_once('calendar_include.php');

class TestOfValidationError extends UnitTestCase {
    var $vError;
    function TestOfValidationError() {
        $this->UnitTestCase('Test of Validation Error');
    }
    function setUp() {
        $this->vError = new Calendar_Validation_Error('foo',20,'bar');
    }
    function testGetUnit() {
        $this->assertEqual($this->vError->getUnit(),'foo');
    }
    function testGetValue() {
        $this->assertEqual($this->vError->getValue(),20);
    }
    function testGetMessage() {
        $this->assertEqual($this->vError->getMessage(),'bar');
    }
    function testToString() {
        $this->assertEqual($this->vError->toString(),'foo = 20 [bar]');
    }
}

if (!defined('TEST_RUNNING')) {
    define('TEST_RUNNING', true);
    $test = &new TestOfValidationError();
    $test->run(new HtmlReporter());
}
?>