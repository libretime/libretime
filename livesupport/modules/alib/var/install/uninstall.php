<?
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
    Version  : $Revision: 1.2 $
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/alib/var/install/uninstall.php,v $

------------------------------------------------------------------------------*/
require_once '../example/conf.php';
require_once 'DB.php';
require_once '../alib.php';

function errCallback($err)
{
    if(assert_options(ASSERT_ACTIVE)==1) return;
    echo "ERROR:\n";
    echo "request: "; print_r($_REQUEST);
    echo "gm:\n".$err->getMessage()."\ndi:\n".$err->getDebugInfo()."\nui:\n".$err->getUserInfo()."\n</pre>\n";
    exit;
}


$dbc = DB::connect($config['dsn'], TRUE);
if(PEAR::isError($dbc)){
    echo "Database connection problem.\n";
    echo "Check if database '{$config['dsn']['database']}' exists with corresponding permissions.\n";
    echo "Database access is defined by 'dsn' values in conf.php.\n";
    exit;
}

#PEAR::setErrorHandling(PEAR_ERROR_PRINT, "%s<hr>\n");
$dbc->setErrorHandling(PEAR_ERROR_RETURN);
$dbc->setFetchMode(DB_FETCHMODE_ASSOC);
$alib =& new Alib($dbc, $config);

#    $dbc->setErrorHandling(PEAR_ERROR_RETURN);
echo "Trying to uninstall all ...\n";
$alib->uninstall();

$dbc->disconnect();
?>
