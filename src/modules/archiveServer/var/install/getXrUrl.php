<?php
/**
 * @author $Author$
 * @version $Revision$
 */

/*------------------------------------------------------------------------------
 *  This script returns storage XMLRPC root URL
 *----------------------------------------------------------------------------*/

header("Content-type: text/plain");
require("../conf.php");
echo "http://{$CC_CONFIG['storageUrlHost']}:{$CC_CONFIG['storageUrlPort']}".
             "{$CC_CONFIG['storageUrlPath']}/{$CC_CONFIG['storageXMLRPC']}";
?>