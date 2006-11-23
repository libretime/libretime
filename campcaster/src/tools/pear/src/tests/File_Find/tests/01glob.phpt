--TEST--
File_Find::glob()
--SKIPIF--
<?php 
include('./setup.php');
print $status; 
?>
--FILE--
<?php 
require_once('./setup.php');

$ff = new File_Find();
$result0  = &$ff->glob( '/.*txt/', $tmpdir.'/File_Find/dir/', 'perl' ) ;
$result1  = &$ff->glob( '/.*txt/', $tmpdir.'/File_Find/dir', 'perl' ) ;
$result2 = &File_Find::glob( '/.*txt/', $tmpdir.'/File_Find/dir/', 'perl' ) ;
$result3 = &File_Find::glob( '/.*txt/', '/nosuch/', 'perl' ) ;

print_r($result0);
print_r($result1);
print_r($result2);
print $result3->getMessage();

?>
--GET--
--POST--
--EXPECT--
Array
(
    [0] => 1.txt
    [1] => 2.txt
    [2] => txtdir
)
Array
(
    [0] => 1.txt
    [1] => 2.txt
    [2] => txtdir
)
Array
(
    [0] => 1.txt
    [1] => 2.txt
    [2] => txtdir
)
Cannot open directory

