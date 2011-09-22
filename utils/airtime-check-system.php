<?php

AirtimeCheck::ExitIfNotRoot();

$sapi_type = php_sapi_name();

//detect if we are running via the command line
if (substr($sapi_type, 0, 3) == 'cli') {
    //we are running from the command-line
       
    $airtimeIni = AirtimeCheck::GetAirtimeConf();
    $apiKey = $airtimeIni['general']['api_key'];

    $status = AirtimeCheck::GetStatus($apiKey);
    AirtimeCheck::PrintStatus($status->status);
}

class AirtimeCheck {
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

        return json_decode($data);
    }

    public static function PrintStatus($p_status){


        self::output_status("KERNEL_VERSION", $p_status->platform->release);
        self::output_status("MACHINE_ARCHITECTURE", $p_status->platform->machine);
        self::output_status("TOTAL_MEMORY_MBYTES", $p_status->platform->memory);
        self::output_status("TOTAL_SWAP_MBYTES", $p_status->platform->swap);
        self::output_status("AIRTIME_VERSION", $p_status->airtime_version);
        self::output_status("PLAYOUT_ENGINE_PROCESS_ID", $p_status->services->pypo->process_id);
        self::output_status("PLAYOUT_ENGINE_RUNNING_SECONDS", $p_status->services->pypo->uptime_seconds);
        self::output_status("PLAYOUT_ENGINE_MEM_PERC", $p_status->services->pypo->memory_perc);
        self::output_status("PLAYOUT_ENGINE_CPU_PERC", $p_status->services->pypo->cpu_perc);
        self::output_status("LIQUIDSOAP_PROCESS_ID", $p_status->services->liquidsoap->process_id);
        self::output_status("LIQUIDSOAP_RUNNING_SECONDS", $p_status->services->liquidsoap->uptime_seconds);
        self::output_status("LIQUIDSOAP_MEM_PERC", $p_status->services->liquidsoap->memory_perc);
        self::output_status("LIQUIDSOAP_CPU_PERC", $p_status->services->liquidsoap->cpu_perc);
        self::output_status("MEDIA_MONITOR_PROCESS_ID", $p_status->services->media_monitor->process_id);
        self::output_status("MEDIA_MONITOR_RUNNING_SECONDS", $p_status->services->media_monitor->uptime_seconds);
        self::output_status("MEDIA_MONITOR_MEM_PERC", $p_status->services->media_monitor->memory_perc);
        self::output_status("MEDIA_MONITOR_CPU_PERC", $p_status->services->media_monitor->cpu_perc);
        self::output_status("SHOW_RECORDER_PROCESS_ID", $p_status->services->show_recorder->process_id);
        self::output_status("SHOW_RECORDER_RUNNING_SECONDS", $p_status->services->show_recorder->uptime_seconds);
        self::output_status("SHOW_RECORDER_MEM_PERC", $p_status->services->show_recorder->memory_perc);
        self::output_status("SHOW_RECORDER_CPU_PERC", $p_status->services->show_recorder->cpu_perc);
        self::output_status("ICECAST_PROCESS_ID", $p_status->services->icecast2->process_id);
        self::output_status("ICECAST_RUNNING_SECONDS", $p_status->services->icecast2->uptime_seconds);
        self::output_status("ICECAST_MEM_PERC", $p_status->services->icecast2->memory_perc);
        self::output_status("ICECAST_CPU_PERC", $p_status->services->icecast2->cpu_perc);
        self::output_status("RABBITMQ_PROCESS_ID", $p_status->services->rabbitmq->process_id);
        self::output_status("RABBITMQ_RUNNING_SECONDS", $p_status->services->rabbitmq->uptime_seconds);
        self::output_status("RABBITMQ_MEM_PERC", $p_status->services->rabbitmq->memory_perc);
        self::output_status("RABBITMQ_CPU_PERC", $p_status->services->rabbitmq->cpu_perc);


    }

    public static function output_status($key, $value){
        echo sprintf("%-31s= %s", $key, $value).PHP_EOL; 
    }

}
