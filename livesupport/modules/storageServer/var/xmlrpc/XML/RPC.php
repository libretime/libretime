<?php
// /* vim: set expandtab tabstop=4 shiftwidth=4: */
// by Edd Dumbill (C) 1999-2001
// <edd@usefulinc.com>
// $Id: RPC.php,v 1.2 2005/02/25 01:42:40 tomas Exp $

// License is granted to use or modify this software ("XML-RPC for PHP")
// for commercial or non-commercial use provided the copyright of the author
// is preserved in any distributed or derivative work.

// THIS SOFTWARE IS PROVIDED BY THE AUTHOR ``AS IS'' AND ANY EXPRESSED OR
// IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES
// OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED.
// IN NO EVENT SHALL THE AUTHOR BE LIABLE FOR ANY DIRECT, INDIRECT,
// INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT
// NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
// DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
// THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
// (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF
// THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.

// Adapted to PEAR standards by Stig S�her Bakken <stig@php.net> and
// Martin Jansen <mj@php.net>
// /* $id$ */

if (!function_exists('xml_parser_create')) {
// Win 32 fix. From: "Leo West" <lwest@imaginet.fr>
    if ($WINDIR) {
        dl("php_xml.dll");
    } else {
        dl("xml.so");
    }
}

define('XML_RPC_ERROR_INVALID_TYPE',        101);
define('XML_RPC_ERROR_NON_NUMERIC_FOUND',   102);
define('XML_RPC_ERROR_CONNECTION_FAILED',   103);
define('XML_RPC_ERROR_ALREADY_INITIALIZED', 104);

$GLOBALS['XML_RPC_I4'] = "i4";
$GLOBALS['XML_RPC_Int'] = "int";
$GLOBALS['XML_RPC_Boolean'] = "boolean";
$GLOBALS['XML_RPC_Double'] = "double";
$GLOBALS['XML_RPC_String'] = "string";
$GLOBALS['XML_RPC_DateTime'] = "dateTime.iso8601";
$GLOBALS['XML_RPC_Base64'] = "base64";
$GLOBALS['XML_RPC_Array'] = "array";
$GLOBALS['XML_RPC_Struct'] = "struct";

$GLOBALS['XML_RPC_Types'] = array($GLOBALS['XML_RPC_I4'] => 1,
                                $GLOBALS['XML_RPC_Int'] => 1,
                                $GLOBALS['XML_RPC_Boolean'] => 1,
                                $GLOBALS['XML_RPC_String'] => 1,
                                $GLOBALS['XML_RPC_Double'] => 1,
                                $GLOBALS['XML_RPC_DateTime'] => 1,
                                $GLOBALS['XML_RPC_Base64'] => 1,
                                $GLOBALS['XML_RPC_Array'] => 2,
                                $GLOBALS['XML_RPC_Struct'] => 3);

$GLOBALS['XML_RPC_entities'] = array("quot" => '"',
                                     "amp" => "&",
                                     "lt" => "<",
                                     "gt" => ">",
                                     "apos" => "'");

$GLOBALS['XML_RPC_err']["unknown_method"] = 1;
$GLOBALS['XML_RPC_str']["unknown_method"] = "Unknown method";
$GLOBALS['XML_RPC_err']["invalid_return"] = 2;
$GLOBALS['XML_RPC_str']["invalid_return"] = "Invalid return payload: enabling debugging to examine incoming payload";
$GLOBALS['XML_RPC_err']["incorrect_params"] = 3;
$GLOBALS['XML_RPC_str']["incorrect_params"] = "Incorrect parameters passed to method";
$GLOBALS['XML_RPC_err']["introspect_unknown"] = 4;
$GLOBALS['XML_RPC_str']["introspect_unknown"] = "Can't introspect: method unknown";
$GLOBALS['XML_RPC_err']["http_error"] = 5;
$GLOBALS['XML_RPC_str']["http_error"] = "Didn't receive 200 OK from remote server.";

$GLOBALS['XML_RPC_defencoding'] = "UTF-8";

// let user errors start at 800
$GLOBALS['XML_RPC_erruser'] = 800;

// let XML parse errors start at 100
$GLOBALS['XML_RPC_errxml'] = 100;

// formulate backslashes for escaping regexp
$GLOBALS['XML_RPC_backslash'] = chr(92) . chr(92);

$GLOBALS['XML_RPC_twoslash'] = $GLOBALS['XML_RPC_backslash'] . $GLOBALS['XML_RPC_backslash'];
$GLOBALS['XML_RPC_twoslash'] = "2SLS";

