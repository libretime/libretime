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
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/storageServer/var/tests/transTest.php,v $

------------------------------------------------------------------------------*/
header("Content-type: text/plain");
echo "\n# archiveServer transport test:\n";

require_once '../conf.php';
require_once 'DB.php';
require_once '../LocStor.php';

#PEAR::setErrorHandling(PEAR_ERROR_PRINT, "%s<hr>\n");
PEAR::setErrorHandling(PEAR_ERROR_RETURN);
$dbc = DB::connect($config['dsn'], TRUE);
$dbc->setFetchMode(DB_FETCHMODE_ASSOC);
$gb = &new LocStor(&$dbc, $config);

$gunid     = 'AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA';
$mediaFile = '../tests/ex1.mp3';
$mdataFile = '../tests/mdata1.xml';

echo"#  Login: ".($sessid = $gb->login('root', 'q'))."\n";

echo"#  Store: ";
$r = $gb->storeAudioClip($sessid, $gunid, $mediaFile, $mdataFile);
if(PEAR::isError($r)){ echo "ERROR: ".$r->getMessage()."\n"; exit(1); }
echo "$r\n";
                                     
echo"#  Upload: "; $r = $gb->uploadFile('', $gunid, $sessid);
if(PEAR::isError($r)){ echo "ERROR: ".$r->getMessage()."\n"; exit(1); }
echo join(', ',$r)."\n";

echo"#  Cron: "; list($r1, $r2) = $gb->cronJob();
if(PEAR::isError($r1)){ echo "ERROR: ".$r1->getMessage()."\n"; exit(1); }
if(PEAR::isError($r2)){ echo "ERROR: ".$r2->getMessage()."\n"; exit(1); }
echo "$r1, $r2\n";

echo"#  Delete: ";    $r = $gb->deleteAudioClip($sessid, $gunid);
if(PEAR::isError($r)){ echo "ERROR: ".$r->getMessage()."\n"; exit(1); }
echo "$r\n";

echo"#  Download: "; $r = $gb->downloadFile($gunid, $sessid);
if(PEAR::isError($r)){ echo "ERROR: ".$r->getMessage()."\n"; exit(1); }
echo join(', ',$r)."\n";

echo"#  Cron: "; $r = $gb->cronJob();
if(PEAR::isError($r1)){ echo "ERROR: ".$r1->getMessage()."\n"; exit(1); }
if(PEAR::isError($r2)){ echo "ERROR: ".$r2->getMessage()."\n"; exit(1); }
echo "$r1, $r2\n";

echo"#  logout: "; $r = $gb->logout($sessid);
if(PEAR::isError($r)){ echo "ERROR: ".$r->getMessage()."\n"; exit(1); }
echo "$r\n";

echo "#Transport test: OK.\n\n"
?>