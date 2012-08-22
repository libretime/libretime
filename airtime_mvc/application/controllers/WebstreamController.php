<?php

class WebstreamController extends Zend_Controller_Action
{
    public function init()
    {
        $ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext->addActionContext('new', 'json')
                    ->addActionContext('save', 'json')
                    ->addActionContext('edit', 'json')
                    ->addActionContext('delete', 'json')
                    ->initContext();
    }

    public function newAction()
    {

        $userInfo = Zend_Auth::getInstance()->getStorage()->read();
        if (!$this->isAuthorized(-1)) {
            header("Status: 401 Not Authorized");
            return;
        }

        $webstream = new CcWebstream();

        //we're not saving this primary key in the DB so it's OK
        $webstream->setDbId(-1);
        $webstream->setDbName("Untitled Webstream");
        $webstream->setDbDescription("");
        $webstream->setDbUrl("http://");
        $webstream->setDbLength("00:00:00");
        $webstream->setDbName("Untitled Webstream");
        $webstream->setDbCreatorId($userInfo->id);
        $webstream->setDbUtime(new DateTime("now", new DateTimeZone('UTC')));
        $webstream->setDbMtime(new DateTime("now", new DateTimeZone('UTC')));
        //$webstream->save();

        /*
        $type = "stream";
        $objInfo = Application_Model_Library::getObjInfo($type);
        
        $obj = new $objInfo['className']($webstream);
        $obj->setName($webstream->getDbName());
        $obj->setMetadata('dc:creator', $userInfo->id);

        $type = "stream";
        Application_Model_Library::changePlaylist($obj->getId(), $type);
        */

        $this->view->obj = new Application_Model_Webstream($webstream);
        $this->view->action = "new";
        $this->view->html = $this->view->render('webstream/webstream.phtml');
    }

    public function editAction()
    {
        $request = $this->getRequest();

        $id = $request->getParam("id");
        if (is_null($id)) {
            throw new Exception("Missing parameter 'id'"); 
        }

        $webstream = CcWebstreamQuery::create()->findPK($id);
        $this->view->obj = new Application_Model_Webstream($webstream);
        $this->view->action = "edit";
        $this->view->html = $this->view->render('webstream/webstream.phtml');
    }

    public function deleteAction()
    {
        $request = $this->getRequest();
        $id = $request->getParam("ids");

        if (!$this->isAuthorized($id)) {
            header("Status: 401 Not Authorized");
            return;
        }       
        
        $type = "stream";
        Application_Model_Library::changePlaylist(null, $type);

        $webstream = CcWebstreamQuery::create()->findPK($id)->delete();

        $this->view->obj = null;
        $this->view->action = "delete";
        $this->view->html = $this->view->render('webstream/webstream.phtml');

    }

    public function isAuthorized($webstream_id)
    {
        $hasPermission = false;
        $user = Application_Model_User::getCurrentUser();
        if ($user->isUserType(array(UTYPE_ADMIN, UTYPE_PROGRAM_MANAGER))) {
            $hasPermission = true;
        }

        if ($user->isHost()) {
            if ($webstream_id != -1) {
                $webstream = CcWebstreamQuery::create()->findPK($webstream_id);
                //we are updating a playlist. Ensure that if the user is a host/dj, that he has the correct permission. 
                $user = Application_Model_User::getCurrentUser();

                if ($webstream->getDbCreatorId() == $user->getId()) {
                    $hasPermission = true;
                }
            } else {
                //we are creating a new stream. Don't need to check whether the DJ/Host owns the stream
                $hasPermission = true;
            }
        }

        return $hasPermission;
    }

    public function saveAction()
    {
        $request = $this->getRequest();

        $id = $request->getParam("id");

        $parameters = array();
        $parameters['id'] = trim($request->getParam("id"));
        $parameters['length'] = trim($request->getParam("length"));
        $parameters['name'] = trim($request->getParam("name"));
        $parameters['description'] = trim($request->getParam("description"));
        $parameters['url'] = trim($request->getParam("url"));

        if (!$this->isAuthorized($id)) {
            header("Status: 401 Not Authorized");
            return;
        }


        list($analysis, $mime, $di) = Application_Model_Webstream::analyzeFormData($parameters);
        try { 
            if (Application_Model_Webstream::isValid($analysis)) {
                $streamId = Application_Model_Webstream::save($parameters, $mime, $di);

                Application_Model_Library::changePlaylist($streamId, "stream");

                $this->view->statusMessage = "<div class='success'>Webstream saved.</div>";
                $this->view->streamId = $streamId;
            } else {
                throw new Exception("isValid returned false");
            }
        } catch (Exception $e) {
            $this->view->statusMessage = "<div class='errors'>Invalid form values.</div>"; 
            $this->view->streamId = -1;
            $this->view->analysis = $analysis;
        }
    }
}
