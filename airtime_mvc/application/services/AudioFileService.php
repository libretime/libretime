<?php

use Airtime\CcMusicDirsQuery;

use Airtime\CcShowInstancesQuery;

use Airtime\MediaItem\AudioFile;
use Airtime\MediaItem\AudioFilePeer;
use Airtime\MediaItem\AudioFileQuery;

class Application_Service_AudioFileService
{
	/**
	 * @holds PDO object reference
	 */
	private $_con;
	
	function __construct() {
		$this->_con = Propel::getConnection(AudioFilePeer::DATABASE_NAME);
	}
	
	private function createFileFromMediaMonitor($md) {
		
		$file = new AudioFile();
		
		//set values that user should not be able to modify when editing the metadata.
		$file->setFilepath($md['MDATA_KEY_FILEPATH']);
		$file->setMd5($md['MDATA_KEY_MD5']);
		$file->setSampleRate($md['MDATA_KEY_SAMPLERATE']);
		$file->setBitRate($md['MDATA_KEY_BITRATE']);
		$file->setLength($md['MDATA_KEY_DURATION']);
		$file->setMime($md['MDATA_KEY_MIME']);
		
		if (isset($md['MDATA_KEY_ENCODER'])) {
			$file->setEncodedBy($md['MDATA_KEY_ENCODER']);
		}
		
		//won't be set when adding a watched folder, uploading files through ftp.
		if (isset($md['MDATA_KEY_OWNER_ID'])) {
			$file->setOwnerId($md['MDATA_KEY_OWNER_ID']);
		}
		
		//set original cueout to be length, silan will update this later.
		$file->setCueout($md['MDATA_KEY_DURATION']);
		
		$file->setMetadata($md);
		
		return $file;
	}
	
	/*
 	[MDATA_KEY_CUE_IN] => 0.0
	[MDATA_KEY_MD5] => f3a2bf569708ceea40cd9201ca6b3155
	[MDATA_KEY_GENRE] => Alt Rock
	[MDATA_KEY_CREATOR] => Muse
	[MDATA_KEY_TITLE] => Hysteria
	[MDATA_KEY_FTYPE] => audioclip
	[MDATA_KEY_ENCODER] => Exact Audio Copy   (Secure mode)
	[MDATA_KEY_TRACKNUMBER] =>
	[MDATA_KEY_YEAR] => 2003
	[MDATA_KEY_MIME] => audio/mp3
	[MDATA_KEY_FILEPATH] => /srv/airtime/stor/imported/1/Muse/Absolution/unknown-Hysteria-206kbps.mp3
	[MDATA_KEY_DURATION] => 0:3:47.500408
	[MDATA_KEY_SAMPLERATE] => 44100
	[MDATA_KEY_SOURCE] => Absolution
	[MDATA_KEY_BITRATE] => 206637
	[MDATA_KEY_CUE_OUT] => 0.0
	[MDATA_KEY_ORIGINAL_PATH] => /srv/airtime/stor/imported/1/Muse/Absolution/unknown-Hysteria-206kbps.mp3
	[is_record] =>
	[MDATA_KEY_OWNER_ID] => 1
	*/
	private function mediaMonitorCreate($md) {
		
		$filepath = Application_Common_OsPath::normpath($md['MDATA_KEY_FILEPATH']);
		
		$file = AudioFileQuery::create()
			->filterByFilepath($filepath)
			->findOne($this->_con);
		
		if (is_null($file)) {
			Logging::info("creating new audiofile");
			$file = $this->createFileFromMediaMonitor($md);
		} 
		else {
			//also called sometimes when using things like EasyTag to edit the file's metadata.
			Logging::info("reactivating audiofile");
			$file->reactivateFile($md);
		}
		
		$file->save($this->_con);
		
		Logging::info($md);
		
		//TODO implement upload recorded.
		if ($md['is_record'] != 0) {
			//think we saved show instance id in field MDATA_KEY_TRACKNUMBER for Airtime recorded shows.
			$this->uploadRecordedFile($md['MDATA_KEY_TRACKNUMBER'], $file);
		}
	}
	
	private function mediaMonitorModify($md) {
	
	}
	
