<?php
// /* vim: set expandtab tabstop=4 shiftwidth=4: */
// by Edd Dumbill (C) 1999,2000
// <edd@usefulinc.com>

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

// Adapted to PEAR standards by Stig S�her Bakken <stig@php.net>
// /* $Id: Server.php,v 1.1 2004/12/15 17:25:50 tomas Exp $ */

require_once "XML/RPC.php";

// listMethods: either a string, or nothing
$GLOBALS['XML_RPC_Server_listMethods_sig'] =
    array(array($GLOBALS['XML_RPC_Array'], $GLOBALS['XML_RPC_String']),
          array($GLOBALS['XML_RPC_Array']));
$GLOBALS['XML_RPC_Server_listMethods_doc'] =
    'This method lists all the methods that the XML-RPC server knows how to dispatch';

function XML_RPC_Server_listMethods($server, $m)
{
    global $XML_RPC_err, $XML_RPC_str, $XML_RPC_Server_dmap;
    $v = new XML_RPC_Value();
    $dmap = $server->dmap;
    $outAr = array();
    for (reset($dmap); list($key, $val) = each($dmap); ) {
        $outAr[] = new XML_RPC_Value($key, "string");
    }
    $dmap = $XML_RPC_Server_dmap;
    for (reset($dmap); list($key, $val) = each($dmap); ) {
        $outAr[] = new XML_RPC_Value($key, "string");
    }
    $v->addArray($outAr);
    return new XML_RPC_Response($v);
}

$GLOBALS['XML_RPC_Server_methodSignature_sig'] =
    array(array($GLOBALS['XML_RPC_Array'], $GLOBALS['XML_RPC_String']));
$GLOBALS['XML_RPC_Server_methodSignature_doc'] =
    'Returns an array of known signatures (an array of arrays) for the method name passed. If no signatures are known, returns a none-array (test for type != array to detect missing signature)';

function XML_RPC_Server_methodSignature($server, $m)
{
    global $XML_RPC_err, $XML_RPC_str, $XML_RPC_Server_dmap;

    $methName = $m->getParam(0);
    $methName = $methName->scalarval();
    if (ereg("^system\.", $methName)) {
        $dmap = $XML_RPC_Server_dmap;
        $sysCall = 1;
    } else {
        $dmap = $server->dmap;
        $sysCall = 0;
    }
    //  print "<!-- ${methName} -->\n";
    if (isset($dmap[$methName])) {
        if ($dmap[$methName]["signature"]) {
            $sigs = array();
            $thesigs = $dmap[$methName]["signature"];
            for ($i = 0; $i < sizeof($thesigs); $i++) {
                $cursig = array();
                $inSig = $thesigs[$i];
                for ($j = 0; $j < sizeof($inSig); $j++) {
                    $cursig[] = new XML_RPC_Value($inSig[$j], "string");
                }
                $sigs[] = new XML_RPC_Value($cursig, "array");
            }
            $r = new XML_RPC_Response(new XML_RPC_Value($sigs, "array"));
        } else {
            $r = new XML_RPC_Response(new XML_RPC_Value("undef", "string"));
        }
    } else {
        $r = new XML_RPC_Response(0, $XML_RPC_err["introspect_unknown"],
                                     $XML_RPC_str["introspect_unknown"]);
    }
    return $r;
}

$GLOBALS['XML_RPC_Server_methodHelp_sig'] =
    array(array($GLOBALS['XML_RPC_String'], $GLOBALS['XML_RPC_String']));
$GLOBALS['XML_RPC_Server_methodHelp_doc'] =
    'Returns help text if defined for the method passed, otherwise returns an empty string';

function XML_RPC_Server_methodHelp($server, $m)
{
    global $XML_RPC_err, $XML_RPC_str, $XML_RPC_Server_dmap;

    $methName = $m->getParam(0);
    $methName = $methName->scalarval();
    if (ereg("^system\.", $methName)) {
        $dmap = $XML_RPC_Server_dmap;
        $sysCall = 1;
    } else {
        $dmap = $server->dmap;
        $sysCall = 0;
    }
    //  print "<!-- ${methName} -->\n";
    if (isset($dmap[$methName])) {
        if ($dmap[$methName]["docstring"]) {
            $r = new XML_RPC_Response(new XML_RPC_Value($dmap[$methName]["docstring"]), "string");
        } else {
            $r = new XML_RPC_Response(new XML_RPC_Value("", "string"));
        }
    } else {
        $r = new XML_RPC_Response(0, $XML_RPC_err["introspect_unknown"],
                                     $XML_RPC_str["introspect_unknown"]);
    }
    return $r;
}

$GLOBALS['XML_RPC_Server_dmap'] = array(
    "system.listMethods" =>
        array("function"  => "XML_RPC_Server_listMethods",
              "signature" => $GLOBALS['XML_RPC_Server_listMethods_sig'],
              "docstring" => $GLOBALS['XML_RPC_Server_listMethods_doc']),

    "system.methodHelp" =>
        array("function"  => "XML_RPC_Server_methodHelp",
              "signature" => $GLOBALS['XML_RPC_Server_methodHelp_sig'],
              "docstring" => $GLOBALS['XML_RPC_Server_methodHelp_doc']),

    "system.methodSignature" =>
        array("function"  => "XML_RPC_Server_methodSignature",
              "signature" => $GLOBALS['XML_RPC_Server_methodSignature_sig'],
              "docstring" => $GLOBALS['XML_RPC_Server_methodSignature_doc'])
);

$GLOBALS['XML_RPC_Server_debuginfo'] = "";

function XML_RPC_Server_debugmsg($m)
{
    global $XML_RPC_Server_debuginfo;
    $XML_RPC_Server_debuginfo = $XML_RPC_Server_debuginfo . $m . "\n";
}

