<?php

AirtimeCheck::ExitIfNotRoot();

date_default_timezone_set("UTC");

$sapi_type = php_sapi_name();

$showColor = !in_array("--no-color", $argv);

//detect if we are running via the command line
if (substr($sapi_type, 0, 3) == 'cli') {
    //we are running from the command-line
       
    $airtimeIni = AirtimeCheck::GetAirtimeConf();
    $apiKey = $airtimeIni['general']['api_key'];
    $baseUrl = $airtimeIni['general']['base_url'];
    $base_port = $airtimeIni['general']['base_port'];
    $base_dir = $airtimeIni['general']['base_dir'];

    $status = AirtimeCheck::GetStatus($baseUrl, $base_port, $base_dir, $apiKey);
    AirtimeCheck::PrintStatus($baseUrl, $base_port, $status);
    //AirtimeCheck::PrintStatus($baseUrl, $status);
}

class AirtimeCheck {

    private static $AIRTIME_STATUS_OK = true;
    CONST UNKNOWN = "UNKNOWN";
    
    /**
     * Ensures that the user is running this PHP script with root
     * permissions. If not running with root permissions, causes the
     * script to exit.
     */
    public static function ExitIfNotRoot()
    {
        // Need to check that we are superuser before running this.
        $euid = posix_geteuid();
        $user = exec('whoami');
        if($euid != 0 && $user != "www-data"){
            echo "Must be root user.\n";
            exit(1);
        }
    }
    
    public static function GetCpuInfo()
    {
        $command = "cat /proc/cpuinfo |grep -m 1 'model name' ";
        exec($command, $output, $rv);

        if ($rv != 0 || !isset($output[0]))
            return self::UNKNOWN;
            
        $choppedStr = explode(":", $output[0]);
        
        if (!isset($choppedStr[1]))
            return self::UNKNOWN;
            
        $status = trim($choppedStr[1]);
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
        if ($rv != 0 || !isset($output[0])){
            $os_string = self::UNKNOWN;
        } else {
            $os_string = $output[0];
        }
        
        unset($output);
        
        // Figure out if 32 or 64 bit
        exec("uname -m", $output, $rv);
        if ($rv != 0 || !isset($output[0])){
            $machine = self::UNKNOWN;
        } else {
            $machine = $output[0];
        }
        
        return $os_string." ".$machine;
    }
    
    public static function GetServerType($p_baseUrl, $p_basePort)
    {
        $headerInfo = get_headers("http://$p_baseUrl:$p_basePort",1);
        
        if (!isset($headerInfo['Server'][0])) {
            return self::UNKNOWN;
        } else if (is_array($headerInfo['Server'])) {
            return $headerInfo['Server'][0];
        } else {
            return $headerInfo['Server'];
        }
    }

    public static function GetStatus($p_baseUrl, $p_basePort, $p_baseDir, $p_apiKey){
        if ($p_baseDir == '/') {
            $url = "http://$p_baseUrl:$p_basePort/api/status/format/json/api_key/%%api_key%%";
        } else {
            $url = "http://$p_baseUrl:$p_basePort/$p_baseDir"."api/status/format/json/api_key/%%api_key%%";
        }
        self::output_status("AIRTIME_STATUS_URL", $url);
        $url = str_replace("%%api_key%%", $p_apiKey, $url);
        
        $ch = curl_init($url);

        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        
        $data = curl_exec($ch);

        //$status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
        curl_close($ch);

        return $data;
    }

