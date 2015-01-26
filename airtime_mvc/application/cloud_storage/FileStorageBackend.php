<?php

class FileStorageBackend extends StorageBackend
{
    //Stub class
    public function FileStorageBackend()
    {
    }

    public function getAbsoluteFilePath($resourceId)
    {
        //TODO
        return $resourceId;
    }

    public function getSignedURL($resourceId)
    {
        return "";
    }

    public function getFileSize($resourceId)
    {
        //TODO
        return filesize($resourceId);
    }

    public function deletePhysicalFile($resourceId)
    {
        //TODO
    }

    public function deleteAllCloudFileObjects()
    {
        return "";
    }

    public function getFilePrefix()
    {
        return "";
    }
}