	/* 
	[MDATA_KEY_MD5] => bb9656a7e81de7c199e932005c4010ba
    [MDATA_KEY_ORIGINAL_PATH] => /srv/airtime/stor/imported/1/Muse/Absolution/unknown-Intro-164kbps.mp3
    [MDATA_KEY_FILEPATH] => /srv/airtime/stor/imported/1/Muse/Absolution/Intro-164kbps.mp3
    [is_record] => 0
	 */
	private function mediaMonitorMoved($md) {
	
		$oldpath = Application_Common_OsPath::normpath($md['MDATA_KEY_ORIGINAL_PATH']);
		$newpath = Application_Common_OsPath::normpath($md['MDATA_KEY_FILEPATH']);
		
		$file = AudioFileQuery::create()
			->filterByFilepath($oldpath)
			->findOne($this->_con);
		
		if (isset($file)) {
			$file->setFilepath($newpath)
				->save($this->_con);		
		}
		else {
			Logging::warn("file at $oldpath does not exist in Airtime");
		}
	}
	
	/*
	[MDATA_KEY_FILEPATH] => /srv/airtime/stor/imported/1/Muse/Absolution/unknown-Apocalypse Please-196kbps.mp3
    [is_record] => 0
	 */
	private function mediaMonitorDelete($md) {
		
		$filepath = Application_Common_OsPath::normpath($md['MDATA_KEY_FILEPATH']);
		
		$file = AudioFileQuery::create()
			->filterByFilepath($filepath)
			->findOne($this->_con);
		
		$file->setFileExists(false)
			->save($this->_con);
	}
	
	/*
	[MDATA_KEY_FILEPATH] => /home/naomi/Music/WatchedFolder/
    [is_record] => 0
	 */
	private function mediaMonitorDeleteDir($md) {
		
		$directorypath = Application_Common_OsPath::normpath($md['MDATA_KEY_FILEPATH']);
		//TODO fix how directories are stored so we can just use this normpath function.
		$directorypath = $directorypath.'/';
		
		$dir = CcMusicDirsQuery::create()
			->filterByDirectory($directorypath)
			->findOne($this->_con);
		
		if (isset($dir)) {
			AudioFileQuery::create()
				->filterByDirectory($dir->getId())
				->update(array('FileExists' => false), $this->_con);
		}
		else {
			Logging::warn("directory at $directorypath does not exist in Airtime");
		}	
	}
	
	public function mediaMonitorTask($md, $mode) {
		
		$this->_con->beginTransaction();
		
		Logging::info($mode);
		Logging::info($md);
		
		try {
			
			switch ($mode) {
				case "create":
					$this->mediaMonitorCreate($md);
					break;
				case "modify":
					$this->mediaMonitorModify($md);
					break;
				case "moved":
					$this->mediaMonitorMoved($md);
					break;
				case "delete":
					$this->mediaMonitorDelete($md);
					break;
				case "delete_dir":
					$this->mediaMonitorDeleteDir($md);
					break;
			}
			
			$this->_con->commit();
		}
		catch (Exception $e) {
			Logging::warn($e->getMessage());
			$this->_con->rollback();
			throw $e;
		}
	}
	
	public function getPlaylistPresentationClass($mediaItem) {
		
	}
	
	public function uploadRecordedFile($showInstanceId, $file)
	{
		$showCanceled = false;
		$this->_con->beginTransaction();
		
		try {
			
			$instance = CcShowInstancesQuery::create()->findPk($showInstanceId, $this->_con);
			
			if (isset($instance)) {
				$instance
					->setDbRecordedMediaItem($file->getId())
					->save($this->_con);
			}
			else {
				//we've reached here probably because the show was
				//cancelled, and therefore the show instance does not exist
				//anymore (ShowInstance constructor threw this error). We've
				//done all we can do (upload the file and put it in the
				//library), now lets just return.
				$showCanceled = true;
			}
			
			$file
				->setMetadataValue('MDATA_KEY_CREATOR', "Airtime Show Recorder")
				->setMetadataValue('MDATA_KEY_TRACKNUMBER', $show_instance_id)
				->save($this->_con);
	
			$this->_con->commit();
		} 
		catch (Exception $e) {
			Logging::warn($e->getMessage());
			$this->_con->rollback();
			throw $e;
		}
	
		if (!$showCanceled && Application_Model_Preference::GetAutoUploadRecordedShowToSoundcloud()) {
			$id = $file->getId();
			//TODO make sure the uploader uses the new media id.
			Application_Model_Soundcloud::uploadSoundcloud($id);
		}
	}
	
