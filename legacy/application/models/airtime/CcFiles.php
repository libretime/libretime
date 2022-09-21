<?php

/**
 * Skeleton subclass for representing a row from the 'cc_files' table.
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 */
class InvalidMetadataException extends Exception
{
}

class LibreTimeFileNotFoundException extends Exception
{
}

class OverDiskQuotaException extends Exception
{
}

class CcFiles extends BaseCcFiles
{
    public const MUSIC_DIRS_STOR_PK = 1;

    public const IMPORT_STATUS_SUCCESS = 0;
    public const IMPORT_STATUS_PENDING = 1;
    public const IMPORT_STATUS_FAILED = 2;

    // fields that are not modifiable via our RESTful API
    private static $blackList = [
        'id',
        'directory',
        'filepath',
        'file_exists',
        'mtime',
        'utime',
        'lptime',
        'silan_check',
        'is_scheduled',
        'is_playlist',
    ];

    // fields we should never expose through our RESTful API
    private static $privateFields = [
        'file_exists',
        'silan_check',
        'is_scheduled',
        'is_playlist',
    ];

    /**
     * Retrieve a sanitized version of the file metadata, suitable for public access.
     *
     * @param $fileId
     */
    public static function getSanitizedFileById($fileId)
    {
        $file = CcFilesQuery::create()->findPk($fileId);
        if ($file) {
            return CcFiles::sanitizeResponse($file);
        }

        throw new LibreTimeFileNotFoundException();
    }

    /** Used to create a CcFiles object from an array containing metadata and a file uploaded by POST.
     *  This is used by our Media REST API!
     *
     * @param $fileArray An array containing metadata for a CcFiles object
     *
     * @return object the sanitized response
     *
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

        // Extract the original filename, which we set as the temporary title for the track
        // until it's finished being processed by the analyzer.
        $originalFilename = $fileArray['file']['name'];
        $tempFilePath = $fileArray['file']['tmp_name'];

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
     *
     * @param string $filePath  the full path to the audio file to import
     * @param bool   $copyFile  True if you want to just copy the false, false if you want to move it (default false)
     * @param mixed  $fileArray
     *
     * @throws Exception
     */
    public static function createFromLocalFile($fileArray, $filePath, $copyFile = false)
    {
        $info = pathinfo($filePath);
        $fileName = basename($filePath) . '.' . $info['extension'];
        self::createAndImport($fileArray, $filePath, $fileName, $copyFile);
    }

    /** Create a new CcFiles object/row and import a file for it.
     *  You shouldn't call this directly. Either use createFromUpload() or createFromLocalFile().
     *
     * @param array  $fileArray        Any metadata to pre-fill for the audio file
     * @param string $filePath         The full path to the audio file to import
     * @param string $originalFilename
     * @param bool   $copyFile
     *
     * @return mixed
     *
     * @throws Exception
     * @throws PropelException
     */
    private static function createAndImport($fileArray, $filePath, $originalFilename, $copyFile = false)
    {
        $file = new CcFiles();

        try {
            // Only accept files with a file extension that we support.
            // Let the analyzer do the heavy lifting in terms of mime verification and playability
            $fileExtension = pathinfo($originalFilename, PATHINFO_EXTENSION);
            if (!in_array(strtolower($fileExtension), array_values(FileDataHelper::getUploadAudioMimeTypeArray()))) {
                throw new Exception('Bad file extension.');
            }

            $fileArray = self::removeBlacklistedFields($fileArray);

            self::validateFileArray($fileArray);

            // Early md5dum processing
            $md5 = md5_file($filePath);

            $importedStorageDir = Config::getStoragePath() . 'imported/' . self::getOwnerId() . '/';
            $importedDbPath = 'imported/' . self::getOwnerId() . '/';
            $artwork = FileDataHelper::saveArtworkData($filePath, $originalFilename, $importedStorageDir, $importedDbPath);
            $trackTypeId = FileDataHelper::saveTrackType();

            $file->fromArray($fileArray);
            $file->setDbOwnerId(self::getOwnerId());
            $now = new DateTime('now', new DateTimeZone('UTC'));
            $file->setDbTrackTitle($originalFilename);
            $file->setDbMd5($md5);
            $file->setDbArtwork($artwork);
            if ($trackTypeId) {
                $file->setDbTrackTypeId($trackTypeId);
            }
            $file->setDbUtime($now);
            $file->setDbHidden(true);
            $file->save();

            Application_Service_MediaService::importFileToLibrary(
                $file->getPrimaryKey(),
                $filePath,
                $originalFilename,
                self::getOwnerId(),
                $copyFile
            );

            return CcFiles::sanitizeResponse($file);
        } catch (Exception $e) {
            $file->setDbImportStatus(self::IMPORT_STATUS_FAILED);
            $file->setDbHidden(true);
            $file->save();

            throw $e;
        }
    }