// used to store state during parsing
// quick explanation of components:
//   st - used to build up a string for evaluation
//   ac - used to accumulate values
//   qt - used to decide if quotes are needed for evaluation
//   cm - used to denote struct or array (comma needed)
//   isf - used to indicate a fault
//   lv - used to indicate "looking for a value": implements
//        the logic to allow values with no types to be strings
//   params - used to store parameters in method calls
//   method - used to store method name

$GLOBALS['XML_RPC_xh'] = array();

function XML_RPC_entity_decode($string)
{
    $top = split("&", $string);
    $op = "";
    $i = 0;
    while($i < sizeof($top)) {
        if (ereg("^([#a-zA-Z0-9]+);", $top[$i], $regs)) {
            $op .= ereg_replace("^[#a-zA-Z0-9]+;",
                                XML_RPC_lookup_entity($regs[1]),
                                $top[$i]);
        } else {
            if ($i == 0) {
                $op = $top[$i];
            } else {
                $op .= "&" . $top[$i];
            }
        }

        $i++;
    }
    return $op;
}


function XML_RPC_lookup_entity($ent)
{
    global $XML_RPC_entities;

    if ($XML_RPC_entities[strtolower($ent)]) {
        return $XML_RPC_entities[strtolower($ent)];
    }

    if (ereg("^#([0-9]+)$", $ent, $regs)) {
        return chr($regs[1]);
    }

    return "?";
}


function XML_RPC_se($parser, $name, $attrs)
{
    global $XML_RPC_xh, $XML_RPC_DateTime, $XML_RPC_String;

    switch ($name) {
    case "STRUCT":
    case "ARRAY":
        $XML_RPC_xh[$parser]['st'] .= "array(";
        $XML_RPC_xh[$parser]['cm']++;
        // this last line turns quoting off
        // this means if we get an empty array we'll
        // simply get a bit of whitespace in the eval
        $XML_RPC_xh[$parser]['qt'] = 0;
        break;

    case "NAME":
        $XML_RPC_xh[$parser]['st'] .= "'";
        $XML_RPC_xh[$parser]['ac'] = "";
        break;

    case "FAULT":
        $XML_RPC_xh[$parser]['isf'] = 1;
        break;

    case "PARAM":
        $XML_RPC_xh[$parser]['st'] = "";
        break;

    case "VALUE":
        $XML_RPC_xh[$parser]['st'] .= "new XML_RPC_Value(";
        $XML_RPC_xh[$parser]['lv'] = 1;
        $XML_RPC_xh[$parser]['vt'] = $XML_RPC_String;
        $XML_RPC_xh[$parser]['ac'] = "";
        $XML_RPC_xh[$parser]['qt'] = 0;
        // look for a value: if this is still 1 by the
        // time we reach the first data segment then the type is string
        // by implication and we need to add in a quote
        break;

    case "I4":
    case "INT":
    case "STRING":
    case "BOOLEAN":
    case "DOUBLE":
    case "DATETIME.ISO8601":
    case "BASE64":
        $XML_RPC_xh[$parser]['ac'] = ""; // reset the accumulator

        if ($name == "DATETIME.ISO8601" || $name == "STRING") {
            $XML_RPC_xh[$parser]['qt'] = 1;

            if ($name == "DATETIME.ISO8601") {
                $XML_RPC_xh[$parser]['vt'] = $XML_RPC_DateTime;
            }

        } elseif ($name == "BASE64") {
            $XML_RPC_xh[$parser]['qt'] = 2;
        } else {
            // No quoting is required here -- but
            // at the end of the element we must check
            // for data format errors.
            $XML_RPC_xh[$parser]['qt'] = 0;
        }
        break;

    case "MEMBER":
    case "METHODNAME":
        $XML_RPC_xh[$parser]['ac'] = "";
        break;

    default:
        break;
    }

    if ($name!="VALUE") {
        $XML_RPC_xh[$parser]['lv'] = 0;
    }
}


