<?
 header("Content-type: text/plain");
 require "../conf.php";
 echo "http://{$config['storageUrlHost']}:{$config['storageUrlPort']}".
             "{$config['storageUrlPath']}/{$config['storageXMLRPC']}";
?>