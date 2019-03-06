<?php

class FileDataHelper {

    public static function getAudioMimeTypeArray() {
        return array(
            "audio/ogg"         => "ogg",
            "application/ogg"   => "ogg",
            "audio/vorbis"      => "ogg",
            "audio/mp3"         => "mp3",
            "audio/mpeg"        => "mp3",
            "audio/mpeg3"       => "mp3",
            "audio/x-aac"       => "aac",
            "audio/aac"         => "aac",
            "audio/aacp"        => "aac",
            "audio/mp4"         => "m4a",
            "video/mp4"         => "mp4",
            "audio/x-flac"      => "flac",
            "audio/flac"        => "flac",
            "audio/wav"         => "wav",
            "audio/x-wav"       => "wav",
            "audio/mp2"         => "mp2",
            "audio/mp1"         => "mp1",
            "audio/x-ms-wma"    => "wma",
            "audio/basic"       => "au",
        );
    }

    /**
     * We want to throw out invalid data and process the upload successfully
     * at all costs, so check the data and sanitize it if necessary
     * @param array $data array containing new file metadata
     */
    public static function sanitizeData(&$data)
    {
        if (array_key_exists("track_number", $data)) {
            // If the track number isn't numeric, this will return 0
            $data["track_number"] = intval($data["track_number"]);
        }
        if (array_key_exists("year", $data)) {
            // If the track number isn't numeric, this will return 0
            $data["year"] = intval($data["year"]);
        }
        if (array_key_exists("expires_date", $data) && array_key_exists("expires_time", $data)) {
            // if these values are not empty we create a new datetime for insertion
            if (!(is_null($data["expires_date"])) && !(is_null($data["expires_time"]))) {
                $data["expirestime"] = $data['expires_date'] . " " . $data['expires_time'];
            } // if they leave the time empty we just default to midnight
            elseif (!(is_null($data["expires_date"])) && (is_null($data["expires_time"]))) {
                $data["expirestime"] = $data['expires_date'] . " 00:00";
            }
        }
        if (array_key_exists("bpm", $data)) {
            //Some BPM tags are silly and include the word "BPM". Let's strip that...
            $data["bpm"] = str_ireplace("BPM", "", $data["bpm"]);
            // This will convert floats to ints too.
            $data["bpm"] = intval($data["bpm"]);
        }
    }

    /**
     * Return a suitable extension for the given file
     *
     * @param string $mime
     *
     * @return string file extension with(!) a dot (for convenience)
     *
     * @throws Exception
     */
    public static function getFileExtensionFromMime($mime)
    {
        $mime = trim(strtolower($mime));
        try {
            return ('.' . static::getAudioMimeTypeArray()[$mime]);
        } catch (Exception $e) {
            throw new Exception("Unknown file type: $mime");
        }
    }

}
