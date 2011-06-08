<?php
/**
 * @package Airtime
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

// Do not allow remote execution
$arr = array_diff_assoc($_SERVER, $_ENV);
if (isset($arr["DOCUMENT_ROOT"]) && ($arr["DOCUMENT_ROOT"] != "") ) {
    header("HTTP/1.1 400");
    header("Content-type: text/plain; charset=UTF-8");
    echo "400 Not executable\r\n";
    exit(1);
}

//make sure user has Postgresql PHP extension installed.
if (!function_exists('pg_connect')) {
    trigger_error("PostgreSQL PHP extension required and not found.", E_USER_ERROR);
    exit(2);
}

class AirtimeIni
{
    const CONF_FILE_AIRTIME = "/etc/airtime/airtime.conf";
    const CONF_FILE_PYPO = "/etc/airtime/pypo.cfg";
    const CONF_FILE_RECORDER = "/etc/airtime/recorder.cfg";
    const CONF_FILE_LIQUIDSOAP = "/etc/airtime/liquidsoap.cfg";
    const CONF_FILE_MEDIAMONITOR = "/etc/airtime/media-monitor.cfg";

    public static function IniFilesExist()
    {
        $configFiles = array(AirtimeIni::CONF_FILE_AIRTIME,
                             AirtimeIni::CONF_FILE_PYPO,
                             AirtimeIni::CONF_FILE_RECORDER,
                             AirtimeIni::CONF_FILE_LIQUIDSOAP,
                             AirtimeIni::CONF_FILE_MEDIAMONITOR);
        $exist = false;
        foreach ($configFiles as $conf) {
            if (file_exists($conf)) {
                echo "Existing config file detected at $conf".PHP_EOL;
                $exist = true;
            }
        }
        return $exist;
    }

    /**
     * This function creates the /etc/airtime configuration folder
     * and copies the default config files to it.
     */
    public static function CreateIniFiles()
    {
        if (!file_exists("/etc/airtime/")){
            if (!mkdir("/etc/airtime/", 0755, true)){
                echo "Could not create /etc/airtime/ directory. Exiting.";
                exit(1);
            }
        }

        if (!copy(AirtimeInstall::GetAirtimeSrcDir()."/build/airtime.conf", AirtimeIni::CONF_FILE_AIRTIME)){
            echo "Could not copy airtime.conf to /etc/airtime/. Exiting.";
            exit(1);
        }
        if (!copy(__DIR__."/../../python_apps/pypo/pypo.cfg", AirtimeIni::CONF_FILE_PYPO)){
            echo "Could not copy pypo.cfg to /etc/airtime/. Exiting.";
            exit(1);
        }
        if (!copy(__DIR__."/../../python_apps/show-recorder/recorder.cfg", AirtimeIni::CONF_FILE_RECORDER)){
            echo "Could not copy recorder.cfg to /etc/airtime/. Exiting.";
            exit(1);
        }
        if (!copy(__DIR__."/../../python_apps/pypo/scripts/liquidsoap.cfg", AirtimeIni::CONF_FILE_LIQUIDSOAP)){
            echo "Could not copy liquidsoap.cfg to /etc/airtime/. Exiting.";
            exit(1);
        }
        if (!copy(__DIR__."/../../python_apps/media-monitor/media-monitor.cfg", AirtimeIni::CONF_FILE_MEDIAMONITOR)){
            echo "Could not copy MediaMonitor.cfg to /etc/airtime/. Exiting.";
            exit(1);
        }
    }

    /**
     * This function removes /etc/airtime and the configuration
     * files present within it.
     */
    public static function RemoveIniFiles()
    {
        if (file_exists(AirtimeIni::CONF_FILE_AIRTIME)){
            unlink(AirtimeIni::CONF_FILE_AIRTIME);
        }

        if (file_exists(AirtimeIni::CONF_FILE_PYPO)){
            unlink(AirtimeIni::CONF_FILE_PYPO);
        }

        if (file_exists(AirtimeIni::CONF_FILE_RECORDER)){
            unlink(AirtimeIni::CONF_FILE_RECORDER);
        }

        if (file_exists(AirtimeIni::CONF_FILE_LIQUIDSOAP)){
            unlink(AirtimeIni::CONF_FILE_LIQUIDSOAP);
        }

        //wait until Airtime 1.9.0
        if (file_exists(AirtimeIni::CONF_FILE_MEDIAMONITOR)){
            unlink(AirtimeIni::CONF_FILE_MEDIAMONITOR);
        }

        if (file_exists("etc/airtime")){
            rmdir("/etc/airtime/");
        }
    }

    /**
     * This function generates a random string.
     *
     * The random string uses two parameters: $p_len and $p_chars. These
     * parameters do not need to be provided, in which case defaults are
     * used.
     *
     * @param string $p_len
     *      How long should the generated string be.
     * @param string $p_chars
     *      String containing chars that should be used for generating.
     * @return string
     *      The generated random string.
     */
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

    /**
     * After the configuration files have been copied to /etc/airtime,
     * this function will update them to values unique to this
     * particular installation.
     */
    public static function UpdateIniFiles()
    {
        $api_key = AirtimeIni::GenerateRandomString();
        AirtimeIni::UpdateIniValue(AirtimeIni::CONF_FILE_AIRTIME, 'api_key', $api_key);
        AirtimeIni::UpdateIniValue(AirtimeIni::CONF_FILE_AIRTIME, 'base_files_dir', AirtimeInstall::CONF_DIR_STORAGE);
        AirtimeIni::UpdateIniValue(AirtimeIni::CONF_FILE_AIRTIME, 'airtime_dir', AirtimeInstall::CONF_DIR_WWW);
        AirtimeIni::UpdateIniValue(AirtimeIni::CONF_FILE_PYPO, 'api_key', "'$api_key'");
        AirtimeIni::UpdateIniValue(AirtimeIni::CONF_FILE_RECORDER, 'api_key', "'$api_key'");
        AirtimeIni::UpdateIniValue(AirtimeIni::CONF_FILE_MEDIAMONITOR, 'api_key', "'$api_key'");
        AirtimeIni::UpdateIniValue(AirtimeInstall::CONF_DIR_WWW.'/build/build.properties', 'project.home', AirtimeInstall::CONF_DIR_WWW);
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

                if($conf === CONF_FILE_AIRTIME) {
                    // Parse with sections
                    $newSettings = parse_ini_file($conf, true);
                    $oldSettings = parse_ini_file("$conf$suffix.bak", true);
                }
                else {
                    $newSettings = AirtimeIni::ReadPythonConfig($conf);
                    $oldSettings = AirtimeIni::ReadPythonConfig("$conf$suffix.bak");
                }

                $settings = array_keys($newSettings);

                foreach($settings as $section) {
                    if(isset($oldSettings[$section])) {
                        if(is_array($oldSettings[$section])) {
                            $sectionKeys = array_keys($newSettings[$section]);
                            foreach($sectionKeys as $sectionKey) {
                                if(isset($oldSettings[$section][$sectionKey])) {
                                    AirtimeIni::UpdateIniValue($conf, $sectionKey, $oldSettings[$section][$sectionKey]);
                                }
                            }
                        }
                        else {
                            AirtimeIni::UpdateIniValue($conf, $section, $oldSettings[$section]);
                        }
                    }
                }
            }
        }
    }
}
