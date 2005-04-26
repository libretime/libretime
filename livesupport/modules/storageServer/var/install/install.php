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
    Version  : $Revision: 1.17 $
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/storageServer/var/install/install.php,v $

------------------------------------------------------------------------------*/
// no remote execution
$arr = array_diff_assoc($_SERVER, $_ENV);
if(isset($arr["DOCUMENT_ROOT"]) && $arr["DOCUMENT_ROOT"] != ""){
    header("HTTP/1.1 400");
    header("Content-type: text/plain; charset=UTF-8");
    echo "400 Not executable\r\n";
    exit(1);
}

require_once '../conf.php';
require_once 'DB.php';
require_once '../GreenBox.php';
require_once "../Transport.php";
require_once "../Prefs.php";

function errCallback($err)
{
    if(assert_options(ASSERT_ACTIVE)==1) return;
    echo "ERROR:\n";
    echo "request: "; print_r($_REQUEST);
    echo "gm:\n".$err->getMessage()."\ndi:\n".$err->getDebugInfo().
        "\nui:\n".$err->getUserInfo()."\n";
    exit(1);
}

if(!function_exists('pg_connect')){
  trigger_error("PostgreSQL PHP extension required and not found.", E_USER_ERROR);
  exit(2);
}

PEAR::setErrorHandling(PEAR_ERROR_PRINT, "%s<hr>\n");
$dbc = DB::connect($config['dsn'], TRUE);
if(PEAR::isError($dbc)){
    echo "Database connection problem.\n";
    echo "Check if database '{$config['dsn']['database']}' exists".
        " with corresponding permissions.\n";
    echo "Database access is defined by 'dsn' values in var/conf.php.\n";
    echo $dbc->getMessage()."\n";
    exit(1);
}

$dbc->setFetchMode(DB_FETCHMODE_ASSOC);
$gb =& new GreenBox($dbc, $config);
$tr =& new Transport($dbc, $gb, $config);
$pr =& new Prefs($gb);

echo "#StorageServer step 2:\n# trying uninstall ...\n";
$dbc->setErrorHandling(PEAR_ERROR_RETURN);
$pr->uninstall();
$tr->uninstall();
$gb->uninstall();

echo "# Install ...\n";
#PEAR::setErrorHandling(PEAR_ERROR_PRINT, "%s<hr>\n");
PEAR::setErrorHandling(PEAR_ERROR_DIE, "%s<hr>\n");
$r = $gb->install();
if(PEAR::isError($r)){ echo $r->getUserInfo()."\n"; exit(1); }

echo "# Testing ...\n";
$r = $gb->test();
if(PEAR::isError($r)){ echo $r->getMessage()."\n"; exit(1); }
$log = $gb->test_log;
if($log) echo "# testlog:\n{$log}";

#echo "#  Reinstall + testdata insert ...\n";
#$gb->reinstall();
#$gb->sessid = $gb->login('root', $gb->config['tmpRootPass']);
#$gb->testData();
#$gb->logout($gb->sessid); unset($gb->sessid);

#echo "#  TREE DUMP:\n";
#echo $gb->dumpTree();

echo "# Delete test data ...\n";
$gb->deleteData();

if(!($fp = @fopen($config['storageDir']."/_writeTest", 'w'))){
    echo "\n<b>make {$config['storageDir']} dir webdaemon-writeable</b>".
        "\nand run install again\n\n";
    exit(1);
}else{
    fclose($fp); unlink($config['storageDir']."/_writeTest");
    echo "#storageServer main: OK\n";
}

echo "# Install Transport submodule ...";
$r = $tr->install();
if(PEAR::isError($r)){ echo $r->getUserInfo()."\n"; exit(1); }
echo "\n";

echo "# Install Prefs submodule ...";
$r = $pr->install();
if(PEAR::isError($r)){ echo $r->getUserInfo()."\n"; exit(1); }
echo "\n";

echo "#storageServer submodules: OK\n";
echo "\n";
$dbc->disconnect();
?>