function XML_RPC_ee($parser, $name)
{
    global $XML_RPC_xh,$XML_RPC_Types,$XML_RPC_String;

    switch ($name) {
    case "STRUCT":
    case "ARRAY":
        if ($XML_RPC_xh[$parser]['cm'] && substr($XML_RPC_xh[$parser]['st'], -1) == ',') {
            $XML_RPC_xh[$parser]['st'] = substr($XML_RPC_xh[$parser]['st'],0,-1);
        }

        $XML_RPC_xh[$parser]['st'] .= ")";
        $XML_RPC_xh[$parser]['vt'] = strtolower($name);
        $XML_RPC_xh[$parser]['cm']--;
        break;

    case "NAME":
        $XML_RPC_xh[$parser]['st'] .= $XML_RPC_xh[$parser]['ac'] . "' => ";
        break;

    case "BOOLEAN":
        // special case here: we translate boolean 1 or 0 into PHP
        // constants true or false
        if ($XML_RPC_xh[$parser]['ac'] == '1') {
            $XML_RPC_xh[$parser]['ac'] = "true";
        } else {
            $XML_RPC_xh[$parser]['ac'] = "false";
        }

        $XML_RPC_xh[$parser]['vt'] = strtolower($name);
        // Drop through intentionally.

    case "I4":
    case "INT":
    case "STRING":
    case "DOUBLE":
    case "DATETIME.ISO8601":
    case "BASE64":
        if ($XML_RPC_xh[$parser]['qt'] == 1) {
            // we use double quotes rather than single so backslashification works OK
            $XML_RPC_xh[$parser]['st'] .= "\"" . $XML_RPC_xh[$parser]['ac'] . "\"";
        } elseif ($XML_RPC_xh[$parser]['qt'] == 2) {
            $XML_RPC_xh[$parser]['st'] .= "base64_decode('" . $XML_RPC_xh[$parser]['ac'] . "')";
        } elseif ($name=="BOOLEAN") {
            $XML_RPC_xh[$parser]['st'] .= $XML_RPC_xh[$parser]['ac'];
        } else {
            // we have an I4, INT or a DOUBLE
            // we must check that only 0123456789-.<space> are characters here
            if (!ereg("^\-?[0123456789 \t\.]+$", $XML_RPC_xh[$parser]['ac'])) {
                $this->raiseError("Non-numeric value recieved in INT or DOUBLE", XML_RPC_ERROR_NON_NUMERIC_FOUND);
                $XML_RPC_xh[$parser]['st'] .= "ERROR_NON_NUMERIC_FOUND";
            } else {
                // it's ok, add it on
                $XML_RPC_xh[$parser]['st'] .= $XML_RPC_xh[$parser]['ac'];
            }
        }

        $XML_RPC_xh[$parser]['ac'] = "";
        $XML_RPC_xh[$parser]['qt'] = 0;
        $XML_RPC_xh[$parser]['lv'] = 3; // indicate we've found a value
        break;

    case "VALUE":
        // deal with a string value
        if (strlen($XML_RPC_xh[$parser]['ac']) > 0 &&
            $XML_RPC_xh[$parser]['vt'] == $XML_RPC_String) {

            $XML_RPC_xh[$parser]['st'] .= "\"" . $XML_RPC_xh[$parser]['ac'] . "\"";
        }

        // This if () detects if no scalar was inside <VALUE></VALUE>
        // and pads an empty "".
        if ($XML_RPC_xh[$parser]['st'][strlen($XML_RPC_xh[$parser]['st'])-1] == '(') {
            $XML_RPC_xh[$parser]['st'] .= '""';
        }
        $XML_RPC_xh[$parser]['st'] .= ", '" . $XML_RPC_xh[$parser]['vt'] . "')";
        if ($XML_RPC_xh[$parser]['cm']) {
            $XML_RPC_xh[$parser]['st'] .= ",";
        }
        break;

    case "MEMBER":
        $XML_RPC_xh[$parser]['ac'] = "";
        $XML_RPC_xh[$parser]['qt'] = 0;
        break;

    case "DATA":
        $XML_RPC_xh[$parser]['ac'] = "";
        $XML_RPC_xh[$parser]['qt'] = 0;
        break;

    case "PARAM":
        $XML_RPC_xh[$parser]['params'][] = $XML_RPC_xh[$parser]['st'];
        break;

    case "METHODNAME":
        $XML_RPC_xh[$parser]['method'] = ereg_replace("^[\n\r\t ]+", "", $XML_RPC_xh[$parser]['ac']);
        break;

    case "BOOLEAN":
        // special case here: we translate boolean 1 or 0 into PHP
        // constants true or false
        if ($XML_RPC_xh[$parser]['ac'] == '1') {
            $XML_RPC_xh[$parser]['ac'] = "true";
        } else {
            $XML_RPC_xh[$parser]['ac'] = "false";
        }

        $XML_RPC_xh[$parser]['vt'] = strtolower($name);
        break;

    default:
        break;
    }

    // if it's a valid type name, set the type
    if (isset($XML_RPC_Types[strtolower($name)])) {
        $XML_RPC_xh[$parser]['vt'] = strtolower($name);
    }
}


