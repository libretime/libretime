#!/usr/bin/php
#-------------------------------------------------------------------------------
#   Copyright (c) 2004 Media Development Loan Fund
#
#   This file is part of the LiveSupport project.
#   http://livesupport.campware.org/
#   To report bugs, send an e-mail to bugs@campware.org
#
#   LiveSupport is free software; you can redistribute it and/or modify
#   it under the terms of the GNU General Public License as published by
#   the Free Software Foundation; either version 2 of the License, or
#   (at your option) any later version.
#
#   LiveSupport is distributed in the hope that it will be useful,
#   but WITHOUT ANY WARRANTY; without even the implied warranty of
#   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#   GNU General Public License for more details.
#
#   You should have received a copy of the GNU General Public License
#   along with LiveSupport; if not, write to the Free Software
#   Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
#
#
#   Author   : $Author: sebastian $
#   Version  : $Revision: 1.2 $
#   Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/htmlUI/bin/configurePHP.php,v $
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
#  This script configures php for the htmlUI
#
#  Invoke as:
#  ./bin/configurePHP.php
#-------------------------------------------------------------------------------
<?php
$path		= '/etc/php4/apache2/php.ini';
$changes 	= array(
				'file_uploads'			=> 'On',
				'upload_max_filesize'	=> '100M',
               'post_max_size'			=> '100M',
             );


require_once 'Config.php';
require_once 'Config/Container.php';

$conf = &new Config();
$root = &$conf->parseConfig($path, 'IniFile');
if (PEAR::isError($root)) {
	die($root->getMessage());
} else {
	echo "Read php settings from $path\n";
}


$php  = &$root->getItem('section', 'PHP');
if (!is_object($php)) {
	die("$path seems not to be valid PHP ini file\n");
}
if (PEAR::isError($php)) {
   die($php->getMessage());
}


foreach ($changes as $key=>$val) {
	$item = &$php->getItem(NULL,  $key);
   if (PEAR::isError($item)) {
    	die ("Error on getting $key setting\n");
   }
	$item->setContent($val);
   if (PEAR::isError($item)) {
   	echo "Error on setting $key to $val\n";
   	die($item->getMessage());
   } else {
   	echo "Changed $key to $val\n";
   }
}

if (copy($path, $path.'.bak')) {
   echo "Backup $path to $path.bak\n";
} else {
   die ("Could not create backup of $path\n");
}

die("Changing php.ini temporarly disabled");


$status = $conf->writeConfig();
if (PEAR::isError($status)) {
	echo "Error writing config to $path\n";
   die($status->getMessage());
} else {
	echo "Wrote php setting to $path\n";
}
?>
