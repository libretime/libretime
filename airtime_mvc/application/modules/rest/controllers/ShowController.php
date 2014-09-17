<?php

/**
 * 
 * Controller class for handling Show-related functionality.
 * 
 * Changelog:
 * 16/09/2014 : v1.0 Created class skeleton, added image upload functionality
 * 
 * @author sourcefabric
 * @version 1.0
 *
 */

$filepath = realpath(dirname(__FILE__));
require_once($filepath."/../helpers/RestAuth.php");

class Rest_ShowController extends Zend_Rest_Controller
{
	private $restAuth;
	
	public function init()
	{
		$this->restAuth = new RestAuth();
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
		if (!$this->restAuth->verifyAuth(true, true))
		{
			Logging::info("Authentication failed");
			return;
		}
		
		$showId = $this->getShowId();
		
		if (!$showId) {
			Logging::info("No show id provided");
			return;
		}
		
		if (Application_Model_Systemstatus::isDiskOverQuota()) {
			$this->getResponse()
				 ->setHttpResponseCode(400)
				 ->appendBody("ERROR: Disk Quota reached.");
			return;
		}
		
		$path = $this->processUploadedImage($showId, $_FILES["file"]["tmp_name"], $_FILES["file"]["name"]);

		$show = CcShowQuery::create()->findPk($showId);

		try {
			$con = Propel::getConnection();
			$con->beginTransaction();
			
        	$show->setDbImagePath($path);
        	// TODO set linked show image paths
        	$show->save();
        	
        	$con->commit();
		} catch (Exception $e) {
        	$con->rollBack();
			Logging::error("Couldn't add show image: " . $e->getMessage());
		}
        
        $this->getResponse()
        	 ->setHttpResponseCode(201);
	}
	
	/**
	 * Verify and process an uploaded image file, copying it first into .../stor/organize/
	 * and then to RabbitMq to process. Processed image files end up in 
	 * .../stor/imported/:owner-id/show-images/:show-id/ to differentiate between 
	 * individual users and shows
	 * 
	 * @param unknown $tempFilePath temporary filepath assigned to the upload
	 * 	generally of the form /tmp/:tmp_name
	 * @param unknown $originalFilename the file name at time of upload
	 * @throws Exception when a file with an unsupported file extension is uploaded
	 */
	private function processUploadedImage($showId, $tempFilePath, $originalFilename) 
	{
		$ownerId = $this->restAuth->getOwnerId();
		 
		$CC_CONFIG = Config::getConfig();
		$apiKey = $CC_CONFIG["apiKey"][0];
		 
		$tempFileName = basename($tempFilePath);
		 
		//Only accept files with a file extension that we support.
		$fileExtension = pathinfo($originalFilename, PATHINFO_EXTENSION);
		if (!in_array(strtolower($fileExtension), explode(",", "jpg,png,gif,jpeg")))
		{
			@unlink($tempFilePath);
			// Should this be an HTTPResponse?
			throw new Exception("Bad file extension.");
		}
		 
		$storDir = Application_Model_MusicDir::getStorDir();
		$importedStorageDirectory = $storDir->getDirectory() . "imported/" . $ownerId . "/show-images/" . $showId;

		Logging::info("Stor directory: " . $storDir->getDirectory());
		Logging::info("Show image directory: " . $importedStorageDirectory);
		 
		try {
			$importedStorageDirectory = $this->copyFileToStor($tempFilePath, $importedStorageDirectory, $fileExtension);
		} catch (Exception $e) {
			@unlink($tempFilePath);
			Logging::error($e->getMessage());
			return;
		}
		 
		return $importedStorageDirectory;
	}
	
	private function copyFileToStor($tempFilePath, $importedStorageDirectory, $fileExtension)
	{
		$image_file = $tempFilePath;

		// check if "organize" dir exists and if not create one
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
	
	public static function deleteFilesFromStor($showId) {
		$auth = new RestAuth();
		$ownerId = $auth->getOwnerId();
		
		$storDir = Application_Model_MusicDir::getStorDir();
		$importedStorageDirectory = $storDir->getDirectory() . "imported/" . $ownerId . "/show-images/" . $showId;
		
		Logging::info("Deleting images from " . $importedStorageDirectory);
		
		// to be safe in case image uploading functionality is extended later
		return Rest_ShowController::delTree($importedStorageDirectory);
	}

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