<?php
require_once(dirname(__FILE__).'/../../include/AirtimeInstall.php');
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

class AirtimeIni194{

    const CONF_FILE_AIRTIME = "/etc/airtime/airtime.conf";
    const CONF_FILE_PYPO = "/etc/airtime/pypo.cfg";
    const CONF_FILE_RECORDER = "/etc/airtime/recorder.cfg";
    const CONF_FILE_LIQUIDSOAP = "/etc/airtime/liquidsoap.cfg";
    const CONF_FILE_MEDIAMONITOR = "/etc/airtime/media-monitor.cfg";
    const CONF_FILE_API_CLIENT = "/etc/airtime/api_client.cfg";
    const CONF_FILE_MONIT = "/etc/monit/conf.d/airtime-monit.cfg";

    /**
     * This function updates an INI style config file.
     *
     * A property and the value the property should be changed to are
     * supplied. If the property is not found, then no changes are made.
     *
     * @param string $p_filename
     *      The path the to the file.
     * @param string $p_property
     *      The property to look for in order to change its value.
     * @param string $p_value
     *      The value the property should be changed to.
     *
     */
    public static function UpdateIniValue($p_filename, $p_property, $p_value)
    {
        $lines = file($p_filename);
        $n=count($lines);
        foreach ($lines as &$line) {
            if ($line[0] != "#"){
                $key_value = explode("=", $line);
                $key = trim($key_value[0]);

                if ($key == $p_property){
                    $line = "$p_property = $p_value".PHP_EOL;
                }
            }
        }

        $fp=fopen($p_filename, 'w');
        for($i=0; $i<$n; $i++){
            fwrite($fp, $lines[$i]);
        }
        fclose($fp);
    }

    public static function ReadPythonConfig($p_filename)
    {
        $values = array();

        $lines = file($p_filename);
        $n=count($lines);
        for ($i=0; $i<$n; $i++) {
            if (strlen($lines[$i]) && !in_array(substr($lines[$i], 0, 1), array('#', PHP_EOL))){
                 $info = explode("=", $lines[$i]);
                 $values[trim($info[0])] = trim($info[1]);
             }
        }

        return $values;
    }

    public static function MergeConfigFiles($configFiles, $suffix) {
        foreach ($configFiles as $conf) {
            if (file_exists("$conf$suffix.bak")) {

                if($conf === AirtimeIni194::CONF_FILE_AIRTIME) {
                    // Parse with sections
                    $newSettings = parse_ini_file($conf, true);
                    $oldSettings = parse_ini_file("$conf$suffix.bak", true);
                }
                else {
                    $newSettings = AirtimeIni194::ReadPythonConfig($conf);
                    $oldSettings = AirtimeIni194::ReadPythonConfig("$conf$suffix.bak");
                }

                $settings = array_keys($newSettings);

                foreach($settings as $section) {
                    // skip airtim_dir as we want to use new value
                    if(isset($oldSettings[$section])) {
                        if(is_array($oldSettings[$section])) {
                            $sectionKeys = array_keys($newSettings[$section]);
                            foreach($sectionKeys as $sectionKey) {
                                if($sectionKey != "airtime_dir"){
                                    if(isset($oldSettings[$section][$sectionKey])) {
                                        AirtimeIni194::UpdateIniValue($conf, $sectionKey, $oldSettings[$section][$sectionKey]);
                                    }
                                }
                            }
                        }
                        else {
                            AirtimeIni194::UpdateIniValue($conf, $section, $oldSettings[$section]);
                        }
                    }
                }
            }
        }
    }

    public static function upgradeConfigFiles(){

        $configFiles = array(AirtimeIni194::CONF_FILE_AIRTIME,
                             AirtimeIni194::CONF_FILE_PYPO,
                             AirtimeIni194::CONF_FILE_RECORDER,
                             AirtimeIni194::CONF_FILE_LIQUIDSOAP);

        // Backup the config files
        $suffix = date("Ymdhis")."-1.9.4";
        foreach ($configFiles as $conf) {
            if (file_exists($conf)) {
                echo "Backing up $conf to $conf$suffix.bak".PHP_EOL;
                copy($conf, $conf.$suffix.".bak");
            }
        }

        $default_suffix = "194";
        AirtimeIni194::CreateIniFiles($default_suffix);
        AirtimeIni194::MergeConfigFiles($configFiles, $suffix);
    }

    /**
     * This function creates the /etc/airtime configuration folder
     * and copies the default config files to it.
     */
    public static function CreateIniFiles($suffix)
    {
        if (!file_exists("/etc/airtime/")){
            if (!mkdir("/etc/airtime/", 0755, true)){
                echo "Could not create /etc/airtime/ directory. Exiting.";
                exit(1);
            }
        }

        if (!copy(__DIR__."/airtime.conf.$suffix", AirtimeIni194::CONF_FILE_AIRTIME)){
            echo "Could not copy airtime.conf to /etc/airtime/. Exiting.";
            exit(1);
        }
        if (!copy(__DIR__."/pypo.cfg.$suffix", AirtimeIni194::CONF_FILE_PYPO)){
            echo "Could not copy pypo.cfg to /etc/airtime/. Exiting.";
            exit(1);
        }
        if (!copy(__DIR__."/recorder.cfg.$suffix", AirtimeIni194::CONF_FILE_RECORDER)){
            echo "Could not copy recorder.cfg to /etc/airtime/. Exiting.";
            exit(1);
        }
        if (!copy(__DIR__."/liquidsoap.cfg.$suffix", AirtimeIni194::CONF_FILE_LIQUIDSOAP)){
            echo "Could not copy liquidsoap.cfg to /etc/airtime/. Exiting.";
            exit(1);
        }
    }
}

echo "* Updating configFiles\n";
AirtimeIni194::upgradeConfigFiles();

$values = parse_ini_file(Airtime194Upgrade::CONF_FILE_AIRTIME, true);
$phpDir = $values['general']['airtime_dir'];
Airtime194Upgrade::InstallAirtimePhpServerCode($phpDir);
Airtime194Upgrade::ModifyHtAccessTimezone($phpDir);
Airtime194Upgrade::upgradeLiquidsoapCfgPerms();

AirtimeInstall::CreateSymlinksToUtils();

