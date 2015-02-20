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

}