<?php

require_once 'Zend/Service/Amazon/S3.php';

/**
 * 
 * This is a wrapper class for Zend_Service_Amazon_S3.
 * Zend_Service_Amazon_S3 doesn't have getters and setters for the bucket,
 * access key, or secret key. This functionality would greatly refine our
 * use of the service so we decided to write our own here.
 *
 */
class Amazon_S3
{
    private $bucket;
    private $accessKey;
    private $secretKey;
    private $zendServiceAmazonS3;
    
    function Amazon_S3()
    {
        $this->initZendServiceAmazonS3();
    }
    
    private function initZendServiceAmazonS3()
    {
        $CC_CONFIG = Config::getConfig();
        
        $this->setBucket($CC_CONFIG['cloud_storage']['bucket']);
        $this->setAccessKey($CC_CONFIG['cloud_storage']['api_key']);
        $this->setSecretKey($CC_CONFIG['cloud_storage']['api_key_secret']);
        
        $this->zendServiceAmazonS3 = new Zend_Service_Amazon_S3(
            $this->getAccessKey(),
            $this->getSecretKey());
    }
    
    public function getZendServiceAmazonS3()
    {
    	return $this->zendServiceAmazonS3;
    }
    
    public function getBucket()
    {
        return $this->bucket;
    }
    
    private function setBucket($bucket)
    {
        $this->bucket = $bucket;
    }
    
    public function getAccessKey()
    {
        return $this->accessKey;
    }
    
    private function setAccessKey($accessKey)
    {
        $this->accessKey = $accessKey;
    }
    
    public function getSecretKey()
    {
        return $this->secretKey;
    }
    
    private function setSecretKey($secretKey)
    {
        $this->secretKey = $secretKey;
    }
}