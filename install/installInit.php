<?php
if (!function_exists('pg_connect')) {
    trigger_error("PostgreSQL PHP extension required and not found.", E_USER_ERROR);
    exit(2);
}

require_once(dirname(__FILE__).'/../library/pear/DB.php');

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
        echo $CC_DBC->getMessage()."\n";
        echo $CC_DBC->getUserInfo()."\n";
        echo "Database connection problem.\n";
        echo "Check if database '{$CC_CONFIG['dsn']['database']}' exists".
            " with corresponding permissions.\n";
        if ($p_exitOnError) {
            exit(1);
        }
    } else {
        echo " * Connected to database\n";
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

//    // Verify Smarty template dir permissions
//    $fileGroup = filegroup($CC_CONFIG["smartyTemplateCompiled"]);
//    $groupOwner = (function_exists('posix_getgrgid'))?@posix_getgrgid($fileGroup):'';
//    if (!empty($groupOwner) && ($groupOwner["name"] != $CC_CONFIG["webServerUser"])) {
//        echo "   * Error: Your directory permissions for {$filePath} are not set correctly.<br>\n";
//        echo "   * The group perms need to be set to the web server user, in this case '{$CC_CONFIG['webServerUser']}'.<br>\n";
//        echo "   * Currently the group is set to be '{$groupOwner['name']}'.<br>\n";
//        exit(1);
//    }
//    if (!($fileperms & 0x0400)) {
//        echo "   * Error: Sticky bit not set for {$filePath}.<br>\n";
//        exit(1);
//    }
}


