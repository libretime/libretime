<?php

class Application_Model_DateHelper
{
   private $_timestamp;
    
   function __construct() {
        $this->_timestamp = date("U");
   }
   
   function getDate(){
       return date("Y-m-d H:i:s", $this->_timestamp);
   }
   
   function setDate($dateString){
        $this->_timestamp = strtotime($dateString);
   }
   
   function getNowDayStartDiff(){
       $dayStartTS = strtotime(date("Y-m-d", $this->_timestamp));
       return $this->_timestamp - $dayStartTS; 
   }

   function getNowDayEndDiff(){
       $dayEndTS = strtotime(date("Y-m-d", $this->_timestamp+(86400)));
       return $dayEndTS - $this->_timestamp;
   }
   
    public static function ConvertMSToHHMMSSmm($time){
        $hours = floor($time / 3600000);
        $time -= 3600000*$hours;
            
        $minutes = floor($time / 60000);
        $time -= 60000*$minutes;
        
        $seconds = floor($time / 1000);
        $time -= 1000*$seconds;
        
        $ms = $time;
        
        if (strlen($hours) == 1)
            $hours = "0".$hours;
        if (strlen($minutes) == 1)
            $minutes = "0".$minutes;
        if (strlen($seconds) == 1)
            $seconds = "0".$seconds;
                    
        return $hours.":".$minutes.":".$seconds.".".$ms;
    }
}

