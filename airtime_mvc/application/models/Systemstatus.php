<?php

class Application_Model_Systemstatus
{

    public static function GetMonitStatus($p_ip){

        $url = "http://$p_ip:2812/_status?format=xml";
        
        $ch = curl_init(); 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
        curl_setopt($ch, CURLOPT_URL, $url);  
        curl_setopt($ch, CURLOPT_USERPWD, "admin:monit");
        $result = curl_exec($ch);
        curl_close($ch);

        $xmlDoc = new DOMDocument();
        $xmlDoc->loadXML($result);

        return $xmlDoc->documentElement;
    }
    
    public static function ExtractServiceInformation($p_docRoot, $p_serviceName){
    
        $data = array("process_id"=>"UNKNOWN",
                      "uptime_seconds"=>"UNKNOWN",
                      "memory_perc"=>"UNKNOWN",
                      "memory_kb"=>"UNKNOWN",
                      "cpu_perc"=>"UNKNOWN"
                      );

        foreach ($p_docRoot->getElementsByTagName("service") AS $item)
        {
            if ($item->getElementsByTagName("name")->item(0)->nodeValue == $p_serviceName){
                $data["process_id"] = $item->getElementsByTagName("pid")->item(0)->nodeValue;
                $data["uptime_seconds"] = $item->getElementsByTagName("uptime")->item(0)->nodeValue;
                $data["memory_perc"] = $item->getElementsByTagName("memory")->item(0)->getElementsByTagName("percenttotal")->item(0)->nodeValue;
                $data["memory_kb"] = $item->getElementsByTagName("memory")->item(0)->getElementsByTagName("kilobytetotal")->item(0)->nodeValue;
                $data["cpu_perc"] = $item->getElementsByTagName("cpu")->item(0)->getElementsByTagName("percent")->item(0)->nodeValue;
                break;
            }
        }
        
        return $data;
    }

    public static function GetPlatformInfo(){
        $docRoot = self::GetMonitStatus("localhost");

        $data = array("release"=>"UNKNOWN",
                      "machine"=>"UNKNOWN",
                      "memory"=>"UNKNOWN",
                      "swap"=>"UNKNOWN");

        foreach ($docRoot->getElementsByTagName("platform") AS $item)
        {
            $data["release"] = $item->getElementsByTagName("release")->item(0)->nodeValue;
            $data["machine"] = $item->getElementsByTagName("machine")->item(0)->nodeValue;
            $data["memory"] = $item->getElementsByTagName("memory")->item(0)->nodeValue;
            $data["swap"] = $item->getElementsByTagName("swap")->item(0)->nodeValue;         
        }
        
        return $data;

    }

    public static function GetPypoStatus(){

        $component = CcComponentQuery::create()->findOneByDbName("pypo");
        $ip = $component->getDbIp();
        
        $docRoot = self::GetMonitStatus($ip);
        $data = self::ExtractServiceInformation($docRoot, "airtime-playout");

        return $data;
    }
    
    public static function GetLiquidsoapStatus(){

        $component = CcComponentQuery::create()->findOneByDbName("pypo");
        $ip = $component->getDbIp();
        
        $docRoot = self::GetMonitStatus($ip);
        $data = self::ExtractServiceInformation($docRoot, "airtime-liquidsoap");

        return $data;
    }
    
    public static function GetShowRecorderStatus(){

        $component = CcComponentQuery::create()->findOneByDbName("show-recorder");
        $ip = $component->getDbIp();
        
        $docRoot = self::GetMonitStatus($ip);
        $data = self::ExtractServiceInformation($docRoot, "airtime-show-recorder");

        return $data;
    }
    
    public static function GetMediaMonitorStatus(){

        $component = CcComponentQuery::create()->findOneByDbName("media-monitor");
        $ip = $component->getDbIp();
        
        $docRoot = self::GetMonitStatus($ip);
        $data = self::ExtractServiceInformation($docRoot, "airtime-media-monitor");

        return $data;
    }
    
    public static function GetIcecastStatus(){     
        $docRoot = self::GetMonitStatus("localhost");
        $data = self::ExtractServiceInformation($docRoot, "icecast2");

        return $data;
    }

    public static function GetRabbitMqStatus(){     
        $docRoot = self::GetMonitStatus("localhost");
        $data = self::ExtractServiceInformation($docRoot, "rabbitmq-server");

        return $data;
    }
    
    
    public static function GetAirtimeVersion(){
        return AIRTIME_VERSION;
    }
/*


    private function getCheckSystemResults(){
        //exec("airtime-check-system", $output);

        require_once "/usr/lib/airtime/utils/airtime-check-system.php";
        $arrs = AirtimeCheck::CheckAirtimeDaemons();

        $status = array("AIRTIME_VERSION" => AIRTIME_VERSION);
        foreach($arrs as $arr){
            $status[$arr[0]] = $arr[1]; 
        }

        $storDir = MusicDir::getStorDir()->getDirectory();

        $freeSpace = disk_free_space($storDir);
        $totalSpace = disk_total_space($storDir);

        $status["DISK_SPACE"] = sprintf("%01.3f%%", $freeSpace/$totalSpace*100);
        
        return $status;
    }

    public function getResults(){
        $keyValues = $this->getCheckSystemResults();

        $results = array();
        $key = "AIRTIME_VERSION";
        $results[$key] = array("Airtime Version", $keyValues[$key], false);

        $triplets = array(array("PLAYOUT_ENGINE_RUNNING_SECONDS", "Playout Engine Status", true),
                array("LIQUIDSOAP_RUNNING_SECONDS", "Liquidsoap Status", true),
                array("MEDIA_MONITOR_RUNNING_SECONDS", "Media-Monitor Status", true),
                array("SHOW_RECORDER_RUNNING_SECONDS", "Show-Recorder Status", true));
                
        foreach($triplets as $triple){
            list($key, $desc, $downloadLog) = $triple;
            $results[$key] = array($desc, $this->convertRunTimeToPassFail($keyValues[$key]), $downloadLog); 
        }

        $key = "DISK_SPACE";
        $results[$key] = array("Disk Space Free: ", $keyValues[$key], false);

        return $results;
    }

    private function convertRunTimeToPassFail($runTime){
        return $runTime > 3 ? "Pass" : "Fail";
    }
    
    */
}
