<?php

class Application_Model_Systemstatus
{

    public static function GetPypoStatus(){

        RabbitMq::SendMessageToPypo("get_status", array());
        
        return array(
            "process_id"=>500,
            "uptime_seconds"=>3600
        );
    }
    
    public static function GetLiquidsoapStatus(){
        return array(
            "process_id"=>500,
            "uptime_seconds"=>3600
        );
    }
    
    public static function GetShowRecorderStatus(){
        return array(
            "process_id"=>500,
            "uptime_seconds"=>3600
        );
    }
    
    public static function GetMediaMonitorStatus(){
        return array(
            "process_id"=>500,
            "uptime_seconds"=>3600
        );
    }
    
    public static function GetIcecastStatus(){
        return array(
            "process_id"=>500,
            "uptime_seconds"=>3600
        );
    }
    
    public static function GetAirtimeVersion(){
        return AIRTIME_VERSION;
    }



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
}
