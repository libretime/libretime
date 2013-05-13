<?php
class Application_Model_StreamSetting
{
    public static function setValue($key, $value, $type)
    {
        $con = Propel::getConnection();

        // Check if key already exists
        $sql = "SELECT COUNT(*) FROM cc_stream_setting"
            ." WHERE keyname = :key";

        $stmt = $con->prepare($sql);
        $stmt->bindParam(':key', $key);
        
        if ($stmt->execute()) {
            $result = $stmt->fetchColumn(0);
        } else {
            $msg = implode(',', $stmt->errorInfo());
            throw new Exception("Error: $msg");
        }

        if ($result == 1) {
            $sql = "UPDATE cc_stream_setting"
            ." SET value = :value, type = :type"
            ." WHERE keyname = :key";
        } else {
            $sql = "INSERT INTO cc_stream_setting (keyname, value, type)"
            ." VALUES (:key, :value, :type)";
        }

        $stmt = $con->prepare($sql);
        $stmt->bindParam(':key', $key);
        $stmt->bindParam(':value', $value);
        $stmt->bindParam(':type', $type);
        
        if ($stmt->execute()) {
            //do nothing
        } else {
            $msg = implode(',', $stmt->errorInfo());
            throw new Exception("Error: $msg");
        }
    }

    public static function getValue($key)
    {
        $con = Propel::getConnection();
        
        //Check if key already exists
        $sql = "SELECT value FROM cc_stream_setting"
        ." WHERE keyname = :key";

        $stmt = $con->prepare($sql);
        $stmt->bindParam(':key', $key);
        
        if ($stmt->execute()) {
            $result = $stmt->fetchColumn(0);
        } else {
            $msg = implode(',', $stmt->errorInfo());
            throw new Exception("Error: $msg");
        }

        return $result ? $result : "";
    }

    /* Returns the id's of all streams that are enabled in an array. An
     * example of the array returned in JSON notation is ["s1", "s2", "s3"] */
    public static function getEnabledStreamIds()
    {
        $con = Propel::getConnection();
        $sql = "SELECT * "
                ."FROM cc_stream_setting "
                ."WHERE keyname LIKE '%_enable' "
                ."AND value='true'";

        $ids = array();
        
        $rows = Application_Common_Database::prepareAndExecute($sql, array(), 'all');

        foreach ($rows as $row) {
            $ids[] = substr($row["keyname"], 0, strpos($row["keyname"], "_"));
        }

        return $ids;
    }

    /* Returns only global data as array*/
    public static function getGlobalData()
    {
        $con = Propel::getConnection();
        $sql = "SELECT * "
            ."FROM cc_stream_setting "
            ."WHERE keyname IN ('output_sound_device', 'icecast_vorbis_metadata')";

        $rows = Application_Common_Database::prepareAndExecute($sql, array(), 'all');
        
        $data = array();

        foreach ($rows as $row) {
            $data[$row["keyname"]] = $row["value"];
        }

        return $data;
    }

    /* Returns all information related to a specific stream. An example
     * of a stream id is 's1' or 's2'. */
    public static function getStreamData($p_streamId)
    {
        $con = Propel::getConnection();
        $streamId = pg_escape_string($p_streamId);
        $sql = "SELECT * "
                ."FROM cc_stream_setting "
                ."WHERE keyname LIKE '{$streamId}_%'";

        $stmt = $con->prepare($sql);
        
        if ($stmt->execute()) {
            $rows = $stmt->fetchAll();
        } else {
            $msg = implode(',', $stmt->errorInfo());
            throw new Exception("Error: $msg");
        }

        $data = array();

        foreach ($rows as $row) {
            $data[$row["keyname"]] = $row["value"];
        }

        return $data;
    }

    /* Similar to getStreamData, but removes all sX prefixes to
     * make data easier to iterate over */
    public static function getStreamDataNormalized($p_streamId)
    {
        $con = Propel::getConnection();
        $streamId = pg_escape_string($p_streamId);
        $sql = "SELECT * "
                ."FROM cc_stream_setting "
                ."WHERE keyname LIKE '{$streamId}_%'";

        $stmt = $con->prepare($sql);
        
        if ($stmt->execute()) {
            $rows = $stmt->fetchAll();
        } else {
            $msg = implode(',', $stmt->errorInfo());
            throw new Exception("Error: $msg");
        }

        $data = array();

        foreach ($rows as $row) {
            list($id, $key) = explode("_", $row["keyname"], 2);
            $data[$key] = $row["value"];
        }

        return $data;
    }

