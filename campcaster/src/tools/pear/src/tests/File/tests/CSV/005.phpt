--TEST--
File_CSV Test Case 005: Mac EOL
--FILE--
<?php
// $Id: 005.phpt,v 1.3 2005/02/19 12:23:28 dufuz Exp $
/**
 * Test for:
 * - File_CSV::discoverFormat()
 * - File_CSV::read()
 */

require_once 'File/CSV.php';

$file = '005.csv';
$conf = File_CSV::discoverFormat($file);

print "Format:\n";
print_r($conf);
print "\n";

$data = array();
while ($res = File_CSV::read($file, $conf)) {
    $data[] = $res;
}

function _dbgBuff($data)
{
    foreach ($data as $key => $row) {
        if (strpos($row, "\r") !== false) {
            $row = str_replace("\r", "_r_", $row);
        }
        if (strpos($row, "\n") !== false) {
            $str = str_replace("\n", "_n_", $row);
        }
        if (strpos($row, "\t") !== false) {
            $row = str_replace("\t", "_t_", $row);
        }
        $data[$key] = $row;
    }
    return $data;
}

$data = array_map('_dbgBuff', $data);

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
            [3] => I'm multiline_r_Field
        )

    [2] => Array
        (
            [0] => Field 3-1
            [1] => Field 3-2
            [2] => Field 3-3
            [3] => 
        )

)