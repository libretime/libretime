<?php
/**
 *  \file simpleGet.php
 *  Returns stored media file identified by global unique ID.
 *
 *  simpleGet.php is remote callable script through HTTP GET method.
 *  Requires valid session ID with read permission for requested file.
 *
 *  This script accepts following HTTP GET parameters:
 *  <ul>
 *      <li>sessid : string, session ID</li>
 *      <li>id : string, global unique ID of requested file</li>
 *  </ul>
 *
 *  On success, returns HTTP return code 200 and requested file.
 *
 *  On errors, returns HTTP return code &gt;200
 *  The possible error codes are:
 *  <ul>
 *      <li> 400    -  Incorrect parameters passed to method</li>
 *      <li> 403    -  Access denied</li>
 *      <li> 404    -  File not found</li>
 *      <li> 500    -  Application error</li>
 *  </ul>
 * @author $Author$
 * @version $Revision$
 *
 */

require_once dirname(__FILE__).'/../conf.php';
require_once 'DB.php';
require_once dirname(__FILE__).'/../LocStor.php';

$dbc = DB::connect($config['dsn'], TRUE);
$dbc->setErrorHandling(PEAR_ERROR_RETURN);
$dbc->setFetchMode(DB_FETCHMODE_ASSOC);

$locStor = new LocStor($dbc, $config);

function http_error($code, $err){
    header("HTTP/1.1 $code");
    header("Content-type: text/plain; charset=UTF-8");
    echo "$err\r\n";
    exit;
}

// parameter checking:
if(preg_match("|^[0-9a-fA-F]{32}$|", $_REQUEST['sessid'])){
    $sessid = $_REQUEST['sessid'];
}else{
    http_error(400, "Error on sessid parameter. ({$_REQUEST['sessid']})");
}
if(preg_match("|^[0-9a-fA-F]{16}$|", $_REQUEST['id'])){
    $gunid = $_REQUEST['id'];
}else{
    http_error(400, "Error on id parameter. ({$_REQUEST['id']})");
}

// stored file recall:
$ac = StoredFile::recallByGunid($locStor, $gunid);
if($dbc->isError($ac)){
    switch($ac->getCode()){
        case GBERR_DENY:
            http_error(403, "403 ".$ac->getMessage());
        case GBERR_FILENEX:
        case GBERR_FOBJNEX:
            http_error(404, "404 File not found");
        default:
            http_error(500, "500 ".$ac->getMessage());
    }
}
$lid = $locStor->_idFromGunid($gunid);
if($dbc->isError($lid)){ http_error(500, $lid->getMessage()); }
if(($res = $locStor->_authorize('read', $lid, $sessid)) !== TRUE){
    http_error(403, "403 Access denied");
}
$ftype = $locStor->getObjType($lid);
if($dbc->isError($ftype)){ http_error(500, $ftype->getMessage()); }
switch($ftype){
    case"audioclip":
        $realFname  = $ac->_getRealRADFname();
        $mime = $ac->rmd->getMime();
        header("Content-type: $mime");
	header("Content-length: ".filesize($realFname));
        readfile($realFname);
        break;
    case"webstream":
        $url = $locStor->bsGetMetadataValue($lid, 'ls:url');
        if(PEAR::isError($url)){ http_error(500, $url->getMessage()); }
        $url = $url[0]['value'];
        $txt = "Location: $url";
        header($txt);
        // echo "$txt\n";
        break;
    case"playlist";
        // $md = $locStor->bsGetMetadata($ac->getId(), $sessid);
        $md = $locStor->getAudioClip($sessid, $gunid);
        // header("Content-type: text/xml");
        header("Content-type: application/smil");
        echo $md;
        break;
    default:
        // var_dump($ftype);
        http_error(500, "500 Unknown ftype ($ftype)");
}
?>