	public static function uploadFile($p_targetDir)
	{
		// HTTP headers for no cache etc
		header('Content-type: text/plain; charset=UTF-8');
		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
		header("Cache-Control: no-store, no-cache, must-revalidate");
		header("Cache-Control: post-check=0, pre-check=0", false);
		header("Pragma: no-cache");
	
		// Settings
		$cleanupTargetDir = false; // Remove old files
		$maxFileAge       = 60 * 60; // Temp file age in seconds
	
		// 5 minutes execution time
		@set_time_limit(5 * 60);
		// usleep(5000);
	
		// Get parameters
		$chunk = isset($_REQUEST["chunk"]) ? $_REQUEST["chunk"] : 0;
		$chunks = isset($_REQUEST["chunks"]) ? $_REQUEST["chunks"] : 0;
		$fileName = isset($_REQUEST["name"]) ? $_REQUEST["name"] : '';
		# TODO : should not log __FILE__ itself. there is general logging for
		#  this
		Logging::info(__FILE__.":uploadFile(): filename=$fileName to $p_targetDir");
		// Clean the fileName for security reasons
		//this needs fixing for songs not in ascii.
		//$fileName = preg_replace('/[^\w\._]+/', '', $fileName);
	
		// Create target dir
		if (!file_exists($p_targetDir))
			@mkdir($p_targetDir);
	
		// Remove old temp files
		if (is_dir($p_targetDir) && ($dir = opendir($p_targetDir))) {
			while (($file = readdir($dir)) !== false) {
				$filePath = $p_targetDir . DIRECTORY_SEPARATOR . $file;
	
				// Remove temp files if they are older than the max age
				if (preg_match('/\.tmp$/', $file) && (filemtime($filePath) < time() - $maxFileAge))
					@unlink($filePath);
			}
	
			closedir($dir);
		} else
			die('{"jsonrpc" : "2.0", "error" : {"code": 100, "message": _("Failed to open temp directory.")}, "id" : "id"}');
	
		// Look for the content type header
		if (isset($_SERVER["HTTP_CONTENT_TYPE"]))
			$contentType = $_SERVER["HTTP_CONTENT_TYPE"];
	
		if (isset($_SERVER["CONTENT_TYPE"]))
			$contentType = $_SERVER["CONTENT_TYPE"];
	
		// create temp file name (CC-3086)
		// we are not using mktemp command anymore.
		// plupload support unique_name feature.
		$tempFilePath= $p_targetDir . DIRECTORY_SEPARATOR . $fileName;
	
		// Old IBM code...
		if (strpos($contentType, "multipart") !== false) {
			if (isset($_FILES['file']['tmp_name']) && is_uploaded_file($_FILES['file']['tmp_name'])) {
				// Open temp file
				$out = fopen($tempFilePath, $chunk == 0 ? "wb" : "ab");
				if ($out) {
					// Read binary input stream and append it to temp file
					$in = fopen($_FILES['file']['tmp_name'], "rb");
	
					if ($in) {
						while (($buff = fread($in, 4096)))
							fwrite($out, $buff);
					} else
						die('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": _("Failed to open input stream.")}, "id" : "id"}');
	
					fclose($out);
					unlink($_FILES['file']['tmp_name']);
				} else
					die('{"jsonrpc" : "2.0", "error" : {"code": 102, "message": _("Failed to open output stream.")}, "id" : "id"}');
			} else
				die('{"jsonrpc" : "2.0", "error" : {"code": 103, "message": _("Failed to move uploaded file.")}, "id" : "id"}');
		} else {
			// Open temp file
			$out = fopen($tempFilePath, $chunk == 0 ? "wb" : "ab");
			if ($out) {
				// Read binary input stream and append it to temp file
				$in = fopen("php://input", "rb");
	
				if ($in) {
					while (($buff = fread($in, 4096)))
						fwrite($out, $buff);
				} else
					die('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": _("Failed to open input stream.")}, "id" : "id"}');
	
				fclose($out);
			} else
				die('{"jsonrpc" : "2.0", "error" : {"code": 102, "message": _("Failed to open output stream.")}, "id" : "id"}');
		}
	
		return $tempFilePath;
	}
	
	/**
	 * Check, using disk_free_space, the space available in the $destination_folder folder to see if it has
	 * enough space to move the $audio_file into and report back to the user if not.
	 **/
	public static function isEnoughDiskSpaceToCopy($destination_folder, $audio_file)
	{
		//check to see if we have enough space in the /organize directory to copy the file
		$freeSpace = disk_free_space($destination_folder);
		$fileSize = filesize($audio_file);
	
		return $freeSpace >= $fileSize;
	}
	
