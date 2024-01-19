<?php

define('MAX_NUM_STREAMS', 4);

class Application_Model_StreamConfig
{
    private static function toOutputKey($id)
    {
        return 's' . $id;
    }

    private static function toOutputId($key)
    {
        return intval(trim($key, 's'));
    }

    public static function getOutput($key, $add_prefix = false)
    {
        $id = self::toOutputId($key);
        $config_id = $id - 1;
        $prefix = $add_prefix ? "s{$id}_" : '';

        if (!Config::has("stream.outputs.merged.{$config_id}")) {
            $result = [
                $prefix . 'enable' => 'false',
                $prefix . 'public_url' => '',
                $prefix . 'output' => 'icecast',
                $prefix . 'host' => 'localhost',
                $prefix . 'port' => 8000,
                $prefix . 'mount' => '',
                $prefix . 'user' => 'source',
                $prefix . 'pass' => '',
                $prefix . 'admin_user' => 'admin',
                $prefix . 'admin_pass' => '',
                $prefix . 'channels' => 'stereo',
                $prefix . 'bitrate' => 128,
                $prefix . 'type' => '',
                $prefix . 'name' => '',
                $prefix . 'description' => '',
                $prefix . 'genre' => '',
                $prefix . 'url' => '',
                $prefix . 'mobile' => 'false',
            ];
        } else {
            $output = Config::get("stream.outputs.merged.{$config_id}");

            $result = [
                $prefix . 'enable' => $output['enabled'] ?? 'false',
                $prefix . 'output' => $output['kind'] ?? 'icecast',
                $prefix . 'public_url' => $output['public_url'] ?? '',
                $prefix . 'host' => $output['host'] ?? 'localhost',
                $prefix . 'port' => $output['port'] ?? 8000,
                $prefix . 'mount' => $output['mount'] ?? '',
                $prefix . 'user' => $output['source_user'] ?? 'source',
                $prefix . 'pass' => $output['source_password'] ?? '',
                $prefix . 'admin_user' => $output['admin_user'] ?? 'admin',
                $prefix . 'admin_pass' => $output['admin_password'] ?? '',
                $prefix . 'name' => $output['name'] ?? '',
                $prefix . 'description' => $output['description'] ?? '',
                $prefix . 'genre' => $output['genre'] ?? '',
                $prefix . 'url' => $output['website'] ?? '',
                $prefix . 'mobile' => $output['mobile'] ?? 'false',
                // $prefix . 'liquidsoap_error' => 'waiting',
            ];
            if (array_key_exists('audio', $output)) {
                $result = array_merge($result, [
                    $prefix . 'channels' => $output['audio']['channels'] ?? 'stereo',
                    $prefix . 'bitrate' => $output['audio']['bitrate'] ?? 128,
                    $prefix . 'type' => $output['audio']['format'],
                ]);
            } elseif ($output['kind'] == 'hls') {
                // HLS : set web server host and port
                $result = array_merge($result, [
                    $prefix . 'port' => Config::get('general.public_url_raw')->getPort(),
                    $prefix . 'type' => 'x-mpegurl',
                    $prefix . 'bitrate' => '',
                    // prefix manifest with webserver hls mount point
                    $prefix . 'mount' => 'hls/' . $output['manifest'],
                ]);
            }
        }

        if (!$result[$prefix . 'public_url']) {
            $host = Config::get('general.public_url_raw')->getHost();
            $port = $result[$prefix . 'port'];
            $mount = $result[$prefix . 'mount'];

            $result[$prefix . 'public_url'] = "http://{$host}:{$port}/{$mount}";

            if ($result[$prefix . 'output'] == 'shoutcast') {
                // The semi-colon is important to make Shoutcast stream URLs play instead turn into a page.
                $result[$prefix . 'public_url'] .= ';';
            }
        }

        return $result;
    }

    public static function getOutputEnabledKeys()
    {
        $keys = [];

        foreach (Config::get('stream.outputs.merged') as $id => $output) {
            if ($output['enabled'] ?? false) {
                $keys[] = self::toOutputKey($id + 1);
            }
        }

        return $keys;
    }
}

