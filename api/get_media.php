<?php
require_once('../conf.php');
require_once('DB.php');

$api_key = $_GET['api_key'];
if(!in_array($api_key, $CC_CONFIG["apiKey"]))
{
	header('HTTP/1.0 401 Unauthorized');
	print 'You are not allowed to access this resource.';
	exit;
}

PEAR::setErrorHandling(PEAR_ERROR_RETURN);

$CC_DBC = DB::connect($CC_CONFIG['dsn'], TRUE);
if (PEAR::isError($CC_DBC)) {
	echo "ERROR: ".$CC_DBC->getMessage()." ".$CC_DBC->getUserInfo()."\n";
	exit(1);
}
$CC_DBC->setFetchMode(DB_FETCHMODE_ASSOC);

$file_id = $_GET[""]
if(!is_file($src))
{
	header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found");
	//print 'Ressource in database, but not in storage. Sorry.';
	exit;
}

// !! binary mode !!
$fp = fopen($src, 'rb');

header("Content-Type: audio/mpeg");
header("Content-Length: " . filesize($src));

fpassthru($fp);
exit;
?>