<?php

class Rest_MediaController extends Zend_Rest_Controller
{
    //fields that are not modifiable via our RESTful API
    private $blackList = array(
        'id',
        'file_exists',
        'hidden',
        'silan_check',
        'soundcloud_id',
        'is_scheduled',
        'is_playlist'
    );

    //fields we should never expose through our RESTful API
    private $privateFields = array(
        'file_exists',
        'hidden',
        'silan_check',
        'is_scheduled',
        'is_playlist'
    );

    public function init()
    {
        $this->view->layout()->disableLayout();
    }

    public function indexAction()
    {
        if (!$this->verifyApiKey() && !$this->verifySession()) {
            return;
        }
        
        $files_array = array();
        foreach (CcFilesQuery::create()->find() as $file)
        {
            array_push($files_array, $this->sanitizeResponse($file));
        }
        
        $this->getResponse()
        ->setHttpResponseCode(200)
        ->appendBody(json_encode($files_array));       
        
        /** TODO: Use this simpler code instead after we upgrade to Propel 1.7 (Airtime 2.6.x branch):
        $this->getResponse()
            ->setHttpResponseCode(200)
            ->appendBody(json_encode(CcFilesQuery::create()->find()->toArray(BasePeer::TYPE_FIELDNAME)));
        */
    }
    
    public function getAction()
    {
        if (!$this->verifyApiKey() && !$this->verifySession()) {
            return;
        }
        $id = $this->getId();
        if (!$id) {
            return;
        }

        $file = CcFilesQuery::create()->findPk($id);
        if ($file) {
            
            $this->getResponse()
                ->setHttpResponseCode(200)
                ->appendBody(json_encode($this->sanitizeResponse($file)));
        } else {
            $this->fileNotFoundResponse();
        }
    }
    
    public function postAction()
    {
        if (!$this->verifyApiKey() && !$this->verifySession()) {
            return;
        }
        //If we do get an ID on a POST, then that doesn't make any sense
        //since POST is only for creating.
        if ($id = $this->_getParam('id', false)) {
            $resp = $this->getResponse();
            $resp->setHttpResponseCode(400);
            $resp->appendBody("ERROR: ID should not be specified when using POST. POST is only used for file creation, and an ID will be chosen by Airtime"); 
            return;
        }

        $file = new CcFiles();
        $file->fromArray($this->validateRequestData($this->getRequest()->getPost()));
        $file->setDbOwnerId($this->getOwnerId());
        $now  = new DateTime("now", new DateTimeZone("UTC"));
        $file->setDbUtime($now);
        $file->setDbMtime($now);
        $file->save();
        
        $callbackUrl = $this->getRequest()->getScheme() . '://' . $this->getRequest()->getHttpHost() . $this->getRequest()->getRequestUri() . "/" . $file->getPrimaryKey();

        $this->processUploadedFile($callbackUrl, $_FILES["file"]["name"], $this->getOwnerId());

        $this->getResponse()
            ->setHttpResponseCode(201)
            ->appendBody(json_encode($this->sanitizeResponse($file)));
    }

    public function putAction()
    {
        if (!$this->verifyApiKey() && !$this->verifySession()) {
            return;
        }
        $id = $this->getId();
        if (!$id) {
            return;
        }
        
        $file = CcFilesQuery::create()->findPk($id);
        if ($file)
        {           
            $fileFromJson = $file->fromArray($this->validateRequestData(json_decode($this->getRequest()->getRawBody(), true)), 
                                                BasePeer::TYPE_FIELDNAME);
            
            //Our RESTful API takes "full_path" as a field, which we then split and translate to match
            //our internal schema. Internally, file path is stored relative to a directory, with the directory
            //as a foreign key to cc_music_dirs.
            if ($fileFromJson["full_path"]) {
                
                $fullPath = $fileFromJson["full_path"];
                $storDir = Application_Model_MusicDir::getStorDir()->getDirectory();
                $pos = strpos($fullPath, $storDir);
                
                if ($pos !== FALSE)
                {
                    assert($pos == 0); //Path must start with the stor directory path
                    
                    $filePathRelativeToStor = substr($fullPath, strlen($storDir));
                    $fileFromJson["filepath"] = $filePathRelativeToStor;
                    $fileFromJson["directory"] = 1; //1 corresponds to the default stor/imported directory.       
                }
            }    

            $now  = new DateTime("now", new DateTimeZone("UTC"));
            $file->setDbMtime($now);
            $file->save();
            $this->getResponse()
                ->setHttpResponseCode(200)
                ->appendBody(json_encode($this->sanitizeResponse($file)));
        } else {
            $this->fileNotFoundResponse();
        }
    }

