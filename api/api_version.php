<?php
require_once('../conf.php');

$api_key = $_GET['api_key'];
if(!in_array($api_key, $CC_CONFIG["apiKey"]))
{
	header('HTTP/1.0 401 Unauthorized');
	print 'You are not allowed to access this resource.';
	exit;
}

print json_encode(array("version"=>CAMPCASTER_VERSION));
?>