    /** Update a file with metadata specified in an array.
     * @param $fileId string The ID of the file to update in the DB
     * @param $fileArray array An associative array containing metadata. Replaces those fields if they exist.
     *
     * @return array a sanitized version of the file metadata array
     *
     * @throws Exception
     * @throws LibreTimeFileNotFoundException
     * @throws PropelException
     */
    public static function updateFromArray($fileId, $fileArray)
    {
        $file = CcFilesQuery::create()->findPk($fileId);

        $fileArray = self::removeBlacklistedFields($fileArray);
        $fileArray = self::stripTimeStampFromYearTag($fileArray);

        try {
            self::validateFileArray($fileArray);
            if ($file) {
                $file->fromArray($fileArray, BasePeer::TYPE_FIELDNAME);

                // Our RESTful API takes "full_path" as a field, which we then split and translate to match
                // our internal schema. Internally, file path is stored relative to a directory, with the directory
                // as a foreign key to cc_music_dirs.
                if (isset($fileArray['full_path'])) {
                    $fileSizeBytes = filesize($fileArray['full_path']);
                    if (!isset($fileSizeBytes) || $fileSizeBytes === false) {
                        throw new LibreTimeFileNotFoundException("Invalid filesize for {$fileId}");
                    }
                    Application_Model_Preference::updateDiskUsage($fileSizeBytes);

                    $fullPath = $fileArray['full_path'];
                    $storDir = Config::getStoragePath();
                    $pos = strpos($fullPath, $storDir);

                    if ($pos !== false) {
                        assert($pos == 0); // Path must start with the stor directory path

                        $filePathRelativeToStor = substr($fullPath, strlen($storDir));
                        $file->setDbFilepath($filePathRelativeToStor);
                    }
                }
            } else {
                throw new LibreTimeFileNotFoundException();
            }

            $now = new DateTime('now', new DateTimeZone('UTC'));
            $file->setDbMtime($now);
            $file->save();
        } catch (LibreTimeFileNotFoundException $e) {
            $file->setDbImportStatus(self::IMPORT_STATUS_FAILED);
            $file->setDbHidden(true);
            $file->save();

            throw $e;
        }

        return CcFiles::sanitizeResponse($file);
    }

