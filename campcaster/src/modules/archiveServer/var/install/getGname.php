<?php
/**
 * @author $Author$
 * @version  : $Revision$
 */

/*------------------------------------------------------------------------------
 *  This (web-callable) script returns group running httpd
 *----------------------------------------------------------------------------*/

 header("Content-type: text/plain");
 $egid = posix_getegid();
 $info = posix_getgrgid($egid);
 echo $info['name'];
?>