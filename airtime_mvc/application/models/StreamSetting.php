<?php
class Application_Model_StreamSetting {

    public static function SetValue($key, $value, $type)
    {
        global $CC_CONFIG;
        $con = Propel::getConnection();

        $key = pg_escape_string($key);
        $value = pg_escape_string($value);

        // Check if key already exists
        $sql = "SELECT COUNT(*) FROM cc_stream_setting"
            ." WHERE keyname = '$key'";

        $result = $con->query($sql)->fetchColumn(0);

        if ($result == 1) {
            $sql = "UPDATE cc_stream_setting"
            ." SET value = '$value', type='$type'"
            ." WHERE keyname = '$key'";
        } else {
            $sql = "INSERT INTO cc_stream_setting (keyname, value, type)"
            ." VALUES ('$key', '$value', '$type')";
        }

        return $con->exec($sql);
    }

    public static function GetValue($key)
    {
        global $CC_CONFIG;
        $con = Propel::getConnection();

        //Check if key already exists
        $sql = "SELECT COUNT(*) FROM cc_stream_setting"
        ." WHERE keyname = '$key'";
        $result = $con->query($sql)->fetchColumn(0);

        if ($result == 0)
            return "";
        else {
            $sql = "SELECT value FROM cc_stream_setting"
                ." WHERE keyname = '$key'";

            $result = $con->query($sql)->fetchColumn(0);
            return $result ? $result : NULL;
        }
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

        $rows = $con->query($sql)->fetchAll();
        $ids = array();

        foreach ($rows as $row) {
            $ids[] = substr($row["keyname"], 0, strpos($row["keyname"], "_"));
        }

        //Logging::log(print_r($ids, true));
        return $ids;
    }

    /* Returns only global data as array*/
    public static function getGlobalData()
    {
        $con = Propel::getConnection();
        $sql = "SELECT * "
            ."FROM cc_stream_setting "
            ."WHERE keyname IN ('output_sound_device', 'icecast_vorbis_metadata')";

        $rows = $con->query($sql)->fetchAll();
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
        $sql = "SELECT * "
                ."FROM cc_stream_setting "
                ."WHERE keyname LIKE '${p_streamId}_%'";

        $rows = $con->query($sql)->fetchAll();
        $data = array();

        foreach ($rows as $row) {
            $data[$row["keyname"]] = $row["value"];
        }

        return $data;
    }

    public static function getStreamSetting()
    {
        $con = Propel::getConnection();
        $sql = "SELECT *"
                ." FROM cc_stream_setting"
                ." WHERE keyname not like '%_error'";

        $rows = $con->query($sql)->fetchAll();

        $exists = array();

        foreach ($rows as $r) {
            if ($r['keyname'] == 'master_live_stream_port') {
                $exists['master_live_stream_port'] = true;
            } elseif($r['keyname'] == 'master_live_stream_mp') {
                $exists['master_live_stream_mp'] = true;
            } elseif($r['keyname'] == 'dj_live_stream_port') {
                $exists['dj_live_stream_port'] = true;
            } elseif($r['keyname'] == 'dj_live_stream_mp') {
                $exists['dj_live_stream_mp'] = true;
            }
        }

        if (!isset($exists["master_live_stream_port"])) {
            $rows[] = (array("keyname" =>"master_live_stream_port", "value"=>self::GetMasterLiveSteamPort(), "type"=>"integer"));
        }
        if (!isset($exists["master_live_stream_mp"])) {
            $rows[] = (array("keyname" =>"master_live_stream_mp", "value"=>self::GetMasterLiveSteamMountPoint(), "type"=>"string"));
        }
        if (!isset($exists["dj_live_stream_port"])) {
            $rows[] = (array("keyname" =>"dj_live_stream_port", "value"=>self::GetDJLiveSteamPort(), "type"=>"integer"));
        }
        if (!isset($exists["dj_live_stream_mp"])) {
            $rows[] = (array("keyname" =>"dj_live_stream_mp", "value"=>self::GetDJLiveSteamMountPoint(), "type"=>"string"));
        }
        return $rows;
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
        $con = Propel::getConnection();

        foreach ($data as $key=>$d) {
            if ($key == "output_sound_device" || $key == "icecast_vorbis_metadata") {
                $v = $d == 1?"true":"false";
                $sql = "UPDATE cc_stream_setting SET value='$v' WHERE keyname='$key'";
                $con->exec($sql);
            } else if ($key == "output_sound_device_type") {
                $sql = "UPDATE cc_stream_setting SET value='$d' WHERE keyname='$key'";
                $con->exec($sql);
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
                    $con->exec($sql);
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
    public static function setIndivisualStreamSetting($data)
    {
        $con = Propel::getConnection();

        foreach ($data as $keyname => $v) {
            $sql = "UPDATE cc_stream_setting SET value='$v' WHERE keyname='$keyname'";
            $con->exec($sql);
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
                ." WHERE keyname = '$keyname'";
            $result = $con->query($sql)->fetchColumn(0);
            if ($result == 1) {
                $sql = "UPDATE cc_stream_setting"
                    ." SET value = '$msg'"
                    ." WHERE keyname = '$keyname'";
            } else {
                $sql = "INSERT INTO cc_stream_setting (keyname, value, type)"
                    ." VALUES ('$keyname', '$msg', 'string')";
            }
            $res = $con->exec($sql);
        }
    }

    public static function getLiquidsoapError($stream_id)
    {
        $con = Propel::getConnection();

        $keyname = "s".$stream_id."_liquidsoap_error";
        $sql = "SELECT value FROM cc_stream_setting"
            ." WHERE keyname = '$keyname'";
        $result = $con->query($sql)->fetchColumn(0);

        return $result ? $result : NULL;
    }

    public static function getStreamEnabled($stream_id)
    {
        $con = Propel::getConnection();

        $keyname = "s" . $stream_id . "_enable";
        $sql = "SELECT value FROM cc_stream_setting"
        ." WHERE keyname = '$keyname'";
        $result = $con->query($sql)->fetchColumn(0);
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
    public static function getStreamInfoForDataCollection()
    {
        $con = Propel::getConnection();

        $out = array();
        $enabled_stream = self::getEnabledStreamIds();

        foreach ($enabled_stream as $stream) {
            $keys = "'".$stream."_output', "."'".$stream."_type', "."'".$stream."_bitrate', "."'".$stream."_host'";

            $sql = "SELECT keyname, value FROM cc_stream_setting"
                ." WHERE keyname IN ($keys)";

            $rows = $con->query($sql)->fetchAll();
            $info = array();
            foreach ($rows as $r) {
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
