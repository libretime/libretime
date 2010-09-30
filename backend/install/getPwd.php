<?php
/*------------------------------------------------------------------------------
 *  This script returns real dir of php scipts for debugging purposes
 *----------------------------------------------------------------------------*/

header("Content-type: text/plain");
//var_dump($_SERVER);
if ( (isset($_SERVER["REMOTE_ADDR"]) && ($_SERVER["REMOTE_ADDR"] == "127.0.0.1"))
    || (isset($_SERVER["HTTP_HOST"]) && ($_SERVER["HTTP_HOST"] == "localhost"))
    || (isset($_SERVER["SHELL"])) ) {
    echo `pwd`;
}
?>