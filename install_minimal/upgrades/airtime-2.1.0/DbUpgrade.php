<?php

/* All functions other than start() should be marked as
 * private.
 */
class AirtimeDatabaseUpgrade{

    public static function start(){
        echo "* Updating Database".PHP_EOL;
        self::task0();
    }

    private static function task0(){
        UpgradeCommon::MigrateTablesToVersion(__DIR__, '20120403143635');
        $sql = "INSERT INTO cc_pref(\"keystr\", \"valstr\") VALUES('scheduled_play_switch', 'on')";
        UpgradeCommon::nonSelectQueryDb($sql);
    }

}
