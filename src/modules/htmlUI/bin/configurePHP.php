#!/usr/bin/php
#   Author   : $Author$
#   Version  : $Revision$
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

$conf = new Config();
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
