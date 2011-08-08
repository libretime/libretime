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

    public static function SetPhone($phone){
    	Application_Model_Preference::SetValue("phone", $phone);
    }

    public static function GetPhone(){
    	return Application_Model_Preference::GetValue("phone");
    }

	public static function SetEmail($email){
    	Application_Model_Preference::SetValue("email", $email);
    }

    public static function GetEmail(){
    	return Application_Model_Preference::GetValue("email");
    }

	public static function SetStationWebSite($site){
    	Application_Model_Preference::SetValue("station_website", $site);
    }

    public static function GetStationWebSite(){
    	return Application_Model_Preference::GetValue("station_website");
    }

	public static function SetSupportFeedback($feedback){
    	Application_Model_Preference::SetValue("support_feedback", $feedback);
    }

    public static function GetSupportFeedback(){
    	return Application_Model_Preference::GetValue("support_feedback");
    }

	public static function SetPublicise($publicise){
    	Application_Model_Preference::SetValue("publicise", $publicise);
    }

    public static function GetPublicise(){
    	return Application_Model_Preference::GetValue("publicise");
    }

	public static function SetRegistered($registered){
    	Application_Model_Preference::SetValue("registered", $registered);
    }

    public static function GetRegistered(){
    	return Application_Model_Preference::GetValue("registered");
    }

	public static function SetStationCountry($country){
    	Application_Model_Preference::SetValue("country", $country);
    }

    public static function GetStationCountry(){
    	return Application_Model_Preference::GetValue("country");
    }

	public static function SetStationCity($city){
    	Application_Model_Preference::SetValue("city", $city);
    }

	public static function GetStationCity(){
    	return Application_Model_Preference::GetValue("city");
    }

	public static function SetStationDescription($description){
    	Application_Model_Preference::SetValue("description", $description);
    }

	public static function GetStationDescription(){
    	return Application_Model_Preference::GetValue("description");
    }

    public static function SetStationLogo($imagePath){
    	if(!empty($imagePath)){
	    	$image = @file_get_contents($imagePath);
	    	$image = base64_encode($image);
	    	Application_Model_Preference::SetValue("logoImage", $image);
    	}
    }

	public static function GetStationLogo(){
    	return Application_Model_Preference::GetValue("logoImage");
    }

    public static function GetUniqueId(){
    	return Application_Model_Preference::GetValue("uniqueId");
    }

    public static function GetCountryList(){
    	global $CC_DBC;
    	$sql = "SELECT * FROM cc_country";
    	$res =  $CC_DBC->GetAll($sql);
    	$out = array();
    	$out[""] = "Select Country";
    	foreach($res as $r){
    		$out[$r["isocode"]] = $r["name"];
    	}
    	return $out;
    }

    public static function GetSystemInfo($returnArray=false){
    	exec('/usr/bin/airtime-check-system', $output);

    	$output = preg_replace('/\s+/', ' ', $output);

    	$systemInfoArray = array();
    	foreach( $output as $key => &$out){
    		$info = explode('=', $out);
    		if(isset($info[1])){
    			$key = str_replace(' ', '_', trim($info[0]));
    			$key = strtoupper($key);
    			$systemInfoArray[$key] = $info[1];
    		}
    	}

    	$outputArray = array();

    	$outputArray['STATION_NAME'] = Application_Model_Preference::GetStationName();
    	$outputArray['PHONE'] = Application_Model_Preference::GetPhone();
    	$outputArray['EMAIL'] = Application_Model_Preference::GetEmail();
    	$outputArray['STATION_WEB_SITE'] = Application_Model_Preference::GetStationWebSite();
    	$outputArray['STATION_COUNTRY'] = Application_Model_Preference::GetStationCountry();
    	$outputArray['STATION_CITY'] = Application_Model_Preference::GetStationCity();
    	$outputArray['STATION_DESCRIPTION'] = Application_Model_Preference::GetStationDescription();

    	// get web server info
    	if(isset($systemInfoArray["AIRTIME_VERSION_URL"])){
    	   $url = $systemInfoArray["AIRTIME_VERSION_URL"];
           $index = strpos($url,'/api/');
           $url = substr($url, 0, $index);
    
           $headerInfo = get_headers(trim($url),1);
           $outputArray['WEB_SERVER'] = $headerInfo['Server'][0];
    	}
    	
    	$outputArray['NUM_OF_USERS'] = User::getUserCount();
    	$outputArray['NUM_OF_SONGS'] = StoredFile::getFileCount();
    	$outputArray['NUM_OF_PLAYLISTS'] = Playlist::getPlaylistCount();
    	$outputArray['NUM_OF_SCHEDULED_PLAYLISTS'] = Schedule::getSchduledPlaylistCount();
    	$outputArray['NUM_OF_PAST_SHOWS'] = ShowInstance::GetShowInstanceCount(date("Y-m-d H:i:s"));
    	$outputArray['UNIQUE_ID'] = Application_Model_Preference::GetUniqueId();

    	$outputArray = array_merge($systemInfoArray, $outputArray);

    	$outputString = "\n";
    	foreach($outputArray as $key => $out){
    	    if($out != ''){
    		    $outputString .= $key.' : '.$out."\n";
    	    }
    	}
    	if($returnArray){
    	    $outputArray['PROMOTE'] = Application_Model_Preference::GetPublicise();
    		$outputArray['LOGOIMG'] = Application_Model_Preference::GetStationLogo();
    	    return $outputArray;
    	}else{
    	    return $outputString;
    	}
    }

    public static function SetRemindMeDate($now){
    	$weekAfter = mktime(0, 0, 0, date("m")  , date("d")+7, date("Y"));
   		Application_Model_Preference::SetValue("remindme", $weekAfter);
    }

    public static function GetRemindMeDate(){
        return Application_Model_Preference::GetValue("remindme");
    }
    
    public static function SetImportTimestamp(){
        $now = time();
        Application_Model_Preference::SetValue("import_timestamp", $now);
    }
    
    public static function GetImportTimestamp(){
        return Application_Model_Preference::GetValue("import_timestamp");
    }
}

