<?php
/**
 * This file is separated out so that it can be run separately for DEB package installation.
 * When installing a DEB package, Postgresql may not be installed yet and thus the database
 * cannot be created.  So this script is run after all DEB packages have been installed.
 */

require_once(__DIR__.'/AirtimeIni.php');
require_once(__DIR__.'/AirtimeInstall.php');
require_once(__DIR__.'/airtime-constants.php');

require_once(AirtimeInstall::GetAirtimeSrcDir().'/application/configs/conf.php');


//Propel classes.
set_include_path(AirtimeInstall::GetAirtimeSrcDir().'/application/models' . PATH_SEPARATOR . get_include_path());

$CC_CONFIG = Config::getConfig();
require_once 'vendor/propel/propel1/runtime/lib/Propel.php';
Propel::init(AirtimeInstall::GetAirtimeSrcDir()."/application/configs/airtime-conf-production.php");

//use this class to set new values in the cache as well.
require_once(AirtimeInstall::GetAirtimeSrcDir().'/application/common/Database.php');
require_once(AirtimeInstall::GetAirtimeSrcDir().'/application/models/Preference.php');

echo PHP_EOL."* Database Installation".PHP_EOL;

AirtimeInstall::CreateDatabaseUser();

$databaseExisted = AirtimeInstall::CreateDatabase();

AirtimeInstall::DbConnect(true);

AirtimeInstall::InstallPostgresScriptingLanguage();

//Load Database parameters
$dbuser = $CC_CONFIG['dsn']['username'];
$dbpasswd = $CC_CONFIG['dsn']['password'];
$dbname = $CC_CONFIG['dsn']['database'];
$dbhost = $CC_CONFIG['dsn']['hostspec'];

if (isset($argv[1]) && $argv[1] == 'y') {
    AirtimeInstall::CreateDatabaseTables($dbuser, $dbpasswd, $dbname, $dbhost);
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
        AirtimeInstall::CreateDatabaseTables($dbuser, $dbpasswd, $dbname, $dbhost);
    }
} else {
    //Database was just created, meaning the tables do not
    //exist. Let's create them.
    AirtimeInstall::CreateDatabaseTables($dbuser, $dbpasswd, $dbname, $dbhost);
}

echo " * Setting Airtime version".PHP_EOL;
AirtimeInstall::SetAirtimeVersion(AIRTIME_VERSION);


if (AirtimeInstall::$databaseTablesCreated) {
    // set up some keys in DB
    AirtimeInstall::SetUniqueId();

    $ini = parse_ini_file(__DIR__."/airtime-install.ini");

    $stor_dir = realpath($ini["storage_dir"])."/";
    echo " * Inserting stor directory location $stor_dir into music_dirs table".PHP_EOL;
    $con = Propel::getConnection();
    $sql = "INSERT INTO cc_music_dirs (directory, type) VALUES ('$stor_dir', 'stor')";
    try {
        $con->exec($sql);
    } catch (Exception $e) {
        echo "  * Failed inserting {$stor_dir} in cc_music_dirs".PHP_EOL;
        echo "  * Message {$e->getMessage()}".PHP_EOL;
        exit(1);
    }
}
