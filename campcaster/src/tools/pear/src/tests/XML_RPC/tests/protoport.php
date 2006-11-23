<?php

/**
 * Tests that properties of XML_RPC_Client get properly set
 *
 * Any individual tests that fail will have their name, expected result
 * and actual result printed out.  So seeing no output when executing
 * this file is a good thing.
 *
 * Can be run via CLI or a web server.
 *
 * PHP versions 4 and 5
 *
 * LICENSE: This source file is subject to version 3.01 of the PHP license
 * that is available through the world-wide-web at the following URI:
 * http://www.php.net/license/3_01.txt.  If you did not receive a copy of
 * the PHP License and are unable to obtain it through the web, please
 * send a note to license@php.net so we can mail you a copy immediately.
 *
 * @category   Web Services
 * @package    XML_RPC
 * @author     Daniel Convissor <danielc@php.net>
 * @copyright  2005-2006 The PHP Group
 * @license    http://www.php.net/license/3_01.txt  PHP License
 * @version    CVS: $Id: protoport.php,v 1.6 2006/06/11 00:25:17 danielc Exp $
 * @link       http://pear.php.net/package/XML_RPC
 * @since      File available since Release 1.2
 */

/*
 * If the package version number is found in the left hand
 * portion of the if() expression below, that means this file has
 * come from the PEAR installer.  Therefore, let's test the
 * installed version of XML_RPC which should be in the include path.
 * 
 * If the version has not been substituted in the if() expression,
 * this file has likely come from a CVS checkout or a .tar file.
 * Therefore, we'll assume the tests should use the version of
 * XML_RPC that has come from there as well.
 */
if ('1.5.0' != '@'.'package_version'.'@') {
    /**
     * Get the needed class from the PEAR installation
     */
    require_once 'XML/RPC.php';
} else {
    /**
     * Get the needed class from the parent directory
     */
    require_once '../RPC.php';
}

/**
 * Compare the test result to the expected result
 *
 * If the test fails, echo out the results.
 *
 * @param array  $expect     the array of object properties you expect
 *                            from the test
 * @param object $actual     the object results from the test
 * @param string $test_name  the name of the test
 *
 * @return void
 */
function compare($expect, $actual, $test_name) {
    $actual = get_object_vars($actual);
    if (count(array_diff($actual, $expect))) {
        echo "$test_name failed.\nExpect: ";
        print_r($expect);
        echo "Actual: ";
        print_r($actual);
        echo "\n";
    }
}

if (php_sapi_name() != 'cli') {
    echo "<pre>\n";
}


$x = array(
    'path' => 'thepath',
    'server' => 'theserver',
    'protocol' => 'http://',
    'port' => 80,
    'proxy' => '',
    'proxy_protocol' => 'http://',
    'proxy_port' => 8080,
    'proxy_user' => '',
    'proxy_pass' => '',
    'errno' => 0,
    'errstring' => '',
    'debug' => 0,
    'username' => '',
    'password' => '',
);
$c = new XML_RPC_Client('thepath', 'theserver');
compare($x, $c, 'defaults');

$x = array(
    'path' => 'thepath',
    'server' => 'theserver',
    'protocol' => 'http://',
    'port' => 80,
    'proxy' => '',
    'proxy_protocol' => 'http://',
    'proxy_port' => 8080,
    'proxy_user' => '',
    'proxy_pass' => '',
    'errno' => 0,
    'errstring' => '',
    'debug' => 0,
    'username' => '',
    'password' => '',
);
$c = new XML_RPC_Client('thepath', 'http://theserver');
compare($x, $c, 'defaults with http');

$x = array(
    'path' => 'thepath',
    'server' => 'theserver',
    'protocol' => 'ssl://',
    'port' => 443,
    'proxy' => '',
    'proxy_protocol' => 'http://',
    'proxy_port' => 8080,
    'proxy_user' => '',
    'proxy_pass' => '',
    'errno' => 0,
    'errstring' => '',
    'debug' => 0,
    'username' => '',
    'password' => '',
);
$c = new XML_RPC_Client('thepath', 'https://theserver');
compare($x, $c, 'defaults with https');

