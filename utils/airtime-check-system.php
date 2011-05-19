<?php
require_once '../airtime_mvc/library/php-amqplib/amqp.inc';

//Step 1: Verify all config files in /etc/airtime/ exist and have size > 0 bytes.

echo "*** Airtime System Check Script ***".PHP_EOL;

set_error_handler("myErrorHandler");

AirtimeCheck::CheckOsTypeVersion();

AirtimeCheck::CheckConfigFilesExist();
$airtimeIni = AirtimeCheck::GetAirtimeConf();
$pypoCfg = AirtimeCheck::GetPypoCfg();

AirtimeCheck::GetDbConnection($airtimeIni);
AirtimeCheck::CheckRabbitMqVersion();
AirtimeCheck::CheckRabbitMqConnection($airtimeIni);

AirtimeCheck::CheckApacheVHostFiles();

AirtimeCheck::GetAirtimeServerVersion($pypoCfg);


class AirtimeCheck{

    public static function CheckPypoRunning(){
        //check using svstat
    }

    public static function CheckLiquidsoapRunning(){
        //connect via telnet
    }

    public static function CheckConfigFilesExist(){
        echo PHP_EOL."Verifying Config Files in /etc/airtime".PHP_EOL;
        $confFiles = array("airtime.conf",
                            "liquidsoap.cfg",
                            "pypo.cfg",
                            "recorder.cfg");

        foreach ($confFiles as $cf){
            $fullPath = "/etc/airtime/$cf";
            echo "\t".$fullPath." ... ";
            if (file_exists($fullPath)){
                echo "[OK]".PHP_EOL;
            } else {
                echo "Not Found!".PHP_EOL;
                exit;
            }
        }
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

        echo PHP_EOL."Verifying Airtime Database".PHP_EOL;

        if ($dbconn === false){
            echo "\tConnection ... [Failed!]".PHP_EOL;
            exit;
        } else {
            echo "\tConnection ... [OK]".PHP_EOL;
        }
    }

    public static function CheckDbTables(){
        
    }

    /* The function tests for whether the rabbitmq-server package is
     * installed. RabbitMQ could be installed manually via tarball
     * and this function will fail to detect it! Unfortunately there
     * seems to be no other way to check RabbitMQ version. Will update
     * this function if I find a more universal solution. */
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
    
    public static function CheckRabbitMqConnection($airtimeIni){
        echo PHP_EOL."Verifying RabbitMQ Service".PHP_EOL;
        try {
            $conn = new AMQPConnection($airtimeIni["rabbitmq"]["host"],
                                             $airtimeIni["rabbitmq"]["port"],
                                             $airtimeIni["rabbitmq"]["user"],
                                             $airtimeIni["rabbitmq"]["password"]);
        } catch (Exception $e){
            echo "\tConnection ... [Failed!]".PHP_EOL;
            exit;
        }
        echo "\tConnection ... [OK]".PHP_EOL;
    }

    public static function GetAirtimeServerVersion($pypoCfg){

        echo PHP_EOL."Checking Airtime Server Version".PHP_EOL;
        echo "\tConnecting to http://localhost/api/version".PHP_EOL;

        $baseUrl = $pypoCfg["base_url"];
        $basePort = $pypoCfg["base_port"];
        $apiKey = $pypoCfg["api_key"];
       
        $rh = fopen("http://$baseUrl:$basePort/api/version/api_key/$apiKey", "r");

        if ($rh !== false){
            while (($buffer = fgets($rh)) !== false) {
                $json = json_decode(trim($buffer), true);
                if (is_null($json)){
                    echo "NULL STRING".PHP_EOL;

                } else {
                    echo "\t\tAirtime Version ".$json["version"]." ... [OK] ".PHP_EOL;
                }
            }
        } else {
            echo "\t\tConnection ... [Failed!]".PHP_EOL;
            exit;           
        }

        function handleError($errno, $errstr,$error_file,$error_line)
        { 
         echo "<b>Error:</b> [$errno] $errstr - $error_file:$error_line";
         echo "<br />";
         echo "Terminating PHP Script";
         die();
        }
        
    }

    public static function CheckApacheVHostFiles(){
        echo PHP_EOL."Checking for Airtime Apache config files".PHP_EOL;

        $fileNames = array("/etc/apache2/sites-available/airtime",
                        "/etc/apache2/sites-enabled/airtime");

        foreach ($fileNames as $fn){
            echo "\tChecking $fn ... ";
            if (file_exists($fn)){
                echo '[OK]'.PHP_EOL;
            } else {
                echo "[Failed!]".PHP_EOL;
                exit;
            }
        }

        //Since apache2 loads config files in alphabetical order
        //from the sites-enabled directory, we need to check if
        //airtime is lexically the first file in this directory.

        //get sorted array of files
        $arr = scandir("/etc/apache2/sites-enabled");

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
    }

    public static function CheckOsTypeVersion(){
        echo PHP_EOL."Checking operating system.".PHP_EOL;

        if (file_exists("/etc/lsb-release")){

            $ini = parse_ini_file("/etc/lsb-release", false);
            
            //lsb-release existing implies a Ubuntu installation.
            echo "\tUbuntu based distribution found: ".$ini["DISTRIB_DESCRIPTION"].PHP_EOL;
        } else if (file_exists("/etc/debian_version")) {
            //if lsb-release does not exist, lets check if we are
            //running on Debian. Look for file /etc/debian_version
            $handler = fopen("/etc/debian_version", "r");

            $version = trim(fgets($handler));
            
            echo "\tDebian based distribution found: ".$version.PHP_EOL;
        } else {
            echo "\tWARNING: Unknown OS Type".PHP_EOL;
        }
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
