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
        $user = exec("whoami");
        if($user != "root" && $user != "www-data"){
            echo "Must be root user.\n";
            exit(1);
        }
    }
    
    public static function GetCpuInfo()
    {
        $command = "cat /proc/cpuinfo |grep -m 1 'model name' ";
        exec($command, $output, $result);
        
        $choppedStr = explode(":", $output[0]);
        $status = trim($choppedStr[1]);
        //output_status("CPU", $status);
        return $status;
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
    
    public static function CheckOsTypeVersion(){
        
        exec("lsb_release -ds", $output, $rv);
        if ($rv != 0){
            $os_string = "Unknown";
        } else {
            $os_string = $output[0];
        }
        
        unset($output);
        
        // Figure out if 32 or 64 bit
        exec("uname -m", $output, $rv);
        if ($rv != 0){
            $machine = "Unknown";
        } else {
            $machine = $output[0];
        }
        
        return $os_string." ".$machine;
    }
    
    public static function GetServerType(){
        $headerInfo = get_headers("http://localhost",1);
        return $headerInfo['Server'][0];
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
            self::output_status("OS", self::CheckOsTypeVersion());
            self::output_status("CPU", self::GetCpuInfo());
            self::output_status("WEB_SERVER", self::GetServerType());
            if ($data->services->pypo){
                self::output_status("PLAYOUT_ENGINE_PROCESS_ID", $data->services->pypo->process_id);
                self::output_status("PLAYOUT_ENGINE_RUNNING_SECONDS", $data->services->pypo->uptime_seconds);
                self::output_status("PLAYOUT_ENGINE_MEM_PERC", $data->services->pypo->memory_perc);
                self::output_status("PLAYOUT_ENGINE_CPU_PERC", $data->services->pypo->cpu_perc);
            } else {
                self::output_status("PLAYOUT_ENGINE_PROCESS_ID", "FAILED");
                self::output_status("PLAYOUT_ENGINE_RUNNING_SECONDS", "0");
                self::output_status("PLAYOUT_ENGINE_MEM_PERC", "0%");
                self::output_status("PLAYOUT_ENGINE_CPU_PERC", "0%");
            }
            if (isset($data->services->liquidsoap)){
                self::output_status("LIQUIDSOAP_PROCESS_ID", $data->services->liquidsoap->process_id);
                self::output_status("LIQUIDSOAP_RUNNING_SECONDS", $data->services->liquidsoap->uptime_seconds);
                self::output_status("LIQUIDSOAP_MEM_PERC", $data->services->liquidsoap->memory_perc);
                self::output_status("LIQUIDSOAP_CPU_PERC", $data->services->liquidsoap->cpu_perc);
            } else {
                self::output_status("LIQUIDSOAP_PROCESS_ID", "FAILED");
                self::output_status("LIQUIDSOAP_RUNNING_SECONDS", "0");
                self::output_status("LIQUIDSOAP_MEM_PERC", "0%");
                self::output_status("LIQUIDSOAP_CPU_PERC", "0%");
            }
            if (isset($data->services->media_monitor)){
                self::output_status("MEDIA_MONITOR_PROCESS_ID", $data->services->media_monitor->process_id);
                self::output_status("MEDIA_MONITOR_RUNNING_SECONDS", $data->services->media_monitor->uptime_seconds);
                self::output_status("MEDIA_MONITOR_MEM_PERC", $data->services->media_monitor->memory_perc);
                self::output_status("MEDIA_MONITOR_CPU_PERC", $data->services->media_monitor->cpu_perc);
            } else {
                self::output_status("MEDIA_MONITOR_PROCESS_ID", "FAILED");
                self::output_status("MEDIA_MONITOR_RUNNING_SECONDS", "0");
                self::output_status("MEDIA_MONITOR_MEM_PERC", "0%");
                self::output_status("MEDIA_MONITOR_CPU_PERC", "0%");
            }
            if (isset($data->services->show_recorder)){
                self::output_status("SHOW_RECORDER_PROCESS_ID", $data->services->show_recorder->process_id);
                self::output_status("SHOW_RECORDER_RUNNING_SECONDS", $data->services->show_recorder->uptime_seconds);
                self::output_status("SHOW_RECORDER_MEM_PERC", $data->services->show_recorder->memory_perc);
                self::output_status("SHOW_RECORDER_CPU_PERC", $data->services->show_recorder->cpu_perc);
            } else {
                self::output_status("SHOW_RECORDER_PROCESS_ID", "FAILED");
                self::output_status("SHOW_RECORDER_RUNNING_SECONDS", "0");
                self::output_status("SHOW_RECORDER_MEM_PERC", "0%");
                self::output_status("SHOW_RECORDER_CPU_PERC", "0%");
            }
            if (isset($data->services->rabbitmq)){
                self::output_status("RABBITMQ_PROCESS_ID", $data->services->rabbitmq->process_id);
                self::output_status("RABBITMQ_RUNNING_SECONDS", $data->services->rabbitmq->uptime_seconds);
                self::output_status("RABBITMQ_MEM_PERC", $data->services->rabbitmq->memory_perc);
                self::output_status("RABBITMQ_CPU_PERC", $data->services->rabbitmq->cpu_perc);
            } else {
                self::output_status("RABBITMQ_PROCESS_ID", "FAILED");
                self::output_status("RABBITMQ_RUNNING_SECONDS", "0");
                self::output_status("RABBITMQ_MEM_PERC", "0%");
                self::output_status("RABBITMQ_CPU_PERC", "0%");
            }
        }

        if (self::$AIRTIME_STATUS_OK){
            self::output_comment("Your installation of Airtime looks OK!");
            exit(0);
        } else {
            self::output_comment("There appears to be a problem with your Airtime installation.");
            self::output_comment("Please visit http://wiki.sourcefabric.org/x/HABQ");
            exit(1);
        }
    }
    
    public static function output_comment($comment){
        echo PHP_EOL."-- $comment".PHP_EOL;
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
