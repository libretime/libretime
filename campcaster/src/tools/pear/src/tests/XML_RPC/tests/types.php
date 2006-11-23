<?php

/**
 * Tests how the XML_RPC server handles a bunch of different parameter
 * data types.
 *
 * If you are running this test from a CVS checkout, you must rename the working
 * directory from "XML_RPC" to "XML".
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
 * @version    CVS: $Id: types.php,v 1.2 2006/06/11 00:25:17 danielc Exp $
 * @link       http://pear.php.net/package/XML_RPC
 * @since      File available since Release 1.4.4
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
    require_once 'XML/RPC/Server.php';
} else {
    if (substr(dirname(__FILE__), -9, -6) != 'XML') {
        echo "The parent directory must be named 'XML'.\n";
        exit;
    }

    ini_set('include_path', '../../'
            . PATH_SEPARATOR . '.' . PATH_SEPARATOR
            . ini_get('include_path')
    );

    /**
     * Get the needed class from the parent directory
     */
    require_once '../Server.php';
}

$GLOBALS['HTTP_RAW_POST_DATA'] = <<<EOPOST
<?xml version="1.0"?>
<methodCall>
 <methodName>allgot</methodName>
  <params>
   <param><value>default to string</value></param>
   <param><value><string>inside string</string></value></param>
   <param><value><int>8</int></value></param>
   <param><value><datetime.iso8601>20050809T01:33:44</datetime.iso8601></value></param>

   <param>
    <value>
     <array>
      <data>
       <value>
        <string>a</string>
       </value>
       <value>
        <string>b</string>
       </value>
      </data>
     </array>
    </value>
   </param>

   <param>
    <value>
     <struct>
      <member>
       <name>a</name>
       <value>
        <string>ay</string>
       </value>
      </member>
      <member>
       <name>b</name>
       <value>
        <string>be</string>
       </value>
      </member>
     </struct>
    </value>
   </param>

  </params>
 </methodCall>
EOPOST;

$expect = <<<EOEXP
<?xml version="1.0" encoding="UTF-8"?>
<methodResponse>
<params>
<param>
<value><string>param 0: 'default to string'
param 1: 'inside string'
param 2: '8'
param 3: '20050809T01:33:44'
param 4: array (
  0 =&gt; 'a',
  1 =&gt; 'b',
)
param 5: array (
  'a' =&gt; 'ay',
  'b' =&gt; 'be',
)
</string></value>
</param>
</params>
</methodResponse>
EOEXP;

include './allgot.inc';
