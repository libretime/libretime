<?php
require_once(dirname(__FILE__).'/../../include/AirtimeInstall.php');
class Airtime195Upgrade{

    const CONF_FILE_LIQUIDSOAP = "/etc/airtime/liquidsoap.cfg";
    const CONF_FILE_AIRTIME = "/etc/airtime/airtime.conf";
    const CONF_PYPO_GRP = "pypo";
    
    
    public static function BackupHtaccess($phpDir){
        exec("mkdir -p /tmp");
        exec("cp $phpDir/public/.htaccess /tmp");
    }
    
    public static function RestoreHtaccess($phpDir){
        exec("cp /tmp/.htaccess $phpDir/public/");
        exec("rm -f /tmp/.htaccess");
    }
   
    public static function InstallAirtimePhpServerCode($phpDir)
    {
        self::BackupHtaccess($phpDir);
        
        $AIRTIME_SRC = realpath(__DIR__.'/../../../airtime_mvc');

        echo "* Installing PHP code to ".$phpDir.PHP_EOL;
        exec("rm -rf \"$phpDir\"");
        exec("mkdir -p $phpDir");
        exec("cp -R ".$AIRTIME_SRC."/* ".$phpDir);
        
        self::RestoreHtaccess($phpDir);
    }
}

$values = parse_ini_file(Airtime195Upgrade::CONF_FILE_AIRTIME, true);
$phpDir = $values['general']['airtime_dir'];
Airtime195Upgrade::InstallAirtimePhpServerCode($phpDir);
