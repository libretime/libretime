<?php

/**
 * 
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
 * @version    CVS: $Id: allgot.inc,v 1.2 2006/06/11 00:25:17 danielc Exp $
 * @link       http://pear.php.net/package/XML_RPC
 * @since      File available since Release 1.4.4
 */

ob_start();

function returnAllGot($params) {
    $out = '';
    $count = count($params->params);
    for ($i = 0; $i < $count; $i++) {
        $param = $params->getParam($i);
        if (!XML_RPC_Value::isValue($param)) {
            $out .= "parameter $i was error: $param\n";
            continue;
        }
        $got = XML_RPC_Decode($param);
        $out .= "param $i: " . var_export($got, true) . "\n";
    }
    $val = new XML_RPC_Value($out, 'string');
    return new XML_RPC_Response($val);
}

$server = new XML_RPC_Server(
    array(
        'allgot' => array(
            'function' => 'returnAllGot',
        ),
    )
);

$got = ob_get_clean();

if ($got == $expect) {
    echo "passed\n";
} else {
    echo "FAILED\n";
    echo "Expected:\n$expect\n";
    echo "Got:\n$got\n";
}
