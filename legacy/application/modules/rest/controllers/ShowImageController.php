<?php

declare(strict_types=1);

/**
 * Controller class for handling ShowImage-related functionality.
 * Changelog:
 * 16/09/2014 : v1.0   Created class skeleton, added image upload functionality
 * 18/09/2014 : v1.1   Changed auth references to static calls
 * 06/02/2015 : v1.2   Changed endpoints to be more RESTful, changed classname to
 *                     better reflect functionality
 * 09/02/2015 : v1.2.1 Added more comments.
 *
 * @author  sourcefabric
 *
 * @version 1.2.1
 */
class Rest_ShowImageController extends Zend_Rest_Controller
{
    public function init()
    {
        // Remove layout dependencies
        $this->view->layout()->disableLayout();
        // Remove reliance on .phtml files to render requests
        $this->_helper->viewRenderer->setNoRender(true);
    }

    /**
     * headAction is needed as it is defined as an abstract function in the base controller.
     */
    public function headAction()
    {
        Logging::info('HEAD action received');
    }

    public function indexAction()
    {
        Logging::info('INDEX action received');
    }

    public function getAction()
    {
        Logging::info('GET action received');
    }

    public function putAction()
    {
        Logging::info('PUT action received');
    }

    /**
     * RESTful POST endpoint; used when uploading show images.
     */
    public function postAction()
    {
        $showId = $this->getShowId();

        if (!$showId) {
            $this->getResponse()
                ->setHttpResponseCode(400)
                ->appendBody('No show ID provided');

            return;
        }

        try {
            $path = $this->processUploadedImage($showId, $_FILES['file']['tmp_name']);
        } catch (Exception $e) {
            $this->getResponse()
                ->setHttpResponseCode(500)
                ->appendBody('Error processing image: ' . $e->getMessage());

            return;
        }

        $show = CcShowQuery::create()->findPk($showId);

        $con = Propel::getConnection();

        try {
            $con->beginTransaction();

            $show->setDbImagePath($path);
            $show->save();

            $con->commit();
        } catch (Exception $e) {
            $con->rollBack();
            $this->getResponse()
                ->setHttpResponseCode(500)
                ->appendBody("Couldn't add show image: " . $e->getMessage());
        }

        $this->getResponse()
            ->setHttpResponseCode(201);
    }

    /**
     * RESTful DELETE endpoint; used when deleting show images.
     */
    public function deleteAction()
    {
        $showId = $this->getShowId();

        if (!$showId) {
            $this->getResponse()
                ->setHttpResponseCode(400)
                ->appendBody('No show ID provided');

            return;
        }

        try {
            self::deleteShowImagesFromStor($showId);
        } catch (Exception $e) {
            $this->getResponse()
                ->setHttpResponseCode(500)
                ->appendBody('Error processing image: ' . $e->getMessage());
        }

        $show = CcShowQuery::create()->findPk($showId);

        $con = Propel::getConnection();

        try {
            $con->beginTransaction();

            $show->setDbImagePath(null);
            $show->save();

            $con->commit();
        } catch (Exception $e) {
            $con->rollBack();
            $this->getResponse()
                ->setHttpResponseCode(500)
                ->appendBody("Couldn't remove show image: " . $e->getMessage());
        }

        $this->getResponse()
            ->setHttpResponseCode(201);
    }

    /**
     * Verify and process an uploaded image file, copying it into
     * .../stor/imported/:owner-id/show-images/:show-id/ to differentiate between
     * individual users and shows.
     *
     * @param int    $showId       the ID of the show we're adding the image to
     * @param string $tempFilePath temporary filepath assigned to the upload generally of the form /tmp/:tmp_name
     *
     * @return string the path to the new location for the file
     *
     * @throws Exception
     *                   - when a file with an unsupported file extension is uploaded or an
     *                   error occurs in copyFileToStor
     */
    private function processUploadedImage($showId, $tempFilePath)
    {
        $ownerId = RestAuth::getOwnerId();

        // Only accept files with a file extension that we support.
        $fileExtension = $this->getFileExtension($tempFilePath);

        if (!in_array(strtolower($fileExtension), explode(',', 'jpg,png,gif,jpeg'))) {
            @unlink($tempFilePath);

            throw new Exception('Bad file extension.');
        }

        $importedStorageDirectory = Config::getStoragePath() . 'imported/' . $ownerId . '/show-images/' . $showId;

        try {
            $importedStorageDirectory = $this->copyFileToStor($tempFilePath, $importedStorageDirectory, $fileExtension);
        } catch (Exception $e) {
            @unlink($tempFilePath);

            throw new Exception('Failed to copy file: ' . $e->getMessage());
        }

        return $importedStorageDirectory;
    }

