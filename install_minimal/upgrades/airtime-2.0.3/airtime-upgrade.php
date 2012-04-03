<?php
/**
 * @package Airtime
 * @subpackage StorageServer
 * @copyright 2010 Sourcefabric O.P.S.
 * @license http://www.gnu.org/licenses/gpl.txt
 */
 
/*
 * In the future, most Airtime upgrades will involve just mutating the
 * data that is stored on the system. For example, The only data
 * we need to preserve between versions is the database, /etc/airtime, and
 * /srv/airtime. Everything else is just binary files that can be removed/replaced
 * with the new version of Airtime. Of course, the data may need to be in a new
 * format, and that's what this upgrade script will be for.
 */

class AirtimeStorWatchedDirsUpgrade{

    public static function start(){
    }
}

/* This class deals with any modifications to config files in /etc/airtime
 * as well as backups. All functions other than start() should be marked
 * as private. */
class AirtimeConfigFileUpgrade{

    public static function start(){
    }
}

/* This class deals with any modifications to config files in /etc/airtime
 * as well as backups. All functions other than start() should be marked
 * as private. */
class AirtimeMiscUpgrade{

    public static function start(){
        self::modifyPypo();
        self::modifyMonitPassword();
    }
    
    public static function modifyPypo(){
        echo "* Modifying User Pypo".PHP_EOL;
        exec("usermod -s /bin/false pypo");
        exec("passwd --delete pypo");
    }
    
    public static function modifyMonitPassword(){
        echo "* Generating Monit password".PHP_EOL;
        copy(__DIR__."/monit-airtime-generic.cfg", "/etc/monit/conf.d/monit-airtime-generic.cfg");
        $pass = self::GenerateRandomString(10);
        exec("sed -i 's/\$admin_pass/$pass/g' /etc/monit/conf.d/monit-airtime-generic.cfg");
    }
    
    public static function GenerateRandomString($p_len=20, $p_chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789')
    {
        $string = '';
        for ($i = 0; $i < $p_len; $i++)
        {
            $pos = mt_rand(0, strlen($p_chars)-1);
            $string .= $p_chars{$pos};
        }
        return $string;
    }
}

AirtimeConfigFileUpgrade::start();
AirtimeMiscUpgrade::start();
