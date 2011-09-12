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
    
    public static function ModifyHtAccessTimezone($phpDir){
        $file = realpath($phpDir)."/public/.htaccess";
        
        $fn = "/etc/timezone";
        $handle = @fopen($fn, "r");
        if ($handle){
            $timezone = trim(fgets($handle, 4096));
            fclose($handle);
        } else {
            echo "Could not open $fn";
        }
        
        $key = "php_value date.timezone";
        //the best way to do this is use cli utility "sed", but I don't have time to learn this
        $handle = @fopen($file, "r");
        if ($handle) {
            while (($buffer = fgets($handle, 4096)) !== false) {
                if (strlen($key) > $buffer){
                    if (substr($buffer, 0, strlen($key)) == $key){
                        $output[] = "$key \"$timezone\"".PHP_EOL;
                    } else {
                        $output[] = $buffer;
                    }
                } else {
                    $output[] = $buffer;
                }
            }
            if (!feof($handle)) {
                echo "Error: unexpected fgets() fail\n";
            }
            fclose($handle);
        } else {
            echo "Could not open $file";
        }
        
        $handle = @fopen($file, 'w');
        if ($handle) {
            foreach ($output as $line){
                fwrite($handle, $line);
            }
            fclose($handle);        
        } else {
            echo "Could not open $file";
        }
    }
}


$values = parse_ini_file(Airtime194Upgrade::CONF_FILE_AIRTIME, true);
$phpDir = $values['general']['airtime_dir'];
Airtime194Upgrade::InstallAirtimePhpServerCode($phpDir);
Airtime194Upgrade::ModifyHtAccessTimezone($phpDir);
Airtime194Upgrade::upgradeLiquidsoapCfgPerms();