$x = array(
    'path' => 'thepath',
    'server' => 'theserver',
    'protocol' => 'ssl://',
    'port' => 443,
    'proxy' => '',
    'proxy_protocol' => 'http://',
    'proxy_port' => 8080,
    'proxy_user' => '',
    'proxy_pass' => '',
    'errno' => 0,
    'errstring' => '',
    'debug' => 0,
    'username' => '',
    'password' => '',
);
$c = new XML_RPC_Client('thepath', 'ssl://theserver');
compare($x, $c, 'defaults with ssl');


$x = array(
    'path' => 'thepath',
    'server' => 'theserver',
    'protocol' => 'http://',
    'port' => 65,
    'proxy' => '',
    'proxy_protocol' => 'http://',
    'proxy_port' => 8080,
    'proxy_user' => '',
    'proxy_pass' => '',
    'errno' => 0,
    'errstring' => '',
    'debug' => 0,
    'username' => '',
    'password' => '',
);
$c = new XML_RPC_Client('thepath', 'theserver', 65);
compare($x, $c, 'port 65');

$x = array(
    'path' => 'thepath',
    'server' => 'theserver',
    'protocol' => 'http://',
    'port' => 65,
    'proxy' => '',
    'proxy_protocol' => 'http://',
    'proxy_port' => 8080,
    'proxy_user' => '',
    'proxy_pass' => '',
    'errno' => 0,
    'errstring' => '',
    'debug' => 0,
    'username' => '',
    'password' => '',
);
$c = new XML_RPC_Client('thepath', 'http://theserver', 65);
compare($x, $c, 'port 65 with http');

$x = array(
    'path' => 'thepath',
    'server' => 'theserver',
    'protocol' => 'ssl://',
    'port' => 65,
    'proxy' => '',
    'proxy_protocol' => 'http://',
    'proxy_port' => 8080,
    'proxy_user' => '',
    'proxy_pass' => '',
    'errno' => 0,
    'errstring' => '',
    'debug' => 0,
    'username' => '',
    'password' => '',
);
$c = new XML_RPC_Client('thepath', 'https://theserver', 65);
compare($x, $c, 'port 65 with https');

$x = array(
    'path' => 'thepath',
    'server' => 'theserver',
    'protocol' => 'ssl://',
    'port' => 65,
    'proxy' => '',
    'proxy_protocol' => 'http://',
    'proxy_port' => 8080,
    'proxy_user' => '',
    'proxy_pass' => '',
    'errno' => 0,
    'errstring' => '',
    'debug' => 0,
    'username' => '',
    'password' => '',
);
$c = new XML_RPC_Client('thepath', 'ssl://theserver', 65);
compare($x, $c, 'port 65 with ssl');


$x = array(
    'path' => 'thepath',
    'server' => 'theserver',
    'protocol' => 'http://',
    'port' => 80,
    'proxy' => 'theproxy',
    'proxy_protocol' => 'http://',
    'proxy_port' => 8080,
    'proxy_user' => '',
    'proxy_pass' => '',
    'errno' => 0,
    'errstring' => '',
    'debug' => 0,
    'username' => '',
    'password' => '',
);
$c = new XML_RPC_Client('thepath', 'theserver', 0,
                        'theproxy');
compare($x, $c, 'defaults proxy');

$x = array(
    'path' => 'thepath',
    'server' => 'theserver',
    'protocol' => 'http://',
    'port' => 80,
    'proxy' => 'theproxy',
    'proxy_protocol' => 'http://',
    'proxy_port' => 8080,
    'proxy_user' => '',
    'proxy_pass' => '',
    'errno' => 0,
    'errstring' => '',
    'debug' => 0,
    'username' => '',
    'password' => '',
);
$c = new XML_RPC_Client('thepath', 'http://theserver', 0,
                        'http://theproxy');
compare($x, $c, 'defaults with http proxy');

$x = array(
    'path' => 'thepath',
    'server' => 'theserver',
    'protocol' => 'ssl://',
    'port' => 443,
    'proxy' => 'theproxy',
    'proxy_protocol' => 'ssl://',
    'proxy_port' => 443,
    'proxy_user' => '',
    'proxy_pass' => '',
    'errno' => 0,
    'errstring' => '',
    'debug' => 0,
    'username' => '',
    'password' => '',
);
$c = new XML_RPC_Client('thepath', 'https://theserver', 0,
                        'https://theproxy');
compare($x, $c, 'defaults with https proxy');

