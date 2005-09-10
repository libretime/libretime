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
    Location : $URL$

------------------------------------------------------------------------------*/
// no remote execution
$arr = array_diff_assoc($_SERVER, $_ENV);
if(isset($arr["DOCUMENT_ROOT"]) && $arr["DOCUMENT_ROOT"] != ""){
    header("HTTP/1.1 400");
    header("Content-type: text/plain; charset=UTF-8");
    echo "400 Not executable\r\n";
    exit;
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
        "\nui:\n".$err->getUserInfo()."\n</pre>\n";
    exit(1);
}


PEAR::setErrorHandling(PEAR_ERROR_PRINT, "%s<hr>\n");
$dbc = DB::connect($config['dsn'], TRUE);
if(PEAR::isError($dbc)){
    echo "Database connection problem.\n";
    echo "Check if database '{$config['dsn']['database']}' exists".
        " with corresponding permissions.\n";
    echo "Database access is defined by 'dsn' values in conf.php.\n";
    exit(1);
}

echo "#StorageServer uninstall:\n";
$dbc->setFetchMode(DB_FETCHMODE_ASSOC);
$gb = &new GreenBox($dbc, $config);
$tr =& new Transport($dbc, $gb, $config);
$pr =& new Prefs($gb);
$dbc->setErrorHandling(PEAR_ERROR_RETURN);

echo "# Uninstall Prefs submodule";
$r = $pr->uninstall();
if(PEAR::isError($r)){ echo $r->getUserInfo()."\n"; exit; }
echo "\n";

echo "# Uninstall Transport submodule ...";
$r = $tr->uninstall();
if(PEAR::isError($r)){ echo $r->getUserInfo()."\n"; exit; }
echo "\n";

echo "# Uninstall StorageServer ...\n";
$gb->uninstall();
echo "#StorageServer uninstall: OK\n";

$dbc->disconnect();
?>
