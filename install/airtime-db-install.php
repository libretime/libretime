<?php

set_include_path(__DIR__.'/../airtime_mvc/library' . PATH_SEPARATOR . get_include_path());

require_once(dirname(__FILE__).'/include/AirtimeIni.php');
require_once(dirname(__FILE__).'/include/AirtimeInstall.php');

require_once(AirtimeInstall::GetAirtimeSrcDir().'/application/configs/conf.php');

//echo PHP_EOL."*** Database Installation ***".PHP_EOL;

AirtimeInstall::CreateDatabaseUser();

AirtimeInstall::CreateDatabase();

AirtimeInstall::DbConnect(true);

AirtimeInstall::InstallPostgresScriptingLanguage();

AirtimeInstall::CreateDatabaseTables();

AirtimeInstall::SetAirtimeVersion(AIRTIME_VERSION);

