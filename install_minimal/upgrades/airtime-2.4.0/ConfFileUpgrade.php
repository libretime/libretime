<?php

/* This class deals with any modifications to config files in /etc/airtime
 * as well as backups. All functions other than start() should be marked
 * as private. */
class AirtimeConfigFileUpgrade{

    public static function start(){
        echo "* Updating configFiles".PHP_EOL;
        self::UpdateIniFiles();
    }
    
    /**
    * After the configuration files have been copied to /etc/airtime,
    * this function will update them to values unique to this
    * particular installation.
    */
    private static function UpdateIniFiles()
    {
       $ini = parse_ini_file(UpgradeCommon::CONF_FILE_AIRTIME, true);

       $ini['rabbitmq']['vhost'] = '/airtime'; 
       $ini['rabbitmq']['user'] = 'airtime'; 
       $ini['rabbitmq']['password'] = UpgradeCommon::GenerateRandomString(); 

       UpgradeCommon::write_ini_file($ini, UpgradeCommon::CONF_FILE_AIRTIME, true);
    }
}