$x = array(
    'path' => 'thepath',
    'server' => 'theserver',
    'protocol' => 'ssl://',
    'port' => 443,
    'proxy' => 'theproxy',
    'proxy_protocol' => 'ssl://',
    'proxy_port' => 443,
    'proxy_user' => '',
    'proxy_pass' => '',
    'errno' => 0,
    'errstring' => '',
    'debug' => 0,
    'username' => '',
    'password' => '',
);
$c = new XML_RPC_Client('thepath', 'ssl://theserver', 0,
                        'ssl://theproxy');
compare($x, $c, 'defaults with ssl proxy');


$x = array(
    'path' => 'thepath',
    'server' => 'theserver',
    'protocol' => 'http://',
    'port' => 65,
    'proxy' => 'theproxy',
    'proxy_protocol' => 'http://',
    'proxy_port' => 6565,
    'proxy_user' => '',
    'proxy_pass' => '',
    'errno' => 0,
    'errstring' => '',
    'debug' => 0,
    'username' => '',
    'password' => '',
);
$c = new XML_RPC_Client('thepath', 'theserver', 65,
                        'theproxy', 6565);
compare($x, $c, 'port 65 proxy 6565');

$x = array(
    'path' => 'thepath',
    'server' => 'theserver',
    'protocol' => 'http://',
    'port' => 65,
    'proxy' => 'theproxy',
    'proxy_protocol' => 'http://',
    'proxy_port' => 6565,
    'proxy_user' => '',
    'proxy_pass' => '',
    'errno' => 0,
    'errstring' => '',
    'debug' => 0,
    'username' => '',
    'password' => '',
);
$c = new XML_RPC_Client('thepath', 'http://theserver', 65,
                        'http://theproxy', 6565);
compare($x, $c, 'port 65 with http proxy 6565');

$x = array(
    'path' => 'thepath',
    'server' => 'theserver',
    'protocol' => 'ssl://',
    'port' => 65,
    'proxy' => 'theproxy',
    'proxy_protocol' => 'ssl://',
    'proxy_port' => 6565,
    'proxy_user' => '',
    'proxy_pass' => '',
    'errno' => 0,
    'errstring' => '',
    'debug' => 0,
    'username' => '',
    'password' => '',
);
$c = new XML_RPC_Client('thepath', 'https://theserver', 65,
                        'https://theproxy', 6565);
compare($x, $c, 'port 65 with https proxy 6565');

$x = array(
    'path' => 'thepath',
    'server' => 'theserver',
    'protocol' => 'ssl://',
    'port' => 65,
    'proxy' => 'theproxy',
    'proxy_protocol' => 'ssl://',
    'proxy_port' => 6565,
    'proxy_user' => '',
    'proxy_pass' => '',
    'errno' => 0,
    'errstring' => '',
    'debug' => 0,
    'username' => '',
    'password' => '',
);
$c = new XML_RPC_Client('thepath', 'ssl://theserver', 65,
                        'ssl://theproxy', 6565);
compare($x, $c, 'port 65 with ssl proxy 6565');


$x = array(
    'path' => 'thepath',
    'server' => 'theserver',
    'protocol' => 'ssl://',
    'port' => 443,
    'proxy' => 'theproxy',
    'proxy_protocol' => 'ssl://',
    'proxy_port' => 443,
    'proxy_user' => '',
    'proxy_pass' => '',
    'errno' => 0,
    'errstring' => '',
    'debug' => 0,
    'username' => '',
    'password' => '',
);
$c = new XML_RPC_Client('thepath', 'theserver', 443,
                        'theproxy', 443);
compare($x, $c, 'port 443 no protocol and proxy port 443 no protocol');

$x = array(
    'path' => 'thepath',
    'server' => 'theserver',
    'protocol' => 'http://',
    'port' => 80,
    'proxy' => 'theproxy',
    'proxy_protocol' => 'ssl://',
    'proxy_port' => 6565,
    'proxy_user' => '',
    'proxy_pass' => '',
    'errno' => 0,
    'errstring' => '',
    'debug' => 0,
    'username' => '',
    'password' => '',
);
$c = new XML_RPC_Client('thepath', 'theserver', 0,
                        'ssl://theproxy', 6565);
compare($x, $c, 'port 443 no protocol and proxy port 443 no protocol');

echo "\nIf no other output was produced, these tests passed.\n";
