<?php
class Application_Model_StreamSetting {
    public function __construct(){
    
    }
    
    public static function getStreamSetting(){
        global $CC_DBC;
        $sql = "SELECT *"
                ." FROM cc_stream_setting";

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
                        $keyname = $k."_".$prefix;
                        if( $d["enable"] == 0){
                            $v = 'disabled';
                        }
                    }
                    $sql = "UPDATE cc_stream_setting SET value='$v' WHERE keyname='$keyname'";
                    $CC_DBC->query($sql);
                }
            }
        }
    }
}