function XML_RPC_cd($parser, $data)
{
    global $XML_RPC_xh, $XML_RPC_backslash;

    if ($XML_RPC_xh[$parser]['lv'] != 3) {
        // "lookforvalue==3" means that we've found an entire value
        // and should discard any further character data

        if ($XML_RPC_xh[$parser]['lv'] == 1) {
            // if we've found text and we're just in a <value> then
            // turn quoting on, as this will be a string
            $XML_RPC_xh[$parser]['qt'] = 1;
            // and say we've found a value
            $XML_RPC_xh[$parser]['lv'] = 2;
        }

        // replace characters that eval would
        // do special things with
        if (isset($XML_RPC_xh[$parser]['ac'])) {
            $XML_RPC_xh[$parser]['ac'] .= str_replace('$', '\$',
                str_replace('"', '\"', str_replace(chr(92),
                $XML_RPC_backslash, $data)));
        } else {
            $XML_RPC_xh[$parser]['ac'] = '';
        }
    }
}


function XML_RPC_dh($parser, $data)
{
    global $XML_RPC_xh;

    if (substr($data, 0, 1) == "&" && substr($data, -1, 1) == ";") {
        if ($XML_RPC_xh[$parser]['lv'] == 1) {
            $XML_RPC_xh[$parser]['qt'] = 1;
            $XML_RPC_xh[$parser]['lv'] = 2;
        }
        $XML_RPC_xh[$parser]['ac'] .= str_replace('$', '\$',
            str_replace('"', '\"', str_replace(chr(92),
                $XML_RPC_backslash, $data)));
    }
}

/**
 * Base class
 *
 * This class provides common functions for all of the XML_RPC classes.
 */
class XML_RPC_Base {
    function raiseError($msg, $code)
    {
        include_once 'PEAR.php';
        PEAR::raiseError(get_class($this) . ": " . $msg, $code);
    }
}

class XML_RPC_Client extends XML_RPC_Base {
    var $path;
    var $server;
    var $port;
    var $errno;
    var $errstring;
    var $debug = 0;
    var $username = "";
    var $password = "";

    function XML_RPC_Client($path, $server, $port = 80,
                            $proxy = '', $proxy_port = 8080,
                            $proxy_user = '', $proxy_pass = '')
    {
        $this->port = $port;
        $this->server = $server;
        $this->path = $path;
        $this->proxy = $proxy;
        $this->proxy_port = $proxy_port;
        $this->proxy_user = $proxy_user;
        $this->proxy_pass = $proxy_pass;
    }

    function setDebug($in)
    {
        if ($in) {
            $this->debug = 1;
        } else {
            $this->debug = 0;
        }
    }

    function setCredentials($u, $p)
    {
        $this->username = $u;
        $this->password = $p;
    }

    function send($msg, $timeout = 0)
    {
        // where msg is an xmlrpcmsg
        $msg->debug = $this->debug;
        return $this->sendPayloadHTTP10($msg, $this->server, $this->port,
                                        $timeout, $this->username,
                                        $this->password);
    }

    function sendPayloadHTTP10($msg, $server, $port, $timeout=0,
                               $username = "", $password = "")
    {
        // If we're using a proxy open a socket to the proxy server instead to the xml-rpc server
        if ($this->proxy){
            if ($timeout > 0) {
                $fp = fsockopen($this->proxy, $this->proxy_port, $this->errno, $this->errstr, $timeout);
            } else {
                $fp = fsockopen($this->proxy, $this->proxy_port, $this->errno, $this->errstr);
            }
        } else {
            if ($timeout > 0) {
                $fp = fsockopen($server, $port, $this->errno, $this->errstr, $timeout);
            } else {
                $fp = fsockopen($server, $port, $this->errno, $this->errstr);
            }
        }

        if (!$fp && $this->proxy) {
            $this->raiseError(
                "Connection to proxy server " . $this->proxy . ":" . $this->proxy_port . " failed",
                XML_RPC_ERROR_CONNECTION_FAILED);
        } elseif (!$fp) {
            $this->raiseError(
                "Connection to RPC server " . $this->server . " failed",
                XML_RPC_ERROR_CONNECTION_FAILED);
        }

        // Only create the payload if it was not created previously
        if (empty($msg->payload)) {
            $msg->createPayload();
        }

        // thanks to Grant Rauscher <grant7@firstworld.net> for this
        $credentials = "";
        if ($username != "") {
            $credentials = "Authorization: Basic " .
                base64_encode($username . ":" . $password) . "\r\n";
        }


        if ($this->proxy) {
            $op = "POST http://" . $this->server;

            if ($this->proxy_port) {
                $op .= ":" . $this->port;
            }
        } else {
           $op = "POST ";
        }

        $op .= $this->path. " HTTP/1.0\r\n" .
               "User-Agent: PEAR XML_RPC\r\n" .
               "Host: " . $this->server . "\r\n";
        if ($this->proxy && $this->proxy_user != '') {
            $op .= 'Proxy-Authorization: Basic ' .
                base64_encode($this->proxy_user . ':' . $this->proxy_pass) .
                "\r\n";
        }
        $op .= $credentials .
               "Content-Type: text/xml\r\n" .
               "Content-Length: " . strlen($msg->payload) . "\r\n\r\n" .
               $msg->payload;

        if (!fputs($fp, $op, strlen($op))) {
            $this->errstr = "Write error";
            return 0;
        }
        $resp = $msg->parseResponseFile($fp);
        fclose($fp);
        return $resp;
    }
}


