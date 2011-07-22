<?php

$airtimeIni = AirtimeCheck::GetAirtimeConf();
$airtime_base_dir = $airtimeIni['general']['airtime_dir'];

require_once "$airtime_base_dir/library/php-amqplib/amqp.inc";

set_error_handler("myErrorHandler");

AirtimeCheck::GetCpuInfo();
AirtimeCheck::GetRamInfo();
AirtimeCheck::CheckOsTypeVersion();

AirtimeCheck::CheckConfigFilesExist();


$apiClientCfg = AirtimeCheck::GetApiClientCfg();

AirtimeCheck::GetDbConnection($airtimeIni);
AirtimeCheck::PythonLibrariesInstalled();

AirtimeCheck::CheckRabbitMqConnection($airtimeIni);

//AirtimeCheck::CheckApacheVHostFiles();

AirtimeCheck::GetAirtimeServerVersion($apiClientCfg);
AirtimeCheck::CheckAirtimeDaemons();
AirtimeCheck::CheckIcecastRunning();

echo PHP_EOL;
if (AirtimeCheck::$check_system_ok){
    output_msg("System setup looks OK!");
} else {
    output_msg("There appears to be problems with your setup. Please visit");
    output_msg("http://wiki.sourcefabric.org/x/HABQ for troubleshooting info.");
}

echo PHP_EOL;

function output_status($key, $value) 
{
    echo sprintf("%-31s= %s", $key, $value).PHP_EOL;
}

function output_msg($msg)
{
    //echo "  -- ".PHP_EOL;
    echo "  -- $msg".PHP_EOL;
    //echo "  -- ".PHP_EOL;
}

class AirtimeCheck {

    const CHECK_OK = "OK";
    const CHECK_FAILED = "FAILED";
    
    const KOMBU_MIN_VERSION = "1.1.2";
    const POSTER_MIN_VERSION = "0.8.1";
    const MUTAGEN_MIN_VERSION = "1.20";
    const PYINOTIFY_MIN_VERSION = "0.9.2";

    public static $check_system_ok = true;
   
    private static function CheckAirtimeDaemonRunning($filename, $process_id_str, $process_running_str, $name, $logFile)
    {
        $pid = false;
        $numSecondsRunning = 0;

        if (file_exists($filename)){
            //first get pid
            $potential_pid = trim(file_get_contents($filename));

            //check if the pid is actually live
            if (file_exists("/proc/$potential_pid")){
                $pid = $potential_pid;

                //now lets get the running time
                $lastModified = filemtime($filename);
                $currentTime = time();

                $numSecondsRunning = $currentTime - $lastModified;
            }
        }

        output_status($process_id_str, $pid);

        output_status($process_running_str, $numSecondsRunning);
        if (is_numeric($numSecondsRunning) && (int)$numSecondsRunning < 3) {
            self::$check_system_ok = false;
            output_msg("WARNING! It looks like the $name engine is continually restarting.");
            $command = "tail -10 $logFile";
            exec($command, $output, $result);
            foreach ($output as $line) {
                output_msg($line);
            }
        } 
    }
   
    public static function CheckAirtimeDaemons()
    {
        self::CheckAirtimeDaemonRunning("/var/run/airtime-playout.pid",
                                "PLAYOUT_ENGINE_PROCESS_ID",
                                "PLAYOUT_ENGINE_RUNNING_SECONDS",
                                "playout",
                                "/var/log/airtime/pypo/pypo.log"
                                );

        self::CheckAirtimeDaemonRunning("/var/run/airtime-liquidsoap.pid",
                                "LIQUIDSOAP_PROCESS_ID",
                                "LIQUIDSOAP_RUNNING_SECONDS",
                                "Liquidsoap",
                                "/var/log/airtime/pypo/ls_script.log"
                                );

        self::CheckAirtimeDaemonRunning("/var/run/airtime-media-monitor.pid",
                                "MEDIA_MONITOR_PROCESS_ID",
                                "MEDIA_MONITOR_RUNNING_SECONDS",
                                "Media Monitor",
                                "/var/log/airtime/media-monitor/media-monitor.log"
                                );

        self::CheckAirtimeDaemonRunning("/var/run/airtime-show-recorder.pid",
                                "SHOW_RECORDER_PROCESS_ID",
                                "SHOW_RECORDER_RUNNING_SECONDS",
                                "Show Recorder",
                                "/var/log/airtime/media-monitor/show-recorder.log"
                                );                                
    }

    
    public static function CheckIcecastRunning()
    {
        $command = "ps aux | grep \"^icecast2\"";
        exec($command, $output, $result);
        
        $status = AirtimeCheck::CHECK_FAILED;
        if (count($output) > 0){
            $delimited = preg_split("/[\s]+/", $output[0]);
            $status = $delimited[1];
        } else {
            self::$check_system_ok = false;
        }
        output_status("ICECAST_PROCESS_ID", $status);
    }

