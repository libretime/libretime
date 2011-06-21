<?php

class Application_Model_Preference
{

    public static function SetValue($key, $value){
        global $CC_CONFIG, $CC_DBC;

        //called from a daemon process
        if(!Zend_Auth::getInstance()->hasIdentity()) {
            $id = NULL;
        }
        else {
            $auth = Zend_Auth::getInstance();
            $id = $auth->getIdentity()->id;
        }

        $key = pg_escape_string($key);
        $value = pg_escape_string($value);

        //Check if key already exists
        $sql = "SELECT COUNT(*) FROM cc_pref"
        ." WHERE keystr = '$key'";
        $result = $CC_DBC->GetOne($sql);

        if ($result == 1 && is_null($id)){
            $sql = "UPDATE cc_pref"
            ." SET subjid = NULL, valstr = '$value'"
            ." WHERE keystr = '$key'";
        }
        else if ($result == 1 && !is_null($id)){
            $sql = "UPDATE cc_pref"
            ." SET subjid = $id, valstr = '$value'"
            ." WHERE keystr = '$key'";
        }
        else if(is_null($id)) {
            $sql = "INSERT INTO cc_pref (keystr, valstr)"
            ." VALUES ('$key', '$value')";
        }
        else {
            $sql = "INSERT INTO cc_pref (subjid, keystr, valstr)"
            ." VALUES ($id, '$key', '$value')";
        }
        return $CC_DBC->query($sql);
    }

    public static function GetValue($key){
        global $CC_CONFIG, $CC_DBC;
        //Check if key already exists
        $sql = "SELECT COUNT(*) FROM cc_pref"
        ." WHERE keystr = '$key'";
        $result = $CC_DBC->GetOne($sql);

        if ($result == 0)
            return "";
        else {
            $sql = "SELECT valstr FROM cc_pref"
            ." WHERE keystr = '$key'";
            $result = $CC_DBC->GetOne($sql);
            return $result;
        }

    }

    public static function GetHeadTitle(){
        /* Caches the title name as a session variable so we dont access
         * the database on every page load. */
        $defaultNamespace = new Zend_Session_Namespace('title_name');
        if (isset($defaultNamespace->title)) {
            $title = $defaultNamespace->title;
        } else {
            $title = Application_Model_Preference::GetValue("station_name");
            $defaultNamespace->title = $title;
        }
        if (strlen($title) > 0)
            $title .= " - ";

        return $title."Airtime";
    }

    public static function SetHeadTitle($title, $view){
        Application_Model_Preference::SetValue("station_name", $title);
        $defaultNamespace = new Zend_Session_Namespace('title_name');
        $defaultNamespace->title = $title;
        RabbitMq::PushSchedule();

        //set session variable to new station name so that html title is updated.
        //should probably do this in a view helper to keep this controller as minimal as possible.
        $view->headTitle()->exchangeArray(array()); //clear headTitle ArrayObject
        $view->headTitle(Application_Model_Preference::GetHeadTitle());
    }

    public static function SetShowsPopulatedUntil($timestamp) {
        Application_Model_Preference::SetValue("shows_populated_until", $timestamp);
    }

    public static function GetShowsPopulatedUntil() {
        return Application_Model_Preference::GetValue("shows_populated_until");
    }

    public static function SetDefaultFade($fade) {
        Application_Model_Preference::SetValue("default_fade", $fade);
    }

    public static function GetDefaultFade() {
        return Application_Model_Preference::GetValue("default_fade");
    }

    public static function SetStreamLabelFormat($type){
        Application_Model_Preference::SetValue("stream_label_format", $type);
        RabbitMq::PushSchedule();
    }

    public static function GetStreamLabelFormat(){
        return Application_Model_Preference::getValue("stream_label_format");
    }

    public static function GetStationName(){
        return Application_Model_Preference::getValue("station_name");
    }

    public static function SetDoSoundCloudUpload($upload) {
        Application_Model_Preference::SetValue("soundcloud_upload", $upload);
    }

    public static function GetDoSoundCloudUpload() {
        return Application_Model_Preference::GetValue("soundcloud_upload");
    }

    public static function SetSoundCloudUser($user) {
        Application_Model_Preference::SetValue("soundcloud_user", $user);
    }

    public static function GetSoundCloudUser() {
        return Application_Model_Preference::GetValue("soundcloud_user");
    }

    public static function SetSoundCloudPassword($password) {
        if (strlen($password) > 0)
            Application_Model_Preference::SetValue("soundcloud_password", $password);
    }

    public static function GetSoundCloudPassword() {
        return Application_Model_Preference::GetValue("soundcloud_password");
    }

    public static function SetSoundCloudTags($tags) {
        Application_Model_Preference::SetValue("soundcloud_tags", $tags);
    }

    public static function GetSoundCloudTags() {
        return Application_Model_Preference::GetValue("soundcloud_tags");
    }

    public static function SetSoundCloudGenre($genre) {
        Application_Model_Preference::SetValue("soundcloud_genre", $genre);
    }

    public static function GetSoundCloudGenre() {
        return Application_Model_Preference::GetValue("soundcloud_genre");
    }

    public static function SetSoundCloudTrackType($track_type) {
        Application_Model_Preference::SetValue("soundcloud_tracktype", $track_type);
    }

    public static function GetSoundCloudTrackType() {
        return Application_Model_Preference::GetValue("soundcloud_tracktype");
    }

    public static function SetSoundCloudLicense($license) {
        Application_Model_Preference::SetValue("soundcloud_license", $license);
    }

    public static function GetSoundCloudLicense() {
        return Application_Model_Preference::GetValue("soundcloud_license");
    }

    public static function SetAllow3rdPartyApi($bool) {
        Application_Model_Preference::SetValue("third_party_api", $bool);
    }

    public static function GetAllow3rdPartyApi() {
        $val = Application_Model_Preference::GetValue("third_party_api");
        if (strlen($val) == 0){
            return "0";
        } else {
            return $val;
        }
    }
}

