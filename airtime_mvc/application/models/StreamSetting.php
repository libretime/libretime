<?php
class Application_Model_StreamSetting {
    
    public static function SetValue($key, $value, $type){
        global $CC_CONFIG, $CC_DBC;

        $key = pg_escape_string($key);
        $value = pg_escape_string($value);

        //Check if key already exists
        $sql = "SELECT COUNT(*) FROM cc_stream_setting"
        ." WHERE keyname = '$key'";
        
        $result = $CC_DBC->GetOne($sql);

        if($result == 1) {
            $sql = "UPDATE cc_stream_setting"
            ." SET value = '$value', type='$type'"
            ." WHERE keyname = '$key'";
        } else {
            $sql = "INSERT INTO cc_stream_setting (keyname, value, type)"
            ." VALUES ('$key', '$value', '$type')";
        }
        
        return $CC_DBC->query($sql);
    }
    
    public static function GetValue($key){
        global $CC_CONFIG, $CC_DBC;
        
        //Check if key already exists
        $sql = "SELECT COUNT(*) FROM cc_stream_setting"
        ." WHERE keyname = '$key'";
        $result = $CC_DBC->GetOne($sql);

        if ($result == 0)
            return "";
        else {
            $sql = "SELECT value FROM cc_stream_setting"
            ." WHERE keyname = '$key'";
            
            $result = $CC_DBC->GetOne($sql);
            return $result;
        }
    }

    /* Returns the id's of all streams that are enabled in an array. An
     * example of the array returned in JSON notation is ["s1", "s2", "s3"] */
    public static function getEnabledStreamIds(){
        global $CC_DBC;
        $sql = "SELECT * "
                ."FROM cc_stream_setting "
                ."WHERE keyname LIKE '%_enable' "
                ."AND value='true'";

        $rows = $CC_DBC->getAll($sql);
        $ids = array();

        foreach ($rows as $row){
            $ids[] = substr($row["keyname"], 0, strpos($row["keyname"], "_"));
        }

        //Logging::log(print_r($ids, true));
        
        return $ids;
    }
    
    /* Retruns only global data as array*/
    public static function getGlobalData(){
        global $CC_DBC;
        $sql = "SELECT * "
        ."FROM cc_stream_setting "
        ."WHERE keyname IN ('output_sound_device', 'icecast_vorbis_metadata')";
        
        $rows = $CC_DBC->getAll($sql);
        $data = array();
        
        foreach($rows as $row){
        $data[$row["keyname"]] = $row["value"];
        }
        
        return $data;
    }
    /* Returns all information related to a specific stream. An example
     * of a stream id is 's1' or 's2'. */
    public static function getStreamData($p_streamId){
        global $CC_DBC;
        $sql = "SELECT * "
                ."FROM cc_stream_setting "
                ."WHERE keyname LIKE '${p_streamId}_%'";

        $rows = $CC_DBC->getAll($sql);
        $data = array();

        foreach($rows as $row){
            $data[$row["keyname"]] = $row["value"];
        }

        return $data;
    }
    
    public static function getStreamSetting(){
        global $CC_DBC;
        $sql = "SELECT *"
                ." FROM cc_stream_setting"
                ." WHERE keyname not like '%_error'";

        $rows = $CC_DBC->getAll($sql);
        
        $exists = array();
        
        foreach($rows as $r){
            if($r['keyname'] == 'master_live_stream_port'){
                $exists['master_live_stream_port'] = true;
            }elseif($r['keyname'] == 'master_live_stream_mp'){
                $exists['master_live_stream_mp'] = true;
            }elseif($r['keyname'] == 'dj_live_stream_port'){
                $exists['dj_live_stream_port'] = true;
            }elseif($r['keyname'] == 'dj_live_stream_mp'){
                $exists['dj_live_stream_mp'] = true;
            }
        }
        
        Logging::log(print_r($exits, true));
        
        if(!isset($exists["master_live_stream_port"])){
            $rows[] = (array("keyname" =>"master_live_stream_port", "value"=>self::GetMasterLiveSteamPort(), "type"=>"integer"));
        }
        if(!isset($exists["master_live_stream_mp"])){
            $rows[] = (array("keyname" =>"master_live_stream_mp", "value"=>self::GetMasterLiveSteamMountPoint(), "type"=>"string"));
        }
        if(!isset($exists["dj_live_stream_port"])){
            $rows[] = (array("keyname" =>"dj_live_stream_port", "value"=>self::GetDJLiveSteamPort(), "type"=>"integer"));
        }
        if(!isset($exists["dj_live_stream_mp"])){
            $rows[] = (array("keyname" =>"dj_live_stream_mp", "value"=>self::GetDJLiveSteamMountPoint(), "type"=>"string"));
        }
        Logging::log(print_r($rows, true));
        return $rows;
    }
    
