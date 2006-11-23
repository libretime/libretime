--TEST--
File_Find bug #2773
--SKIPIF--
<?php 
include('./setup.php');
print $status; 
?>
--FILE--
<?php 
require_once('./setup.php');

$ff = new File_Find();
$result  = $ff->mapTreeMultiple('File_Find/dir2') ;
$result2 = File_Find::mapTreeMultiple('File_Find/dir2') ;

print_r($result);
print_r($result2);

?>
--GET--
--POST--
--EXPECT--
Array
(
    [0] => Array
        (
            [0] => 1.txt
        )

    [1] => Array
        (
            [0] => 1.txt
        )

    [2] => Array
        (
            [0] => 1.txt
        )

)
Array
(
    [0] => Array
        (
            [0] => 1.txt
        )

    [1] => Array
        (
            [0] => 1.txt
        )

    [2] => Array
        (
            [0] => 1.txt
        )

)
