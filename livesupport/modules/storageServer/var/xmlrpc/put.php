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
    Version  : $Revision: 1.3 $
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/storageServer/var/xmlrpc/put.php,v $

------------------------------------------------------------------------------*/

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
 *  @see XR_LocStor
 */

require_once '../conf.php';
require_once 'DB.php';
require_once '../LocStor.php';

PEAR::setErrorHandling(PEAR_ERROR_RETURN);
$dbc = DB::connect($config['dsn'], TRUE);
$dbc->setFetchMode(DB_FETCHMODE_ASSOC);
$gb = &new GreenBox(&$dbc, $config);

function http_error($code, $err){
    header("HTTP/1.1 $code");
    header("Content-type: text/plain; charset=UTF-8");
    echo "$err\r\n";
    exit;
}

if(preg_match("|^[0-9a-f]{16}$|", $_REQUEST['token'])){
    $token = $_REQUEST['token'];
}else{
    http_error(400, "Error on token parameter. ({$_REQUEST['token']})");
}

$tc = $gb->bsCheckToken($token, 'put');
if(PEAR::isError($tc)){ http_error(500, $ex->getMessage()); }
if(!$tc){ http_error(403, "Token not valid."); }
#var_dump($tc); exit;

header("Content-type: text/plain");
#var_dump($_SERVER); var_dump($_REQUEST); exit;

#$destfile = $_SERVER['PATH_TRANSLATED'];
$destfile = "{$config['accessDir']}/{$token}";

/* PUT data comes in on the input stream */
$putdata = @fopen("php://input", "r") or
    http_error(500, "put.php: Can't read input");

/* Open a file for writing */
$fp = @fopen($destfile, "ab") or
    http_error(500, "put.php: Can't write to destination file (token=$token)");

/* Read the data 1 KB at a time and write to the file */
while ($data = fread($putdata, 1024)){
    fwrite($fp, $data);
}

/* Close the streams */
fclose($fp);
fclose($putdata);

header("HTTP/1.1 200");
?>