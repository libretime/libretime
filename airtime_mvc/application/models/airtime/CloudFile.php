<?php

require_once 'Zend/Service/Amazon/S3.php';

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
    public function getAbsoluteFilePath()
    {
        $CC_CONFIG = Config::getConfig();
        return $this->get_s3_signed_url(
            $CC_CONFIG['cloud_storage']['api_key'],
            $CC_CONFIG['cloud_storage']['api_key_secret'],
            $CC_CONFIG['cloud_storage']['bucket']);
    }
    
    private function get_s3_signed_url($s3_key, $s3_key_secret, $bucket)
    {
        //should be longer than track length
        $expires = 120;
        $resource_id = $this->getResourceId();
        
        $expires = time()+$expires;
        $string_to_sign = utf8_encode("GET\n\n\n$expires\n/$bucket/$resource_id");
        // We need to urlencode the entire signature in case the hashed signature
        // has spaces. (NOTE: utf8_encode() does not work here because it turns
        // spaces into non-breaking spaces)
        $signature = urlencode(base64_encode((hash_hmac("sha1", $string_to_sign, $s3_key_secret, true))));
        
        $authentication_params = "AWSAccessKeyId=$s3_key&Expires=$expires&Signature=$signature";
        
        $s3 = new Zend_Service_Amazon_S3($s3_key, $s3_key_secret);
        $endpoint = $s3->getEndpoint();
        $scheme = $endpoint->getScheme();
        $host = $endpoint->getHost();
        $url = "$scheme://$host/$bucket/".utf8_encode($resource_id)."?$authentication_params";
        return $url;
    }
    
    public function getFileSize()
    {
        return strlen(file_get_contents($this->getAbsoluteFilePath()));
    }
    
    public function getFilename()
    {
        return $this->getDbFilepath();
    }
    
    public function isValidFile()
    {
        $ch = curl_init();
        curl_setopt_array($ch, array(
            CURLOPT_URL => $this->getAbsoluteFilePath(),
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
     * 
     * If the file was successfully deleted the filesize of that file is returned
     */
    public function deletePhysicalFile()
    {
        $CC_CONFIG = Config::getConfig();
        
        $s3 = new Zend_Service_Amazon_S3(
            $CC_CONFIG['cloud_storage']['api_key'],
            $CC_CONFIG['cloud_storage']['api_key_secret']);
        
        $bucket = $CC_CONFIG['cloud_storage']['bucket'];
        $resource_id = $this->getResourceId();
        $amz_resource = utf8_encode("$bucket/$resource_id");
        
        if ($s3->isObjectAvailable($amz_resource)) {
            $obj_info = $s3->getInfo($amz_resource);
            $filesize = $obj_info["size"];
            
            // removeObject() returns true even if the object was not deleted (bug?)
            // so that is not a good way to do error handling. isObjectAvailable()
            // does however return the correct value; We have to assume that if the
            // object is available the removeObject() function will work.
            $s3->removeObject($amz_resource);
            return $filesize;
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
