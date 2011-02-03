<?php

class Application_Model_Preference
{

    public static function UpdateStationName($name, $id){
        global $CC_CONFIG, $CC_DBC;
        
        //Check if key already exists
        $sql = "SELECT COUNT(*) FROM cc_pref"
        ." WHERE keystr = 'station_name'";
        $result = $CC_DBC->GetOne($sql);
        
        if ($result == 1){
            $sql = "UPDATE cc_pref"
            ." SET subjid = $id, valstr = '$name'"
            ." WHERE keystr = 'station_name'";            
        } else {
            $sql = "INSERT INTO cc_pref (subjid, keystr, valstr)"
            ." VALUES ($id, 'station_name', '$name')";
        }
        return $CC_DBC->query($sql);
    }
    
    public static function GetStationName(){
        global $CC_CONFIG, $CC_DBC;
        //Check if key already exists
        $sql = "SELECT COUNT(*) FROM cc_pref"
        ." WHERE keystr = 'station_name'";
        $result = $CC_DBC->GetOne($sql);
        
        if ($result == 0)
            return "Airtime";
        else {
            $sql = "SELECT valstr FROM cc_pref"
            ." WHERE keystr = 'station_name'";
            $result = $CC_DBC->GetOne($sql);
            return $result." - Airtime";
        }
        
    }

}

