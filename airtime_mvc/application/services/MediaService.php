<?php

class Application_Service_MediaService
{

    const PENDING_FILE_TIMEOUT_SECONDS = 3600;

    /**
     * @var array store an internal array of the pending files so we don't have
     *            to go to the database twice
     */
    private static $_pendingFiles;

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
        //and accessible by libretime-analyzer which could be running on a different machine.
        $newTempFilePath = Application_Model_StoredFile::moveFileToStor($filePath, $originalFilename, $copyFile);

        //Dispatch a message to libretime-analyzer through RabbitMQ,
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
     * @throws LibreTimeFileNotFoundException
     */
    public static function streamFileDownload($fileId, $inline=false)
    {
        $media = Application_Model_StoredFile::RecallById($fileId);
        if ($media == null) {
            throw new LibreTimeFileNotFoundException();
        }
        // Make sure we don't have some wrong result because of caching
        clearstatcache();

        $filePath = "";

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

            /*
            In this block of code below, we're getting the list of download URLs for a track
            and then streaming the file as the response. A file can be stored in more than one location,
            with the alternate locations used as a fallback, so that's why we're looping until we
            are able to actually send the file.

            This mechanism is used to try fetching our file from our internal S3 caching proxy server first.
            If the file isn't found there (or the cache is down), then we attempt to download the file
            directly from Amazon S3. We do this to save bandwidth costs!
            */

            $filePaths = $media->getFilePaths();
            assert(is_array($filePaths));

            do {
                //Read from $filePath and stream it to the browser.
                $filePath = array_shift($filePaths);
                try {
                    $size= $media->getFileSize();
                    $mimeType = $media->getPropelOrm()->getDbMime();
                    Application_Common_FileIO::smartReadFile($filePath, $size, $mimeType);
                    break; //Break out of the loop if we successfully read the file!
                } catch (LibreTimeFileNotFoundException $e) {
                    //If we have no alternate filepaths left, then let the exception bubble up.
                    if (sizeof($filePaths) == 0) {
                        throw $e;
                    }
                }
                //Retry with the next alternate filepath in the list
            } while (sizeof($filePaths) > 0);

            exit;

        } else {
            throw new LibreTimeFileNotFoundException($filePath);
        }
    }

    /**
     * Check if there are any files that have been stuck
     * in Pending status for over an hour
     *
     * @return bool true if there are any files stuck pending,
     *              otherwise false
     */
    public static function areFilesStuckInPending() {
        $oneHourAgo = gmdate(DEFAULT_TIMESTAMP_FORMAT, (microtime(true) - self::PENDING_FILE_TIMEOUT_SECONDS));
        self::$_pendingFiles = CcFilesQuery::create()
            ->filterByDbImportStatus(CcFiles::IMPORT_STATUS_PENDING)
            ->filterByDbUtime($oneHourAgo, Criteria::LESS_EQUAL)
            ->find();
        $pendingEpisodes = Application_Service_PodcastEpisodeService::getStuckPendingImports();
        return !self::$_pendingFiles->isEmpty() || !empty($pendingEpisodes);
    }

    /**
     * Clean up stuck imports by changing their import status to Failed
     */
    public static function clearStuckPendingImports() {
        $pendingEpisodes = Application_Service_PodcastEpisodeService::getStuckPendingImports();
        foreach (self::$_pendingFiles as $file) {
            /** @var $file CcFiles */
            $file->setDbImportStatus(CcFiles::IMPORT_STATUS_FAILED)->save();
        }
        foreach ($pendingEpisodes as $episode) {
            /** @var $episode PodcastEpisodes */
            $episode->delete();
        }
    }

}