class XML_RPC_Response extends XML_RPC_Base
{
    var $xv;
    var $fn;
    var $fs;
    var $hdrs;

    function XML_RPC_Response($val, $fcode = 0, $fstr = "")
    {
        if ($fcode != 0) {
            $this->fn = $fcode;
            $this->fs = htmlspecialchars($fstr);
        } else {
            $this->xv = $val;
        }
    }

    function faultCode()
    {
        if (isset($this->fn)) {
            return $this->fn;
        } else {
            return 0;
        }
    }

    function faultString()
    {
        return $this->fs;
    }

    function value()
    {
        return $this->xv;
    }

    function serialize()
    {
        $rs = "<methodResponse>\n";
        if ($this->fn) {
            $rs .= "<fault>
  <value>
    <struct>
      <member>
        <name>faultCode</name>
        <value><int>" . $this->fn . "</int></value>
      </member>
      <member>
        <name>faultString</name>
        <value><string>" . $this->fs . "</string></value>
      </member>
    </struct>
  </value>
</fault>";
        } else {
            $rs .= "<params>\n<param>\n" . $this->xv->serialize() .
        "</param>\n</params>";
        }
        $rs .= "\n</methodResponse>";
        return $rs;
    }
}


class XML_RPC_Message extends XML_RPC_Base
{
    var $payload;
    var $methodname;
    var $params = array();
    var $debug = 0;

    function XML_RPC_Message($meth, $pars = 0)
    {
        $this->methodname = $meth;
        if (is_array($pars) && sizeof($pars)>0) {
            for($i = 0; $i < sizeof($pars); $i++) {
                $this->addParam($pars[$i]);
            }
        }
    }

    function xml_header()
    {
        return "<?xml version=\"1.0\"?>\n<methodCall>\n";
    }

    function xml_footer()
    {
        return "</methodCall>\n";
    }

    function createPayload()
    {
        $this->payload = $this->xml_header();
        $this->payload .= "<methodName>" . $this->methodname . "</methodName>\n";
        $this->payload .= "<params>\n";
        for($i = 0; $i < sizeof($this->params); $i++) {
            $p = $this->params[$i];
            $this->payload .= "<param>\n" . $p->serialize() . "</param>\n";
        }
        $this->payload .= "</params>\n";
        $this->payload .= $this->xml_footer();
        $this->payload = str_replace("\n", "\r\n", $this->payload);
    }

    function method($meth = "")
    {
        if ($meth != "") {
            $this->methodname = $meth;
        }
        return $this->methodname;
    }

    function serialize()
    {
        $this->createPayload();
        return $this->payload;
    }

    function addParam($par)
    {
        $this->params[] = $par;
    }

    function getParam($i)
    {
        return $this->params[$i];
    }

    function getNumParams()
    {
        return sizeof($this->params);
    }

    function parseResponseFile($fp)
    {
        $ipd = "";

        while($data = fread($fp, 32768)) {
            $ipd .= $data;
        }
        return $this->parseResponse($ipd);
    }

    function parseResponse($data = "")
    {
        global $XML_RPC_xh,$XML_RPC_err,$XML_RPC_str;
        global $XML_RPC_defencoding;

        $parser = xml_parser_create($XML_RPC_defencoding);

        $XML_RPC_xh[$parser] = array();

        $XML_RPC_xh[$parser]['st'] = "";
        $XML_RPC_xh[$parser]['cm'] = 0;
        $XML_RPC_xh[$parser]['isf'] = 0;
        $XML_RPC_xh[$parser]['ac'] = "";
        $XML_RPC_xh[$parser]['qt'] = "";

        xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, true);
        xml_set_element_handler($parser, "XML_RPC_se", "XML_RPC_ee");
        xml_set_character_data_handler($parser, "XML_RPC_cd");
        xml_set_default_handler($parser, "XML_RPC_dh");
        $xmlrpc_value = new XML_RPC_Value;

        $hdrfnd = 0;
        if ($this->debug) {
            print "<PRE>---GOT---\n";
            print isset($_SERVER['SERVER_PROTOCOL']) ? htmlspecialchars($data) : $data;
            print "\n---END---\n</PRE>";
        }

