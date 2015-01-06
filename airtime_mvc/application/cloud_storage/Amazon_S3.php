<?php

require_once 'StorageBackend.php';

use Aws\S3\S3Client;

class Amazon_S3 extends StorageBackend
{
    
    private $s3Client;
    
    public function Amazon_S3($securityCredentials)
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
    
    public function getFileSize($resourceId)
    {
        $obj = $this->s3Client->getObject(array(
            'Bucket' => $this->getBucket(),
            'Key' => $resourceId,
        ));
        if (isset($obj["ContentLength"])) {
            return (int)$obj["ContentLength"];
        } else {
            return 0;
        }
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
}
