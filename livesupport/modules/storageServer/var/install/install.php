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
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/storageServer/var/install/install.php,v $

------------------------------------------------------------------------------*/
require_once '../conf.php';
require_once 'DB.php';
require_once '../GreenBox.php';

function errCallback($err)
{
    if(assert_options(ASSERT_ACTIVE)==1) return;
    echo "ERROR:\n";
    echo "request: "; print_r($_REQUEST);
    echo "gm:\n".$err->getMessage()."\ndi:\n".$err->getDebugInfo().
        "\nui:\n".$err->getUserInfo()."\n</pre>\n";
    exit(1);
}

if(!function_exists('domxml_open_file')){
  trigger_error("DOMXML PHP extension required and not found.", E_USER_ERROR);
  exit(2);
}

PEAR::setErrorHandling(PEAR_ERROR_PRINT, "%s<hr>\n");
$dbc = DB::connect($config['dsn'], TRUE);
if(PEAR::isError($dbc)){
    echo "Database connection problem.\n";
    echo "Check if database '{$config['dsn']['database']}' exists".
        " with corresponding permissions.\n";
    echo "Database access is defined by 'dsn' values in var/conf.php.\n";
    exit(1);
}

$dbc->setFetchMode(DB_FETCHMODE_ASSOC);
$gb = &new GreenBox(&$dbc, $config);

echo "\n# storageServer: Install ...\n";
$dbc->setErrorHandling(PEAR_ERROR_RETURN);
$gb->uninstall();
PEAR::setErrorHandling(PEAR_ERROR_PRINT, "%s<hr>\n");
$gb->install();

echo "#  Testing ...\n";
$gb->test();
$log = $gb->test_log;
echo " TESTS:\n{$log}";

#echo "#  Reinstall + testdata insert ...\n";
#$gb->reinstall();
#$gb->sessid = $gb->login('root', $gb->config['tmpRootPass']);
#$gb->testData();
#$gb->logout($gb->sessid); unset($gb->sessid);

#echo "#  TREE DUMP:\n";
#echo $gb->dumpTree();

echo "#  Delete test data ...\n";
$gb->deleteData();

if(!($fp = @fopen($config['storageDir']."/_writeTest", 'w'))){
    echo "\n<b>make {$config['storageDir']} dir webdaemon-writeable</b>".
        "\nand run install again\n\n";
    exit(1);
}else{
    fclose($fp); unlink($config['storageDir']."/_writeTest");
    echo "#storageServer install: OK\n\n";
}

echo "# Install Transport submodule\n";
require_once "../Transport.php";
$tr =& new Transport(&$dbc, $config);
$r = $tr->install();
echo "\n";

$dbc->disconnect();
?>
