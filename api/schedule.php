<?php
require_once('../conf.php');
require_once('../backend/Schedule.php');

$api_key = $_GET['api_key'];
if(!in_array($api_key, $CC_CONFIG["apiKey"]))
{
    header('HTTP/1.0 401 Unauthorized');
    print 'You are not allowed to access this resource.';
    exit;
}

PEAR::setErrorHandling(PEAR_ERROR_RETURN);

$from = $_GET["from"];
$to = $_GET["to"];
echo Schedule::ExportRangeAsJson($from, $to);
?>