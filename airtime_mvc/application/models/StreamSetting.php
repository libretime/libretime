<?php

define("MAX_NUM_STREAMS", 4);

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

    public static function getValue($key, $default="")
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

        return $result ? $result : $default;
    }

    public static function getEnabledStreamData()
    {
        $streams = Array();
        $streamIds = self::getEnabledStreamIds();
        foreach ($streamIds as $id) {
            $streamData = self::getStreamData($id);
            $prefix = $id."_";
            $host = $streamData[$prefix."host"];
            $port = $streamData[$prefix."port"];
            $mount = $streamData[$prefix."mount"];
            if ($streamData[$prefix."output"] == "shoutcast") {
                $url = "http://$host:$port/;"; //The semi-colon is important to make Shoutcast stream URLs play instead turn into a page.
            } else { //Icecast
                $url = "http://$host:$port/$mount";
            }
            $streams[$id] = Array(
                "url" => $url,
                "codec" => $streamData[$prefix."type"],
                "bitrate" => $streamData[$prefix."bitrate"],
                "mobile" => $streamData[$prefix."mobile"]
            );
        }
        return $streams;
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

    /* Returns all information related to a specific stream. An example
     * of a stream id is 's1' or 's2'. */
    public static function getStreamData($p_streamId)
    {
        $rows = CcStreamSettingQuery::create()
            ->filterByDbKeyName("${p_streamId}_%")
            ->find();

        //This is way too much code because someone made only stupid decisions about how
        //the layout of this table worked. The git history doesn't lie.
        $data = array();
        foreach ($rows as $row) {
            $key = $row->getDbKeyName();
            $value = $row->getDbValue();
            $type = $row->getDbType();
            //Fix stupid defaults so we end up with proper typing in our JSON
            if ($row->getDbType() == "boolean") {
                if (empty($value)) {
                    //In Python, there is no way to tell the difference between ints and booleans,
                    //which we need to differentiate between for when we're generating the Liquidsoap
                    //config file. Returning booleans as a string is a workaround that lets us do that.
                    $value = "false";
                }
                $data[$key] = $value;
            }
            elseif ($row->getDbType() == "integer") {
                if (empty($value)) {
                    $value = 0;
                }
                $data[$key] = intval($value);
            }
            else {
                $data[$key] = $value;
            }
        }

        //Add in defaults in case they don't exist in the database.
        $keyPrefix = $p_streamId . '_';
        self::ensureKeyExists($keyPrefix . 'admin_pass', $data);
        self::ensureKeyExists($keyPrefix . 'admin_user', $data);
        self::ensureKeyExists($keyPrefix . 'bitrate', $data, 128);
        self::ensureKeyExists($keyPrefix . 'channels', $data, "stereo");
        self::ensureKeyExists($keyPrefix . 'description', $data);
        self::ensureKeyExists($keyPrefix . 'enable', $data, "false");
        self::ensureKeyExists($keyPrefix . 'genre', $data);
        self::ensureKeyExists($keyPrefix . 'host', $data);
        self::ensureKeyExists($keyPrefix . 'liquidsoap_error', $data, "waiting");
        self::ensureKeyExists($keyPrefix . 'mount', $data);
        self::ensureKeyExists($keyPrefix . 'name', $data);
        self::ensureKeyExists($keyPrefix . 'output', $data);
        self::ensureKeyExists($keyPrefix . 'pass', $data);
        self::ensureKeyExists($keyPrefix . 'port', $data, 8000);
        self::ensureKeyExists($keyPrefix . 'type', $data);
        self::ensureKeyExists($keyPrefix . 'url', $data);
        self::ensureKeyExists($keyPrefix . 'user', $data);
        self::ensureKeyExists($keyPrefix . 'mobile', $data);

        return $data;
    }

    /* Similar to getStreamData, but removes all sX prefixes to
     * make data easier to iterate over */
    public static function getStreamDataNormalized($p_streamId)
    {
        $settings = self::getStreamData($p_streamId);
        foreach ($settings as $key => $value)
        {
            unset($settings[$key]);
            $newKey = substr($key, strlen($p_streamId)+1); //$p_streamId is assumed to be the key prefix.
            $settings[$newKey] = $value;
        }
        return $settings;
    }

    private static function ensureKeyExists($key, &$array, $default='')
    {
        if (!array_key_exists($key, $array)) {
            $array[$key] = $default;
        }
        return $array;
    }

    public static function getStreamSetting()
    {
        $settings = array();
        $numStreams = MAX_NUM_STREAMS;
        for ($streamIdx = 1; $streamIdx <= $numStreams; $streamIdx++)
        {
            $settings = array_merge($settings, self::getStreamData("s" . $streamIdx));
        }
        $settings["master_live_stream_port"] = self::getMasterLiveStreamPort();
        $settings["master_live_stream_mp"] = self::getMasterLiveStreamMountPoint();
        $settings["dj_live_stream_port"] = self::getDjLiveStreamPort();
        $settings["dj_live_stream_mp"] = self::getDjLiveStreamMountPoint();
        $settings["off_air_meta"] = self::getOffAirMeta();
        $settings["icecast_vorbis_metadata"] = self::getIcecastVorbisMetadata();
        $settings["output_sound_device"] = self::getOutputSoundDevice();
        $settings["output_sound_device_type"] = self::getOutputSoundDeviceType();
        return $settings;
    }


    private static function saveStreamSetting($key, $value)
    {
        $stream_setting = CcStreamSettingQuery::create()->filterByDbKeyName($key)->findOne();
        if (is_null($stream_setting)) {
            //throw new Exception("Keyname $key does not exist!");
            $stream_setting = new CcStreamSetting();
            $stream_setting->setDbKeyName($key);
            $stream_setting->setDbType("");
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
                // SAAS-876 - If we're using Airtime Pro streaming, set the stream to use the default settings
                if (!Application_Model_Preference::getUsingCustomStreamSettings()) {
                    $d = array_merge($d, static::getDefaults($prefix));
                }
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

    /**
     * SAAS-876 - Get the default stream settings values for Airtime Pro streaming
     *
     * @param int $prefix
     *
     * @return array array of default stream setting values
     */
    public static function getDefaults($prefix) {
        $config = Config::getConfig();
        return array(
            'host'   => $config['baseUrl'],
            'port'   => DEFAULT_ICECAST_PORT,
            'output' => 'icecast',
            'user'   => $config['stationId'],
            'pass'   => Application_Model_Preference::getDefaultIcecastPassword(),
            // Forcing default mountpoint string for now
            'mount'  => 'airtime_128',
        );
    }

    /*
     * Sets individual stream setting.
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
        return self::getValue("master_live_stream_port", 8001);
    }

    public static function setMasterLiveStreamMountPoint($value)
    {
        self::setValue("master_live_stream_mp", $value, "string");
    }

    public static function getMasterLiveStreamMountPoint()
    {
        return self::getValue("master_live_stream_mp", "/master");
    }

    public static function setDjLiveStreamPort($value)
    {
        self::setValue("dj_live_stream_port", $value, "integer");
    }

    public static function getDjLiveStreamPort()
    {
        return self::getValue("dj_live_stream_port", 8002);
    }

    public static function setDjLiveStreamMountPoint($value)
    {
        self::setValue("dj_live_stream_mp", $value, "string");
    }

    public static function getDjLiveStreamMountPoint()
    {
        return self::getValue("dj_live_stream_mp", "/show");
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

    public static function getIcecastVorbisMetadata() {
        return self::getValue("icecast_vorbis_metadata", "");
    }

    public static function getOutputSoundDevice() {
        return self::getValue("output_sound_device", "false");
    }

    public static function getOutputSoundDeviceType() {
        return self::getValue("output_sound_device_type", "");
    }
}
