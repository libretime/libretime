<?php
/**
 *  \file put.php
 *  Store PUT data as temporary file.
 *
 *  put.php is remote callable script through HTTP PUT method.
 *  Requires token returned by appropriate storageServer XMLRPC call.
 *  Appropriate closing XMLRPC call should follow.
 *
 *  This script accepts following HTTP GET parameter:
 *  <ul>
 *      <li>token : string, put token returned by appropriate
 *                      XMLRPC call</li>
 *  </ul>
 *
 *  On success, returns HTTP return code 200.
 *
 *  On errors, returns HTTP return code &gt;200
 *  The possible error codes are:
 *  <ul>
 *      <li> 400    -  Incorrect parameters passed to method</li>
 *      <li> 403    -  Access denied</li>
 *      <li> 500    -  Application error</li>
 *  </ul>
 *
 * @see XR_LocStor
 * @author $Author$
 * @version $Revision$
 * @package Campcaster
 * @subpackage ArchiveServer
 */

define('USE_FLOCK', TRUE);

require_once(dirname(__FILE__).'/../conf.php');
require_once('DB.php');
require_once(dirname(__FILE__).'/../Archive.php');

PEAR::setErrorHandling(PEAR_ERROR_RETURN);
$CC_DBC = DB::connect($CC_CONFIG['dsn'], TRUE);
$CC_DBC->setFetchMode(DB_FETCHMODE_ASSOC);
$gb = new Archive($CC_DBC, $CC_CONFIG);

function http_error($code, $err)
{
    header("HTTP/1.1 $code");
    header("Content-type: text/plain; charset=UTF-8");
    echo "$err\r\n";
    flush();
    exit;
}

if (preg_match("|^[0-9a-fA-F]{16}$|", $_REQUEST['token'])) {
    $token = $_REQUEST['token'];
} else {
    http_error(400, "Error on token parameter. ({$_REQUEST['token']})");
}

$tc = BasicStor::bsCheckToken($token, 'put');
if (PEAR::isError($tc)) {
    http_error(500, $ex->getMessage());
}
if (!$tc) {
    http_error(403, "put.php: Token not valid ($token).");
}

header("Content-type: text/plain");

$destfile = "{$CC_CONFIG['accessDir']}/{$token}";

/* PUT data comes in on the input stream */
$putdata = @fopen("php://input", "r") or
    http_error(500, "put.php: Can't read input");

/* Open a file for writing */
$fp = @fopen($destfile, "ab") or
    http_error(500, "put.php: Can't write to destination file (token=$token)");

if ( USE_FLOCK ) {
    // lock the file
    $lockres = flock($fp,LOCK_EX+LOCK_NB);
    if ($lockres !== TRUE) {
        http_error(409, "put.php: file locked (token=$token)");
    }
}

/* Read the data 1 KB at a time and write to the file */
while ($data = fread($putdata, 1024)){
    fwrite($fp, $data);
}

if ( USE_FLOCK ) {
    // unlock the file
    flock($fp,LOCK_UN);
}

/* Close the streams */
fclose($fp);
fclose($putdata);

header("HTTP/1.1 200");
?>