class Application_Model_StreamSetting
{
    public static function getEnabledStreamData()
    {
        $streams = [];
        $streamIds = self::getEnabledStreamIds();
        foreach ($streamIds as $id) {
            $streamData = self::getStreamData($id);
            $prefix = $id . '_';
            $streams[$id] = [
                'url' => $streamData[$prefix . 'public_url'],
                'codec' => $streamData[$prefix . 'type'],
                'bitrate' => $streamData[$prefix . 'bitrate'],
                'mobile' => $streamData[$prefix . 'mobile'],
            ];
        }

        return $streams;
    }

    /* Returns the id's of all streams that are enabled in an array. An
     * example of the array returned in JSON notation is ["s1", "s2", "s3"] */
    public static function getEnabledStreamIds()
    {
        return Application_Model_StreamConfig::getOutputEnabledKeys();
    }

    /* Returns all information related to a specific stream. An example
     * of a stream id is 's1' or 's2'. */
    public static function getStreamData($p_streamId)
    {
        return Application_Model_StreamConfig::getOutput($p_streamId, true);
    }

    /* Similar to getStreamData, but removes all sX prefixes to
     * make data easier to iterate over */
    public static function getStreamDataNormalized($p_streamId)
    {
        return Application_Model_StreamConfig::getOutput($p_streamId, false);
    }

    public static function getStreamSetting()
    {
        $settings = [];
        $numStreams = MAX_NUM_STREAMS;
        for ($streamIdx = 1; $streamIdx <= $numStreams; ++$streamIdx) {
            $settings = array_merge($settings, self::getStreamData('s' . $streamIdx));
        }
        $settings['master_live_stream_port'] = self::getMasterLiveStreamPort();
        $settings['master_live_stream_mp'] = self::getMasterLiveStreamMountPoint();
        $settings['dj_live_stream_port'] = self::getDjLiveStreamPort();
        $settings['dj_live_stream_mp'] = self::getDjLiveStreamMountPoint();
        $settings['off_air_meta'] = Application_Model_Preference::getOffAirMeta();
        $settings['icecast_vorbis_metadata'] = self::getIcecastVorbisMetadata();
        $settings['output_sound_device'] = self::getOutputSoundDevice();
        $settings['output_sound_device_type'] = self::getOutputSoundDeviceType();

        return $settings;
    }

    public static function getStreamEnabled($stream_id)
    {
        return in_array('s' . $stream_id, self::getEnabledStreamIds());
    }

    /*
     * Only returns info that is needed for data collection
     * returns array('s1'=>array(keyname=>value))
     */
    public static function getStreamInfoForDataCollection()
    {
        $result = [];
        $stream_ids = self::getEnabledStreamIds();

        foreach ($stream_ids as $stream_id) {
            $stream = self::getStreamDataNormalized($stream_id);
            $keys = array_flip(['output', 'type', 'bitrate', 'host']);
            $result[$stream_id] = array_intersect_key($stream, $keys);
        }

        return $result;
    }

    public static function getMasterLiveStreamPort()
    {
        return Config::get('stream.inputs.main.port') ?? 8001;
    }

    public static function getMasterLiveStreamMountPoint()
    {
        return Config::get('stream.inputs.main.mount') ?? 'main';
    }

    public static function getMasterLiveStreamSecure()
    {
        return Config::get('stream.inputs.main.secure') ?? false;
    }

    public static function getDjLiveStreamPort()
    {
        return Config::get('stream.inputs.show.port') ?? 8002;
    }

    public static function getDjLiveStreamMountPoint()
    {
        return Config::get('stream.inputs.show.mount') ?? 'show';
    }

    public static function getDjLiveStreamSecure()
    {
        return Config::get('stream.inputs.show.secure') ?? false;
    }

    public static function getAdminUser($stream)
    {
        return self::getStreamDataNormalized($stream)['admin_user'];
    }

    public static function getAdminPass($stream)
    {
        return self::getStreamDataNormalized($stream)['admin_pass'];
    }

    public static function getIcecastVorbisMetadata()
    {
        foreach (Config::get('stream.outputs.merged') as $output) {
            if ($output['audio']['enable_metadata'] ?? false) {
                return true;
            }
        }

        return '';
    }

    public static function getOutputSoundDevice()
    {
        return Config::get('stream.outputs.system.0.enabled') ?? 'false';
    }

    public static function getOutputSoundDeviceType()
    {
        return Config::get('stream.outputs.system.0.kind') ?? '';
    }
}
