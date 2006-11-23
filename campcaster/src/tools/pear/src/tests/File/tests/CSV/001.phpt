--TEST--
File_CSV Test Case 001: Fields count less than expected
--FILE--
<?php
// $Id: 001.phpt,v 1.2 2005/02/18 11:19:33 dufuz Exp $
/**
 * Test for:
 * - File_CSV::discoverFormat()
 * - File_CSV::read()
 */

require_once 'File/CSV.php';

$file = '001.csv';
$conf = File_CSV::discoverFormat($file);

print "Format:\n";
print_r($conf);
print "\n";

$data = array();
while ($res = File_CSV::read($file, $conf)) {
    $data[] = $res;
}

print "Data:\n";
print_r($data);
?>
--EXPECT--
Format:
Array
(
    [fields] => 4
    [sep] => ,
    [quote] => "
)

Data:
Array
(
    [0] => Array
        (
            [0] => Field 1-1
            [1] => Field 1-2
            [2] => Field 1-3
            [3] => Field 1-4
        )

    [1] => Array
        (
            [0] => Field 2-1
            [1] => Field 2-2
            [2] => Field 2-3
            [3] => 
        )

    [2] => Array
        (
            [0] => Field 3-1
            [1] => Field 3-2
            [2] => 
            [3] => 
        )

    [3] => Array
        (
            [0] => Field 4-1
            [1] => 
            [2] => 
            [3] => 
        )

)