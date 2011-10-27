<?php

AirtimeCheck::ExitIfNotRoot();

$sapi_type = php_sapi_name();

$showColor = !in_array("--no-color", $argv);

//detect if we are running via the command line
if (substr($sapi_type, 0, 3) == 'cli') {
    //we are running from the command-line
       
    $airtimeIni = AirtimeCheck::GetAirtimeConf();
    $apiKey = $airtimeIni['general']['api_key'];

    $status = AirtimeCheck::GetStatus($apiKey);
    AirtimeCheck::PrintStatus($status);
}

class AirtimeCheck {

    private static $AIRTIME_STATUS_OK = true;
    
    /**
     * Ensures that the user is running this PHP script with root
     * permissions. If not running with root permissions, causes the
     * script to exit.
     */
    public static function ExitIfNotRoot()
    {
        // Need to check that we are superuser before running this.
        if(exec("whoami") != "root"){
            echo "Must be root user.\n";
            exit(1);
        }
    }

    public static function GetAirtimeConf()
    {
        $ini = parse_ini_file("/etc/airtime/airtime.conf", true);

        if ($ini === false){
            echo "Error reading /etc/airtime/airtime.conf.".PHP_EOL;
            exit;
        }

        return $ini;
    }

    public static function GetStatus($p_apiKey){

        $url = "http://localhost/api/status/format/json/api_key/%%api_key%%";
        self::output_status("AIRTIME_STATUS_URL", $url);
        $url = str_replace("%%api_key%%", $p_apiKey, $url);

        
        $ch = curl_init($url);

        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        
        $data = curl_exec($ch);

        //$status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
        curl_close($ch);

        return $data;
    }

    public static function PrintStatus($p_status){
        
        if ($p_status === false){
            self::output_status("AIRTIME_SERVER_RESPONDING", "FAILED");
        } else {
            self::output_status("AIRTIME_SERVER_RESPONDING", "OK");
            $p_status = json_decode($p_status);
            
            $data = $p_status->status;
                        
            self::output_status("KERNEL_VERSION", $data->platform->release);
            self::output_status("MACHINE_ARCHITECTURE", $data->platform->machine);
            self::output_status("TOTAL_MEMORY_MBYTES", $data->platform->memory);
            self::output_status("TOTAL_SWAP_MBYTES", $data->platform->swap);
            self::output_status("AIRTIME_VERSION", $data->airtime_version);
            self::output_status("PLAYOUT_ENGINE_PROCESS_ID", $data->services->pypo->process_id);
            self::output_status("PLAYOUT_ENGINE_RUNNING_SECONDS", $data->services->pypo->uptime_seconds);
            self::output_status("PLAYOUT_ENGINE_MEM_PERC", $data->services->pypo->memory_perc);
            self::output_status("PLAYOUT_ENGINE_CPU_PERC", $data->services->pypo->cpu_perc);
            self::output_status("LIQUIDSOAP_PROCESS_ID", $data->services->liquidsoap->process_id);
            self::output_status("LIQUIDSOAP_RUNNING_SECONDS", $data->services->liquidsoap->uptime_seconds);
            self::output_status("LIQUIDSOAP_MEM_PERC", $data->services->liquidsoap->memory_perc);
            self::output_status("LIQUIDSOAP_CPU_PERC", $data->services->liquidsoap->cpu_perc);
            self::output_status("MEDIA_MONITOR_PROCESS_ID", $data->services->media_monitor->process_id);
            self::output_status("MEDIA_MONITOR_RUNNING_SECONDS", $data->services->media_monitor->uptime_seconds);
            self::output_status("MEDIA_MONITOR_MEM_PERC", $data->services->media_monitor->memory_perc);
            self::output_status("MEDIA_MONITOR_CPU_PERC", $data->services->media_monitor->cpu_perc);
            self::output_status("SHOW_RECORDER_PROCESS_ID", $data->services->show_recorder->process_id);
            self::output_status("SHOW_RECORDER_RUNNING_SECONDS", $data->services->show_recorder->uptime_seconds);
            self::output_status("SHOW_RECORDER_MEM_PERC", $data->services->show_recorder->memory_perc);
            self::output_status("SHOW_RECORDER_CPU_PERC", $data->services->show_recorder->cpu_perc);
            self::output_status("RABBITMQ_PROCESS_ID", $data->services->rabbitmq->process_id);
            self::output_status("RABBITMQ_RUNNING_SECONDS", $data->services->rabbitmq->uptime_seconds);
            self::output_status("RABBITMQ_MEM_PERC", $data->services->rabbitmq->memory_perc);
            self::output_status("RABBITMQ_CPU_PERC", $data->services->rabbitmq->cpu_perc);
        }

        if (self::$AIRTIME_STATUS_OK){
            echo PHP_EOL."-- Your installation of Airtime looks OK!".PHP_EOL;
            exit(0);
        } else {
            echo PHP_EOL."-- There appears to be a problem with your Airtime installation.".PHP_EOL;
            echo "-- Please visit http://wiki.sourcefabric.org/x/HABQ".PHP_EOL;
            exit(1);
        }
    }

    public static function output_status($key, $value){
        global $showColor;
        
        $RED = "[0;31m";
        $GREEN = "[1;32m";

        $color = $GREEN;
        
        if ($value == "FAILED"){
            $color = $RED;
            self::$AIRTIME_STATUS_OK = false;
        }
        
        if ($showColor)
            echo sprintf("%-31s= %s", $key, self::term_color($value, $color)).PHP_EOL;
        else
            echo sprintf("%-31s= %s", $key, $value).PHP_EOL; 
    }

    public static function term_color($text, $color){

        if($color == ""){
            $color = "[0m";
        }

        return chr(27)."$color$text".chr(27)."[0m";
    }
}
