<?php

/**
 * 
 * Controller class for handling Show-related functionality.
 * 
 * Changelog:
 * 16/09/2014 : v1.0 Created class skeleton, added image upload functionality
 * 18/09/2014 : v1.1 Changed auth references to static calls
 * 
 * @author sourcefabric
 * @version 1.1
 *
 */

$filepath = realpath(__DIR__);
require_once($filepath."/../helpers/RestAuth.php");

class Rest_ShowController extends Zend_Rest_Controller
{
	public function init()
	{
		// Remove layout dependencies
		$this->view->layout()->disableLayout();
		// Remove reliance on .phtml files to render requests
   		$this->_helper->viewRenderer->setNoRender(true);
	}
	
	/*
	 * TODO shift functionality from add-show.js and ScheduleController to here,
	 * and have a referenceable Show object
	 */
	
	public function indexAction() {
		Logging::info("INDEX action received");
	}
	
	public function getAction() {
		Logging::info("GET action received");
	}
	
	public function putAction() {
		Logging::info("PUT action received");
	}
	
	public function postAction() {
		Logging::info("POST action received");
	}
	
	public function deleteAction() {
		Logging::info("DELETE action received");
	}
	
	public function uploadImageAction() 
	{
		if (!RestAuth::verifyAuth(true, true))
		{
			$this->getResponse()
				 ->setHttpResponseCode(401)
				 ->appendBody("Authentication failed");
			return;
		}
		
		$showId = $this->getShowId();
		
		if (!$showId) {
			$this->getResponse()
				 ->setHttpResponseCode(400)
				 ->appendBody("No show ID provided");
			return;
		}
		
		try {
			$path = $this->processUploadedImage($showId, $_FILES["file"]["tmp_name"], $_FILES["file"]["name"]);
		} catch (Exception $e) {
			$this->getResponse()
				 ->setHttpResponseCode(500)
				 ->appendBody("Error processing image: " . $e->getMessage());
		}

		$show = CcShowQuery::create()->findPk($showId);

		try {
			$con = Propel::getConnection();
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
	
	public function deleteImageAction()
	{
		if (!RestAuth::verifyAuth(true, true))
		{
			$this->getResponse()
				 ->setHttpResponseCode(401)
				 ->appendBody("Authentication failed");
			return;
		}
		
		$showId = $this->getShowId();
		
		if (!$showId) {
			$this->getResponse()
				 ->setHttpResponseCode(400)
				 ->appendBody("No show ID provided");
			return;
		}
		
		try {
			Rest_ShowController::deleteShowImagesFromStor($showId);
		} catch (Exception $e) {
			$this->getResponse()
				 ->setHttpResponseCode(500)
				 ->appendBody("Error processing image: " . $e->getMessage());
		}
		
		$show = CcShowQuery::create()->findPk($showId);
		
		try {
			$con = Propel::getConnection();
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
	 * individual users and shows
	 * 
	 * @param unknown $tempFilePath 
	 * 		- temporary filepath assigned to the upload generally of the form /tmp/:tmp_name
	 * @param unknown 
	 * 		- $originalFilename the file name at time of upload
	 * @throws Exception 
	 * 		- when a file with an unsupported file extension is uploaded or an 
	 * 		  error occurs in copyFileToStor
	 */
	private function processUploadedImage($showId, $tempFilePath, $originalFilename) 
	{
		$ownerId = RestAuth::getOwnerId();
		 
		$CC_CONFIG = Config::getConfig();
		$apiKey = $CC_CONFIG["apiKey"][0];
		 
		$tempFileName = basename($tempFilePath);
		 
		//Only accept files with a file extension that we support.
		$fileExtension = $this->getFileExtension($originalFilename, $tempFilePath);

		if (!in_array(strtolower($fileExtension), explode(",", "jpg,png,gif,jpeg")))
		{
			@unlink($tempFilePath);
			throw new Exception("Bad file extension.");
		}
		 
		$storDir = Application_Model_MusicDir::getStorDir();
		$importedStorageDirectory = $storDir->getDirectory() . "imported/" . $ownerId . "/show-images/" . $showId;

		try {
			$importedStorageDirectory = $this->copyFileToStor($tempFilePath, $importedStorageDirectory, $fileExtension);
		} catch (Exception $e) {
			@unlink($tempFilePath);
			throw new Exception("Failed to copy file: " . $e->getMessage());
		}
		 
		return $importedStorageDirectory;
	}
	
	private function getFileExtension($originalFileName, $tempFilePath) 
	{
		// Don't trust the extension - get the MIME-type instead
		$fileInfo = finfo_open();
		$mime = finfo_file($fileInfo, $tempFilePath, FILEINFO_MIME_TYPE);
		return $this->getExtensionFromMime($mime);
	}
	
	private function getExtensionFromMime($mime) 
	{
		$extensions = array(
			'image/jpeg' => 'jpg',
			'image/png'  => 'png',
			'image/gif'  => 'gif'
		);
		
		return $extensions[$mime];
	}
	
	private function copyFileToStor($tempFilePath, $importedStorageDirectory, $fileExtension)
	{
		$image_file = $tempFilePath;

		// check if show image dir exists and if not, create one
		if (!file_exists($importedStorageDirectory)) {
			if (!mkdir($importedStorageDirectory, 0777, true)) {
				throw new Exception("Failed to create storage directory.");
			}
		}
	
		if (chmod($image_file, 0644) === false) {
			Logging::info("Warning: couldn't change permissions of $image_file to 0644");
		}
	
		$newFileName = substr($tempFilePath, strrpos($tempFilePath, "/")).".".$fileExtension;
		
		// Did all the checks for real, now trying to copy
		$image_stor = Application_Common_OsPath::join($importedStorageDirectory, $newFileName);
		Logging::info("Adding image: " . $image_stor);
		Logging::info("copyFileToStor: moving file $image_file to $image_stor");

		if (@rename($image_file, $image_stor) === false) {
			//something went wrong likely there wasn't enough space in .
			//the audio_stor to move the file too warn the user that   .
			//the file wasn't uploaded and they should check if there  .
			//is enough disk space                                     .
			unlink($image_file); //remove the file after failed rename
	
			throw new Exception("The file was not uploaded, this error can occur if the computer "
					."hard drive does not have enough disk space or the stor "
					."directory does not have correct write permissions.");
		}
		
		return $image_stor;
	}
	
	// Should this be an endpoint instead?
	public static function deleteShowImagesFromStor($showId) {
		$ownerId = RestAuth::getOwnerId();
		
		$storDir = Application_Model_MusicDir::getStorDir();
		$importedStorageDirectory = $storDir->getDirectory() . "imported/" . $ownerId . "/show-images/" . $showId;
		
		Logging::info("Deleting images from " . $importedStorageDirectory);
		
		// to be safe in case image uploading functionality is extended later
		if (!file_exists($importedStorageDirectory)) {
			Logging::info("No uploaded images for show with id " . $showId);
			return true;
		} else {
			return Rest_ShowController::delTree($importedStorageDirectory);
		}
	}

	// from a note @ http://php.net/manual/en/function.rmdir.php
	private static function delTree($dir) {
		$files = array_diff(scandir($dir), array('.','..'));
		foreach ($files as $file) {
			(is_dir("$dir/$file")) ? delTree("$dir/$file") : unlink("$dir/$file");
		}
		return rmdir($dir);
	}
		
	/**
	 * Fetch the id parameter from the request.
	 *
	 * @return boolean|unknown false if the show id wasn't
	 * provided, otherwise returns the id
	 */
	private function getShowId()
	{
		if (!$id = $this->_getParam('id', false)) {
			$resp = $this->getResponse();
			$resp->setHttpResponseCode(400);
			$resp->appendBody("ERROR: No show ID specified.");
			return false;
		}
		return $id;
	}
	
}