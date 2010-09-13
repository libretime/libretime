<?php
/*------------------------------------------------------------------------------
 *  This (web-callable) script returns group running httpd
 *----------------------------------------------------------------------------*/

header("Content-type: text/plain");
$egid = posix_getegid();
$info = posix_getgrgid($egid);
if ( (isset($_SERVER["REMOTE_ADDR"]) && ($_SERVER["REMOTE_ADDR"] == "127.0.0.1"))
    || (isset($_SERVER["HTTP_HOST"]) && ($_SERVER["HTTP_HOST"] == "localhost"))
    || (isset($_SERVER["SHELL"])) ) {
    echo $info['name'];
}
?>