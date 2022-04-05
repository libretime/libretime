<?php

/**
 * Controls access to the storage backend class where a file is stored.
 */
class ProxyStorageBackend extends StorageBackend
{
    private $storageBackend;

    /**
     * Receives the file's storage backend and instantiates the appropriate
     * object.
     *
     * @param mixed $storageBackend
     */
    public function __construct($storageBackend)
    {
        $CC_CONFIG = Config::getConfig();

        // The storage backend in the config file directly corresponds to
        // the name of the class that implements it, so we can create the
        // right backend object dynamically:
        if ($storageBackend == 'file') {
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
