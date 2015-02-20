<?php

require_once 'ProxyStorageBackend.php';

/**
 * Skeleton subclass for representing a row from the 'cloud_file' table.
 *
 * Each cloud_file has a corresponding cc_file referenced as a foreign key.
 * The file's metadata is stored in the cc_file table. This, cloud_file,
 * table represents files that are stored in the cloud.
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.airtime
 */
class CloudFile extends BaseCloudFile
{
    private $proxyStorageBackend;

    /**
     * Returns a signed URL to the file's object on Amazon S3. Since we are
     * requesting the file's object via this URL, it needs to be signed because
     * all objects stored on Amazon S3 are private.
     */
    public function getURLForTrackPreviewOrDownload()
    {
        if ($this->proxyStorageBackend == null) {
            $this->proxyStorageBackend = new ProxyStorageBackend($this->getStorageBackend());
        }
        return $this->proxyStorageBackend->getSignedURL($this->getResourceId());
    }
    
    /**
     * 
     * Returns a url to the file's object on Amazon S3.
     */
    public function getAbsoluteFilePath()
    {
        if ($this->proxyStorageBackend == null) {
            $this->proxyStorageBackend = new ProxyStorageBackend($this->getStorageBackend());
        }
        return $this->proxyStorageBackend->getAbsoluteFilePath($this->getResourceId());
    }
    
    /**
     * Returns the file size in bytes.
     */
    public function getFileSize()
    {
        if ($this->proxyStorageBackend == null) {
            $this->proxyStorageBackend = new ProxyStorageBackend($this->getStorageBackend());
        }
        return $this->proxyStorageBackend->getFileSize($this->getResourceId());
    }
    
    public function getFilename()
    {
        return $this->getDbFilepath();
    }
    
    /**
     * Checks if the file is a regular file that can be previewed and downloaded.
     */
    public function isValidPhysicalFile()
    {
        // We don't need to check if the cloud file is a valid file because
        // before it is imported the Analyzer runs it through Liquidsoap
        // to check its playability. If Liquidsoap can't play the file it
        // does not get imported into the Airtime library.
        return true;
    }
    
    /**
     * 
     * Deletes the file from cloud storage
     */
    public function deletePhysicalFile()
    {
        if ($this->proxyStorageBackend == null) {
            $this->proxyStorageBackend = new ProxyStorageBackend($this->getStorageBackend());
        }
        $this->proxyStorageBackend->deletePhysicalFile($this->getResourceId());
    }
    
    /**
     * 
     * Deletes the cc_file and cloud_file entries from the database.
     */
    public function delete(PropelPDO $con = NULL)
    {
        CcFilesQuery::create()->findPk($this->getCcFileId())->delete();
        parent::delete();
    }
}
