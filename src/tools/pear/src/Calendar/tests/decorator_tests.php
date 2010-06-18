<?php
// $Id: decorator_tests.php,v 1.1 2004/05/24 22:25:43 quipo Exp $

require_once('simple_include.php');
require_once('calendar_include.php');

class DecoratorTests extends GroupTest {
    function DecoratorTests() {
        $this->GroupTest('Decorator Tests');
        $this->addTestFile('decorator_test.php');
        $this->addTestFile('decorator_textual_test.php');
        $this->addTestFile('decorator_uri_test.php');
    }
}

if (!defined('TEST_RUNNING')) {
    define('TEST_RUNNING', true);
    $test = &new DecoratorTests();
    $test->run(new HtmlReporter());
}
?>