    public static function GetCpuInfo()
    {
        $command = "cat /proc/cpuinfo |grep -m 1 'model name' ";
        exec($command, $output, $result);
        
        $choppedStr = explode(":", $output[0]);
        $status = trim($choppedStr[1]);
        output_status("CPU", $status);
    }

    public static function GetRamInfo()
    {
        $command = "cat /proc/meminfo |grep 'MemTotal' ";
        exec($command, $output, $result);
        $choppedStr = explode(":", $output[0]);
        $status = trim($choppedStr[1]);
        output_status("Total RAM", $status);	

	$output = null;
        $command = "cat /proc/meminfo |grep 'MemFree' ";
        exec($command, $output, $result);
        $choppedStr = explode(":", $output[0]);
        $status = trim($choppedStr[1]);
        output_status("Free RAM", $status);	
    }

    public static function CheckConfigFilesExist()
    {
        //echo PHP_EOL."Verifying Config Files in /etc/airtime".PHP_EOL;
        $confFiles = array("airtime.conf",
                            "liquidsoap.cfg",
                            "pypo.cfg",
                            "media-monitor.cfg",
                            "recorder.cfg");

        $allFound = AirtimeCheck::CHECK_OK;

        foreach ($confFiles as $cf){
            $fullPath = "/etc/airtime/$cf";
            if (!file_exists($fullPath)){
                $allFound = AirtimeCheck::CHECK_FAILED;
                self::$check_system_ok = false;
                break;
            }
        }

        output_status("AIRTIME_CONFIG_FILES", $allFound);
        
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

    public static function GetApiClientCfg()
    {
        $ini = parse_ini_file("/etc/airtime/api_client.cfg", false);

        if ($ini === false){
            echo "Error reading /etc/airtime/api_client.cfg.".PHP_EOL;
            exit;
        }

        return $ini;
    }

    public static function GetDbConnection($airtimeIni)
    {
        $host = $airtimeIni["database"]["host"];
        $dbname = $airtimeIni["database"]["dbname"];
        $dbuser = $airtimeIni["database"]["dbuser"];
        $dbpass = $airtimeIni["database"]["dbpass"];
        
        $dbconn = pg_connect("host=$host port=5432 dbname=$dbname user=$dbuser password=$dbpass");

        if ($dbconn === false){
            $status = AirtimeCheck::CHECK_FAILED;
            self::$check_system_ok = false;
        } else {
            $status = AirtimeCheck::CHECK_OK;
        }

        output_status("POSTGRESQL_DATABASE", $status);
    }
    
    private static function isMinVersionSatisfied($minVersion, $version){
        $minVersionArr = explode(".", $minVersion);
        $versionArr = explode(".", $version);
        
        if (count($minVersionArr) != count($versionArr)){
            return false;
        }
        
        //when comparing 1.20 and 1.19, first compare "1" and "1"
        //and then the "20" to the "19"
        for ($i=0, $n = count($minVersionArr); $i<$n; $i++){
            if ($minVersionArr[$i] < $versionArr[$i]){
                return true;
            } else if ($minVersionArr[$i] > $versionArr[$i]){
                return false;
            }
            //else continue if equal
        }
        
        return true;
    }
    
    private static function CheckPythonLibrary($lib, $minVersion){
        $command = "/usr/lib/airtime/airtime_virtualenv/bin/pip freeze | grep $lib";
        exec($command, $output, $result);
            
        $status = AirtimeCheck::CHECK_FAILED;
        if (count($output[0]) > 0){
            $key_value = explode("==", $output[0]);
            $version = trim($key_value[1]);
            if (self::isMinVersionSatisfied($minVersion, $version)){
                $status = $version;
            } else {
                output_msg("Minimum require version for \"$lib\" is $minVersion. Your version: $version");
                self::$check_system_ok = false;
            }
        } else {
            self::$check_system_ok = false;
        }
        
        return $status;
    }
        
    public static function PythonLibrariesInstalled()
    {
        output_status("PYTHON_KOMBU_VERSION", self::CheckPythonLibrary("kombu", self::KOMBU_MIN_VERSION));
        output_status("PYTHON_POSTER_VERSION", self::CheckPythonLibrary("poster", self::POSTER_MIN_VERSION));
        output_status("PYTHON_MUTAGEN_VERSION", self::CheckPythonLibrary("mutagen", self::MUTAGEN_MIN_VERSION));
        output_status("PYTHON_PYINOTIFY_VERSION", self::CheckPythonLibrary("pyinotify", self::PYINOTIFY_MIN_VERSION));
    }

    public static function CheckDbTables()
    {
        
    }

    /* The function tests for whether the rabbitmq-server package is
     * installed. RabbitMQ could be installed manually via tarball
     * and this function will fail to detect it! Unfortunately there
     * seems to be no other way to check RabbitMQ version. Will update
     * this function if I find a more universal solution. */
     /*
    public static function CheckRabbitMqVersion(){
        echo PHP_EOL."Checking RabbitMQ Version".PHP_EOL;
        
        $command = "dpkg -l | grep rabbitmq-server";
        exec($command, $output, $result);

        if (count($output) > 0){
            //version string always starts at character 45. Lets find
            //the end of this version string by looking for the first space.
            $start = 45;
            $end = strpos($output[0], " ", $start);
            
            $version = substr($output[0], $start, $end-$start);

            echo "\t$version ... [OK]".PHP_EOL;
        } else {
            echo "\trabbitmq-server package not found. [Failed!]".PHP_EOL;
        }
    }
    * */
    
    public static function CheckRabbitMqConnection($airtimeIni)
    {
        try {
            $status = AirtimeCheck::CHECK_OK;
            $conn = new AMQPConnection($airtimeIni["rabbitmq"]["host"],
                                             $airtimeIni["rabbitmq"]["port"],
                                             $airtimeIni["rabbitmq"]["user"],
                                             $airtimeIni["rabbitmq"]["password"]);
        } catch (Exception $e){
            $status = AirtimeCheck::CHECK_FAILED;
            self::$check_system_ok = false;
        }
        
        output_status("RABBITMQ_SERVER", $status);
    }

    public static function GetAirtimeServerVersion($apiClientCfg)
    {

        $baseUrl = $apiClientCfg["base_url"];
        $basePort = $apiClientCfg["base_port"];
        $apiKey = "%%api_key%%";

        $url = "http://$baseUrl:$basePort/api/version/api_key/$apiKey";
        output_status("AIRTIME_VERSION_URL", $url);

        $apiKey = $apiClientCfg["api_key"];
        $url = "http://$baseUrl:$basePort/api/version/api_key/$apiKey";
       
        $rh = fopen($url, "r");

        $version = "Could not contact server";
        if ($rh !== false) {
            output_status("APACHE_CONFIGURED", "YES");
            while (($buffer = fgets($rh)) !== false) {
                $json = json_decode(trim($buffer), true);
                if (!is_null($json)){
                    $version = $json["version"];
                }
            }
        } else {
            output_status("APACHE_CONFIGURED", "NO");
        }
        output_status("AIRTIME_VERSION", $version);
    }

    public static function CheckApacheVHostFiles(){
        $fileNames = array("/etc/apache2/sites-available/airtime",
                        "/etc/apache2/sites-enabled/airtime");

        $status = AirtimeCheck::CHECK_OK;

        foreach ($fileNames as $fn){
            if (!file_exists($fn)){
                $status = AirtimeCheck::CHECK_FAILED;
                self::$check_system_ok = false;
            }
        }

        //Since apache2 loads config files in alphabetical order
        //from the sites-enabled directory, we need to check if
        //airtime is lexically the first file in this directory.
        //get sorted array of files
        $arr = scandir("/etc/apache2/sites-enabled");

        /*
        foreach ($arr as $a){
            if ($a == "." || $a == ".."){
                continue;
            }
            if ($a == "airtime"){
                break;
            } else {
                echo "\t\t*Warning, the file \"$a\" is lexically ahead of the file \"airtime\" in".PHP_EOL;
                echo"\t\t /etc/apache2/sites-enabled and preventing airtime from being loaded".PHP_EOL;
            }
        }
        */
    }

    public static function CheckOsTypeVersion(){

        if (file_exists("/etc/lsb-release")){
            //lsb-release existing implies a Ubuntu installation.

            $ini = parse_ini_file("/etc/lsb-release", false);
            $os_string = $ini["DISTRIB_DESCRIPTION"];
        } else if (file_exists("/etc/debian_version")) {
            //if lsb-release does not exist, lets check if we are
            //running on Debian. Look for file /etc/debian_version
            $handler = fopen("/etc/debian_version", "r");
            $os_string = trim(fgets($handler));
            
        } else {
            $os_string = "Unknown";
        }

	// Figure out if 32 or 64 bit
  	$command = "file -b /sbin/init";
	exec($command, $output, $result);
	$splitStr = explode(",", $output[0]);
	$os_string .= $splitStr[1];

        output_status("OS", $os_string);
    }
}


// error handler function
function myErrorHandler($errno, $errstr, $errfile, $errline)
{
    //Don't execute PHP internal error handler
    return true;
}
