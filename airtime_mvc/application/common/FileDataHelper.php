<?php
/**
 * Created by PhpStorm.
 * User: sourcefabric
 * Date: 17/02/15
 */

class FileDataHelper {

    /**
     * We want to throw out invalid data and process the upload successfully
     * at all costs, so check the data and sanitize it if necessary
     * @param array $data array containing new file metadata
     */
    public static function sanitizeData(&$data) {
        // If the track number isn't numeric, this will return 0
        $data["track_number"] = intval($data["track_number"]);
    }

}