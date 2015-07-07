<?php

/**
 * Skeleton subclass for representing a row from the 'cc_files' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.campcaster
 */

class InvalidMetadataException extends Exception
{
}

class FileNotFoundException extends Exception
{
}

class OverDiskQuotaException extends Exception
{

}

class CcFiles extends BaseCcFiles {

    const MUSIC_DIRS_STOR_PK = 1;

    const IMPORT_STATUS_SUCCESS = 0;
    const IMPORT_STATUS_PENDING = 1;
    const IMPORT_STATUS_FAILED = 2;


    //fields that are not modifiable via our RESTful API
    private static $blackList = array(
        'id',
        'directory',
        'filepath',
        'file_exists',
        'mtime',
        'utime',
        'lptime',
        'silan_check',
        'soundcloud_id',
        'is_scheduled',
        'is_playlist'
    );

    //fields we should never expose through our RESTful API
    private static $privateFields = array(
        'file_exists',
        'silan_check',
        'is_scheduled',
        'is_playlist'
    );

    /**
     * Retrieve a sanitized version of the file metadata, suitable for public access.
     * @param $fileId
     */
    public static function getSanitizedFileById($fileId)
    {
        $file = CcFilesQuery::create()->findPk($fileId);
        if ($file) {
            return CcFiles::sanitizeResponse($file);
        } else {
            throw new FileNotFoundException();
        }
    }

    /** Used to create a CcFiles object from an array containing metadata and a file uploaded by POST.
     *  This is used by our Media REST API!
     * @param $fileArray An array containing metadata for a CcFiles object.
     *
     * @return object the sanitized response
     * @throws Exception
     */
    public static function createFromUpload($fileArray)
    {
        if (Application_Model_Systemstatus::isDiskOverQuota()) {
            throw new OverDiskQuotaException();
        }

        /* If full_path is set, the post request came from ftp.
         * Users are allowed to upload folders via ftp. If this is the case
         * we need to include the folder name with the file name, otherwise
         * files won't get removed from the organize folder.
         */

        //Extract the original filename, which we set as the temporary title for the track
        //until it's finished being processed by the analyzer.
        $originalFilename = $_FILES["file"]["name"];
        $tempFilePath = $_FILES['file']['tmp_name'];

        try {
            return self::createAndImport($fileArray, $tempFilePath, $originalFilename);
        } catch (Exception $e) {
            if (file_exists($tempFilePath)) {
                unlink($tempFilePath);
            }
            throw $e;
        }
    }

    /** Import a music file to the library from a local file on disk (something pre-existing).
     *  This function allows you to copy a file rather than move it, which is useful for importing
     *  static music files (like sample tracks).
     * @param string $filePath The full path to the audio file to import.
     * @param bool $copyFile True if you want to just copy the false, false if you want to move it (default false)
     * @throws Exception
     */
    public static function createFromLocalFile($fileArray, $filePath, $copyFile=false)
    {
        $info = pathinfo($filePath);
        $fileName =  basename($filePath).'.'.$info['extension'];
        self::createAndImport($fileArray, $filePath, $fileName, $copyFile);
    }

    /** Create a new CcFiles object/row and import a file for it.
     *  You shouldn't call this directly. Either use createFromUpload() or createFromLocalFile().
     * @param array $fileArray Any metadata to pre-fill for the audio file
     * @param string $filePath The full path to the audio file to import
     * @param string $originalFilename
     * @param bool $copyFile
     * @return mixed
     * @throws Exception
     * @throws PropelException
     */
    private static function createAndImport($fileArray, $filePath, $originalFilename, $copyFile=false)
    {
        $file = new CcFiles();

        try
        {
            $fileArray = self::removeBlacklistedFields($fileArray);

            self::validateFileArray($fileArray);

            $file->fromArray($fileArray);
            $file->setDbOwnerId(self::getOwnerId());
            $now  = new DateTime("now", new DateTimeZone("UTC"));
            $file->setDbTrackTitle($originalFilename);
            $file->setDbUtime($now);
            $file->setDbHidden(true);
            $file->save();

            //Only accept files with a file extension that we support.
            $fileExtension = pathinfo($originalFilename, PATHINFO_EXTENSION);
            if (!in_array(strtolower($fileExtension), explode(",", "ogg,mp3,oga,flac,wav,m4a,mp4,opus"))) {
                throw new Exception("Bad file extension.");
            }

            $callbackUrl = Application_Common_HTTPHelper::getStationUrl() . "/rest/media/" . $file->getPrimaryKey();

            Application_Service_MediaService::importFileToLibrary($callbackUrl, $filePath,
                $originalFilename, self::getOwnerId(), $copyFile);

            return CcFiles::sanitizeResponse($file);

        } catch (Exception $e) {
            $file->setDbImportStatus(self::IMPORT_STATUS_FAILED);
            $file->setDbHidden(true);
            $file->save();
            throw $e;
        }
    }

