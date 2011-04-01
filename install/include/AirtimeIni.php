<?php
/**
 * @package Newscoop
 * @subpackage Subscriptions
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

class AirtimeIni{

    /**
     * This function creates the /etc/airtime configuration folder
     * and copies the default config files to it.
     */
    static function CreateIniFile()
    {
        if (!file_exists("/etc/airtime/")){
            if (!mkdir("/etc/airtime/", 0755, true)){
                echo "Could not create /etc/airtime/ directory. Exiting.";
                exit(1);
            }
        }
        
        if (!copy(__DIR__."/../../build/airtime.conf", "/etc/airtime/airtime.conf")){
            echo "Could not copy airtime.conf to /etc/airtime/. Exiting.";
            exit(1);
        }
        if (!copy(__DIR__."/../../python_apps/pypo/pypo.cfg", "/etc/airtime/pypo.cfg")){
            echo "Could not copy pypo.cfg to /etc/airtime/. Exiting.";
            exit(1);
        }
        if (!copy(__DIR__."/../../python_apps/show-recorder/recorder.cfg", "/etc/airtime/recorder.cfg")){
            echo "Could not copy recorder.cfg to /etc/airtime/. Exiting.";
            exit(1);
        }
        if (!copy(__DIR__."/../../python_apps/pypo/scripts/liquidsoap.cfg", "/etc/airtime/liquidsoap.cfg")){
            echo "Could not copy liquidsoap.cfg to /etc/airtime/. Exiting.";
            exit(1);
        }
    }

    /**
     * This function removes /etc/airtime and the configuration
     * files present within it.
     */
    static function RemoveIniFiles()
    {
        if (file_exists("/etc/airtime/airtime.conf")){
            unlink("/etc/airtime/airtime.conf");
        }

        if (file_exists("/etc/airtime/pypo.cfg")){
            unlink("/etc/airtime/pypo.cfg");
        }

        if (file_exists("/etc/airtime/recorder.cfg")){
            unlink("/etc/airtime/recorder.cfg");
        }

        if (file_exists("/etc/airtime/liquidsoap.cfg")){
            unlink("/etc/airtime/liquidsoap.cfg");
        }

        if (file_exists("etc/airtime")){
            rmdir("/etc/airtime/");
        }
    }

    /**
     * Ensures that the user is running this PHP script with root
     * permissions. If not running with root permissions, causes the
     * script to exit. 
     */
    static function ExitIfNotRoot()
    {
        // Need to check that we are superuser before running this.
        if(exec("whoami") != "root"){
            echo "Must be root user.\n";
            exit(1);
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
    static function GenerateRandomString($p_len=20, $p_chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789')
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
    static function UpdateIniValue($p_filename, $p_property, $p_value)
    {
        $lines = file($p_filename);
        $n=count($lines);
        for ($i=0; $i<$n; $i++) {
            if (strlen($lines[$i]) > strlen($p_property))
            if ($p_property == substr($lines[$i], 0, strlen($p_property))){
                $lines[$i] = "$p_property = $p_value\n";
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
    static function UpdateIniFiles()
    {
        $api_key = AirtimeIni::GenerateRandomString();
        AirtimeIni::UpdateIniValue('/etc/airtime/airtime.conf', 'api_key', $api_key);
        AirtimeIni::UpdateIniValue('/etc/airtime/airtime.conf', 'base_files_dir', realpath(__DIR__.'/../../').'/files');
        AirtimeIni::UpdateIniValue('/etc/airtime/airtime.conf', 'airtime_dir', realpath(__DIR__.'/../../'));
        AirtimeIni::UpdateIniValue('/etc/airtime/pypo.cfg', 'api_key', "'$api_key'");
        AirtimeIni::UpdateIniValue('/etc/airtime/recorder.cfg', 'api_key', "'$api_key'");
        AirtimeIni::UpdateIniValue(__DIR__.'/../../build/build.properties', 'project.home', realpath(__dir__.'/../../'));
    }
}
