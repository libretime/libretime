<?php

/* All functions other than start() should be marked as
 * private.
 */
class AirtimeDatabaseUpgrade{

    public static function start($p_dbValues){
        echo "* Updating Database".PHP_EOL;
        self::task0($p_dbValues);
        echo " * Complete".PHP_EOL;
    }

    private static function task0($p_dbValues){

        $username = $p_dbValues['database']['dbuser'];
        $password = $p_dbValues['database']['dbpass'];
        $host = $p_dbValues['database']['host'];
        $database = $p_dbValues['database']['dbname'];
        $dir = __DIR__;

        passthru("export PGPASSWORD=$password && psql -h $host -U $username -q -f $dir/data/upgrade.sql $database 2>&1 | grep -v \"will create implicit index\"");
    }
}
