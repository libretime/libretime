<?php

class FileDataHelper
{
    public static function getAudioMimeTypeArray()
    {
        return [
            'audio/ogg' => 'ogg',
            'application/ogg' => 'ogg',
            'audio/vorbis' => 'ogg',
            'audio/mp3' => 'mp3',
            'audio/mpeg' => 'mp3',
            'audio/mpeg3' => 'mp3',
            'audio/x-aac' => 'aac',
            'audio/aac' => 'aac',
            'audio/aacp' => 'aac',
            'audio/mp4' => 'm4a',
            'video/mp4' => 'mp4',
            'audio/x-flac' => 'flac',
            'audio/flac' => 'flac',
            'audio/wav' => 'wav',
            'audio/x-wav' => 'wav',
            'audio/mp2' => 'mp2',
            'audio/mp1' => 'mp1',
            'audio/x-ms-wma' => 'wma',
            'audio/basic' => 'au',
        ];
    }

    public static function getUploadAudioMimeTypeArray()
    {
        $mimes = self::getAudioMimeTypeArray();
        unset($mimes['audio/x-ms-wma']);

        return $mimes;
    }

    /**
     * We want to throw out invalid data and process the upload successfully
     * at all costs, so check the data and sanitize it if necessary.
     *
     * @param array $data array containing new file metadata
     */
    public static function sanitizeData(&$data)
    {
        if (array_key_exists('track_number', $data)) {
            // If the track number isn't numeric, this will return 0
            $data['track_number'] = intval($data['track_number']);
        }
        if (array_key_exists('year', $data)) {
            // If the track number isn't numeric, this will return 0
            $data['year'] = intval($data['year']);
        }
        if (array_key_exists('bpm', $data)) {
            // Some BPM tags are silly and include the word "BPM". Let's strip that...
            $data['bpm'] = str_ireplace('BPM', '', $data['bpm']);
            // This will convert floats to ints too.
            $data['bpm'] = intval($data['bpm']);
        }
        if (array_key_exists('track_type_id', $data)) {
            $data['track_type_id'] = intval($data['track_type_id']);
        }
    }

    /**
     * Return a suitable extension for the given file.
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
            return '.' . static::getAudioMimeTypeArray()[$mime];
        } catch (Exception $e) {
            throw new Exception("Unknown file type: {$mime}");
        }
    }

    /**
     * Gets data URI from artwork file.
     *
     * @param string $file
     * @param int    $size
     * @param string $filepath
     *
     * @return string Data URI for artwork
     */
    public static function getArtworkData($file, $size, $filepath = false)
    {
        $baseUrl = Config::getPublicUrl();
        $default = $baseUrl . 'css/images/no-cover.jpg';

        if ($filepath != false) {
            $path = $filepath . $file . '-' . $size;
            if (!file_exists($path)) {
                $get_file_content = $default;
            } else {
                $get_file_content = file_get_contents($path);
            }
        } else {
            $path = Config::getStoragePath() . $file . '-' . $size;
            if (!file_exists($path)) {
                $get_file_content = $default;
            } else {
                $get_file_content = file_get_contents($path);
            }
        }

        return $get_file_content;
    }

    /**
     * Add artwork file.
     *
     * @param string $analyzeFile
     * @param string $filename
     * @param string $importDir
     * @param string $DbPath
     *
     * @return string Path to artwork
     */
    public static function saveArtworkData($analyzeFile, $filename, $importDir = null, $DbPath = null)
    {
        if (class_exists('getID3')) {
            $getID3 = new \getID3();
            $getFileInfo = $getID3->analyze($analyzeFile);
        } else {
            $getFileInfo = [];
            Logging::error('Failed to load getid3 library. Please upgrade Libretime.');
        }

        if (isset($getFileInfo['comments']['picture'][0])) {
            $get_img = '';
            $timestamp = time();
            $mime = $getFileInfo['comments']['picture'][0]['image_mime'];
            $Image = 'data:' . $mime . ';charset=utf-8;base64,' . base64_encode($getFileInfo['comments']['picture'][0]['data']);
            $base64 = @$Image;

            if (!file_exists($importDir . 'artwork/')) {
                if (!mkdir($importDir . 'artwork/', 0777, true)) {
                    Logging::error('Failed to create artwork directory.');

                    throw new Exception('Failed to create artwork directory.');
                }
            }

            $path_parts = pathinfo($filename);
            $file = $importDir . 'artwork/' . $path_parts['filename'];

            // Save Data URI
            if (file_put_contents($file, $base64)) {
                $get_img = $DbPath . 'artwork/' . $path_parts['filename'];
            } else {
                Logging::error('Could not save Data URI');
            }

            if ($mime == 'image/png') {
                $ext = 'png';
            } elseif ($mime == 'image/gif') {
                $ext = 'gif';
            } elseif ($mime == 'image/bmp') {
                $ext = 'bmp';
            } else {
                $ext = 'jpg';
            }
            self::resizeGroup($file, $ext);
        } else {
            $get_img = '';
        }

        return $get_img;
    }

