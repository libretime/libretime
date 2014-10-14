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
        $resource = $this->getResourceId();
        
        $expires = time()+$expires;
        $string_to_sign = "GET\n\n\n{$expires}\n/{$bucket}/{$resource}";
        $signature = base64_encode((hash_hmac("sha1", utf8_encode($string_to_sign), $s3_key_secret, TRUE)));
        
        $authentication_params = "AWSAccessKeyId={$s3_key}&Expires={$expires}&Signature={$signature}";
        
        $s3 = new Zend_Service_Amazon_S3($s3_key, $s3_key_secret);
        $endpoint = $s3->getEndpoint();
        $scheme = $endpoint->getScheme();
        $host = $endpoint->getHost();
        
        $url = "{$scheme}://{$host}/{$bucket}/".urlencode($resource)."?{$authentication_params}";
        Logging::info($url);
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
     * Deletes the file from cloud storage by executing a python script
     * that uses Apache Libcloud to connect with the cloud storage service
     * 
     * If the file was successfully deleted the filesize of that file is returned
     */
    public function deletePhysicalFile()
    {
        $CC_CONFIG = Config::getConfig();

        $provider = escapeshellarg($CC_CONFIG["cloud_storage"]["provider"]);
        $bucket = escapeshellarg($CC_CONFIG["cloud_storage"]["bucket"]);
        $apiKey = escapeshellarg($CC_CONFIG["cloud_storage"]["api_key"]);
        $apiSecret = escapeshellarg($CC_CONFIG["cloud_storage"]["api_key_secret"]);
        $objName = escapeshellarg($this->getResourceId());
        
        $command = "/usr/lib/airtime/pypo/bin/cloud_storage_deleter.py $provider $bucket $apiKey $apiSecret $objName 2>&1 echo $?";
        
        $output = shell_exec($command);
        if ($output != "") {
            if (stripos($output, 'filesize') === false) {
                Logging::info($output);
                throw new Exception("Could not delete file from cloud storage");
            }
        }
        
        $outputArr = json_decode($output, true);
        return $outputArr["filesize"];
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
    
    public function downloadFile()
    {
        $CC_CONFIG = Config::getConfig();
        
        $s3 = new Zend_Service_Amazon_S3($CC_CONFIG['cloud_storage']['api_key'], $CC_CONFIG['cloud_storage']['api_key_secret']);
        //$fileObj = $s3->getObject($CC_CONFIG['cloud_storage']['bucket']."/".$this->getResourceId());
        
        $response_stream = $s3->getObjectStream($CC_CONFIG['cloud_storage']['bucket']."/".$this->getResourceId());
        copy($response_stream->getStreamName(), "/tmp/".$this->getResourceId());
        Logging::info($response_stream);
    }
}
