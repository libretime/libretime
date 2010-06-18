<?php
// $Id: simple_include.php,v 1.1 2004/05/24 22:25:43 quipo Exp $
if (!defined('SIMPLE_TEST')) {
    define('SIMPLE_TEST', '../../../simpletest/');
}

require_once(SIMPLE_TEST . 'unit_tester.php');
require_once(SIMPLE_TEST . 'reporter.php');
require_once(SIMPLE_TEST . 'mock_objects.php');
?>