        // see if we got an HTTP 200 OK, else bomb
        // but only do this if we're using the HTTP protocol.
        if (ereg("^HTTP",$data) &&
            !ereg("^HTTP/[0-9\.]+ 200 ", $data)) {
                $errstr = substr($data, 0, strpos($data, "\n")-1);
                error_log("HTTP error, got response: " . $errstr);
                $r = new XML_RPC_Response(0, $XML_RPC_err["http_error"],
                                          $XML_RPC_str["http_error"] . " (" .
                                          $errstr . ")");
                xml_parser_free($parser);
                return $r;
        }
        // gotta get rid of headers here


        if ((!$hdrfnd) && ($brpos = strpos($data,"\r\n\r\n"))) {
            $XML_RPC_xh[$parser]['ha'] = substr($data, 0, $brpos);
            $data = substr($data, $brpos + 4);
            $hdrfnd = 1;
        }

        // be tolerant of junk after methodResponse (e.g. javascript automatically inserted by free hosts)
        // thanks to Luca Mariano <luca.mariano@email.it>
        $data = substr($data, 0, strpos($data, "</methodResponse>") + 17);

        if (!xml_parse($parser, $data, sizeof($data))) {
            // thanks to Peter Kocks <peter.kocks@baygate.com>
            if ((xml_get_current_line_number($parser)) == 1) {
                $errstr = "XML error at line 1, check URL";
            } else {
                $errstr = sprintf("XML error: %s at line %d",
                                  xml_error_string(xml_get_error_code($parser)),
                                  xml_get_current_line_number($parser));
            }
            error_log($errstr);
            $r = new XML_RPC_Response(0, $XML_RPC_err["invalid_return"],
                                      $XML_RPC_str["invalid_return"]);
            xml_parser_free($parser);
            return $r;
        }
        xml_parser_free($parser);
        if ($this->debug) {
            print "<PRE>---EVALING---[" .
            strlen($XML_RPC_xh[$parser]['st']) . " chars]---\n" .
            htmlspecialchars($XML_RPC_xh[$parser]['st']) . ";\n---END---</PRE>";
        }
        if (strlen($XML_RPC_xh[$parser]['st']) == 0) {
            // then something odd has happened
            // and it's time to generate a client side error
            // indicating something odd went on
            $r = new XML_RPC_Response(0, $XML_RPC_err["invalid_return"],
                                         $XML_RPC_str["invalid_return"]);
        } else {
            eval('$v=' . $XML_RPC_xh[$parser]['st'] . '; $allOK=1;');
            if ($XML_RPC_xh[$parser]['isf']) {
                $f = $v->structmem("faultCode");
                $fs = $v->structmem("faultString");
                $r = new XML_RPC_Response($v, $f->scalarval(),
                                              $fs->scalarval());
            } else {
                $r = new XML_RPC_Response($v);
            }
        }
        $r->hdrs = split("\r?\n", $XML_RPC_xh[$parser]['ha'][1]);
        return $r;
    }

}


class XML_RPC_Value extends XML_RPC_Base
{
    var $me = array();
    var $mytype = 0;

    function XML_RPC_Value($val = -1, $type = "")
    {
        global $XML_RPC_Types;
        $this->me = array();
        $this->mytype = 0;
        if ($val != -1 || $type != "") {
            if ($type == "") {
                $type="string";
            }
            if ($XML_RPC_Types[$type] == 1) {
                $this->addScalar($val,$type);
            } elseif ($XML_RPC_Types[$type] == 2) {
                $this->addArray($val);
            } elseif ($XML_RPC_Types[$type] == 3) {
                $this->addStruct($val);
            }
        }
    }

    function addScalar($val, $type = "string")
    {
        global $XML_RPC_Types, $XML_RPC_Boolean;

        if ($this->mytype == 1) {
            $this->raiseError("Scalar can have only one value", XML_RPC_ERROR_INVALID_TYPE);
            return 0;
        }
        $typeof = $XML_RPC_Types[$type];
        if ($typeof != 1) {
            $this->raiseError("Not a scalar type (${typeof})", XML_RPC_ERROR_INVALID_TYPE);
            return 0;
        }

        if ($type == $XML_RPC_Boolean) {
            if (strcasecmp($val,"true") == 0 ||
                $val == 1 ||
                ($val == true &&
                    strcasecmp($val,"false"))) {

                $val = 1;
            } else {
                $val = 0;
            }
        }

        if ($this->mytype == 2) {
            // we're adding to an array here
            $ar = $this->me["array"];
            $ar[] = new XML_RPC_Value($val, $type);
            $this->me["array"] = $ar;
        } else {
            // a scalar, so set the value and remember we're scalar
            $this->me[$type] = $val;
            $this->mytype = $typeof;
        }
        return 1;
    }