    public function deleteAction()
    {
        if (!$this->verifyApiKey() && !$this->verifySession()) {
            return;
        }
        $id = $this->getId();
        if (!$id) {
            return;
        }
        $file = CcFilesQuery::create()->findPk($id);
        if ($file) {
            $storedFile = Application_Model_StoredFile($file);
            $storedFile->delete(); //TODO: This checks your session permissions... Make it work without a session?
            $file->delete();
            $this->getResponse()
                ->setHttpResponseCode(204);
        } else {
            $this->fileNotFoundResponse();
        }
    }

    private function getId()
    {
        if (!$id = $this->_getParam('id', false)) {
            $resp = $this->getResponse();
            $resp->setHttpResponseCode(400);
            $resp->appendBody("ERROR: No file ID specified."); 
            return false;
        } 
        return $id;
    }

    private function verifyAPIKey()
    {
        //The API key is passed in via HTTP "basic authentication":
        // http://en.wikipedia.org/wiki/Basic_access_authentication

        $CC_CONFIG = Config::getConfig();

        //Decode the API key that was passed to us in the HTTP request.
        $authHeader = $this->getRequest()->getHeader("Authorization");
        $encodedRequestApiKey = substr($authHeader, strlen("Basic "));
        $encodedStoredApiKey = base64_encode($CC_CONFIG["apiKey"][0] . ":");
        
        if ($encodedRequestApiKey === $encodedStoredApiKey) 
        {
            return true;
        } else {
            $resp = $this->getResponse();
            $resp->setHttpResponseCode(401);
            $resp->appendBody("ERROR: Incorrect API key."); 
            return false;
        }
    }
    
    private function verifySession()
    {
        $auth = Zend_Auth::getInstance();
        if ($auth->hasIdentity())
        {
            return true;
        }
        
        //Token checking stub code. We'd need to change LoginController.php to generate a token too, but
        //but luckily all the token code already exists and works.
        //$auth = new Application_Model_Auth();
        //$auth->checkToken(Application_Model_Preference::getUserId(), $token);
    }

    private function fileNotFoundResponse()
    {
        $resp = $this->getResponse();
        $resp->setHttpResponseCode(404);
        $resp->appendBody("ERROR: Media not found."); 
    }
    
    private function processUploadedFile($callbackUrl, $originalFilename, $ownerId)
    {
        $CC_CONFIG = Config::getConfig();
        $apiKey = $CC_CONFIG["apiKey"][0];
        
        $upload_dir = ini_get("upload_tmp_dir") . DIRECTORY_SEPARATOR . "plupload";
        $tempFilePath = Application_Model_StoredFile::uploadFile($upload_dir);
        $tempFileName = basename($tempFilePath);
        
        //TODO: Remove copyFileToStor from StoredFile...
        
        //TODO: Remove uploadFileAction from ApiController.php **IMPORTANT** - It's used by the recorder daemon?
        
        $upload_dir = ini_get("upload_tmp_dir") . DIRECTORY_SEPARATOR . "plupload";
        $tempFilePath = $upload_dir . "/" . $tempFileName;
 
        $storDir = Application_Model_MusicDir::getStorDir();
        //$finalFullFilePath = $storDir->getDirectory() . "/imported/" . $ownerId . "/" . $originalFilename;
        $importedStorageDirectory = $storDir->getDirectory() . "/imported/" . $ownerId;
        
        
        try {
            //Copy the temporary file over to the "organize" folder so that it's off our webserver
            //and accessible by airtime_analyzer which could be running on a different machine.
            $newTempFilePath = Application_Model_StoredFile::copyFileToStor($tempFilePath, $originalFilename);
        } catch (Exception $e) {
            Logging::error($e->getMessage());
        }
        
        //Logging::info("New temporary file path: " . $newTempFilePath);
        //Logging::info("Final file path: " . $finalFullFilePath);

        //Dispatch a message to airtime_analyzer through RabbitMQ,
        //notifying it that there's a new upload to process!
        Application_Model_RabbitMq::SendMessageToAnalyzer($newTempFilePath,
                 $importedStorageDirectory, $originalFilename,
                 $callbackUrl, $apiKey);
    }

    private function getOwnerId()
    {
        try {
            if ($this->verifySession()) {
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

    /**
     * 
     * Strips out fields from incoming request data that should never be modified
     * from outside of Airtime
     * @param array $data
     */
    private function validateRequestData($data)
    {
        foreach ($this->blackList as $key) {
            unset($data[$key]);
            }
    
            return $data;
        }

    /**
     * 
     * Strips out the private fields we do not want to send back in API responses
     */
    //TODO: rename this function?
    public function sanitizeResponse($file)
    {
        $response = $file->toArray(BasePeer::TYPE_FIELDNAME);

        foreach ($this->privateFields as $key) {
            unset($response[$key]);
        }

        return $response;
    }
}