    public static function getStreamSetting()
    {
        $con = Propel::getConnection();
        $sql = "SELECT *"
                ." FROM cc_stream_setting"
                ." WHERE keyname not like '%_error' AND keyname not like '%_admin_%'";

        $rows = Application_Common_Database::prepareAndExecute($sql, array(), 'all');

        $exists = array();

        foreach ($rows as $r) {
            if ($r['keyname'] == 'master_live_stream_port') {
                $exists['master_live_stream_port'] = true;
            } elseif ($r['keyname'] == 'master_live_stream_mp') {
                $exists['master_live_stream_mp'] = true;
            } elseif ($r['keyname'] == 'dj_live_stream_port') {
                $exists['dj_live_stream_port'] = true;
            } elseif ($r['keyname'] == 'dj_live_stream_mp') {
                $exists['dj_live_stream_mp'] = true;
            }
        }

        if (!isset($exists["master_live_stream_port"])) {
            $rows[] = array("keyname" =>"master_live_stream_port",
                            "value"=>self::getMasterLiveStreamPort(),
                            "type"=>"integer");
        }
        if (!isset($exists["master_live_stream_mp"])) {
            $rows[] = array("keyname" =>"master_live_stream_mp",
                            "value"=>self::getMasterLiveStreamMountPoint(),
                            "type"=>"string");
        }
        if (!isset($exists["dj_live_stream_port"])) {
            $rows[] = array("keyname" =>"dj_live_stream_port",
                            "value"=>self::getDjLiveStreamPort(),
                            "type"=>"integer");
        }
        if (!isset($exists["dj_live_stream_mp"])) {
            $rows[] = array("keyname" =>"dj_live_stream_mp",
                            "value"=>self::getDjLiveStreamMountPoint(),
                            "type"=>"string");
        }

        return $rows;
    }


    private static function saveStreamSetting($key, $value)
    {
        $stream_setting = CcStreamSettingQuery::create()->filterByDbKeyName($key)->findOne();
        if (is_null($stream_setting)) {
            throw new Exception("Keyname $key does not exist!");
        }

        $stream_setting->setDbValue($value);
        $stream_setting->save();
    }

    /*
     * function that take all the information of stream and sets them.
     * This is used by stream setting via UI.
     *
     * @param $data - array that contains all the data. $data is [][] which
     * contains multiple stream information
     */
    public static function setStreamSetting($data)
    {
        foreach ($data as $key => $d) {
            if ($key == "output_sound_device" || $key == "icecast_vorbis_metadata") {
                $v = ($d == 1) ? "true" : "false";

                self::saveStreamSetting($key, $v);
            } elseif ($key == "output_sound_device_type") {
                self::saveStreamSetting($key, $d);
            } elseif (is_array($d)) {
                $temp = explode('_', $key);
                $prefix = $temp[0];
                foreach ($d as $k => $v) {
                    $keyname = $prefix . "_" . $k;
                    if ($k == 'enable') {
                        $v = $d['enable'] == 1 ? 'true' : 'false';
                    }
                    $v = trim($v);
                    if ($k != 'admin_pass') {
                        self::saveStreamSetting($keyname, $v);
                    /* We use 'xxxxxx' as the admin password placeholder so we
                     * only want to save it when it is a different string
                     */
                    } elseif ($v != 'xxxxxx') {
                        self::saveStreamSetting($keyname, $v);
                    }
                }
            }
        }
    }

    /*
     * Sets indivisual stream setting.
     *
     * $data - data array. $data is [].
     * TODO: Make this SQL a prepared statement!
     *
     * Do not remove this function. It is called by airtime-system.php
     */
    public static function setIndividualStreamSetting($data)
    {
        foreach ($data as $keyname => $v) {
            $sql = "UPDATE cc_stream_setting SET value=:v WHERE keyname=:keyname";
            $map = array(":v" => $v, ":keyname"=>$keyname);

            $res = Application_Common_Database::prepareAndExecute($sql, $map, 
                Application_Common_Database::EXECUTE);
        }
    }


    /*
     * Stores liquidsoap status if $boot_time > save time.
     * save time is the time that user clicked save on stream setting page
     */
    public static function setLiquidsoapError($stream_id, $msg, $boot_time=null)
    {
        $con = Propel::getConnection();

        $update_time = Application_Model_Preference::GetStreamUpdateTimestemp();
        
        if ($boot_time == null || $boot_time > $update_time) {
            $keyname = "s".$stream_id."_liquidsoap_error";
            $sql = "SELECT COUNT(*) FROM cc_stream_setting"
                ." WHERE keyname = :keyname";

            $stmt = $con->prepare($sql);
            $stmt->bindParam(':keyname', $keyname);

            if ($stmt->execute()) {
                $result= $stmt->fetchColumn(0);
            } else {
                $msg = implode(',', $stmt->errorInfo());
                throw new Exception("Error: $msg");
            }

            if ($result == 1) {
                $sql = "UPDATE cc_stream_setting"
                    ." SET value = :msg"
                    ." WHERE keyname = :keyname";
            } else {
                $sql = "INSERT INTO cc_stream_setting (keyname, value, type)"
                    ." VALUES (:keyname, :msg, 'string')";
            }

            $stmt = $con->prepare($sql);
            $stmt->bindParam(':keyname', $keyname);
            $stmt->bindParam(':msg', $msg);
            
            if ($stmt->execute()) {
                //do nothing
            } else {
                $msg = implode(',', $stmt->errorInfo());
                throw new Exception("Error: $msg");
            }
        }
    }