    function addArray($vals)
    {
        global $XML_RPC_Types;
        if ($this->mytype != 0) {
            $this->raiseError(
                "Already initialized as a [" . $this->kindOf() . "]",
                XML_RPC_ERROR_ALREADY_INITIALIZED);
            return 0;
        }
        $this->mytype = $XML_RPC_Types["array"];
        $this->me["array"] = $vals;
        return 1;
    }

    function addStruct($vals)
    {
        global $XML_RPC_Types;
        if ($this->mytype != 0) {
            $this->raiseError(
                "Already initialized as a [" . $this->kindOf() . "]",
                XML_RPC_ERROR_ALREADY_INITIALIZED);
            return 0;
        }
        $this->mytype = $XML_RPC_Types["struct"];
        $this->me["struct"] = $vals;
        return 1;
    }

    function dump($ar)
    {
        reset($ar);
        while (list( $key, $val ) = each($ar)) {
            echo "$key => $val<br>";
            if ($key == 'array') {
                while ( list( $key2, $val2 ) = each( $val ) ) {
                    echo "-- $key2 => $val2<br>";
                }
            }
        }
    }

    function kindOf()
    {
        switch ($this->mytype) {
        case 3:
            return "struct";
            break;
        case 2:
            return "array";
            break;
        case 1:
            return "scalar";
            break;
        default:
            return "undef";
        }
    }

    function serializedata($typ, $val)
    {
        $rs = "";
        global $XML_RPC_Types, $XML_RPC_Base64, $XML_RPC_String, $XML_RPC_Boolean;
        switch ($XML_RPC_Types[$typ]) {
        case 3:
            // struct
            $rs .= "<struct>\n";
            reset($val);
            while(list($key2, $val2) = each($val)) {
                $rs .= "<member><name>${key2}</name>\n";
                $rs .= $this->serializeval($val2);
                $rs .= "</member>\n";
            }
            $rs .= "</struct>";
            break;
        case 2:
            // array
            $rs .= "<array>\n<data>\n";
            for($i = 0; $i < sizeof($val); $i++) {
                $rs .= $this->serializeval($val[$i]);
            }
            $rs .= "</data>\n</array>";
            break;
        case 1:
            switch ($typ) {
            case $XML_RPC_Base64:
                $rs .= "<${typ}>" . base64_encode($val) . "</${typ}>";
                break;
            case $XML_RPC_Boolean:
                $rs .= "<${typ}>" . ($val ? "1" : "0") . "</${typ}>";
                break;
            case $XML_RPC_String:
                $rs .= "<${typ}>" . htmlspecialchars($val). "</${typ}>";
                break;
            default:
                $rs .= "<${typ}>${val}</${typ}>";
            }
            break;
        default:
            break;
        }
        return $rs;
    }

    function serialize()
    {
        return $this->serializeval($this);
    }

    function serializeval($o)
    {
        $rs = "";
        $ar = $o->me;
        reset($ar);
        list($typ, $val) = each($ar);
        $rs .= "<value>";
        $rs .= $this->serializedata($typ, $val);
        $rs .= "</value>\n";
        return $rs;
    }

    function structmem($m)
    {
        $nv = $this->me["struct"][$m];
        return $nv;
    }

    function structreset()
    {
        reset($this->me["struct"]);
    }

    function structeach()
    {
        return each($this->me["struct"]);
    }

    function getval() {
        // UNSTABLE
        global $XML_RPC_BOOLEAN, $XML_RPC_Base64;

        reset($this->me);
        list($a,$b) = each($this->me);

        // contributed by I Sofer, 2001-03-24
        // add support for nested arrays to scalarval
        // i've created a new method here, so as to
        // preserve back compatibility

        if (is_array($b)) {
            foreach ($b as $id => $cont) {
                $b[$id] = $cont->scalarval();
            }
        }

        // add support for structures directly encoding php objects
        if (is_object($b)) {
            $t = get_object_vars($b);
            foreach ($t as $id => $cont) {
                $t[$id] = $cont->scalarval();
            }
            foreach ($t as $id => $cont) {
                eval('$b->'.$id.' = $cont;');
            }
        }

        // end contrib
        return $b;
    }

    function scalarval()
    {
        global $XML_RPC_Boolean, $XML_RPC_Base64;
        reset($this->me);
        list($a,$b) = each($this->me);
        return $b;
    }

    function scalartyp()
    {
        global $XML_RPC_I4, $XML_RPC_Int;
        reset($this->me);
        list($a,$b) = each($this->me);
        if ($a == $XML_RPC_I4) {
            $a = $XML_RPC_Int;
        }
        return $a;
    }

    function arraymem($m)
    {
        $nv = $this->me["array"][$m];
        return $nv;
    }

    function arraysize()
    {
        reset($this->me);
        list($a,$b) = each($this->me);
        return sizeof($b);
    }
}