    /*
     * function that take all the information of stream and sets them.
     * This is used by stream setting via UI.
     * 
     * @param $data - array that contains all the data. $data is [][] which
     * contains multiple stream information
     */
    public static function setStreamSetting($data){
        global $CC_DBC;
        
        foreach ($data as $key=>$d) {
            if ($key == "output_sound_device" || $key == "icecast_vorbis_metadata") {
                $v = $d == 1?"true":"false";
                $sql = "UPDATE cc_stream_setting SET value='$v' WHERE keyname='$key'";
                $CC_DBC->query($sql);
            } else if ($key == "output_sound_device_type") {
                $sql = "UPDATE cc_stream_setting SET value='$d' WHERE keyname='$key'";
                $CC_DBC->query($sql);
            } else if ($key == "streamFormat"){
                // this goes into cc_pref table
                Logging::log("Insert stream label format $d");
                Application_Model_Preference::SetStreamLabelFormat($d);
            } else if (is_array($d)) {
                $temp = explode('_', $key);
                $prefix = $temp[0];
                foreach ($d as $k=>$v) {
                    $keyname = $prefix . "_" . $k;
                    if ($k == 'enable') {
                        $v = $d['enable'] == 1 ? 'true' : 'false';
                    }
                    $v = trim($v);
                    $sql = "UPDATE cc_stream_setting SET value='$v' WHERE keyname='$keyname'";
                    $CC_DBC->query($sql);
                }
            } else {
                Logging::log("Warning unexpected value: ".$key);
            }
        }
    }
    
    /*
     * Sets indivisual stream setting.
     * 
     * $data - data array. $data is [].
     */
    public static function setIndivisualStreamSetting($data){
        global $CC_DBC;
        
        foreach($data as $keyname => $v){
            $sql = "UPDATE cc_stream_setting SET value='$v' WHERE keyname='$keyname'";
            $CC_DBC->query($sql);
        }
    }
    
    /*
     * Stores liquidsoap status if $boot_time > save time.
     * save time is the time that user clicked save on stream setting page
     */
    public static function setLiquidsoapError($stream_id, $msg, $boot_time=null){
        global $CC_DBC;
        
        $update_time = Application_Model_Preference::GetStreamUpdateTimestemp();
        if($boot_time == null || $boot_time > $update_time ){
            $keyname = "s".$stream_id."_liquidsoap_error";
            $sql = "SELECT COUNT(*) FROM cc_stream_setting"
                ." WHERE keyname = '$keyname'";
            $result = $CC_DBC->GetOne($sql);
            if ($result == 1){
                $sql = "UPDATE cc_stream_setting"
                    ." SET value = '$msg'"
                    ." WHERE keyname = '$keyname'";
            }else{
                $sql = "INSERT INTO cc_stream_setting (keyname, value, type)"
                    ." VALUES ('$keyname', '$msg', 'string')";
            }
            $res = $CC_DBC->query($sql);
        }
    }
    
    public static function getLiquidsoapError($stream_id){
        global $CC_DBC;
        
        $keyname = "s".$stream_id."_liquidsoap_error";
        $sql = "SELECT value FROM cc_stream_setting"
            ." WHERE keyname = '$keyname'";
        $result = $CC_DBC->GetOne($sql);
        
        return $result;
    }
    
    public static function getStreamEnabled($stream_id){
        global $CC_DBC;
        
        $keyname = "s" . $stream_id . "_enable";
        $sql = "SELECT value FROM cc_stream_setting"
        ." WHERE keyname = '$keyname'";
        $result = $CC_DBC->GetOne($sql);
        if ($result == 'false') {
            $result = false;
        } else {
            $result = true;
        }
        return $result;
    }
    
    /*
     * Only returns info that is needed for data collection
     * returns array('s1'=>array(keyname=>value))
     */
    public static function getStreamInfoForDataCollection(){
        global $CC_DBC;
        
        $out = array();
        $enabled_stream = self::getEnabledStreamIds();
        
        foreach($enabled_stream as $stream){
            $keys = "'".$stream."_output', "."'".$stream."_type', "."'".$stream."_bitrate', "."'".$stream."_host'";
            
            $sql = "SELECT keyname, value FROM cc_stream_setting"
            ." WHERE keyname IN ($keys)";
            
            $rows = $CC_DBC->getAll($sql);
            $info = array();
            foreach($rows as $r){
                $temp = explode("_", $r['keyname']);
                $info[$temp[1]] = $r['value'];
                $out[$stream] = $info; 
            }
        }
        return $out;
    }
    
    public static function SetMasterLiveSteamPort($value){
        self::SetValue("master_live_stream_port", $value, "integer");
    }
    
    public static function GetMasterLiveSteamPort(){
        return self::GetValue("master_live_stream_port");
    }
    
    public static function SetMasterLiveSteamMountPoint($value){
        self::SetValue("master_live_stream_mp", $value, "string");
    }
    
    public static function GetMasterLiveSteamMountPoint(){
        return self::GetValue("master_live_stream_mp");
    }
    
    public static function SetDJLiveSteamPort($value){
        self::SetValue("dj_live_stream_port", $value, "integer");
    }
    
    public static function GetDJLiveSteamPort(){
        return self::GetValue("dj_live_stream_port");
    }
    
    public static function SetDJLiveSteamMountPoint($value){
        self::SetValue("dj_live_stream_mp", $value, "string");
    }
    
    public static function GetDJLiveSteamMountPoint(){
        return self::GetValue("dj_live_stream_mp");
    }
}
