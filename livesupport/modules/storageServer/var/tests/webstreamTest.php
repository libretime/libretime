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
 
 
    Author   : $Author$
    Version  : $Revision$
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/storageServer/var/tests/webstreamTest.php,v $

------------------------------------------------------------------------------*/
header("Content-type: text/plain");
echo "\n#StorageServer storeWebstream  test:\n";

require_once '../conf.php';
require_once 'DB.php';
require_once '../GreenBox.php';

#PEAR::setErrorHandling(PEAR_ERROR_PRINT, "%s<hr>\n");
PEAR::setErrorHandling(PEAR_ERROR_RETURN);
$dbc = DB::connect($config['dsn'], TRUE);
$dbc->setFetchMode(DB_FETCHMODE_ASSOC);
$gb = &new GreenBox($dbc, $config);

#$gunid = "123456789abcdee0";
$gunid = "";
#$mdataFileLP = '../tests/mdata1.xml';
$mdataFileLP = NULL;

echo"#  Login: ".($sessid = $gb->login('root', 'q'))."\n";
$parid = $gb->_getHomeDirId($sessid);

echo"#  storeWebstream: "; $r = $gb->storeWebstream(
    $parid, 'test stream', $mdataFileLP, $sessid, $gunid, 'http://localhost/y');
if(PEAR::isError($r)){ echo "ERROR: ".$r->getMessage()." ".$r->getUserInfo()."\n"; exit(1); }
echo ""; var_dump($r);
#$id = $gb->_idFromGunid($gunid);
$id = $r;

echo"#  getMdata: "; $r = $gb->getMdata($id, $sessid);
if(PEAR::isError($r)){ echo "ERROR: ".$r->getMessage()." ".$r->getUserInfo()."\n"; exit(1); }
echo "\n$r\n";

echo"#  deleteFile: "; $r = $gb->deleteFile($id, $sessid);
if(PEAR::isError($r)){ echo "ERROR: ".$r->getMessage()." ".$r->getUserInfo()."\n"; exit(1); }
echo "\n$r\n";

echo"#  logout: "; $r = $gb->logout($sessid);
if(PEAR::isError($r)){ echo "ERROR: ".$r->getMessage()."\n"; exit(1); }
echo "$r\n";

echo "#storeWebstream test: OK.\n\n"
?>