    /** Delete a file from the database and disk.
     * @param $id The file ID
     *
     * @throws DeleteScheduledFileException
     * @throws Exception
     * @throws FileNoPermissionException
     * @throws LibreTimeFileNotFoundException
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
            throw new LibreTimeFileNotFoundException();
        }
    }

    private static function validateFileArray(&$fileArray)
    {
        // Sanitize any wildly incorrect metadata before it goes to be validated
        FileDataHelper::sanitizeData($fileArray);

        // EditAudioMD form is used here for validation
        $fileForm = new Application_Form_EditAudioMD();
        $fileForm->startForm(0); // The file ID doesn't matter here
        $fileForm->populate($fileArray);

        /*
         * Here we are truncating metadata of any characters greater than the
         * max string length set in the database. In the rare case a track's
         * genre is more than 64 chars, for example, we don't want to reject
         * tracks for that reason
         */
        foreach ($fileArray as $tag => &$value) {
            if ($fileForm->getElement($tag)) {
                $stringLengthValidator = $fileForm->getElement($tag)->getValidator('StringLength');
                // $stringLengthValidator will be false if the StringLength validator doesn't exist on the current element
                // in which case we don't have to truncate the extra characters
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

            throw new Exception("Data validation failed: {$errors} - {$messages}");
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

        return Application_Common_DateHelper::secondsToPlaylistTime($lengthSec);
    }

    public function setDbTrackNumber($v)
    {
        $max = 2 ** 31 - 1;
        $v = ($v > $max) ? $max : $v;

        return parent::setDbTrackNumber($v);
    }

    // returns true if the file exists and is not hidden
    public function visible()
    {
        return $this->getDbFileExists() && !$this->getDbHidden();
    }

    public function reassignTo($user)
    {
        $this->setDbOwnerId($user->getDbId());
        $this->save();
    }

    /**
     * Strips out the private fields we do not want to send back in API responses.
     *
     * @param CcFiles $file a CcFiles object
     *
     * @return array
     */
    // TODO: rename this function?
    public static function sanitizeResponse($file)
    {
        $response = $file->toArray(BasePeer::TYPE_FIELDNAME);

        foreach (self::$privateFields as $key) {
            unset($response[$key]);
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

        // filename doesn't contain the extension because PHP is awful
        $mime = $this->getDbMime();
        $extension = FileDataHelper::getFileExtensionFromMime($mime);

        return $info['filename'] . $extension;
    }

    /**
     * Returns the file's absolute file path stored on disk.
     */
    public function getURLsForTrackPreviewOrDownload()
    {
        return [$this->getAbsoluteFilePath()];
    }

    /**
     * Returns the file's absolute file path stored on disk.
     */
    public function getAbsoluteFilePath()
    {
        $directory = Config::getStoragePath();
        $filepath = $this->getDbFilepath();

        return Application_Common_OsPath::join($directory, $filepath);
    }

    /**
     * Returns the artwork's absolute file path stored on disk.
     */
    public function getAbsoluteArtworkPath()
    {
        $directory = Config::getStoragePath();
        $filepath = $this->getDbArtwork();

        return Application_Common_OsPath::join($directory, $filepath);
    }

    /**
     * Strips out fields from incoming request data that should never be modified
     * from outside of Airtime.
     *
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
            }
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
        } catch (Exception $e) {
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
        if (isset($metadata['year'])) {
            if (preg_match('/^(\d{4})-(\d{2})-(\d{2})(?:\s+(\d{2}):(\d{2}):(\d{2}))?$/', $metadata['year'])) {
                $metadata['year'] = substr($metadata['year'], 0, 4);
            }
        }

        return $metadata;
    }

    private static function stripInvalidUtf8Characters($string)
    {
        // Remove invalid UTF-8 characters
        // reject overly long 2 byte sequences, as well as characters above U+10000 and replace with ?
        $string = preg_replace(
            '/[\x00-\x08\x10\x0B\x0C\x0E-\x19\x7F]' .
                '|[\x00-\x7F][\x80-\xBF]+' .
                '|([\xC0\xC1]|[\xF0-\xFF])[\x80-\xBF]*' .
                '|[\xC2-\xDF]((?![\x80-\xBF])|[\x80-\xBF]{2,})' .
                '|[\xE0-\xEF](([\x80-\xBF](?![\x80-\xBF]))|(?![\x80-\xBF]{2})|[\x80-\xBF]{3,})/S',
            '?',
            $string
        );

        // reject overly long 3 byte sequences and UTF-16 surrogates and replace with ?
        $string = preg_replace('/\xE0[\x80-\x9F][\x80-\xBF]' .
            '|\xED[\xA0-\xBF][\x80-\xBF]/S', '?', $string);

        // Do a final encoding conversion to
        return mb_convert_encoding($string, 'UTF-8', 'UTF-8');
    }

    private function removeEmptySubFolders($path)
    {
        exec("find {$path} -empty -type d -delete");
    }

    /**
     * Checks if the file is a regular file that can be previewed and downloaded.
     */
    public function isValidPhysicalFile()
    {
        return is_file($this->getAbsoluteFilePath());
    }

    /**
     * Deletes the file from the stor directory on disk.
     */
    public function deletePhysicalFile()
    {
        $filepath = $this->getAbsoluteFilePath();
        $artworkpath = $this->getAbsoluteArtworkPath();
        if (file_exists($filepath)) {
            unlink($filepath);
            // also delete related images (dataURI and jpeg files)
            foreach (glob("{$artworkpath}*", GLOB_NOSORT) as $filename) {
                unlink($filename);
            }
            unlink($artworkpath);
        } else {
            throw new Exception('Could not locate file ' . $filepath);
        }
    }

    /**
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
