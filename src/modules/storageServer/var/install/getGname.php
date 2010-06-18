<?php
/*------------------------------------------------------------------------------
 *  This (web-callable) script returns group running httpd
 *----------------------------------------------------------------------------*/

header("Content-type: text/plain");
$egid = posix_getegid();
$info = posix_getgrgid($egid);
if ($_SERVER["REMOTE_ADDR"] == "127.0.0.1") {
    echo $info['name'];
}
?>