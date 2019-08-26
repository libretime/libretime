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

    /**
     * Gets data from artwork file
     *
     * @param string $file
     * @param string $filepath
     *
     * @return string Data URI for artwork
     */
    public static function getArtworkData($file, $filepath = false)
    {
        $baseUrl = Application_Common_HTTPHelper::getStationUrl();

        //default cover, maybe make option to change in settings
        $default = $baseUrl . "css/images/no-cover.jpg";

        if ($file === '') {
           //default cover, this opion
           $get_file_content = $default;
        } else {
            if ($filepath != false) {
                $path = $filepath . $file;
            } else {
                $storDir = Application_Model_MusicDir::getStorDir();
                $path = $storDir->getDirectory() . $file;
            }

            if($filecontent = file_get_contents($path) !== false){
                $get_file_content = file_get_contents($path);
            } else {
                $get_file_content = $default;
            }
        }
        return $get_file_content;
    }

    /**
     * Add artwork file
     *
     * @param string $analyzeFile
     * @param string $filename
     * @param string $importDir
     * @param string $DbPath
     *
     * @return string Path to artwork
     */
    public static function saveArtworkData($analyzeFile, $filename, $importDir, $DbPath)
    {
        $getID3 = new getID3;
        $getFileInfo = $getID3->analyze($analyzeFile);

        if(isset($getFileInfo['comments']['picture'][0])) {

              $get_img = "";
              $Image = 'data:'.$getFileInfo['comments']['picture'][0]['image_mime'].';charset=utf-8;base64,'.base64_encode($getFileInfo['comments']['picture'][0]['data']);
              $base64 = @$Image;

              if (!file_exists($importDir . "artwork/")) {
                  if (!mkdir($importDir . "artwork/", 0777)) {
                      Logging::info("Failed to create artwork directory.");
                      throw new Exception("Failed to create artwork directory.");
                  }
              }

              $normalizeValue = self::normalizePath($filename);
              $path_parts = pathinfo($normalizeValue);
              $file = $importDir . "artwork/" . $path_parts['filename'];

              if (file_put_contents($file, $base64)) {
                  $get_img = $DbPath . "artwork/". $path_parts['filename'];
                  Logging::info("Saved Data URI ($get_img)");
              } else {
                  Logging::info("Could not save Data URI");
              }

              /* So i decided to add the actual image as well,
              data URI is good for most cases, but ran into some issues in Swift the way I had it
              Added to API (I'm leaving versioning API to whom ever is going to manage it)
              You can retrieve image from any file with ID: Example:

              http://192.168.10.100:8080/api/track-metadata?id=165&return=artwork_img

              A lot of these are tests, still needs refining
              */
              $gencodedBase = base64_encode($base64);
              $imgp = str_replace('data:image/jpeg;base64,', '', $gencodedBase);
              $img = str_replace(' ', '+', $imgp);
              $datap = base64_decode($imgp);
              $filep = $file . '.jpg';
              $success = file_put_contents($filep, $datap);

        } else {
              //leave empty
              $get_img = '';
        }

        return $get_img;
    }

    private static function normalizePath($string)
    {
        static $normal = array (
          'ƒ' => 'f',
          'Š' => 'S',
          'š' => 's',
          'Ð' => 'Dj',
          'Ž' => 'Z',
          'ž' => 'z',
          'À' => 'A',
          'Á' => 'A',
          'Â' => 'A',
          'Ã' => 'A',
          'Ä' => 'A',
          'Å' => 'A',
          'Æ' => 'E',
          'Ç' => 'C',
          'È' => 'E',
          'É' => 'E',
          'Ê' => 'E',
          'Ë' => 'E',
          'Ì' => 'I',
          'Í' => 'I',
          'Î' => 'I',
          'Ï' => 'I',
          'Ñ' => 'N',
          'Ò' => 'O',
          'Ó' => 'O',
          'Ô' => 'O',
          'Õ' => 'O',
          'Ö' => 'O',
          'Ø' => 'O',
          'Ù' => 'U',
          'Ú' => 'U',
          'Û' => 'U',
          'Ü' => 'U',
          'Ý' => 'Y',
          'Þ' => 'B',
          'ß' => 'Ss',
          'à' => 'a',
          'á' => 'a',
          'â' => 'a',
          'ã' => 'a',
          'ä' => 'a',
          'å' => 'a',
          'æ' => 'e',
          'ç' => 'c',
          'è' => 'e',
          'é' => 'e',
          'ê' => 'e',
          'ë' => 'e',
          'ì' => 'i',
          'í' => 'i',
          'î' => 'i',
          'ï' => 'i',
          'ð' => 'o',
          'ñ' => 'n',
          'ò' => 'o',
          'ó' => 'o',
          'ô' => 'o',
          'õ' => 'o',
          'ö' => 'o',
          'ø' => 'o',
          'ù' => 'u',
          'ú' => 'u',
          'û' => 'u',
          'ý' => 'y',
          'ý' => 'y',
          'þ' => 'b',
          'ÿ' => 'y',
          ' ' => '_'
        );
        return strtr($string, $normal);
    }

}
