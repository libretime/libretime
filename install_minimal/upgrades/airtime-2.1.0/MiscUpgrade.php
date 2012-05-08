<?php

/* Stuff not related to upgrading database +
 * config files goes here. */
class AirtimeMiscUpgrade{
    public static function start($p_ini){
        self::adjustMonitCfgPermissions();
        self::adjustAirtimeStorPermissions($p_ini);
    }
    
    public static function adjustMonitCfgPermissions(){
        /* Make the read permission of Monit cfg files more strict */
        
        chmod("/etc/monit/conf.d/monit-airtime-generic.cfg", 0600);
        chmod("/etc/monit/conf.d/monit-airtime-liquidsoap.cfg", 0600);
        chmod("/etc/monit/conf.d/monit-airtime-media-monitor.cfg", 0600);
        chmod("/etc/monit/conf.d/monit-airtime-playout.cfg", 0600);
        chmod("/etc/monit/conf.d/monit-airtime-rabbitmq-server.cfg", 0600);
    }
    
    public static function adjustAirtimeStorPermissions($p_ini){
        /* Make the read permission of Monit cfg files more strict */
        $webUser = $p_ini["general"]["web_server_user"];
        exec("chown -R root:$webUser");
        exec("chmod -R 2775 /srv/airtime");
    }
}
