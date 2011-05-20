<?php
require_once '../airtime_mvc/library/php-amqplib/amqp.inc';

set_error_handler("myErrorHandler");

AirtimeCheck::CheckOsTypeVersion();

AirtimeCheck::CheckConfigFilesExist();

$airtimeIni = AirtimeCheck::GetAirtimeConf();
$pypoCfg = AirtimeCheck::GetPypoCfg();

AirtimeCheck::GetDbConnection($airtimeIni);
AirtimeCheck::PythonLibrariesInstalled();

AirtimeCheck::CheckRabbitMqConnection($airtimeIni);

AirtimeCheck::CheckApacheVHostFiles();

AirtimeCheck::GetAirtimeServerVersion($pypoCfg);
AirtimeCheck::CheckPypoRunning();
AirtimeCheck::CheckLiquidsoapRunning();



class AirtimeCheck{

    const CHECK_OK = "OK";
    const CHECK_FAILED = "FAILED";

    public static function CheckPypoRunning(){
        $command = "sudo svstat /etc/service/pypo";
        exec($command, $output, $result);


        $key_value = split(":", $output[0]);
        $value = trim($key_value[1]);

        $status = AirtimeCheck::CHECK_FAILED;
        $pos = strpos($value, "pid");
        if ($pos !== false){
            $start = $pos + 4;
            $end = strpos($value, ")", $start);
            $status = substr($value, $start, $end-$start);
        }

        echo "PYPO_PID=".$status.PHP_EOL;

        $status = AirtimeCheck::CHECK_FAILED;
        $pos = strpos($value, ")");
        if ($pos !== false){
            $start = $pos + 2;
            $end = strpos($value, " ", $start);
            $status = substr($value, $start, $end-$start);
        }

        echo "PYPO_RUNNING_SECONDS=".$status.PHP_EOL;
    }

    public static function CheckLiquidsoapRunning(){
        $command = "sudo svstat /etc/service/pypo-liquidsoap";
        exec($command, $output, $result);

        $key_value = split(":", $output[0]);
        $value = trim($key_value[1]);

        $status = AirtimeCheck::CHECK_FAILED;
        $pos = strpos($value, "pid");
        if ($pos !== false){
            $start = $pos + 4;
            $end = strpos($value, ")", $start);
            $status = substr($value, $start, $end-$start);
        }

        echo "LIQUIDSOAP_PID=".$status.PHP_EOL;

        $status = AirtimeCheck::CHECK_FAILED;
        $pos = strpos($value, ")");
        if ($pos !== false){
            $start = $pos + 2;
            $end = strpos($value, " ", $start);
            $status = substr($value, $start, $end-$start);

        }

        echo "LIQUIDSOAP_RUNNING_SECONDS=".$status.PHP_EOL;
    }

    public static function CheckConfigFilesExist(){
        //echo PHP_EOL."Verifying Config Files in /etc/airtime".PHP_EOL;
        $confFiles = array("airtime.conf",
                            "liquidsoap.cfg",
                            "pypo.cfg",
                            "recorder.cfg");

        $allFound = AirtimeCheck::CHECK_OK;

        foreach ($confFiles as $cf){
            $fullPath = "/etc/airtime/$cf";
            if (!file_exists($fullPath)){
                $allFound = AirtimeCheck::CHECK_FAILED;
            }
        }

        echo "AIRTIME_CONFIG_FILES=$allFound".PHP_EOL;
        
    }

    public static function GetAirtimeConf(){
        $ini = parse_ini_file("/etc/airtime/airtime.conf", true);

        if ($ini === false){
            echo "Error reading /etc/airtime/airtime.conf.".PHP_EOL;
            exit;
        }

        return $ini;
    }

    public static function GetPypoCfg(){
        $ini = parse_ini_file("/etc/airtime/pypo.cfg", false);

        if ($ini === false){
            echo "Error reading /etc/airtime/pypo.cfg.".PHP_EOL;
            exit;
        }

        return $ini;
    }