    /** Update a file with metadata specified in an array.
     * @param $fileId string The ID of the file to update in the DB.
     * @param $fileArray array An associative array containing metadata. Replaces those fields if they exist.
     * @return array A sanitized version of the file metadata array.
     * @throws Exception
     * @throws FileNotFoundException
     * @throws PropelException
     */
    public static function updateFromArray($fileId, $fileArray)
    {
        $file = CcFilesQuery::create()->findPk($fileId);

        $fileArray = self::removeBlacklistedFields($fileArray);
        $fileArray = self::stripTimeStampFromYearTag($fileArray);

        try {

            self::validateFileArray($fileArray);
            if ($file && isset($fileArray["resource_id"])) {

                $file->fromArray($fileArray, BasePeer::TYPE_FIELDNAME);

                //store the original filename
                $file->setDbFilepath($fileArray["filename"]);

                $fileSizeBytes = $fileArray["filesize"];
                if (!isset($fileSizeBytes) || $fileSizeBytes === false) {
                    throw new FileNotFoundException("Invalid filesize for $fileId");
                }

                $cloudFile = new CloudFile();
                $cloudFile->setStorageBackend($fileArray["storage_backend"]);
                $cloudFile->setResourceId($fileArray["resource_id"]);
                $cloudFile->setCcFiles($file);
                $cloudFile->save();

                Application_Model_Preference::updateDiskUsage($fileSizeBytes);
            } else if ($file) {

                // Since we check for this value when deleting files, set it first
                $file->setDbDirectory(self::MUSIC_DIRS_STOR_PK);

                $file->fromArray($fileArray, BasePeer::TYPE_FIELDNAME);

                //Our RESTful API takes "full_path" as a field, which we then split and translate to match
                //our internal schema. Internally, file path is stored relative to a directory, with the directory
                //as a foreign key to cc_music_dirs.
                if (isset($fileArray["full_path"])) {
                    $fileSizeBytes = filesize($fileArray["full_path"]);
                    if (!isset($fileSizeBytes) || $fileSizeBytes === false) {
                        throw new FileNotFoundException("Invalid filesize for $fileId");
                    }
                    Application_Model_Preference::updateDiskUsage($fileSizeBytes);

                    $fullPath = $fileArray["full_path"];
                    $storDir = Application_Model_MusicDir::getStorDir()->getDirectory();
                    $pos = strpos($fullPath, $storDir);

                    if ($pos !== FALSE) {
                        assert($pos == 0); //Path must start with the stor directory path

                        $filePathRelativeToStor = substr($fullPath, strlen($storDir));
                        $file->setDbFilepath($filePathRelativeToStor);
                    }
                }
            } else {
                throw new FileNotFoundException();
            }

            $now = new DateTime("now", new DateTimeZone("UTC"));
            $file->setDbMtime($now);
            $file->save();
        }
        catch (FileNotFoundException $e)
        {
            $file->setDbImportStatus(self::IMPORT_STATUS_FAILED);
            $file->setDbHidden(true);
            $file->save();
            throw $e;
        }

        return CcFiles::sanitizeResponse($file);
    }

