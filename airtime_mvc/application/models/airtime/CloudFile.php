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
        //$pathToScript = isset($_SERVER['AIRTIME_BASE']) ? $_SERVER['AIRTIME_BASE']."cloud_storage_deleter.py" : "/home/denise/airtime/cloud_storage_deleter.py";
        
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
}
