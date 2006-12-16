<?php
/**
 * @author $Author$
 * @version $Revision$
 */
header("Content-type: text/plain");
require_once('conf.php');
require_once("$storageServerPath/var/conf.php");

echo $CC_CONFIG['storageDir']."\n";
?>