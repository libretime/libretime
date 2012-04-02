<?php

/* This class deals with any modifications to config files in /etc/airtime
 * as well as backups. All functions other than start() should be marked
 * as private. */
class AirtimeConfigFileUpgrade{

    public static function start(){
        echo "* Updating configFiles".PHP_EOL;
        self::task0();
    }
    
    private static function task0(){
        UpgradeCommon::upgradeConfigFiles();
    }
}
