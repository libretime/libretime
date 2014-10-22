<?php

require_once 'Amazon_S3.php';

/**
 * Skeleton subclass for representing a row from the 'cloud_file' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.airtime
 */
class CloudFile extends BaseCloudFile
{

    public function getURLForTrackPreviewOrDownload()
    {
        return $this->getAbsoluteFilePath()."?".$this->getAuthenticationParams();
    }
    
    /**
     * 
     * Enter description here ...
     */
    public function getAbsoluteFilePath()
    {
        $amazon_s3 = new Amazon_S3();
        $zend_s3 = $amazon_s3->getZendServiceAmazonS3();
        $resource_id = $this->getResourceId();
        $endpoint = $zend_s3->getEndpoint();
        $scheme = $endpoint->getScheme();
        $host = $endpoint->getHost();
        $s3_bucket = $amazon_s3->getBucket();
        return "$scheme://$host/$s3_bucket/".utf8_encode($resource_id);
    }
    
    /**
     * 
     * Returns a string of authentication paramaters to append to the cloud
     * object's URL. We need this for track preview and download because the
     * objects are privately stored on Amazon S3.
     */
    public function getAuthenticationParams()
    {
        $expires = time()+120;
        $resource_id = $this->getResourceId();

        $amazon_s3 = new Amazon_S3();
        $s3_bucket = $amazon_s3->getBucket();
        $s3_secret_key = $amazon_s3->getSecretKey();
        $s3_access_key = $amazon_s3->getAccessKey();
        
        $string_to_sign = utf8_encode("GET\n\n\n$expires\n/$s3_bucket/$resource_id");
        // We need to urlencode the entire signature in case the hashed signature
        // has spaces. (NOTE: utf8_encode() does not work here because it turns
        // spaces into non-breaking spaces)
        $signature = urlencode(base64_encode((hash_hmac("sha1", $string_to_sign, $s3_secret_key, true))));
        
        return "AWSAccessKeyId=$s3_access_key&Expires=$expires&Signature=$signature";
    }
    
    public function getFileSize()
    {
        $amazon_s3 = new Amazon_S3();
        
        $zend_s3 = $amazon_s3->getZendServiceAmazonS3();
        $bucket = $amazon_s3->getBucket();
        $resource_id = $this->getResourceId();
        
        $amz_resource = utf8_encode("$bucket/$resource_id");
        $amz_resource_info = $zend_s3->getInfo($amz_resource);
        return $amz_resource_info["size"];
    }
    
    public function getFilename()
    {
        return $this->getDbFilepath();
    }
    
    public function isValidFile()
    {
        $ch = curl_init();
        curl_setopt_array($ch, array(
            CURLOPT_URL => $this->getURLForTrackPreviewOrDownload(),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_VERBOSE => false
        ));
        curl_exec($ch);
        $http_status = curl_getinfo($ch);
        
        if ($http_status["http_code"] === 200)
        {
            return true;
        } else {
            return false;
        }
    }
    
    /**
     * 
     * Deletes the file from Amazon S3
     */
    public function deletePhysicalFile()
    {
        $amazon_s3 = new Amazon_S3();
        $zend_s3 = $amazon_s3->getZendServiceAmazonS3();
        $bucket = $amazon_s3->getBucket();
        $resource_id = $this->getResourceId();
        $amz_resource = utf8_encode("$bucket/$resource_id");
        
        if ($zend_s3->isObjectAvailable($amz_resource)) {
            // removeObject() returns true even if the object was not deleted (bug?)
            // so that is not a good way to do error handling. isObjectAvailable()
            // does however return the correct value; We have to assume that if the
            // object is available the removeObject() function will work.
            $zend_s3->removeObject($amz_resource);
        } else {
            throw new Exception("ERROR: Could not locate object on Amazon S3");
        }
    }
    
    /**
     * 
     * Deletes the cloud_file's 'parent' object before itself
     */
    public function delete(PropelPDO $con = NULL)
    {
        CcFilesQuery::create()->findPk($this->getCcFileId())->delete();
        parent::delete();
    }
}
