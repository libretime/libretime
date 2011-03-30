<?php

// Do not allow remote execution
$arr = array_diff_assoc($_SERVER, $_ENV);
if (isset($arr["DOCUMENT_ROOT"]) && ($arr["DOCUMENT_ROOT"] != "") ) {
    header("HTTP/1.1 400");
    header("Content-type: text/plain; charset=UTF-8");
    echo "400 Not executable\r\n";
    exit(1);
}

if (!function_exists('pg_connect')) {
    trigger_error("PostgreSQL PHP extension required and not found.", E_USER_ERROR);
    exit(2);
}

class AirtimeIni{

    static function CreateIniFile(){
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

    static function RemoveIniFiles(){
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

    static function ExitIfNotRoot()
    {
        // Need to check that we are superuser before running this.
        if(exec("whoami") != "root"){
            echo "Must be root user.\n";
            exit(1);
        }
    }

    static function GenerateRandomString($len=20, $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789')
    {
        $string = '';
        for ($i = 0; $i < $len; $i++)
        {
            $pos = mt_rand(0, strlen($chars)-1);
            $string .= $chars{$pos};
        }
        return $string;
    }

    static function UpdateIniValue($filename, $property, $value)
    {
        $lines = file($filename);
        $n=count($lines);
        for ($i=0; $i<$n; $i++) {
            if (strlen($lines[$i]) > strlen($property))
            if ($property == substr($lines[$i], 0, strlen($property))){
                $lines[$i] = "$property = $value\n";
            }
        }

        $fp=fopen($filename, 'w');
        for($i=0; $i<$n; $i++){
            fwrite($fp, $lines[$i]);
        }
        fclose($fp);
    }

    static function UpdateIniFiles()
    {
        $api_key = AirtimeIni::GenerateRandomString();
        AirtimeIni::UpdateIniValue('/etc/airtime/airtime.conf', 'api_key', $api_key);
        AirtimeIni::UpdateIniValue('/etc/airtime/airtime.conf', 'baseFilesDir', realpath(__DIR__.'/../../').'/files');
        AirtimeIni::UpdateIniValue('/etc/airtime/pypo.cfg', 'api_key', "'$api_key'");
        AirtimeIni::UpdateIniValue('/etc/airtime/recorder.cfg', 'api_key', "'$api_key'");
        AirtimeIni::UpdateIniValue(__DIR__.'/../../build/build.properties', 'project.home', realpath(__dir__.'/../../'));
    }
}
