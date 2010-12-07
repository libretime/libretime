<?php
require_once('../conf.php');
require_once('../backend/StoredFile.php');

$api_key = $_GET['api_key'];
if(!in_array($api_key, $CC_CONFIG["apiKey"]))
{
	header('HTTP/1.0 401 Unauthorized');
	print 'You are not allowed to access this resource.';
	exit;
}

PEAR::setErrorHandling(PEAR_ERROR_RETURN);

$filename = $_GET["file"];
$file_id = substr($filename, 0, strpos($filename, "."));
if (ctype_alnum($file_id) && strlen($file_id) == 32) {
  $media = StoredFile::RecallByGunid($file_id);
  if ($media != null && !PEAR::isError($media)) {
    //var_dump($media);
    $filepath = $media->getRealFilePath();
    if(!is_file($filepath))
    {
    	header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found");
    	//print 'Ressource in database, but not in storage. Sorry.';
    	exit;
    }

    // !! binary mode !!
    $fp = fopen($filepath, 'rb');

    header("Content-Type: audio/mpeg");
    header("Content-Length: " . filesize($filepath));

    fpassthru($fp);
  }
  else {
      header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found");
      exit;
  }
} else {
  header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found");
  exit;
}
exit;

?>