    /**
     * Reset artwork.
     *
     * @param string $trackid
     *
     * @return string $get_img Path to artwork
     */
    public static function resetArtwork($trackid)
    {
        $file = Application_Model_StoredFile::RecallById($trackid);
        $md = $file->getMetadata();

        $fp = Config::getStoragePath();

        $dbAudioPath = $md['MDATA_KEY_FILEPATH'];
        $fullpath = $fp . $dbAudioPath;

        if (class_exists('getID3')) {
            $getID3 = new \getID3();
            $getFileInfo = $getID3->analyze($fullpath);
        } else {
            $getFileInfo = [];
            Logging::error('Failed to load getid3 library. Please upgrade Libretime.');
        }

        if (isset($getFileInfo['comments']['picture'][0])) {
            $get_img = '';
            $mime = $getFileInfo['comments']['picture'][0]['image_mime'];
            $Image = 'data:' . $getFileInfo['comments']['picture'][0]['image_mime'] . ';charset=utf-8;base64,' . base64_encode($getFileInfo['comments']['picture'][0]['data']);
            $base64 = @$Image;

            $audioPath = dirname($fullpath);
            $dbPath = dirname($dbAudioPath);
            $path_parts = pathinfo($fullpath);
            $file = $path_parts['filename'];

            // Save Data URI
            if (file_put_contents($audioPath . '/' . $file, $base64)) {
                $get_img = $dbPath . '/' . $file;
            } else {
                Logging::error('Could not save Data URI');
            }

            $rfile = $audioPath . '/' . $file;

            if ($mime == 'image/png') {
                $ext = 'png';
            } elseif ($mime == 'image/gif') {
                $ext = 'gif';
            } elseif ($mime == 'image/bmp') {
                $ext = 'bmp';
            } else {
                $ext = 'jpg';
            }
            self::resizeGroup($rfile, $ext);
        } else {
            $get_img = '';
        }

        return $get_img;
    }

    /**
     * Upload artwork.
     *
     * @param string $trackid
     * @param string $data
     *
     * @return string Path to artwork
     */
    public static function setArtwork($trackid, $data)
    {
        $file = Application_Model_StoredFile::RecallById($trackid);
        $md = $file->getMetadata();

        $fp = Config::getStoragePath();

        $dbAudioPath = $md['MDATA_KEY_FILEPATH'];
        $fullpath = $fp . $dbAudioPath;

        if ($data == '0') {
            $get_img = '';
            self::removeArtwork($trackid, $data);
        } else {
            $base64 = @$data;
            $mime = explode(';', $base64)[0];

            $audioPath = dirname($fullpath);
            $dbPath = dirname($dbAudioPath);
            $path_parts = pathinfo($fullpath);
            $file = $path_parts['filename'];

            // Save Data URI
            if (file_put_contents($audioPath . '/' . $file, $base64)) {
                $get_img = $dbPath . '/' . $file;
            } else {
                Logging::error('Could not save Data URI');
            }

            $rfile = $audioPath . '/' . $file;

            if ($mime == 'data:image/png') {
                $ext = 'png';
            } elseif ($mime == 'data:image/gif') {
                $ext = 'gif';
            } elseif ($mime == 'data:image/bmp') {
                $ext = 'bmp';
            } else {
                $ext = 'jpg';
            }
            self::resizeGroup($rfile, $ext);
        }

        return $get_img;
    }

    /**
     * Deletes just the artwork.
     *
     * @param mixed $trackid
     */
    public static function removeArtwork($trackid)
    {
        $file = Application_Model_StoredFile::RecallById($trackid);
        $md = $file->getMetadata();

        $fp = Config::getStoragePath();

        $dbMetadataPath = $md['MDATA_KEY_ARTWORK'];
        $fullpath = $fp . $dbMetadataPath;

        $audioPath = $fp . $md['MDATA_KEY_FILEPATH'];

        if (file_exists($fullpath)) {
            foreach (glob("{$fullpath}*", GLOB_NOSORT) as $filename) {
                
                // Do not delete the audio file.
                if($filename !== $audioPath){
                    unlink($filename);
                }
            }
        } else {
            throw new Exception('Could not locate file ' . $filepath);
        }

        return '';
    }

