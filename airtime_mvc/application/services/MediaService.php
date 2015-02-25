<?php

require_once('ProxyStorageBackend.php');
require_once("FileIO.php");

class Application_Service_MediaService
{
    /** Move (or copy) a file to the stor/organize directory and send it off to the
    analyzer to be processed.
     * @param $callbackUrl
     * @param $filePath string Path to the local file to import to the library
     * @param $originalFilename string The original filename, if you want it to be preserved after import.
     * @param $ownerId string The ID of the user that will own the file inside Airtime.
     * @param $copyFile bool True if you want to copy the file to the "organize" directory, false if you want to move it (default)
     * @return Ambigous
     * @throws Exception
     */
    public static function importFileToLibrary($callbackUrl, $filePath, $originalFilename, $ownerId, $copyFile)
    {
        $CC_CONFIG = Config::getConfig();
        $apiKey = $CC_CONFIG["apiKey"][0];

        $importedStorageDirectory = "";
        if ($CC_CONFIG["current_backend"] == "file") {
            $storDir = Application_Model_MusicDir::getStorDir();
            $importedStorageDirectory = $storDir->getDirectory() . "/imported/" . $ownerId;
        }

        //Copy the temporary file over to the "organize" folder so that it's off our webserver
        //and accessible by airtime_analyzer which could be running on a different machine.
        $newTempFilePath = Application_Model_StoredFile::moveFileToStor($filePath, $originalFilename, $copyFile);

        //Dispatch a message to airtime_analyzer through RabbitMQ,
        //notifying it that there's a new upload to process!
        $storageBackend = new ProxyStorageBackend($CC_CONFIG["current_backend"]);
        Application_Model_RabbitMq::SendMessageToAnalyzer($newTempFilePath,
            $importedStorageDirectory, basename($originalFilename),
            $callbackUrl, $apiKey,
            $CC_CONFIG["current_backend"],
            $storageBackend->getFilePrefix());

        return $newTempFilePath;
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

            $filepath = $media->getFilePath();
            $size= $media->getFileSize();
            $mimeType = $media->getPropelOrm()->getDbMime();
            Application_Common_FileIO::smartReadFile($filepath, $size, $mimeType);
            exit;
        } else {
            throw new FileNotFoundException();
        }
    }





}

