<?php

class Airtime194Upgrade{

    const CONF_FILE_LIQUIDSOAP = "/etc/airtime/liquidsoap.cfg";
    const CONF_FILE_AIRTIME = "/etc/airtime/airtime.conf";
    const CONF_PYPO_GRP = "pypo";
    
    public static function upgradeLiquidsoapCfgPerms(){
        chmod(self::CONF_FILE_LIQUIDSOAP, 0640);
        chgrp(self::CONF_FILE_LIQUIDSOAP, self::CONF_PYPO_GRP);
    }
    
    public static function InstallAirtimePhpServerCode($phpDir)
    {
        $AIRTIME_SRC = realpath(__DIR__.'/../../../airtime_mvc');

        echo "* Installing PHP code to ".$phpDir.PHP_EOL;
        exec("mkdir -p ".$phpDir);
        exec("cp -R ".$AIRTIME_SRC."/* ".$phpDir);
    }

}


$values = parse_ini_file(AirtimeIni194::CONF_FILE_AIRTIME, true);
$phpDir = $values['general']['airtime_dir'];
Airtime194Upgrade::InstallAirtimePhpServerCode($phpDir);
Airtime194Upgrade::upgradeLiquidsoapCfgPerms();
