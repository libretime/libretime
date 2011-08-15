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
}