/**
 * date helpers
 */
function XML_RPC_iso8601_encode($timet, $utc = 0) {
    // return an ISO8601 encoded string
    // really, timezones ought to be supported
    // but the XML-RPC spec says:
    //
    // "Don't assume a timezone. It should be specified by the server in its
    // documentation what assumptions it makes about timezones."
    //
    // these routines always assume localtime unless
    // $utc is set to 1, in which case UTC is assumed
    // and an adjustment for locale is made when encoding
    if (!$utc) {
        $t = strftime("%Y%m%dT%H:%M:%S", $timet);
    } else {
        if (function_exists("gmstrftime")) {
            // gmstrftime doesn't exist in some versions
            // of PHP
            $t = gmstrftime("%Y%m%dT%H:%M:%S", $timet);
        } else {
            $t = strftime("%Y%m%dT%H:%M:%S", $timet - date("Z"));
        }
    }

    return $t;
}

function XML_RPC_iso8601_decode($idate, $utc = 0) {
    // return a timet in the localtime, or UTC
    $t = 0;
    if (ereg("([0-9]{4})([0-9]{2})([0-9]{2})T([0-9]{2}):([0-9]{2}):([0-9]{2})", $idate, $regs)) {

        if ($utc) {
            $t = gmmktime($regs[4], $regs[5], $regs[6], $regs[2], $regs[3], $regs[1]);
        } else {
            $t = mktime($regs[4], $regs[5], $regs[6], $regs[2], $regs[3], $regs[1]);
        }
    }

    return $t;
}

/**
 * Takes a message in PHP XML_RPC object format and translates it into native PHP types.
 *
 * @author Dan Libby <dan@libby.com>
 **/
function XML_RPC_decode($XML_RPC_val) {
    $kind = $XML_RPC_val->kindOf();

   if ($kind == "scalar") {
      return $XML_RPC_val->scalarval();

   } elseif ($kind == "array") {
      $size = $XML_RPC_val->arraysize();
      $arr = array();

      for($i = 0; $i < $size; $i++) {
         $arr[] = XML_RPC_decode($XML_RPC_val->arraymem($i));
      }
      return $arr;

   } elseif ($kind == "struct") {
      $XML_RPC_val->structreset();
      $arr = array();

      while(list($key,$value) = $XML_RPC_val->structeach()) {
         $arr[$key] = XML_RPC_decode($value);
      }
      return $arr;
   }
}

/**
 * Takes native php types and encodes them into XML_RPC PHP object format.
 *
 * Feature creep -- could support more types via optional type argument.
 *
 * @author Dan Libby <dan@libby.com>
 **/
function XML_RPC_encode($php_val) {
   global $XML_RPC_Boolean;
   global $XML_RPC_Int;
   global $XML_RPC_Double;
   global $XML_RPC_String;
   global $XML_RPC_Array;
   global $XML_RPC_Struct;

   $type = gettype($php_val);
   $XML_RPC_val = new XML_RPC_Value;

   switch ($type) {
   case "array":
       $keys = array_keys($php_val);
       $count = count($php_val);
       $firstkey = ($count>0 ? $keys[0] : -1);
       $lastkey = ($count>0 ? $keys[$count - 1] : -1);
       if ($firstkey === 0 && is_int($lastkey) && ($lastkey + 1) == $count) {
           $is_continuous = true;
           $expected = 0;
           foreach ($keys as $actual) {
               if ($actual != $expected) {
                   $is_continuous = false;
                   break;
               }
               $expected++;
           }

           if ($is_continuous) {
               $arr = array();
               foreach ($php_val as $k => $v) {
                   $arr[$k] = XML_RPC_encode($v);
               }
               $XML_RPC_val->addArray($arr);
               break;
           }
       }
       // fall though if not numerical and continuous
   case "object":
       $arr = array();
       foreach ($php_val as $k => $v) {
           $arr[$k] = XML_RPC_encode($v);
       }
       $XML_RPC_val->addStruct($arr);
       break;

   case "integer":
       $XML_RPC_val->addScalar($php_val, $XML_RPC_Int);
       break;

   case "double":
       $XML_RPC_val->addScalar($php_val, $XML_RPC_Double);
       break;

   case "string":
   case "NULL":
       $XML_RPC_val->addScalar($php_val, $XML_RPC_String);
       break;

   // <G_Giunta_2001-02-29>
   // Add support for encoding/decoding of booleans, since they are supported in PHP
   case "boolean":
       $XML_RPC_val->addScalar($php_val, $XML_RPC_Boolean);
       break;
   // </G_Giunta_2001-02-29>

   case "unknown type":
   default:
       $XML_RPC_val = false;
       break;
   }
   return $XML_RPC_val;
}

?>