    /** Delete a file from the database and disk (or cloud).
     * @param $id The file ID
     * @throws DeleteScheduledFileException
     * @throws Exception
     * @throws FileNoPermissionException
     * @throws FileNotFoundException
     * @throws PropelException
     */
    public static function deleteById($id)
    {
        $file = CcFilesQuery::create()->findPk($id);
        if ($file) {
            $con = Propel::getConnection();
            $storedFile = Application_Model_StoredFile::RecallById($id, $con);
            $storedFile->delete();
        } else {
            throw new FileNotFoundException();
        }

    }


    private static function validateFileArray(&$fileArray)
    {
        // Sanitize any wildly incorrect metadata before it goes to be validated
        FileDataHelper::sanitizeData($fileArray);

        // EditAudioMD form is used here for validation
        $fileForm = new Application_Form_EditAudioMD();
        $fileForm->startForm(0); //The file ID doesn't matter here
        $fileForm->populate($fileArray);

        /*
         * Here we are truncating metadata of any characters greater than the
         * max string length set in the database. In the rare case a track's
         * genre is more than 64 chars, for example, we don't want to reject
         * tracks for that reason
         */
        foreach($fileArray as $tag => &$value) {
            if ($fileForm->getElement($tag)) {
                $stringLengthValidator = $fileForm->getElement($tag)->getValidator('StringLength');
                //$stringLengthValidator will be false if the StringLength validator doesn't exist on the current element
                //in which case we don't have to truncate the extra characters
                if ($stringLengthValidator) {
                    $value = substr($value, 0, $stringLengthValidator->getMax());
                }

                $value = self::stripInvalidUtf8Characters($value);
            }
        }

        if (!$fileForm->isValidPartial($fileArray)) {
            $errors = $fileForm->getErrors();
            $messages = $fileForm->getMessages();
            Logging::error($messages);
            throw new Exception("Data validation failed: $errors - $messages");
        }

        return true;
    }


    public function getCueLength()
	{
		$cuein = $this->getDbCuein();
		$cueout = $this->getDbCueout();
		
		$cueinSec = Application_Common_DateHelper::calculateLengthInSeconds($cuein);
		$cueoutSec = Application_Common_DateHelper::calculateLengthInSeconds($cueout);
		$lengthSec = bcsub($cueoutSec, $cueinSec, 6);
		
		$length = Application_Common_DateHelper::secondsToPlaylistTime($lengthSec);
		
		return $length;
	}

    public function setDbTrackNumber($v)
    {
        $max = pow(2, 31)-1;
        $v = ($v > $max) ? $max : $v;

        return parent::setDbTrackNumber($v);
    }

    // returns true if the file exists and is not hidden
    public function visible() {
        return $this->getDbFileExists() && !$this->getDbHidden();
    }

    public function reassignTo($user) 
    {
        $this->setDbOwnerId( $user->getDbId() );
        $this->save();
    }

    /**
     *
     * Strips out the private fields we do not want to send back in API responses
     *
     * @param CcFiles $file a CcFiles object
     *
     * @return array
     */
    //TODO: rename this function?
    public static function sanitizeResponse($file) {
        $response = $file->toArray(BasePeer::TYPE_FIELDNAME);

        foreach (self::$privateFields as $key) {
            unset($response[$key]);
        }

        $mime = $file->getDbMime();
        if (!empty($mime)) {
            // Get an extension based on the file's mime type and change the path to use this extension
            $path = pathinfo($file->getDbFilepath());
            $ext = FileDataHelper::getFileExtensionFromMime($mime);
            $response["filepath"] = ($path["dirname"] . '/' . $path["filename"] . $ext);
        }

        return $response;
    }

    /**
     * Returns the file size in bytes.
     */
    public function getFileSize()
    {
        return $this->getDbFilesize();
    }

    public function getFilename()
    {
        $info = pathinfo($this->getAbsoluteFilePath());
        //filename doesn't contain the extension because PHP is awful
        return $info['filename'].".".$info['extension'];
    }

    /**
     * Returns the file's absolute file path stored on disk.
     */
    public function getURLsForTrackPreviewOrDownload()
    {
        return array($this->getAbsoluteFilePath());
    }