    /**
     * Resize artwork group.
     *
     * @param string $file
     * @param string $ext
     */
    public static function resizeGroup($file, $ext)
    {
        if (file_exists($file)) {
            self::resizeImage($file, $file . '-32.jpg', $ext, 32, 100);
            self::resizeImage($file, $file . '-64.jpg', $ext, 64, 100);
            self::resizeImage($file, $file . '-128.jpg', $ext, 128, 100);
            self::resizeImage($file, $file . '-256.jpg', $ext, 256, 100);
            self::resizeImage($file, $file . '-512.jpg', $ext, 512, 100);
            self::imgToDataURI($file . '-32.jpg', $file . '-32');
            self::imgToDataURI($file . '-64.jpg', $file . '-64');
            self::imgToDataURI($file . '-128.jpg', $file . '-128');
            self::imgToDataURI($file . '-256.jpg', $file . '-256');
        } else {
            Logging::error("The file {$file} does not exist");
        }
    }

    /**
     * Render image
     * Used in API to render JPEG.
     *
     * @param string $file
     */
    public static function renderImage($file)
    {
        $im = @imagecreatefromjpeg($file);
        $img = $im;

        if ($im) {
            header('Content-Type: image/jpeg');
            imagejpeg($img);
            imagedestroy($img);
        }
    }

    /**
     * Render Data URI
     * Used in API to render Data URI.
     *
     * @param string $dataFile
     */
    public static function renderDataURI($dataFile)
    {
        if ($filecontent = file_get_contents($dataFile) !== false) {
            $image = @file_get_contents($dataFile);
            $image = base64_encode($image);
            if (!$image || $image === '') {
                return;
            }
            $blob = base64_decode($image);
            $f = finfo_open();
            $mime_type = finfo_buffer($f, $blob, FILEINFO_MIME_TYPE);
            finfo_close($f);
            header('Content-Type: ' . $mime_type);
            echo $blob;
        } else {
            return;
        }
    }

    /**
     * Resize Image.
     *
     * @param string $orig_filename
     * @param string $converted_filename
     * @param string $ext
     * @param string $size               Default: 500
     * @param string $quality            Default: 75
     */
    public static function resizeImage($orig_filename, $converted_filename, $ext, $size = 500, $quality = 75)
    {
        $get_cont = file_get_contents($orig_filename);
        if ($ext == 'png') {
            $im = @imagecreatefrompng($get_cont);
        } elseif ($ext == 'gif') {
            $im = @imagecreatefromgif($get_cont);
        } else {
            $im = @imagecreatefromjpeg($get_cont);
        }

        // if one of those bombs, create an error image instead
        if (!$im) {
            $im = imagecreatetruecolor(150, 30);
            $bgc = imagecolorallocate($im, 255, 255, 255);
            $tc = imagecolorallocate($im, 0, 0, 0);
            imagefilledrectangle($im, 0, 0, 150, 30, $bgc);
            imagestring($im, 1, 5, 5, 'Error loading ' . $converted_filename, $tc);
        }

        // scale if appropriate
        if ($size) {
            $im = imagescale($im, $size);
        }

        $img = $im;
        imagejpeg($img, $converted_filename, $quality);
        imagedestroy($img);
    }

    /**
     * Convert image to Data URI.
     *
     * @param string $orig_filename
     * @param string $conv_filename
     */
    public static function imgToDataURI($orig_filename, $conv_filename)
    {
        $file = file_get_contents($orig_filename);
        $Image = 'data:image/jpeg;charset=utf-8;base64,' . base64_encode($file);
        $base64 = @$Image;

        // Save Data URI
        if (file_put_contents($conv_filename, $base64)) {
        } else {
            Logging::error('Could not save Data URI');
        }
    }

    /**
     * Track Type.
     *
     * @return string Track type key value
     */
    public static function saveTrackType()
    {
        if (isset($_COOKIE['tt_upload'])) {
            $tt = $_COOKIE['tt_upload'];
        } else {
            // Use default track type
            $tt = Application_Model_Preference::GetTrackTypeDefault();
        }

        return $tt;
    }
}