	public static function copyFileToStor($p_targetDir, $fileName, $tempname)
	{
		$audio_file = $p_targetDir . DIRECTORY_SEPARATOR . $tempname;
		Logging::info('copyFileToStor: moving file '.$audio_file);
	
		$storDir = Application_Model_MusicDir::getStorDir();
		$stor    = $storDir->getDirectory();
		// check if "organize" dir exists and if not create one
		if (!file_exists($stor."/organize")) {
			if (!mkdir($stor."/organize", 0777)) {
				return array(
						"code"    => 109,
						"message" => _("Failed to create 'organize' directory."));
			}
		}
	
		if (chmod($audio_file, 0644) === false) {
			Logging::info("Warning: couldn't change permissions of $audio_file to 0644");
		}
	
		// Check if we have enough space before copying
		if (!self::isEnoughDiskSpaceToCopy($stor, $audio_file)) {
			$freeSpace = disk_free_space($stor);
			$fileSize = filesize($audio_file);
	
			return array("code" => 107,
					"message" => sprintf(_("The file was not uploaded, there is "
							."%s MB of disk space left and the file you are "
							."uploading has a size of %s MB."), $freeSpace, $fileSize));
		}
	
		// Check if liquidsoap can play this file
		if (!self::liquidsoapFilePlayabilityTest($audio_file)) {
			return array(
					"code"    => 110,
					"message" => _("This file appears to be corrupted and will not "
							."be added to media library."));
		}
	
		// Did all the checks for real, now trying to copy
		$audio_stor = Application_Common_OsPath::join($stor, "organize",
				$fileName);
		$user = Application_Model_User::getCurrentUser();
		if (is_null($user)) {
			$uid = Application_Model_User::getFirstAdminId();
		} else {
			$uid = $user->getId();
		}
		$id_file = "$audio_stor.identifier";
		if (file_put_contents($id_file, $uid) === false) {
			Logging::info("Could not write file to identify user: '$uid'");
			Logging::info("Id file path: '$id_file'");
			Logging::info("Defaulting to admin (no identification file was
					written)");
		} else {
			Logging::info("Successfully written identification file for
					uploaded '$audio_stor'");
		}
		//if the uploaded file is not UTF-8 encoded, let's encode it. Assuming source
		//encoding is ISO-8859-1
		$audio_stor = mb_detect_encoding($audio_stor, "UTF-8") == "UTF-8" ? $audio_stor : utf8_encode($audio_stor);
		Logging::info("copyFileToStor: moving file $audio_file to $audio_stor");
		// Martin K.: changed to rename: Much less load + quicker since this is
		// an atomic operation
		if (@rename($audio_file, $audio_stor) === false) {
			//something went wrong likely there wasn't enough space in .
			//the audio_stor to move the file too warn the user that   .
			//the file wasn't uploaded and they should check if there  .
			//is enough disk space                                     .
			unlink($audio_file); //remove the file after failed rename
			unlink($id_file); // Also remove the identifier file
	
			return array(
					"code"    => 108,
					"message" => _("The file was not uploaded, this error can occur if the computer "
							."hard drive does not have enough disk space or the stor "
							."directory does not have correct write permissions."));
		}
		// Now that we successfully added this file, we will add another tag
		// file that will identify the user that owns it
		return null;
	}
	
	/*
	 * Pass the file through Liquidsoap and test if it is readable. Return True if readable, and False otherwise.
	*/
	public static function liquidsoapFilePlayabilityTest($audio_file)
	{
		$LIQUIDSOAP_ERRORS = array('TagLib: MPEG::Properties::read() -- Could not find a valid last MPEG frame in the stream.');
	
		// Ask Liquidsoap if file is playable
		$ls_command = sprintf('/usr/bin/airtime-liquidsoap -v -c "output.dummy(audio_to_stereo(single(%s)))" 2>&1',
				escapeshellarg($audio_file));
	
		$command = "export PATH=/usr/local/bin:/usr/bin:/bin/usr/bin/ && $ls_command";
		exec($command, $output, $rv);
	
		$isError = count($output) > 0 && in_array($output[0], $LIQUIDSOAP_ERRORS);
	
		return ($rv == 0 && !$isError);
	}
}