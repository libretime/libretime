<?php

require_once 'StorageBackend.php';
require_once 'Billing.php';

use Aws\S3\S3Client;

class Amazon_S3StorageBackend extends StorageBackend
{
    
    private $s3Client;
    
    public function Amazon_S3StorageBackend($securityCredentials)
    {
        $this->setBucket($securityCredentials['bucket']);
        $this->setAccessKey($securityCredentials['api_key']);
        $this->setSecretKey($securityCredentials['api_key_secret']);

        $this->s3Client = S3Client::factory(array(
            'key' => $securityCredentials['api_key'],
            'secret' => $securityCredentials['api_key_secret'],
            'region' => $securityCredentials['region']
        ));
    }
    
    public function getAbsoluteFilePath($resourceId)
    {
        return $this->s3Client->getObjectUrl($this->getBucket(), $resourceId);
    }

    public function getSignedURL($resourceId)
    {
        return $this->s3Client->getObjectUrl($this->getBucket(), $resourceId, '+60 minutes');
    }
    
    public function deletePhysicalFile($resourceId)
    {
        $bucket = $this->getBucket();

        if ($this->s3Client->doesObjectExist($bucket, $resourceId)) {

            $result = $this->s3Client->deleteObject(array(
                'Bucket' => $bucket,
                'Key' => $resourceId,
            ));
        } else {
            throw new Exception("ERROR: Could not locate file to delete.");
        }
    }
    
    // This should only be called for station termination.
    // We are only deleting the file objects from Amazon S3.
    // Records in the database will remain in case we have to restore the files.
    public function deleteAllCloudFileObjects()
    {
        $bucket = $this->getBucket();
        $prefix = $this->getFilePrefix();

        //Add a trailing slash in for safety
        //(so that deleting /13/413 doesn't delete /13/41313 !)
        $prefix = $prefix . "/";

        //Do a bunch of safety checks to ensure we don't delete more than we intended.
        //An valid prefix is like "12/4312" for instance 4312.
        $slashPos = strpos($prefix, "/");
        if (($slashPos === FALSE) || //Slash must exist
            ($slashPos != 2) ||      //Slash must be the third character
            (strlen($prefix) > $slashPos) ||    //String must have something after the first slash
            (substr_count($prefix, "/") != 2))  //String must have two slashes
        {
            throw new Exception("Invalid file prefix in " . __FUNCTION__);
    }
        $this->s3Client->deleteMatchingObjects($bucket, $prefix);
    }

    public function getFilePrefix()
    {
        $hostingId = Billing::getClientInstanceId();
        $filePrefix = substr($hostingId, -2)."/".$hostingId;
        return $filePrefix;
    }
}
