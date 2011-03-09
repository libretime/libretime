<?php
if (!function_exists('pg_connect')) {
    trigger_error("PostgreSQL PHP extension required and not found.", E_USER_ERROR);
    exit(2);
}

require_once(dirname(__FILE__).'/../library/pear/DB.php');
require_once(dirname(__FILE__).'/../application/configs/conf.php');

function airtime_db_table_exists($p_name)
{
    global $CC_DBC;
    $sql = "SELECT * FROM ".$p_name;
    $result = $CC_DBC->GetOne($sql);
    if (PEAR::isError($result)) {
        return false;
    }
    return true;
}

function airtime_get_query($sql)
{
    global $CC_DBC;
    $result = $CC_DBC->GetAll($sql);
    if (PEAR::isError($result)) {
        return array();
    }
    return $result;
}

function airtime_install_query($sql, $verbose = true)
{
    global $CC_DBC;
    $result = $CC_DBC->query($sql);
    if (PEAR::isError($result)) {
        echo "Error! ".$result->getMessage()."\n";
        echo "   SQL statement was:\n";
        echo "   ".$sql."\n\n";
    } else {
        if ($verbose) {
            echo "done.\n";
        }
    }
}

function airtime_db_connect($p_exitOnError = true) {
    global $CC_DBC, $CC_CONFIG;
    $CC_DBC = DB::connect($CC_CONFIG['dsn'], TRUE);
    if (PEAR::isError($CC_DBC)) {
        echo $CC_DBC->getMessage().PHP_EOL;
        echo $CC_DBC->getUserInfo().PHP_EOL;
        echo "Database connection problem.".PHP_EOL;
        echo "Check if database '{$CC_CONFIG['dsn']['database']}' exists".
            " with corresponding permissions.".PHP_EOL;
        if ($p_exitOnError) {
            exit(1);
        }
    } else {
        echo "* Connected to database".PHP_EOL;
        $CC_DBC->setFetchMode(DB_FETCHMODE_ASSOC);
    }
}

function install_setDirPermissions($filePath) {
    global $CC_CONFIG;
    $success = chgrp($filePath, $CC_CONFIG["webServerUser"]);
    $fileperms=@fileperms($filePath);
    $fileperms = $fileperms | 0x0010; // group write bit
    $fileperms = $fileperms | 0x0400; // group sticky bit
    chmod($filePath, $fileperms);
}

function rand_string($len=20, $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789')
{
    $string = '';
    for ($i = 0; $i < $len; $i++)
    {
        $pos = mt_rand(0, strlen($chars)-1);
        $string .= $chars{$pos};
    }
    return $string;
}

function createAPIKey(){

    $api_key = rand_string();
    updateINIKeyValues(__DIR__.'/../build/airtime.conf', 'api_key', $api_key);
    updateINIKeyValues(__DIR__.'/../pypo/config.cfg', 'api_key', "'$api_key'");
}

function checkIfRoot(){
    // Need to check that we are superuser before running this.
    if(exec("whoami") != "root"){
      echo "Must be root user.\n";
      exit(1);
    }
}

function updateINIKeyValues($filename, $property, $value){
    $lines = file($filename);
    $n=count($lines);
    for ($i=0; $i<$n; $i++) {
        if (strlen($lines[$i]) > strlen($property))
            if ($property == substr($lines[$i], 0, strlen($property))){
                $lines[$i] = "$property = $value\n";
            }
    }

    $fp=fopen($filename, 'w');
    for($i=0; $i<$n; $i++){
        fwrite($fp, $lines[$i]);
    }
    fclose($fp);
}

function storageDirectorySetup($CC_CONFIG){
    global $CC_CONFIG, $CC_DBC;
    
    echo PHP_EOL."*** Directory Setup ***".PHP_EOL;
    foreach (array('baseFilesDir', 'storageDir') as $d) {
        if ( !file_exists($CC_CONFIG[$d]) ) {
            @mkdir($CC_CONFIG[$d], 02775, true);
            if (file_exists($CC_CONFIG[$d])) {
                $rp = realpath($CC_CONFIG[$d]);
                echo "* Directory $rp created".PHP_EOL;
            } else {
                echo "* Failed creating {$CC_CONFIG[$d]}".PHP_EOL;
                exit(1);
            }
        } elseif (is_writable($CC_CONFIG[$d])) {
            $rp = realpath($CC_CONFIG[$d]);
            echo "* Skipping directory already exists: $rp".PHP_EOL;
        } else {
            $rp = realpath($CC_CONFIG[$d]);
            echo "* WARNING: Directory already exists, but is not writable: $rp".PHP_EOL;
        }
        $CC_CONFIG[$d] = $rp;
    }
}

function createAirtimeDatabaseUser(){
    global $CC_CONFIG;
    // Create the database user
    $command = "sudo -u postgres psql postgres --command \"CREATE USER {$CC_CONFIG['dsn']['username']} "
      ." ENCRYPTED PASSWORD '{$CC_CONFIG['dsn']['password']}' LOGIN CREATEDB NOCREATEUSER;\" 2>/dev/null";
      
    @exec($command, $output, $results);
    if ($results == 0) {
      echo "* User {$CC_CONFIG['dsn']['username']} created.".PHP_EOL;
    } else {
      echo "* User {$CC_CONFIG['dsn']['username']} already exists.".PHP_EOL;
    }
}

function createAirtimeDatabase(){
    global $CC_CONFIG;
    
    $command = "sudo -u postgres createdb {$CC_CONFIG['dsn']['database']} --owner {$CC_CONFIG['dsn']['username']} 2> /dev/null";
    @exec($command, $output, $results);
    if ($results == 0) {
      echo "* Database '{$CC_CONFIG['dsn']['database']}' created.".PHP_EOL;
    } else {
      echo "* Database '{$CC_CONFIG['dsn']['database']}' already exists.".PHP_EOL;
    }
}

function installPostgresScriptingLanguage(){
    global $CC_DBC;
    // Install postgres scripting language
    $langIsInstalled = $CC_DBC->GetOne('SELECT COUNT(*) FROM pg_language WHERE lanname = \'plpgsql\'');
    if ($langIsInstalled == '0') {
      echo "* Installing Postgres scripting language".PHP_EOL;
      $sql = "CREATE LANGUAGE 'plpgsql'";
      airtime_install_query($sql, false);
    } else {
      echo "* Postgres scripting language already installed".PHP_EOL;
    }
}


function createAirtimeDatabaseTables(){
    // Put Propel sql files in Database
    $command = __DIR__."/../library/propel/generator/bin/propel-gen ../build/ insert-sql 2>propel-error.log";
    @exec($command, $output, $results);
}