class XML_RPC_Server
{
    var $dmap = array();

    function XML_RPC_Server($dispMap, $serviceNow = 1)
    {
        global $HTTP_RAW_POST_DATA;
        // dispMap is a despatch array of methods
        // mapped to function names and signatures
        // if a method
        // doesn't appear in the map then an unknown
        // method error is generated
        $this->dmap = $dispMap;
        if ($serviceNow) {
            $this->service();
        }
    }

    function serializeDebug()
    {
        global $XML_RPC_Server_debuginfo;
        if ($XML_RPC_Server_debuginfo != "")
            return "<!-- DEBUG INFO:\n\n" . $XML_RPC_Server_debuginfo . "\n-->\n";
        else
            return "";
    }

    function service()
    {
        $r = $this->parseRequest();
        $payload = "<?xml version=\"1.0\"?>\n" .
            $this->serializeDebug() .
            $r->serialize();
        header('Content-Length: ' . strlen($payload));
        header('Content-Type: text/xml');
        print $payload;
    }

    function verifySignature($in, $sig)
    {
        for ($i = 0; $i < sizeof($sig); $i++) {
            // check each possible signature in turn
            $cursig = $sig[$i];
            if (sizeof($cursig) == $in->getNumParams() + 1) {
                $itsOK = 1;
                for ($n = 0; $n < $in->getNumParams(); $n++) {
                    $p = $in->getParam($n);
                    // print "<!-- $p -->\n";
                    if ($p->kindOf() == "scalar") {
                        $pt = $p->scalartyp();
                    } else {
                        $pt = $p->kindOf();
                    }
                    // $n+1 as first type of sig is return type
                    if ($pt != $cursig[$n+1]) {
                        $itsOK = 0;
                        $pno = $n+1;
                        $wanted = $cursig[$n+1];
                        $got = $pt;
                        break;
                    }
                }
                if ($itsOK)
                    return array(1);
            }
        }
        return array(0, "Wanted ${wanted}, got ${got} at param ${pno})");
    }

    function parseRequest($data = "")
    {
        global $XML_RPC_xh,$HTTP_RAW_POST_DATA;
        global $XML_RPC_err, $XML_RPC_str, $XML_RPC_errxml,
            $XML_RPC_defencoding, $XML_RPC_Server_dmap;

        if ($data == "") {
            $data = $HTTP_RAW_POST_DATA;
        }
        $parser = xml_parser_create($XML_RPC_defencoding);

        $XML_RPC_xh[$parser] = array();
        $XML_RPC_xh[$parser]['st'] = "";
        $XML_RPC_xh[$parser]['cm'] = 0;
        $XML_RPC_xh[$parser]['isf'] = 0;
        $XML_RPC_xh[$parser]['params'] = array();
        $XML_RPC_xh[$parser]['method'] = "";

        $plist = '';

        // decompose incoming XML into request structure

        xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, true);
        xml_set_element_handler($parser, "XML_RPC_se", "XML_RPC_ee");
        xml_set_character_data_handler($parser, "XML_RPC_cd");
        xml_set_default_handler($parser, "XML_RPC_dh");
        if (!xml_parse($parser, $data, 1)) {
            // return XML error as a faultCode
            $r = new XML_RPC_Response(0,
                                      $XML_RPC_errxml+xml_get_error_code($parser),
                                      sprintf("XML error: %s at line %d",
                                              xml_error_string(xml_get_error_code($parser)),
                                              xml_get_current_line_number($parser)));
            xml_parser_free($parser);
        } else {
            xml_parser_free($parser);
            $m = new XML_RPC_Message($XML_RPC_xh[$parser]['method']);
            // now add parameters in
            for ($i = 0; $i < sizeof($XML_RPC_xh[$parser]['params']); $i++) {
                // print "<!-- " . $XML_RPC_xh[$parser]['params'][$i]. "-->\n";
                $plist .= "$i - " . $XML_RPC_xh[$parser]['params'][$i] . " \n";
                eval('$m->addParam(' . $XML_RPC_xh[$parser]['params'][$i] . ");");
            }
            XML_RPC_Server_debugmsg($plist);
            // now to deal with the method
            $methName = $XML_RPC_xh[$parser]['method'];
            if (ereg("^system\.", $methName)) {
                $dmap = $XML_RPC_Server_dmap;
                $sysCall = 1;
            } else {
                $dmap = $this->dmap;
                $sysCall = 0;
            }
            if (isset($dmap[$methName]['function'])) {
                // dispatch if exists
                if (isset($dmap[$methName]['signature'])) {
                    $sr = $this->verifySignature($m,
                                                 $dmap[$methName]['signature'] );
                }
                if ( (!isset($dmap[$methName]['signature'])) || $sr[0]) {
                    // if no signature or correct signature
                    if ($sysCall) {
                        eval('$r=' . $dmap[$methName]['function'] . '($this, $m);');
                    } else {
                        eval('$r=' . $dmap[$methName]['function'] . '($m);');
                    }
                } else {
                    $r = new XML_RPC_Response(0, $XML_RPC_err["incorrect_params"],
                                                 $XML_RPC_str["incorrect_params"] .
                                                 ": " . $sr[1]);
                }
            } else {
                // else prepare error response
                $r = new XML_RPC_Response(0, $XML_RPC_err["unknown_method"],
                                             $XML_RPC_str["unknown_method"]);
            }
        }
        return $r;
    }

  function echoInput() {
        global $HTTP_RAW_POST_DATA;

        // a debugging routine: just echos back the input
        // packet as a string value

        $r = new XML_RPC_Response;
        $r->xv = new XML_RPC_Value("'Aha said I: '" . $HTTP_RAW_POST_DATA, "string");
        print $r->serialize();
  }
}

?>
