<?php



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
        return $CC_CONFIG["cloud_storage"]["host"]."/".$CC_CONFIG["cloud_storage"]["bucket"]."/" . urlencode($this->getResourceId());
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
    
    public function deletePhysicalFile()
    {
        //TODO: execute a python script that deletes the file from the cloud
        
        //Dispatch a message to airtime_analyzer through RabbitMQ,
        //notifying it that we need to delete a file from the cloud
        /*$CC_CONFIG = Config::getConfig();
        $apiKey = $CC_CONFIG["apiKey"][0];
        
        //If the file was successfully deleted from the cloud the analyzer
        //will make a request to the Media API to do the deletion cleanup.
        $callbackUrl = 'http://'.$_SERVER['HTTP_HOST'].'/rest/media/'.$file_id.'/delete-success';
        
        Application_Model_RabbitMq::SendDeleteMessageToAnalyzer(
            $callbackUrl, $this->_file->getDbResourceId(), $apiKey, 'delete');*/
    }
}
