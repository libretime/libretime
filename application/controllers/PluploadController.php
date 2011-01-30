<?php

class PluploadController extends Zend_Controller_Action
{

    public function init()
    {
		if(!Zend_Auth::getInstance()->hasIdentity())
        {
            $this->_redirect('login/index');
        }

		$ajaxContext = $this->_helper->getHelper('AjaxContext');
		$ajaxContext->addActionContext('upload', 'json')
				    ->initContext();
    }

    public function indexAction()
    {
        // action body
    }

    public function uploadAction()
    {
        // HTTP headers for no cache etc
		header('Content-type: text/plain; charset=UTF-8');
		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
		header("Cache-Control: no-store, no-cache, must-revalidate");
		header("Cache-Control: post-check=0, pre-check=0", false);
		header("Pragma: no-cache");

		// Settings
		$targetDir = ini_get("upload_tmp_dir") . DIRECTORY_SEPARATOR . "plupload";
		$cleanupTargetDir = false; // Remove old files
		$maxFileAge = 60 * 60; // Temp file age in seconds

		// 5 minutes execution time
		@set_time_limit(5 * 60);
		// usleep(5000);

		// Get parameters
		$chunk = isset($_REQUEST["chunk"]) ? $_REQUEST["chunk"] : 0;
		$chunks = isset($_REQUEST["chunks"]) ? $_REQUEST["chunks"] : 0;
		$fileName = isset($_REQUEST["name"]) ? $_REQUEST["name"] : '';

		// Clean the fileName for security reasons
		//$fileName = preg_replace('/[^\w\._]+/', '', $fileName);

		// Create target dir
		if (!file_exists($targetDir))
			@mkdir($targetDir);

		// Remove old temp files
		if (is_dir($targetDir) && ($dir = opendir($targetDir))) {
			while (($file = readdir($dir)) !== false) {
				$filePath = $targetDir . DIRECTORY_SEPARATOR . $file;

				// Remove temp files if they are older than the max age
				if (preg_match('/\.tmp$/', $file) && (filemtime($filePath) < time() - $maxFileAge))
					@unlink($filePath);
			}

			closedir($dir);
		} else
			die('{"jsonrpc" : "2.0", "error" : {"code": 100, "message": "Failed to open temp directory."}, "id" : "id"}');

		// Look for the content type header
		if (isset($_SERVER["HTTP_CONTENT_TYPE"]))
			$contentType = $_SERVER["HTTP_CONTENT_TYPE"];

		if (isset($_SERVER["CONTENT_TYPE"]))
			$contentType = $_SERVER["CONTENT_TYPE"];

		if (strpos($contentType, "multipart") !== false) {
			if (isset($_FILES['file']['tmp_name']) && is_uploaded_file($_FILES['file']['tmp_name'])) {
				// Open temp file
				$out = fopen($targetDir . DIRECTORY_SEPARATOR . $fileName, $chunk == 0 ? "wb" : "ab");
				if ($out) {
					// Read binary input stream and append it to temp file
					$in = fopen($_FILES['file']['tmp_name'], "rb");

					if ($in) {
						while ($buff = fread($in, 4096))
							fwrite($out, $buff);
					} else
						die('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "Failed to open input stream."}, "id" : "id"}');

					fclose($out);
					unlink($_FILES['file']['tmp_name']);
				} else
					die('{"jsonrpc" : "2.0", "error" : {"code": 102, "message": "Failed to open output stream."}, "id" : "id"}');
			} else
				die('{"jsonrpc" : "2.0", "error" : {"code": 103, "message": "Failed to move uploaded file."}, "id" : "id"}');
		} else {
			// Open temp file
			$out = fopen($targetDir . DIRECTORY_SEPARATOR . $fileName, $chunk == 0 ? "wb" : "ab");
			if ($out) {
				// Read binary input stream and append it to temp file
				$in = fopen("php://input", "rb");

				if ($in) {
					while ($buff = fread($in, 4096))
						fwrite($out, $buff);
				} else
					die('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "Failed to open input stream."}, "id" : "id"}');

				fclose($out);
			} else
				die('{"jsonrpc" : "2.0", "error" : {"code": 102, "message": "Failed to open output stream."}, "id" : "id"}');
		}

		$audio_file = $targetDir . DIRECTORY_SEPARATOR . $fileName;

		$md5 = md5_file($audio_file);
		$duplicate = StoredFile::RecallByMd5($md5);
		if ($duplicate) {
			if (PEAR::isError($duplicate)) {
				die('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": ' . $duplicate->getMessage() .'}}');
			}
			else {
				$duplicateName = $duplicate->getMetadataValue(UI_MDATA_KEY_TITLE);
				die('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "An identical audioclip named ' . $duplicateName . ' already exists in the storage server."}}');
			}
		}

		$metadata = camp_get_audio_metadata($audio_file);

		if (PEAR::isError($metadata)) {
			die('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": ' + $metadata->getMessage() + '}}');
		}

		// #2196 no id tag -> use the original filename
		if (basename($audio_file) == $metadata[UI_MDATA_KEY_TITLE]) {
			$metadata[UI_MDATA_KEY_TITLE] = basename($audio_file);
			$metadata[UI_MDATA_KEY_FILENAME] = basename($audio_file);
		}

		// setMetadataBatch doesnt like these values
		unset($metadata['audio']);
		unset($metadata['playtime_seconds']);

		$values = array(
		    "filename" =>  basename($audio_file),
		    "filepath" => $audio_file,
		    "filetype" => "audioclip",
		    "mime" => $metadata[UI_MDATA_KEY_FORMAT],
		    "md5" => $md5
		);
		$storedFile = StoredFile::Insert($values);

		if (PEAR::isError($storedFile)) {
			die('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": ' + $storedFile->getMessage() + '}}');
		}

		$storedFile->setMetadataBatch($metadata);

		// Return JSON-RPC response
		die('{"jsonrpc" : "2.0", "id" : '.$storedFile->getId().' }');
    }

    public function pluploadAction()
    {                 
        $this->view->headScript()->appendFile('/js/plupload/plupload.full.min.js','text/javascript');
		$this->view->headScript()->appendFile('/js/plupload/jquery.plupload.queue.min.js','text/javascript');
		$this->view->headScript()->appendFile('/js/airtime/library/plupload.js','text/javascript');
        $this->view->headScript()->appendFile('/js/playlist/helperfunctions.js','text/javascript');
		$this->view->headScript()->appendFile('/js/playlist/playlist.js','text/javascript');

		$this->view->headLink()->appendStylesheet('/css/plupload.queue.css');
		$this->view->headLink()->appendStylesheet('/css/pro_dropdown_3.css');
		$this->view->headLink()->appendStylesheet('/css/styles.css');
    }


}





