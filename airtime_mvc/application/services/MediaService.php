<?php

require_once('ProxyStorageBackend.php');

class Application_Service_MediaService
{
    public static function processUploadedFile($callbackUrl, $originalFilename, $ownerId)
    {
        $CC_CONFIG = Config::getConfig();
        $apiKey = $CC_CONFIG["apiKey"][0];

        $tempFilePath = $_FILES['file']['tmp_name'];
        $tempFileName = basename($tempFilePath);

        //Only accept files with a file extension that we support.
        $fileExtension = pathinfo($originalFilename, PATHINFO_EXTENSION);
        if (!in_array(strtolower($fileExtension), explode(",", "ogg,mp3,oga,flac,wav,m4a,mp4,opus"))) {
            @unlink($tempFilePath);
            throw new Exception("Bad file extension.");
        }

        //TODO: Remove uploadFileAction from ApiController.php **IMPORTANT** - It's used by the recorder daemon...

        $importedStorageDirectory = "";
        if ($CC_CONFIG["current_backend"] == "file") {
            $storDir = Application_Model_MusicDir::getStorDir();
            $importedStorageDirectory = $storDir->getDirectory() . "/imported/" . $ownerId;
        }

        try {
            //Copy the temporary file over to the "organize" folder so that it's off our webserver
            //and accessible by airtime_analyzer which could be running on a different machine.
            $newTempFilePath = Application_Model_StoredFile::copyFileToStor($tempFilePath, $originalFilename);
        } catch (Exception $e) {
            @unlink($tempFilePath);
            Logging::error($e->getMessage());
            return;
        }

        //Dispatch a message to airtime_analyzer through RabbitMQ,
        //notifying it that there's a new upload to process!
        $storageBackend = new ProxyStorageBackend($CC_CONFIG["current_backend"]);
        Application_Model_RabbitMq::SendMessageToAnalyzer($newTempFilePath,
            $importedStorageDirectory, basename($originalFilename),
            $callbackUrl, $apiKey,
            $CC_CONFIG["current_backend"],
            $storageBackend->getFilePrefix());
    }


    /**
     * @param $fileId
     * @param bool $inline Set the Content-Disposition header to inline to prevent a download dialog from popping up (or attachment if false)
     * @throws Exception
     * @throws FileNotFoundException
     */
    public static function streamFileDownload($fileId, $inline=false)
    {
        $media = Application_Model_StoredFile::RecallById($fileId);
        if ($media == null) {
            throw new FileNotFoundException();
        }
        $filepath = $media->getFilePath();
        // Make sure we don't have some wrong result beecause of caching
        clearstatcache();
        $media = Application_Model_StoredFile::RecallById($fileId);
        if ($media == null) {
            throw new FileNotFoundException();
        }

        // Make sure we don't have some wrong result beecause of caching
        clearstatcache();

        if ($media->getPropelOrm()->isValidPhysicalFile()) {
            $filename = $media->getPropelOrm()->getFilename();

            //Download user left clicks a track and selects Download.
            if (!$inline) {
                //We are using Content-Disposition to specify
                //to the browser what name the file should be saved as.
                header('Content-Disposition: attachment; filename="' . $filename . '"');
            } else {
                //user clicks play button for track and downloads it.
                header('Content-Disposition: inline; filename="' . $filename . '"');
            }

            self::smartReadFile($media);
            exit;
        } else {
            throw new FileNotFoundException();
        }
    }


    /**
     * Reads the requested portion of a file and sends its contents to the client with the appropriate headers.
     *
     * This HTTP_RANGE compatible read file function is necessary for allowing streaming media to be skipped around in.
     *
     * @param CcFile $media
     * @return void
     *
     * @link https://groups.google.com/d/msg/jplayer/nSM2UmnSKKA/Hu76jDZS4xcJ
     * @link http://php.net/manual/en/function.readfile.php#86244
     */
    private static function smartReadFile($media)
    {
        $filepath = $media->getFilePath();
        $size= $media->getFileSize();
        $mimeType = $media->getPropelOrm()->getDbMime();

        $fm = @fopen($filepath, 'rb');
        if (!$fm) {
            header ("HTTP/1.1 505 Internal server error");

            return;
        }

        $begin = 0;
        $end   = $size - 1;

        if (isset($_SERVER['HTTP_RANGE'])) {
            if (preg_match('/bytes=\h*(\d+)-(\d*)[\D.*]?/i', $_SERVER['HTTP_RANGE'], $matches)) {
                $begin = intval($matches[1]);
                if (!empty($matches[2])) {
                    $end = intval($matches[2]);
                }
            }
        }

        if (isset($_SERVER['HTTP_RANGE'])) {
            header('HTTP/1.1 206 Partial Content');
        } else {
            header('HTTP/1.1 200 OK');
        }
        header("Content-Type: $mimeType");
        header('Cache-Control: public, must-revalidate, max-age=0');
        header('Pragma: no-cache');
        header('Accept-Ranges: bytes');
        header('Content-Length:' . (($end - $begin) + 1));
        if (isset($_SERVER['HTTP_RANGE'])) {
            header("Content-Range: bytes $begin-$end/$size");
        }
        header("Content-Transfer-Encoding: binary");

        //We can have multiple levels of output buffering. Need to
        //keep looping until all have been disabled!!!
        //http://www.php.net/manual/en/function.ob-end-flush.php
        while (@ob_end_flush());

        // NOTE: We can't use fseek here because it does not work with streams
        // (a.k.a. Files stored in the cloud)
        while(!feof($fm) && (connection_status() == 0)) {
            echo fread($fm, 1024 * 8);
        }
        fclose($fm);
    }
}

