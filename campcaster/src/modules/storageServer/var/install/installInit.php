<?php
if (!function_exists('pg_connect')) {
    trigger_error("PostgreSQL PHP extension required and not found.", E_USER_ERROR);
    exit(2);
}

require_once('DB.php');
require_once('File/Find.php');

function camp_db_table_exists($p_name)
{
    global $CC_DBC;
    $sql = "SELECT * FROM ".$p_name;
    $result = $CC_DBC->GetOne($sql);
    if (PEAR::isError($result)) {
        return false;
    }
    return true;
}

function camp_install_query($sql, $verbose = true)
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

function campcaster_db_connect($p_exitOnError = true) {
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

?>