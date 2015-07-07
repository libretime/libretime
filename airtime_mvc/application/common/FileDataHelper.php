<?php

class FileDataHelper {

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
     * @return string file extension with(!) a dot
     *
     * @throws Exception
     */
    public static function getFileExtensionFromMime($mime)
    {
        if ($mime == "audio/ogg" || $mime == "application/ogg" || $mime == "audio/vorbis") {
            return ".ogg";
        } elseif ($mime == "audio/mp3" || $mime == "audio/mpeg" || $mime == "audio/mpeg3") {
            return ".mp3";
        } elseif ($mime == "audio/x-flac") {
            return ".flac";
        } elseif ($mime == "audio/mp4") {
            return ".mp4";
        } elseif ($mime == "audio/wav" || $mime == "audio/x-wav") {
            return ".wav";
        } else {
            throw new Exception("Unknown $mime");
        }
    }

}