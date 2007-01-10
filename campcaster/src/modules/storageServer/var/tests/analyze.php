#!/usr/bin/php -q
<?php
header("Content-type: text/plain");
echo "TEST\n";

#$gunid = "5716b53127c3761f92fddde3412c7773";
$gunid = $argv[1];
echo "GUNID: $gunid\n";

require_once('../conf.php');
require_once('DB.php');
require_once('../GreenBox.php');

$rmd = new StoredFile($gunid, '../stor/'.substr($gunid, 0, 3));
$r = $rmd->analyzeFile();

echo "r=$r  (".gettype($r).")\n";
if (PEAR::isError($r)) {
    echo "ERR: ".$r->getMessage()."\n".$r->getUserInfo()."\n";
}
if (is_array($r)) {
    print_r($r);
}
echo"\n";
?>