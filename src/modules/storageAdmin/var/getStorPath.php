<?php
/**
 * @author $Author$
 * @version $Revision$
 */
header("Content-type: text/plain");
require_once('conf.php');
require_once("$STORAGE_SERVER_PATH/var/conf.php");

echo $CC_CONFIG['storageDir']."\n";
?>