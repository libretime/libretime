<?php

class Rest_MediaController extends Zend_Rest_Controller
{
    public function init()
    {
        $this->view->layout()->disableLayout();
    }

    public function indexAction()
    {
        if (!$this->verifyApiKey()) {
            return;
        }
        $this->getResponse()
            ->setHttpResponseCode(200)
            ->appendBody(json_encode(CcFilesQuery::create()->find()->toArray()));
    }
    public function getAction()
    {
        if (!$this->verifyApiKey()) {
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
                ->appendBody(json_encode($file->toArray()));
        } else {
            $this->fileNotFoundResponse();
        }
    }
    
    public function postAction()
    {
        if (!$this->verifyApiKey()) {
            return;
        }
        //If we do get an ID on a POST, then that doesn't make any sense
        //since POST is only for creating.
        if ($id = $this->_getParam('id', false)) {
            $resp = $this->getResponse();
            $resp->setHttpResponseCode(400);
            $resp->appendBody("ERROR: ID should not be specified when using POST. POST is only used for show creation, and an ID will be chosen by Airtime"); 
            return;
        }

        $file = new CcFiles();
        $file->fromArray($this->getRequest()->getPost());
        $file->save();

        $this->getResponse()
            ->setHttpResponseCode(201)
            ->appendBody(json_encode($file->toArray()));
    }

    public function putAction()
    {
        if (!$this->verifyApiKey()) {
            return;
        }
        $id = $this->getId();
        if (!$id) {
            return;
        }
        
        $file = CcFilesQuery::create()->findPk($id);
        if ($file)
        {
            $file->fromArray(json_decode($this->getRequest()->getRawBody(), true));
            $file->save();
            $this->getResponse()
                ->setHttpResponseCode(200)
                ->appendBody(json_encode($file->toArray()));
        } else {
            $this->fileNotFoundResponse();
        }
    }

    public function deleteAction()
    {
        if (!$this->verifyApiKey()) {
            return;
        }
        $id = $this->getId();
        if (!$id) {
            return;
        }
        $file = CcFilesQuery::create()->findPk($id);
        if ($file) {
            $file->delete();
            $this->getResponse()
                ->setHttpResponseCode(200);
        } else {
            $this->fileNotFoundResponse();
        }
    }

    private function getId()
    {
        if (!$id = $this->_getParam('id', false)) {
            $resp = $this->getResponse();
            $resp->setHttpResponseCode(400);
            $resp->appendBody("ERROR: No show ID specified."); 
            return false;
        } 
        return $id;
    }

    private function verifyAPIKey()
    {
        //The API key is passed in via HTTP "basic authentication":
        //  http://en.wikipedia.org/wiki/Basic_access_authentication

        //TODO: Fetch the user's API key from the database to check against 
        $unencodedStoredApiKey = "foobar"; 
        $encodedStoredApiKey = base64_encode($unencodedStoredApiKey . ":");

        //Decode the API key that was passed to us in the HTTP request.
        $authHeader = $this->getRequest()->getHeader("Authorization");
        $encodedRequestApiKey = substr($authHeader, strlen("Basic "));

        //if ($encodedRequestApiKey === $encodedStoredApiKey)
        if (true)
        {
            return true;
        }
        else
        {
            $resp = $this->getResponse();
            $resp->setHttpResponseCode(401);
            $resp->appendBody("ERROR: Incorrect API key."); 
            return false;
        }
    }

    private function fileNotFoundResponse()
    {
        $resp = $this->getResponse();
        $resp->setHttpResponseCode(404);
        $resp->appendBody("ERROR: Media not found."); 
    }
}