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

    public function uploadTrack($filepath, $filename) 
    {
        if($this->getToken())
        {
            $track_data = array(
                'track[sharing]' => 'private',
                'track[title]' => $filename,
                'track[asset_data]' => '@' . $filepath
            );

            try {
                $response = json_decode(
                    $this->_soundcloud->post('tracks', $track_data),
                    true
                );
            } 
            catch (Services_Soundcloud_Invalid_Http_Response_Code_Exception $e) {
                echo $e->getMessage();
                echo var_dump($track_data);
            }
        }
    }

}
