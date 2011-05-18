<?php

set_include_path(__DIR__.'/../airtime_mvc/library' . PATH_SEPARATOR . get_include_path());

require_once(dirname(__FILE__).'/include/AirtimeIni.php');
require_once(dirname(__FILE__).'/include/AirtimeInstall.php');

require_once(AirtimeInstall::GetAirtimeSrcDir().'/application/configs/conf.php');

echo PHP_EOL."*** Database Installation ***".PHP_EOL;

AirtimeInstall::CreateDatabaseUser();

$databaseExisted = AirtimeInstall::CreateDatabase();

AirtimeInstall::DbConnect(true);

AirtimeInstall::InstallPostgresScriptingLanguage();

if ($databaseExisted){
    //Database already exists. Ask the user how they want to
    //proceed. Warn them that creating the database tables again
    //will cause them to lose their old ones.
    
    $userAnswer = "x";
    while (!in_array($userAnswer, array("y", "Y", "n", "N", ""))) {
        echo PHP_EOL."Database already exists. Do you want to delete all tables and recreate? (y/N)";
        $userAnswer = trim(fgets(STDIN));
    }
    if (in_array($userAnswer, array("y", "Y"))) {
        AirtimeInstall::CreateDatabaseTables();
    }
} else {
    //Database was just created, meaning the tables do not
    //exist. Let's create them.
    AirtimeInstall::CreateDatabaseTables();
}

AirtimeInstall::SetAirtimeVersion(AIRTIME_VERSION);

