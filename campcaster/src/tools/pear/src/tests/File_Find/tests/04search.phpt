--TEST--
File_Find::search()
--SKIPIF--
<?php 
include('./setup.php');
print $status; 
?>
--FILE--
<?php 
require_once('./setup.php');

$ff = new File_Find();
$result[0] = $ff->search('/txt/', 'File_Find/dir/', 'perl') ;
$result[1] = $ff->search('/txt/', 'File_Find/dir', 'perl') ;
$result[2] = File_Find::search('/3/', 'File_Find/dir/', 'perl') ;

if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
    foreach($result as $k => $r) {
        $result[$k] = str_replace("\\", '/', $result[$k]);
    }
}

print_r($result[0]);
print_r($result[1]);
print_r($result[2]);

?>
--GET--
--POST--
--EXPECT--
Array
(
    [0] => File_Find/dir/1.txt
    [1] => File_Find/dir/2.txt
    [2] => File_Find/dir/txtdir/5.txt
    [3] => File_Find/dir/dir3/4.txt
    [4] => File_Find/dir/dir2/3.txt
)
Array
(
    [0] => File_Find/dir/1.txt
    [1] => File_Find/dir/2.txt
    [2] => File_Find/dir/txtdir/5.txt
    [3] => File_Find/dir/dir3/4.txt
    [4] => File_Find/dir/dir2/3.txt
)
Array
(
    [0] => File_Find/dir/dir3/4.bak
    [1] => File_Find/dir/dir3/4.txt
    [2] => File_Find/dir/dir2/3.bak
    [3] => File_Find/dir/dir2/3.txt
)