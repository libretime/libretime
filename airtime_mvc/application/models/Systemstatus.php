<?php

class Application_Model_Systemstatus
{

    private function getCheckSystemResults(){
        exec("airtime-check-system", $output);

        //require_once "/usr/lib/airtime/utils/airtime-check-system.php";

        $status = array();
        foreach($output as $row){
            $row = trim($row);
            if (substr_count($row, "=") == 1 && "--" != substr($row, 0, 2)){
                list($key, $value) = array_map("trim", explode("=", $row));
                $status[$key] = $value;
            }
        }

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

        return $results;
    }

    private function convertRunTimeToPassFail($runTime){
        return $runTime > 3 ? "Pass" : "Fail";
    }
}