    public static function GetDbConnection($airtimeIni){
        $host = $airtimeIni["database"]["host"];
        $dbname = $airtimeIni["database"]["dbname"];
        $dbuser = $airtimeIni["database"]["dbuser"];
        $dbpass = $airtimeIni["database"]["dbpass"];
        
        $dbconn = pg_connect("host=$host port=5432 dbname=$dbname user=$dbuser password=$dbpass");

        if ($dbconn === false){
            $status = AirtimeCheck::CHECK_FAILED;
        } else {
            $status = AirtimeCheck::CHECK_OK;
        }

        echo "TEST_PGSQL_DATABASE=$status".PHP_EOL;
    }
    
    public static function PythonLibrariesInstalled(){
        $command = "pip freeze | grep kombu";
        exec($command, $output, $result);
        
        $status = AirtimeCheck::CHECK_FAILED;
        if (count($output[0]) > 0){
            $key_value = split("==", $output[0]);
            $status = trim($key_value[1]);
        }
        
        echo "PYTHON_KOMBU_VERSION=$status".PHP_EOL;
        
        unset($output);
        $command = "pip freeze | grep poster";
        exec($command, $output, $result);
            
        $status = AirtimeCheck::CHECK_FAILED;
        if (count($output[0]) > 0){
            $key_value = split("==", $output[0]);
            $status = trim($key_value[1]);
        }
        
        echo "PYTHON_POSTER_VERSION=$status".PHP_EOL;
    }

    public static function CheckDbTables(){
        
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
    
    public static function CheckRabbitMqConnection($airtimeIni){
        try {
            $status = AirtimeCheck::CHECK_OK;
            $conn = new AMQPConnection($airtimeIni["rabbitmq"]["host"],
                                             $airtimeIni["rabbitmq"]["port"],
                                             $airtimeIni["rabbitmq"]["user"],
                                             $airtimeIni["rabbitmq"]["password"]);
        } catch (Exception $e){
            $status = AirtimeCheck::CHECK_FAILED;
        }
        
        echo "TEST_RABBITMQ_SERVER=$status".PHP_EOL;
    }

    public static function GetAirtimeServerVersion($pypoCfg){

        $baseUrl = $pypoCfg["base_url"];
        $basePort = $pypoCfg["base_port"];
        $apiKey = "%%api_key%%";

        $url = "http://$baseUrl:$basePort/api/version/api_key/$apiKey";
        echo "AIRTIME_VERSION_URL=$url".PHP_EOL;

        $apiKey = $pypoCfg["api_key"];
        $url = "http://$baseUrl:$basePort/api/version/api_key/$apiKey";
       
        $rh = fopen($url, "r");

        if ($rh !== false){
            while (($buffer = fgets($rh)) !== false) {
                $json = json_decode(trim($buffer), true);
                if (!is_null($json)){
                    $version = $json["version"];
                    echo "AIRTIME_VERSION_STRING=$version".PHP_EOL;
                }
            }
        }
    }

    public static function CheckApacheVHostFiles(){
        $fileNames = array("/etc/apache2/sites-available/airtime",
                        "/etc/apache2/sites-enabled/airtime");

        $status = AirtimeCheck::CHECK_OK;

        foreach ($fileNames as $fn){
            if (!file_exists($fn)){
                $status = AirtimeCheck::CHECK_FAILED;
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

        echo "OS_TYPE=$os_string".PHP_EOL;
    }
}


// error handler function
function myErrorHandler($errno, $errstr, $errfile, $errline)
{
    if ($errno == E_WARNING){
        if (strpos($errstr, "401") !== false){
            echo "\t\tServer is running but could not find Airtime".PHP_EOL;
        } else if (strpos($errstr, "Connection refused") !== false){
            echo "\t\tServer does not appear to be running".PHP_EOL;
        } else {
            //echo $errstr;
        } 
    }

    /* Don't execute PHP internal error handler */
    return true;
}
