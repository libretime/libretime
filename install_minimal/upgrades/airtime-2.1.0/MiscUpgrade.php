<?php

/* Stuff not related to upgrading database +
 * config files goes here. */
class AirtimeMiscUpgrade{
    public static function start(){
    }
    
    public static function adjustMonitCfgPermissions(){
        /* Make the read permission of Monit cfg files more strict */
        
        chmod("/etc/monit/conf.d/monit-airtime-generic.cfg", 0600);
        chmod("/etc/monit/conf.d/monit-airtime-liquidsoap.cfg", 0600);
        chmod("/etc/monit/conf.d/monit-airtime-media-monitor.cfg", 0600);
        chmod("/etc/monit/conf.d/monit-airtime-playout.cfg", 0600);
        chmod("/etc/monit/conf.d/monit-airtime-rabbitmq-server.cfg", 0600);
    }
}
