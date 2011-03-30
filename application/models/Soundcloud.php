<?php

require_once 'soundcloud-api/Services/Soundcloud.php';

class ATSoundcloud {

    private $_soundcloud;

	public function __construct()
    {
        global $CC_CONFIG;

        $this->_soundcloud = new Services_Soundcloud($CC_CONFIG['soundcloud-client-id'], $CC_CONFIG['soundcloud-client-secret']);
    }

    private function getToken()
    {
        $username = Application_Model_Preference::GetSoundCloudUser();
        $password = Application_Model_Preference::GetSoundCloudPassword();

        if($username === "" || $password === "")
        {
            return false;
        }

        $token = $this->_soundcloud->accessTokenResourceOwner($username, $password);

        return $token;
    }

    public function uploadTrack($filepath, $filename, $description, $tags=array()) 
    {
        if($this->getToken())
        {
            if(count($tags)) {
                $tags = join(" ", $tags);
                $tags = $tags." ".Application_Model_Preference::GetSoundCloudTags();
            }
            else {
                $tags = Application_Model_Preference::GetSoundCloudTags();
            }

            $track_data = array(
                'track[sharing]' => 'private',
                'track[title]' => $filename,
                'track[asset_data]' => '@' . $filepath,
                'track[tag_list]' => $tags,
                'track[description]' => $description,
                'track[downloadable]' => true,
                
            );

            $response = json_decode(
                $this->_soundcloud->post('tracks', $track_data),
                true
            );

            return $response["id"];
        }  
    }

}
