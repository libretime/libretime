<?php

/* Stuff not related to upgrading database +
 * config files goes here. */
class AirtimeMiscUpgrade{
    public static function start($p_ini){
        self::adjustAirtimeStorPermissions($p_ini);
        self::cleanupOldFiles();
    }
        
    public static function adjustAirtimeStorPermissions($p_ini){
        /* Make the read permission of Monit cfg files more strict */
        $webUser = $p_ini["general"]["web_server_user"];
        echo " * Updating /srv/airtime owner to root:$webUser".PHP_EOL;
        exec("chown -R root:$webUser /srv/airtime");
        echo " * Updating /srv/airtime permissions to 02755".PHP_EOL;
        exec("chmod -R 2775 /srv/airtime");
    }
    
    public static function cleanupOldFiles(){
        exec("rm -f /usr/bin/airtime-user");
        exec("rm -f /etc/init.d/airtime-show-recorder");
    }
}
