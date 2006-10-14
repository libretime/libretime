<?php
/*------------------------------------------------------------------------------
 *  This script returns storage root URL
 *----------------------------------------------------------------------------*/

 header("Content-type: text/plain");
 require "../conf.php";
 echo "http://{$config['storageUrlHost']}:{$config['storageUrlPort']}".
             "{$config['storageUrlPath']}";
?>