    public static function getLiquidsoapError($stream_id)
    {
        $con = Propel::getConnection();

        $keyname = "s".$stream_id."_liquidsoap_error";
        $sql = "SELECT value FROM cc_stream_setting"
            ." WHERE keyname = :keyname";

        $stmt = $con->prepare($sql);
        $stmt->bindParam(':keyname', $keyname);

        if ($stmt->execute()) {
            $result= $stmt->fetchColumn(0);
        } else {
            $msg = implode(',', $stmt->errorInfo());
            throw new Exception("Error: $msg");
        }

        return ($result !== false) ? $result : null;
    }

    public static function getStreamEnabled($stream_id)
    {
        $con = Propel::getConnection();

        $keyname = "s" . $stream_id . "_enable";
        $sql = "SELECT value FROM cc_stream_setting"
        ." WHERE keyname = :keyname";

        $stmt = $con->prepare($sql);
        $stmt->bindParam(':keyname', $keyname);

        if ($stmt->execute()) {
            $result= $stmt->fetchColumn(0);
        } else {
            $msg = implode(',', $stmt->errorInfo());
            throw new Exception("Error: $msg");
        }

        return ($result != 'false');
    }

    /*
     * Only returns info that is needed for data collection
     * returns array('s1'=>array(keyname=>value))
     */
    public static function getStreamInfoForDataCollection()
    {
        $con = Propel::getConnection();

        $out = array();
        $enabled_stream = self::getEnabledStreamIds();

        foreach ($enabled_stream as $stream) {
            $keys = array("{$stream}_output", "{$stream}_type", "{$stream}_bitrate", "{$stream}_host");
            $key_csv = implode(',', $keys);

            $sql = "SELECT keyname, value FROM cc_stream_setting"
                ." WHERE keyname IN (:key_csv)";

            $stmt = $con->prepare($sql);
            $stmt->bindParam(':key_csv', $key_csv);

            if ($stmt->execute()) {
                $rows = $stmt->fetchAll();
            } else {
                $msg = implode(',', $stmt->errorInfo());
                throw new Exception("Error: $msg");
            }

            $info = array();
            foreach ($rows as $r) {
                $temp = explode("_", $r['keyname']);
                $info[$temp[1]] = $r['value'];
                $out[$stream] = $info;
            }
        }

        return $out;
    }

    public static function setMasterLiveStreamPort($value)
    {
        self::setValue("master_live_stream_port", $value, "integer");
    }

    public static function getMasterLiveStreamPort()
    {
        return self::getValue("master_live_stream_port");
    }

    public static function setMasterLiveStreamMountPoint($value)
    {
        self::setValue("master_live_stream_mp", $value, "string");
    }

    public static function getMasterLiveStreamMountPoint()
    {
        return self::getValue("master_live_stream_mp");
    }

    public static function setDjLiveStreamPort($value)
    {
        self::setValue("dj_live_stream_port", $value, "integer");
    }

    public static function getDjLiveStreamPort()
    {
        return self::getValue("dj_live_stream_port");
    }

    public static function setDjLiveStreamMountPoint($value)
    {
        self::setValue("dj_live_stream_mp", $value, "string");
    }

    public static function getDjLiveStreamMountPoint()
    {
        return self::getValue("dj_live_stream_mp");
    }
    
    public static function getAdminUser($stream){
        return self::getValue($stream."_admin_user");
    }
    
    public static function setAdminUser($stream, $v){
        self::setValue($stream."_admin_user", $v, "string");
    }
    
    public static function getAdminPass($stream){
        return self::getValue($stream."_admin_pass");
    }
    
    public static function setAdminPass($stream, $v){
        self::setValue($stream."_admin_pass", $v, "string");
    }
    
    public static function getOffAirMeta(){
        return self::getValue("off_air_meta");
    }
    
    public static function setOffAirMeta($offAirMeta){
        self::setValue("off_air_meta", $offAirMeta, "string");
    }
    
    public static function GetAllListenerStatErrors(){

    	$sql = "SELECT * FROM cc_stream_setting WHERE keyname like :p1";
    	$mounts =  Application_Common_Database::prepareAndExecute($sql, array(':p1'=>'%_mount'));
    	
    	$mps = array();
    	
    	foreach($mounts as $mount) {
    		$mps[] = "'" .$mount["value"] . "_listener_stat_error'";
    	}
    	
    	$in = implode(",", $mps);
    	
        $sql = "SELECT * FROM cc_stream_setting WHERE keyname IN ( $in )";
        return Application_Common_Database::prepareAndExecute($sql, array());
    }
    
    public static function SetListenerStatError($key, $v) {
        self::setValue($key, $v, 'string');
    }
}