    public static function PrintStatus($p_baseUrl, $p_basePort, $p_status){
        
        if ($p_status === false){
            self::output_status("AIRTIME_SERVER_RESPONDING", "FAILED");
        } else {
            self::output_status("AIRTIME_SERVER_RESPONDING", "OK");
                        
            $p_status = json_decode($p_status);
            
            if (isset($p_status->status)) {
                $data = $p_status->status;
            } else {
                $data = array();
            }
            
            if (isset($data->platform)) {
                self::output_status("KERNEL_VERSION", $data->platform->release);
                self::output_status("MACHINE_ARCHITECTURE", $data->platform->machine);
                self::output_status("TOTAL_MEMORY_MBYTES", $data->platform->memory);
                self::output_status("TOTAL_SWAP_MBYTES", $data->platform->swap);
                self::output_status("AIRTIME_VERSION", $data->airtime_version);
            } else {
                self::output_status("KERNEL_VERSION", "UNKNOWN");
                self::output_status("MACHINE_ARCHITECTURE", "UNKNOWN");
                self::output_status("TOTAL_MEMORY_MBYTES", "UNKNOWN");
                self::output_status("TOTAL_SWAP_MBYTES", "UNKNOWN");
                self::output_status("AIRTIME_VERSION", "UNKNOWN");
            }
            self::output_status("OS", self::CheckOsTypeVersion());
            self::output_status("CPU", self::GetCpuInfo());
            self::output_status("WEB_SERVER", self::GetServerType($p_baseUrl, $p_basePort));
            
            if (isset($data->services)) {
                $services = $data->services;
            } else {
                $services = array();
            }
            
            if (isset($services->pypo) && $services->pypo->process_id != "FAILED") {
                self::output_status("PLAYOUT_ENGINE_PROCESS_ID", $data->services->pypo->process_id);
                self::output_status("PLAYOUT_ENGINE_RUNNING_SECONDS", $data->services->pypo->uptime_seconds);
                self::output_status("PLAYOUT_ENGINE_MEM_PERC", $data->services->pypo->memory_perc);
                self::output_status("PLAYOUT_ENGINE_CPU_PERC", $data->services->pypo->cpu_perc);
            } else {
                self::output_status("PLAYOUT_ENGINE_PROCESS_ID", "FAILED");
                self::output_status("PLAYOUT_ENGINE_RUNNING_SECONDS", "0");
                self::output_status("PLAYOUT_ENGINE_MEM_PERC", "0%");
                self::output_status("PLAYOUT_ENGINE_CPU_PERC", "0%");
                $log = "/var/log/airtime/pypo/pypo.log";
                self::show_log_file($log);

            }
            if (isset($services->liquidsoap) && $services->liquidsoap->process_id != "FAILED") {
                self::output_status("LIQUIDSOAP_PROCESS_ID", $data->services->liquidsoap->process_id);
                self::output_status("LIQUIDSOAP_RUNNING_SECONDS", $data->services->liquidsoap->uptime_seconds);
                self::output_status("LIQUIDSOAP_MEM_PERC", $data->services->liquidsoap->memory_perc);
                self::output_status("LIQUIDSOAP_CPU_PERC", $data->services->liquidsoap->cpu_perc);
            } else {
                self::output_status("LIQUIDSOAP_PROCESS_ID", "FAILED");
                self::output_status("LIQUIDSOAP_RUNNING_SECONDS", "0");
                self::output_status("LIQUIDSOAP_MEM_PERC", "0%");
                self::output_status("LIQUIDSOAP_CPU_PERC", "0%");
                $log = "/var/log/airtime/pypo-liquidsoap/ls_script.log";
                self::show_log_file($log);
            }
            
            #if (isset($services->media_monitor) && $services->media_monitor->process_id != "FAILED") {
            #    self::output_status("MEDIA_MONITOR_PROCESS_ID", $data->services->media_monitor->process_id);
            #    self::output_status("MEDIA_MONITOR_RUNNING_SECONDS", $data->services->media_monitor->uptime_seconds);
            #    self::output_status("MEDIA_MONITOR_MEM_PERC", $data->services->media_monitor->memory_perc);
            #    self::output_status("MEDIA_MONITOR_CPU_PERC", $data->services->media_monitor->cpu_perc);
            #} else {
            #    self::output_status("MEDIA_MONITOR_PROCESS_ID", "FAILED");
            #    self::output_status("MEDIA_MONITOR_RUNNING_SECONDS", "0");
            #    self::output_status("MEDIA_MONITOR_MEM_PERC", "0%");
            #    self::output_status("MEDIA_MONITOR_CPU_PERC", "0%");
            #    $log = "/var/log/airtime/media-monitor/media-monitor.log";
            #    self::show_log_file($log);
            #}
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
    
    public static function show_log_file($log) {
        self::output_comment("Check the log file $log");
        self::output_comment("");
    }

    public static function output_comment($comment){
        if (!is_array($comment)) {
            $comment = array($comment);
        }

        foreach ($comment as $c) {
            echo "-- $c".PHP_EOL;
        }

    }

    public static function output_status($key, $value){
        global $showColor;
        
        $RED = "[0;31m";
        $ORANGE = "[0;33m";
        $GREEN = "[1;32m";

        $color = $GREEN;
        
        if ($value == "FAILED"){
            $color = $RED;
            self::$AIRTIME_STATUS_OK = false;
        } else if ($value == "NOT MONITORED"){
            $color = $ORANGE;
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
