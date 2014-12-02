<?php

/**
 * 
 * Provides access to file objects stored on a specific storage backend.
 */
abstract class StorageBackend
{
    private $bucket;
    private $accessKey;
    private $secretKey;
    
    /** Returns the file object's URL to the storage backend it is located on. */
    abstract public function getAbsoluteFilePath($resourceId);
    
    /** Returns the file object's signed URL. The URL must be signed since they
     *  privately stored on the storage backend. */
    abstract public function getSignedURL($resourceId);
    
    /** Returns the file's size in bytes. */
    abstract public function getFileSize($resourceId);
    
    /** Deletes the file from the storage backend. */
    abstract public function deletePhysicalFile($resourceId);
    
    protected function getBucket()
    {
        return $this->bucket;
    }
    
    protected function setBucket($bucket)
    {
        $this->bucket = $bucket;
    }
    
    protected function getAccessKey()
    {
        return $this->accessKey;
    }
    
    protected function setAccessKey($accessKey)
    {
        $this->accessKey = $accessKey;
    }
    
    protected function getSecretKey()
    {
        return $this->secretKey;
    }
    
    protected function setSecretKey($secretKey)
    {
        $this->secretKey = $secretKey;
    }
}
