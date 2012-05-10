<?php

/* Stuff not related to upgrading database +
 * config files goes here. */
class AirtimeMiscUpgrade{
    public static function start($p_ini){
        self::adjustAirtimeStorPermissions($p_ini);
    }
        
    public static function adjustAirtimeStorPermissions($p_ini){
        /* Make the read permission of Monit cfg files more strict */
        $webUser = $p_ini["general"]["web_server_user"];
        exec("chown -R root:$webUser /srv/airtime");
        exec("chmod -R 2775 /srv/airtime");
    }
}