    /**
     * Returns the file's absolute file path stored on disk.
     */
    public function getAbsoluteFilePath()
    {
        $music_dir = Application_Model_MusicDir::getDirByPK($this->getDbDirectory());
        if (!$music_dir) {
            throw new Exception("Invalid music_dir for file " . $this->getDbId() . " in database.");
        }
        $directory = $music_dir->getDirectory();
        $filepath  = $this->getDbFilepath();
        return Application_Common_OsPath::join($directory, $filepath);
    }

    /**
     *
     * Strips out fields from incoming request data that should never be modified
     * from outside of Airtime
     * @param array $data
     */
    private static function removeBlacklistedFields($data)
    {
        foreach (self::$blackList as $key) {
            unset($data[$key]);
        }

        return $data;
    }


    private static function getOwnerId()
    {
        try {
            if (Zend_Auth::getInstance()->hasIdentity()) {
                $service_user = new Application_Service_UserService();
                return $service_user->getCurrentUser()->getDbId();
            } else {
                $defaultOwner = CcSubjsQuery::create()
                    ->filterByDbType('A')
                    ->orderByDbId()
                    ->findOne();
                if (!$defaultOwner) {
                    // what to do if there is no admin user?
                    // should we handle this case?
                    return null;
                }
                return $defaultOwner->getDbId();
            }
        } catch(Exception $e) {
            Logging::info($e->getMessage());
        }
    }
    /*
     * It's possible that the year tag will be a timestamp but Airtime doesn't support this.
     * The year field in cc_files can only be 16 chars max.
     *
     * This functions strips the year field of it's timestamp, if one, and leaves just the year
     */
    private static function stripTimeStampFromYearTag($metadata)
    {
        if (isset($metadata["year"])) {
            if (preg_match("/^(\d{4})-(\d{2})-(\d{2})(?:\s+(\d{2}):(\d{2}):(\d{2}))?$/", $metadata["year"])) {
                $metadata["year"] = substr($metadata["year"], 0, 4);
            }
        }
        return $metadata;
    }

    private static function stripInvalidUtf8Characters($string)
    {
        //Remove invalid UTF-8 characters
        //reject overly long 2 byte sequences, as well as characters above U+10000 and replace with ?
        $string = preg_replace('/[\x00-\x08\x10\x0B\x0C\x0E-\x19\x7F]'.
            '|[\x00-\x7F][\x80-\xBF]+'.
            '|([\xC0\xC1]|[\xF0-\xFF])[\x80-\xBF]*'.
            '|[\xC2-\xDF]((?![\x80-\xBF])|[\x80-\xBF]{2,})'.
            '|[\xE0-\xEF](([\x80-\xBF](?![\x80-\xBF]))|(?![\x80-\xBF]{2})|[\x80-\xBF]{3,})/S',
            '?', $string );

        //reject overly long 3 byte sequences and UTF-16 surrogates and replace with ?
        $string = preg_replace('/\xE0[\x80-\x9F][\x80-\xBF]'.
            '|\xED[\xA0-\xBF][\x80-\xBF]/S','?', $string );

        //Do a final encoding conversion to
        $string = mb_convert_encoding($string, 'UTF-8', 'UTF-8');
        return $string;
    }

    private function removeEmptySubFolders($path)
    {
        exec("find $path -empty -type d -delete");
    }


    /**
     * Checks if the file is a regular file that can be previewed and downloaded.
     */
    public function isValidPhysicalFile()
    {
        return is_file($this->getAbsoluteFilePath());
    }
    
    /**
     * 
     * Deletes the file from the stor directory on disk.
     */
    public function deletePhysicalFile()
    {
        $filepath = $this->getAbsoluteFilePath();
        if (file_exists($filepath)) {
            unlink($filepath);
        } else {
            throw new Exception("Could not locate file ".$filepath);
        }
    }
    
    /**
     * 
     * This function refers to the file's Amazon S3 resource id.
     * Returns null because cc_files are stored on local disk.
     */
    public function getResourceId()
    {
        return null;
    }
    
    public function getCcFileId()
    {
        return $this->id;
    }
    
} // CcFiles
