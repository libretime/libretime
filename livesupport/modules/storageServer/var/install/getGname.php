<?
 header("Content-type: text/plain");
 $gid  = posix_getgid();
 $egid = posix_getegid();
 $info = posix_getgrgid($egid);
 echo $info['name'];
?>