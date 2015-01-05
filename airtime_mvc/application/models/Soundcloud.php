<?php
require_once 'soundcloud-api/Services/Soundcloud.php';

class Application_Model_Soundcloud
{
    private $_soundcloud;

    public function __construct()
    {
        $CC_CONFIG = Config::getConfig();
        $this->_soundcloud = new Services_Soundcloud(
            $CC_CONFIG['soundcloud-client-id'],
            $CC_CONFIG['soundcloud-client-secret']);
    }

    private function getToken()
    {
        $username = Application_Model_Preference::GetSoundCloudUser();
        $password = Application_Model_Preference::GetSoundCloudPassword();

        $token = $this->_soundcloud->accessTokenResourceOwner($username, $password);

        return $token;
    }

    public function uploadTrack($filepath, $filename, $description,
        $tags=array(), $release=null, $genre=null)
    {

        if (!$this->getToken()) {
            throw new NoSoundCloundToken();
        }
        if (count($tags)) {
            $tags = join(" ", $tags);
            $tags = $tags." ".Application_Model_Preference::GetSoundCloudTags();
        } else {
            $tags = Application_Model_Preference::GetSoundCloudTags();
        }

        $downloadable = Application_Model_Preference::GetSoundCloudDownloadbleOption() == '1';

        $track_data = array(
            'track[sharing]'      => 'private',
            'track[title]'        => $filename,
            'track[asset_data]'   => '@' . $filepath,
            'track[tag_list]'     => $tags,
            'track[description]'  => $description,
            'track[downloadable]' => $downloadable,

        );

        if (isset($release)) {
            $release = str_replace(" ", "-", $release);
            $release = str_replace(":", "-", $release);

            //YYYY-MM-DD-HH-mm-SS
            $release = explode("-", $release);
            $track_data['track[release_year]']  = $release[0];
            $track_data['track[release_month]'] = $release[1];
            $track_data['track[release_day]']   = $release[2];
        }

        if (isset($genre) && $genre != "") {
            $track_data['track[genre]'] = $genre;
        } else {
            $default_genre = Application_Model_Preference::GetSoundCloudGenre();
            if ($default_genre != "") {
                $track_data['track[genre]'] = $default_genre;
            }
        }

        $track_type = Application_Model_Preference::GetSoundCloudTrackType();
        if ($track_type != "") {
            $track_data['track[track_type]'] = $track_type;
        }

        $license = Application_Model_Preference::GetSoundCloudLicense();
        if ($license != "") {
            $track_data['track[license]'] = $license;
        }

        $response = json_decode(
            $this->_soundcloud->post('tracks', $track_data),
            true
        );

        return $response;

    }

    public static function uploadSoundcloud($id)
    {
        $cmd = "/usr/lib/airtime/utils/soundcloud-uploader $id > /dev/null &";
        Logging::info("Uploading soundcloud with command: $cmd");
        exec($cmd);
    }
}

class NoSoundCloundToken extends Exception {}
