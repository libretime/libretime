<?php
// This file generated by Propel 1.7.0 convert-conf target
// from XML runtime conf file /home/wcrs/airtime-dev-test/airtime_mvc/build/runtime-conf.xml
$conf = array (
  'datasources' => 
  array (
    'airtime' => 
    array (
      'adapter' => 'pgsql',
      'connection' => 
      array (
        'dsn' => 'pgsql:host=localhost;port=5432;dbname=airtime;user=airtime;password=airtime',
      ),
    ),
    'airtime_test' => 
    array (
      'adapter' => 'pgsql',
      'connection' => 
      array (
        'dsn' => 'pgsql:host=localhost;port=5432;dbname=airtime_test;user=airtime;password=airtime',
      ),
    ),
    'default' => 'airtime',
  ),
  'log' => 
  array (
    'type' => 'file',
    'name' => './propel.log',
    'ident' => 'propel',
    'level' => '7',
    'conf' => '',
  ),
  'generator_version' => '1.7.0',
);
$conf['classmap'] = include(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'classmap-airtime-conf.php');
return $conf;