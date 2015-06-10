<?php

class Application_Model_Systemstatus
{

    public static function GetMonitStatus($p_ip)
    {
        $CC_CONFIG = Config::getConfig();
//         $monit_user = $CC_CONFIG['monit_user'];
//         $monit_password = $CC_CONFIG['monit_password'];

        $url = "http://$p_ip:2812/_status?format=xml";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
//         curl_setopt($ch, CURLOPT_USERPWD, "$monit_user:$monit_password");
        //wait a max of 3 seconds before aborting connection attempt
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);
        $result = curl_exec($ch);

        $info = curl_getinfo($ch);
        curl_close($ch);

        $docRoot = null;
        if ($result !== FALSE && $info["http_code"] === 200) {
            if ($result != "") {
                $xmlDoc = new DOMDocument();
                $xmlDoc->loadXML($result);
                $docRoot = $xmlDoc->documentElement;
            }
        }

        return $docRoot;
    }

    public static function ExtractServiceInformation($p_docRoot, $p_serviceName)
    {
        $starting = array(
                        "name"=>"",
                        "process_id"=>"STARTING...",
                        "uptime_seconds"=>"-1",
                        "status"=>0,
                        "memory_perc"=>"0%",
                        "memory_kb"=>"0",
                        "cpu_perc"=>"0%");

        $notMonitored = array(
                        "name"=>$p_serviceName,
                        "process_id"=>"NOT MONITORED",
                        "uptime_seconds"=>"1",
                        "status"=>1,
                        "memory_perc"=>"0%",
                        "memory_kb"=>"0",
                        "cpu_perc"=>"0%"
                      );

        $notRunning = array(
                        "name"=>$p_serviceName,
                        "process_id"=>"FAILED",
                        "uptime_seconds"=>"-1",
                        "status"=>0,
                        "memory_perc"=>"0%",
                        "memory_kb"=>"0",
                        "cpu_perc"=>"0%"
                      );
        $data = $notRunning;

        if (!is_null($p_docRoot)) {
            foreach ($p_docRoot->getElementsByTagName("service") AS $item) {
                if ($item->getElementsByTagName("name")->item(0)->nodeValue == $p_serviceName) {

                    $monitor = $item->getElementsByTagName("monitor");
                    if ($monitor->length > 0) {
                        $status = $monitor->item(0)->nodeValue;
                        if ($status == "2") {
                            $data = $starting;
                        } elseif ($status == 1) {
                            //is monitored, but is it running?
                            $pid = $item->getElementsByTagName("pid");
                            if ($pid->length == 0) {
                                $data = $notRunning;
                            } else {
                                //running!
                            }
                        } elseif ($status == 0) {
                            $data = $notMonitored;
                        }
                    }

                    $process_id = $item->getElementsByTagName("name");
                    if ($process_id->length > 0) {
                        $data["name"] = $process_id->item(0)->nodeValue;
                    }

                    $process_id = $item->getElementsByTagName("pid");
                    if ($process_id->length > 0) {
                        $data["process_id"] = $process_id->item(0)->nodeValue;
                        $data["status"] = 0;
                    }

                    $uptime = $item->getElementsByTagName("uptime");
                    if ($uptime->length > 0) {
                        $data["uptime_seconds"] = $uptime->item(0)->nodeValue;
                    }

                    $memory = $item->getElementsByTagName("memory");
                    if ($memory->length > 0) {
                        $data["memory_perc"] = $memory->item(0)->getElementsByTagName("percenttotal")->item(0)->nodeValue."%";
                        $data["memory_kb"] = $memory->item(0)->getElementsByTagName("kilobytetotal")->item(0)->nodeValue;
                    }

                    $cpu = $item->getElementsByTagName("cpu");
                    if ($cpu->length > 0) {
                        $data["cpu_perc"] = $cpu->item(0)->getElementsByTagName("percent")->item(0)->nodeValue."%";
                    }
                    break;
                }
            }
        }

        return $data;
    }

    public static function GetPlatformInfo()
    {
        $keys = array("release", "machine", "memory", "swap");
        $data = array();
        foreach ($keys as $key) {
            $data[$key] = "UNKNOWN";
        }

        $docRoot = self::GetMonitStatus("localhost");
        if (!is_null($docRoot)) {
            foreach ($docRoot->getElementsByTagName("platform") AS $item) {
                foreach ($keys as $key) {
                    $keyElement = $item->getElementsByTagName($key);
                    if ($keyElement->length > 0) {
                        $data[$key] = $keyElement->item(0)->nodeValue;
                    }
                }
            }
        }

        return $data;
    }

    public static function GetPypoStatus()
    {
        $component = CcServiceRegisterQuery::create()->findOneByDbName("pypo");
        if (is_null($component)) {
            return null;
        } else {
            $ip = $component->getDbIp();

            $docRoot = self::GetMonitStatus($ip);
            $data = self::ExtractServiceInformation($docRoot, "airtime-playout");

            return $data;
        }
    }

    public static function GetLiquidsoapStatus()
    {
        $component = CcServiceRegisterQuery::create()->findOneByDbName("pypo");
        if (is_null($component)) {
            return null;
        } else {
            $ip = $component->getDbIp();

            $docRoot = self::GetMonitStatus($ip);
            $data = self::ExtractServiceInformation($docRoot, "airtime-liquidsoap");

            return $data;
        }
    }

    public static function GetMediaMonitorStatus()
    {
        $component = CcServiceRegisterQuery::create()->findOneByDbName("media-monitor");
        if (is_null($component)) {
            return null;
        } else {
            $ip = $component->getDbIp();

            $docRoot = self::GetMonitStatus($ip);
            $data = self::ExtractServiceInformation($docRoot, "airtime-media-monitor");

            return $data;
        }
    }

    public static function GetIcecastStatus()
    {
        $docRoot = self::GetMonitStatus("localhost");
        $data = self::ExtractServiceInformation($docRoot, "icecast2");

        return $data;
    }

    public static function GetRabbitMqStatus()
    {
        if (isset($_SERVER["RABBITMQ_HOST"])) {
            $rabbitmq_host = $_SERVER["RABBITMQ_HOST"];
        } else {
            $rabbitmq_host = "localhost";
        }
        $docRoot = self::GetMonitStatus($rabbitmq_host);
        $data = self::ExtractServiceInformation($docRoot, "rabbitmq-server");

        return $data;
    }

    public static function GetDiskInfo()
    {
        $partitions = array();

        //connect to DB and find how much total space user has allocated.
        $totalSpace = Application_Model_Preference::GetDiskQuota();

        $usedSpace = Application_Model_Preference::getDiskUsage();
        if (empty($usedSpace)) {
            $usedSpace = 0;
        }
        /* $path = $_SERVER['AIRTIME_BASE']."etc/airtime/num_bytes.ini";
        $arr = parse_ini_file($path);

        $usedSpace = 0;
        if ($arr !== false) {
            $usedSpace = $arr['num_bytes'];
        } */
        
        $partitions[$totalSpace] = new stdClass();
        $partitions[$totalSpace]->totalSpace = $totalSpace;
        $partitions[$totalSpace]->totalFreeSpace = $totalSpace - $usedSpace;
        //Logging::info($partitions[$totalSpace]->totalFreeSpace);

        return array_values($partitions);
    }

    public static function isDiskOverQuota()
    {
        $diskInfo = self::GetDiskInfo();
        $diskInfo = $diskInfo[0];
        $diskUsage = $diskInfo->totalSpace - $diskInfo->totalFreeSpace;
        if ($diskUsage > 0 && $diskUsage >= $diskInfo->totalSpace) {
            return true;
        }

        return false;
    }
}
