<?php

require_once 'StorageBackend.php';

class Amazon_S3 extends StorageBackend
{
    private $zendServiceAmazonS3;
    
    public function Amazon_S3()
    {
        $CC_CONFIG = Config::getConfig();
        
        $this->setBucket($CC_CONFIG['storage_backend']['bucket']);
        $this->setAccessKey($CC_CONFIG['storage_backend']['api_key']);
        $this->setSecretKey($CC_CONFIG['storage_backend']['api_key_secret']);
        
        $this->zendServiceAmazonS3 = new Zend_Service_Amazon_S3(
            $this->getAccessKey(),
            $this->getSecretKey());
    }
    
    public function getAbsoluteFilePath($resourceId)
    {
        $endpoint = $this->zendServiceAmazonS3->getEndpoint();
        $scheme = $endpoint->getScheme();
        $host = $endpoint->getHost();
        $bucket = $this->getBucket();
        return "$scheme://$bucket.$host/".utf8_encode($resourceId);
    }

    public function getSignedURL($resourceId)
    {
        //URL will be active for 30 minutes
        $expires = time()+1800;
        
        $bucket = $this->getBucket();
        $secretKey = $this->getSecretKey();
        $accessKey = $this->getAccessKey();
        
        $string_to_sign = utf8_encode("GET\n\n\n$expires\n/$bucket/$resourceId");
        // We need to urlencode the entire signature in case the hashed signature
        // has spaces. (NOTE: utf8_encode() does not work here because it turns
        // spaces into non-breaking spaces)
        $signature = urlencode(base64_encode((hash_hmac("sha1", $string_to_sign, $secretKey, true))));
        
        $resourceURL = $this->getAbsoluteFilePath($resourceId);
        return $resourceURL."?AWSAccessKeyId=$accessKey&Expires=$expires&Signature=$signature";
    }
    
    public function getFileSize($resourceId)
    {
        $bucket = $this->getBucket();
        
        $amz_resource = utf8_encode("$bucket/$resourceId");
        $amz_resource_info = $this->zendServiceAmazonS3->getInfo($amz_resource);
        return $amz_resource_info["size"];
    }
    
    public function deletePhysicalFile($resourceId)
    {
        $bucket = $this->getBucket();
        $amz_resource = utf8_encode("$bucket/$resourceId");
        
        if ($this->zendServiceAmazonS3->isObjectAvailable($amz_resource)) {
           // removeObject() returns true even if the object was not deleted (bug?)
           // so that is not a good way to do error handling. isObjectAvailable()
           // does however return the correct value; We have to assume that if the
           // object is available the removeObject() function will work.
           $this->zendServiceAmazonS3->removeObject($amz_resource);
           } else {
               throw new Exception("ERROR: Could not locate object on Amazon S3");
           }
    }
}
