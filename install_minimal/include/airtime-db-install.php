<?php
/**
 * This file is separated out so that it can be run separately for DEB package installation.
 * When installing a DEB package, Postgresql may not be installed yet and thus the database
 * cannot be created.  So this script is run after all DEB packages have been installed.
 */

set_include_path(__DIR__.'/../airtime_mvc/library' . PATH_SEPARATOR . get_include_path());

require_once(dirname(__FILE__).'/AirtimeIni.php');
require_once(dirname(__FILE__).'/AirtimeInstall.php');

require_once(AirtimeInstall::GetAirtimeSrcDir().'/application/configs/constants.php');
require_once(AirtimeInstall::GetAirtimeSrcDir().'/application/configs/conf.php');

echo PHP_EOL."*** Database Installation ***".PHP_EOL;

AirtimeInstall::CreateDatabaseUser();

$databaseExisted = AirtimeInstall::CreateDatabase();

AirtimeInstall::DbConnect(true);

AirtimeInstall::InstallPostgresScriptingLanguage();

if (isset($argv[1]) && $argv[1] == 'y') {
    AirtimeInstall::CreateDatabaseTables();
} else if ($databaseExisted) {
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

echo "* Setting Airtime version".PHP_EOL;
AirtimeInstall::SetAirtimeVersion(AIRTIME_VERSION);


if (AirtimeInstall::$databaseTablesCreated) {
    // set up some keys in DB
    AirtimeInstall::SetUniqueId();
    AirtimeInstall::SetImportTimestamp();

    $ini = parse_ini_file(__DIR__."/airtime-install.ini");

    $stor_dir = realpath($ini["storage_dir"])."/";
    echo "* Inserting stor directory location $stor_dir into music_dirs table".PHP_EOL;

    $sql = "INSERT INTO cc_music_dirs (directory, type) VALUES ('$stor_dir', 'stor')";
    $result = $CC_DBC->query($sql);
    if (PEAR::isError($result)) {
        echo "* Failed inserting {$stor_dir} in cc_music_dirs".PHP_EOL;
        echo "* Message {$result->getMessage()}".PHP_EOL;
        exit(1);
    }
}