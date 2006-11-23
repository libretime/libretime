--TEST--
File_Find::search() inside another object
--SKIPIF--
<?php 
include('./setup.php');
print $status; 
?>
--FILE--
<?php 
require_once('./setup.php');

class Foo {
 
   function search($pattern, $path, $type='php') {
       $retval = File_Find::search($pattern, $path, $type) ;
       return($retval);
   }

}

$f = new Foo();
$result[0] = $f->search('/txt/', 'File_Find/dir/', 'perl') ;
$result[1] = $f->search('/txt/', 'File_Find/dir', 'perl') ;
$result[2] = Foo::search('/txt/', 'File_Find/dir/', 'perl') ;

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
    [0] => File_Find/dir/1.txt
    [1] => File_Find/dir/2.txt
    [2] => File_Find/dir/txtdir/5.txt
    [3] => File_Find/dir/dir3/4.txt
    [4] => File_Find/dir/dir2/3.txt
)
