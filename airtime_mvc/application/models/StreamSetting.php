<?php
class Application_Model_StreamSetting {

    /* Returns the id's of all streams that are enabled in an array. An
     * example of the array returned in JSON notation is ["s1", "s2", "s3"] */
    public static function getEnabledStreamIds(){
        global $CC_DBC;
        $sql = "SELECT * "
                ."FROM cc_stream_setting "
                ."WHERE keyname LIKE '%_output' "
                ."AND value != 'disabled'";

        $rows = $CC_DBC->getAll($sql);
        $ids = array();

        foreach ($rows as $row){
            $ids[] = substr($row["keyname"], 0, strpos($row["keyname"], "_"));
        }

        //Logging::log(print_r($ids, true));
        
        return $ids;
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
        return $rows;
    }
    
    public static function setStreamSetting($data){
        global $CC_DBC;
        foreach($data as $key=>$d){
            if($key == "output_sound_device"){
                $v = $d == 1?"true":"false";
                $sql = "UPDATE cc_stream_setting SET value='$v' WHERE keyname='$key'";
                $CC_DBC->query($sql);
            }
            else{
                $temp = explode('_', $key);
                $prefix = $temp[0];
                foreach($d as $k=>$v){
                    $keyname = $prefix."_".$k;
                    if( $k == 'output'){
                        if( $d["enable"] == 0){
                            $v = 'disabled';
                        }
                    }
                    $v = trim($v);
                    $sql = "UPDATE cc_stream_setting SET value='$v' WHERE keyname='$keyname'";
                    $CC_DBC->query($sql);
                }
            }
        }
    }
    
    public static function setLiquidsoapError($stream_id, $msg){
        global $CC_DBC;
        
        $keyname = "s".$stream_id."_liquidsoap_error";
        $sql = "SELECT COUNT(*) FROM cc_stream_setting"
            ." WHERE keyname = '$keyname'";
        $result = $CC_DBC->GetOne($sql);
        Logging::log("setError");
        if ($result == 1){
            $sql = "UPDATE cc_stream_setting"
                ." SET value = '$msg'"
                ." WHERE keyname = '$keyname'";
        }else{
            $sql = "INSERT INTO cc_stream_setting (keyname, value, type)"
                ." VALUES ('$keyname', '$msg', 'string')";
        }
        Logging::log($sql);
        $res = $CC_DBC->query($sql);
        Logging::log($res);
    }
    
    public static function getLiquidsoapError($stream_id){
        global $CC_DBC;
        
        $keyname = "s".$stream_id."_liquidsoap_error";
        $sql = "SELECT value FROM cc_stream_setting"
            ." WHERE keyname = '$key'";
        $result = $CC_DBC->GetOne($sql);
        
        return $result;
    }
}
