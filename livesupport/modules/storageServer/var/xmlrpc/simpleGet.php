<?php
/*------------------------------------------------------------------------------

    Copyright (c) 2004 Media Development Loan Fund
 
    This file is part of the LiveSupport project.
    http://livesupport.campware.org/
    To report bugs, send an e-mail to bugs@campware.org
 
    LiveSupport is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.
  
    LiveSupport is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.
 
    You should have received a copy of the GNU General Public License
    along with LiveSupport; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 
 
    Author   : $Author: tomas $
    Version  : $Revision: 1.6 $
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/storageServer/var/xmlrpc/simpleGet.php,v $

------------------------------------------------------------------------------*/

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
 *
 */

require_once 'DB.php';
require_once '../conf.php';
require_once '../LocStor.php';

PEAR::setErrorHandling(PEAR_ERROR_RETURN);
$dbc = DB::connect($config['dsn'], TRUE);
$dbc->setFetchMode(DB_FETCHMODE_ASSOC);

$locStor = &new LocStor(&$dbc, $config);

function http_error($code, $err){
    header("HTTP/1.1 $code");
    header("Content-type: text/plain; charset=UTF-8");
    echo "$err\r\n";
    exit;
}

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

$ex = $locStor->existsAudioClip($sessid, $gunid);
if(PEAR::isError($ex)){
    if($ex->getCode() == GBERR_DENY){ http_error(403, $ex->getMessage()); }
    else{ http_error(500, $ex->getMessage()); }
}
if(!$ex){ http_error(404, "File not found"); }
$ac =& StoredFile::recallByGunid(&$locStor, $gunid);
if(PEAR::isError($ac)){ http_error(500, $ac->getMessage()); }
$realFname  = $ac->_getRealRADFname();
$mime = $ac->rmd->getMime();
header("Content-type: $mime");
readfile($realFname);
?>
