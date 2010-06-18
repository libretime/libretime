<?php
/*------------------------------------------------------------------------------
 *  This script returns real dir of php scipts for debugging purposes
 *----------------------------------------------------------------------------*/

header("Content-type: text/plain");
if ($_SERVER["REMOTE_ADDR"] == "127.0.0.1") {
    echo `pwd`;
}
?>