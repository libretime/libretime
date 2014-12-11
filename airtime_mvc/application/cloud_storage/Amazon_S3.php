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
    
    public function deleteObjects()
    {
        $cloudFilePager = CloudFileQuery::create()
            ->filterByStorageBackend("amazon_S3")
            ->paginate($page=1, $maxPerPage=1000);
        
        if ($cloudFilePager->haveToPaginate()) {
            $numPages = $cloudFilePager->getLastPage();
            $currentPage = 1;
            while ($currentPage <= $numPages) {
                $cloudFilePager = CloudFileQuery::create()
                    ->filterByStorageBackend("amazon_S3")
                    ->paginate($page = $currentPage, $maxPerPage = 1000);

                $this->deleteObjectSet($cloudFilePager->getResults());

                $currentPage += 1;
            }
        } else {
            $this->deleteObjectSet($cloudFilePager->getResults());
        }
    }
    
    /**
     * Deletes objects from Amazon S3 1000 at a time.
     * 1000 is the max number of objects that can be deleted using the aws sdk
     * api, per request.
     */
    private function deleteObjectSet($cloudFiles)
    {
        if (!$cloudFiles->isEmpty()) {
            $cloudFilesToDelete = array();

            foreach ($cloudFiles as $cloudFile) {
                array_push($cloudFilesToDelete, array("Key" => $cloudFile->getResourceId()));
            }
        
            $this->s3Client->deleteObjects(array(
                "Bucket" => $this->getBucket(),
                "Objects" => $cloudFilesToDelete));
        }
    }
}
