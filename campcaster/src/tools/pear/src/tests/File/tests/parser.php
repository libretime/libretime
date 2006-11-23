<?php
$path = ini_get('include_path');
ini_set('include_path', realpath('../') . ":$path");
require_once 'File/CSV.php';

/*/Example conf:
$conf = array(
    'fields' => 4,
    'sep'    => "\t",
    'quote'  => '"',
    'header' => false
);
//*/
ob_implicit_flush(true);
$argv = $_SERVER['argv'];
$file = $argv[1];
$write = (isset($argv[2])) ? $argv[2] : false;
PEAR::setErrorHandling(PEAR_ERROR_PRINT, "warning: %s\n");

$conf = File_CSV::discoverFormat($file);
while ($fields = File_CSV::read($file, $conf)) {
    if ($write) {
        File_CSV::write($write, $fields, $conf);
    }
    print_r($fields);
}

var_dump($conf);
echo "\n"

?>