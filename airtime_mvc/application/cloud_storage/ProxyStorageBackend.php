<?php

require_once 'StorageBackend.php';
require_once 'FileStorageBackend.php';
require_once 'Amazon_S3StorageBackend.php';

/**
 *
 * Controls access to the storage backend class where a file is stored.
 *
 */
class ProxyStorageBackend extends StorageBackend
{
    private $storageBackend;

    /**
     * Receives the file's storage backend and instantiates the appropriate
     * object.
     */
    public function ProxyStorageBackend($storageBackend)
    {
        $CC_CONFIG = Config::getConfig();

        //The storage backend in the airtime.conf directly corresponds to
        //the name of the class that implements it (eg. Amazon_S3), so we 
        //can easily create the right backend object dynamically:
        if ($storageBackend == "amazon_S3") {
            $this->storageBackend = new Amazon_S3StorageBackend($CC_CONFIG["amazon_S3"]);
        } else if ($storageBackend == "file") {
            $this->storageBackend = new FileStorageBackend();
        } else {
            $this->storageBackend = new $storageBackend($CC_CONFIG[$storageBackend]);
        }
    }

    public function getAbsoluteFilePath($resourceId)
    {
        return $this->storageBackend->getAbsoluteFilePath($resourceId);
    }

    public function getDownloadURLs($resourceId, $contentDispositionFilename)
    {
        return $this->storageBackend->getDownloadURLs($resourceId, $contentDispositionFilename);
    }

    public function deletePhysicalFile($resourceId)
    {
        $this->storageBackend->deletePhysicalFile($resourceId);
    }
    
    public function deleteAllCloudFileObjects()
    {
        $this->storageBackend->deleteAllCloudFileObjects();
    }

    public function getFilePrefix()
    {
        return $this->storageBackend->getFilePrefix();
    }
}
