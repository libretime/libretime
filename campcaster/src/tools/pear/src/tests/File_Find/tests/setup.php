<?php
error_reporting(E_ALL);

if (@include(dirname(__FILE__)."/../Find.php")) {
    $status = '';
} else if (@include('File/Find.php')) {
    $status = '';
} else {
    $status = 'skip - PEAR File_Find class is not available';
    return;
}

$tmpdir = (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') 
           ? getenv('TMP')
           : '/tmp';

chdir($tmpdir);

@mkdir('File_Find/');
@mkdir('File_Find/dir');

touch('File_Find/dir/1.txt');
touch('File_Find/dir/2.txt');

@mkdir('File_Find/dir/dir2');
touch('File_Find/dir/dir2/3.bak');
touch('File_Find/dir/dir2/3.txt');

@mkdir('File_Find/dir/dir3');
touch('File_Find/dir/dir3/4.bak');
touch('File_Find/dir/dir3/4.txt');

@mkdir('File_Find/dir/txtdir');
touch('File_Find/dir/txtdir/5.txt');

@mkdir('File_Find/dir2/');
@mkdir('File_Find/dir2/0');
touch('File_Find/dir2/0/1.txt');

@mkdir('File_Find/dir2/1');
touch('File_Find/dir2/1/1.txt');

@mkdir('File_Find/dir2/2');
touch('File_Find/dir2/2/1.txt');

?>