    /**
     * Check the MIME type of an uploaded file to determine what extension it should have.
     *
     * @param $tempFilePath the file path to the uploaded file in /tmp
     *
     * @return string the file extension for the new file based on its MIME type
     */
    private function getFileExtension($tempFilePath)
    {
        // Don't trust the extension - get the MIME-type instead
        $fileInfo = finfo_open();
        $mime = finfo_file($fileInfo, $tempFilePath, FILEINFO_MIME_TYPE);

        return $this->getExtensionFromMime($mime);
    }

    /**
     * Use a hardcoded list of accepted MIME types to return a file extension.
     *
     * @param $mime the MIME type of the file
     *
     * @return string the file extension based on the given MIME type
     */
    private function getExtensionFromMime($mime)
    {
        $extensions = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/gif' => 'gif',
        ];

        return $extensions[$mime];
    }

    /**
     * Copy a given file in /tmp to the user's stor directory.
     *
     * @param string $tempFilePath             the path to the file in /tmp
     * @param string $importedStorageDirectory the path to the new location for the file
     * @param string $fileExtension            the file's extension based on its MIME type
     *
     * @return string the new full path to the file in stor
     *
     * @throws Exception if either the storage directory does not exist and cannot be
     *                   created, the storage directory does not have write permissions
     *                   enabled, or the user's hard drive does not have enough space to
     *                   store the file
     */
    private function copyFileToStor($tempFilePath, $importedStorageDirectory, $fileExtension)
    {
        $image_file = $tempFilePath;

        // check if show image dir exists and if not, create one
        if (!file_exists($importedStorageDirectory)) {
            if (!mkdir($importedStorageDirectory, 0777, true)) {
                throw new Exception('Failed to create storage directory.');
            }
        }

        if (chmod($image_file, 0644) === false) {
            Logging::info("Warning: couldn't change permissions of {$image_file} to 0644");
        }

        $newFileName = substr($tempFilePath, strrpos($tempFilePath, '/')) . '.' . $fileExtension;

        // Did all the checks for real, now trying to copy
        $image_stor = Application_Common_OsPath::join($importedStorageDirectory, $newFileName);
        Logging::info('Adding image: ' . $image_stor);
        Logging::info("copyFileToStor: moving file {$image_file} to {$image_stor}");

        if (@rename($image_file, $image_stor) === false) {
            // something went wrong likely there wasn't enough space in .
            // the audio_stor to move the file too warn the user that   .
            // the file wasn't uploaded and they should check if there  .
            // is enough disk space                                     .
            unlink($image_file); // remove the file after failed rename

            throw new Exception('The file was not uploaded, this error can occur if the computer '
                . 'hard drive does not have enough disk space or the stor '
                . 'directory does not have correct write permissions.');
        }

        return $image_stor;
    }

    // Should this be an endpoint instead?
    /**
     * Delete any images belonging to the show with the given ID.
     *
     * @param int $showId the ID of the show we're deleting images from
     *
     * @return bool true if the images were successfully deleted, otherwise false
     */
    public static function deleteShowImagesFromStor($showId)
    {
        $ownerId = RestAuth::getOwnerId();

        $importedStorageDirectory = Config::getStoragePath() . 'imported/' . $ownerId . '/show-images/' . $showId;

        Logging::info('Deleting images from ' . $importedStorageDirectory);

        // to be safe in case image uploading functionality is extended later
        if (!file_exists($importedStorageDirectory)) {
            Logging::info('No uploaded images for show with id ' . $showId);

            return true;
        }

        return self::delTree($importedStorageDirectory);
    }

    // from a note @ https://php.net/manual/en/function.rmdir.php
    private static function delTree($dir)
    {
        $files = array_diff(scandir($dir), ['.', '..']);
        foreach ($files as $file) {
            (is_dir("{$dir}/{$file}")) ? self::delTree("{$dir}/{$file}") : unlink("{$dir}/{$file}");
        }

        return rmdir($dir);
    }

    /**
     * Fetch the id parameter from the request.
     *
     * @return bool|int false if the show id wasn't
     *                  provided, otherwise returns the id
     */
    private function getShowId()
    {
        if (!($id = $this->_getParam('id', false))) {
            $resp = $this->getResponse();
            $resp->setHttpResponseCode(400);
            $resp->appendBody('ERROR: No show ID specified.');

            return false;
        }

        $id = filter_var($id, FILTER_VALIDATE_INT);

        if ($id === false) {
            $resp = $this->getResponse();
            $resp->setHttpResponseCode(400);
            $resp->appendBody('ERROR: Invalid show ID specified.');

            return false;
        }

        return $id;
    }
}
