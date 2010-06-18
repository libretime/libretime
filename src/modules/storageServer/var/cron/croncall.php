#!/usr/bin/php 
<?php
chdir(dirname(__FILE__));
$p =  unserialize($argv[1]);
require_once (dirname(__FILE__).'/'.$p['class'].'.php');
$cronjob = new $p['class']();
$ret = $cronjob->execute($p['params']);
exit(0);
?>