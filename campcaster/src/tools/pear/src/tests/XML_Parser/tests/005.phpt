--TEST--
XML Parser: mixing character encodings
--SKIPIF--
<?php if (!extension_loaded("xml")) echo 'skip'; ?>
--FILE--
<?php // -*- C++ -*-
//
// Test for: XML/Parser.php
// Parts tested: - mixing character encodings
//
// This is what we test:
// 1 UTF-8      -> ISO-8859-1
// 2 UTF-8      -> US-ASCII
// 3 ISO-8859-1 -> UTF-8
// 4 ISO-8859-1 -> US-ASCII
// 5 US-ASCII   -> UTF-8
// 6 US-ASCII   -> ISO-8859-1
//

require_once "../Parser.php";

class TestEncodings1 extends XML_Parser {
    var $output = '';

    function TestEncodings1($to, $from) {
        $this->XML_Parser($from, 'event', $to);
    }
    function startHandler($xp, $elem, $attribs) {
        $this->output .= "<$elem>";
    }
    function endHandler($xp, $elem) {
        $this->output .= "</$elem>";
    }
    function cdataHandler($xp, $data) {
        $this->output .= $data;
    }
    function test($data) {
        // $this->output = '';
        $this->parseString($data, true);
        return $this->output;
    }
}

$xml = "<?xml version='1.0' ?>";
$input = array(
    "UTF-8"      => "<a>abcÃ¦Ã¸Ã¥</a>",
    "ISO-8859-1" => "<a>abcæøå</a>",
    "US-ASCII"   => "<a>abcaoa</a>"
);

$encodings = array_keys($input);
foreach ($input as $srcenc => $string) {
    foreach ($encodings as $tgtenc) {
        if ($srcenc == $tgtenc) {
            continue;
        }
        print "Testing $srcenc -> $tgtenc: ";
        $p =& new TestEncodings1($tgtenc, $srcenc);
        $e = $p->test($input[$srcenc]);
        if (PEAR::isError($e)) {
            printf("OOPS: %s\n", $e->getMessage());
        } else {
            var_dump($e);
        }
    }
}

?>
--EXPECT--
Testing UTF-8 -> ISO-8859-1: string(13) "<A>abcæøå</A>"
Testing UTF-8 -> US-ASCII: string(13) "<A>abc???</A>"
Testing ISO-8859-1 -> UTF-8: string(16) "<A>abcÃ¦Ã¸Ã¥</A>"
Testing ISO-8859-1 -> US-ASCII: string(13) "<A>abc???</A>"
Testing US-ASCII -> UTF-8: string(13) "<A>abcaoa</A>"
Testing US-ASCII -> ISO-8859-1: string(13) "<A>abcaoa</A>"
