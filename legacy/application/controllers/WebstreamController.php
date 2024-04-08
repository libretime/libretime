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
            // TODO: this header call does not actually print any error message
            header('Status: 401 Not Authorized');

            return;
        }

        $webstream = new CcWebstream();

        // we're not saving this primary key in the DB so it's OK to be -1
        $webstream->setDbId(-1);
        $webstream->setDbName(_('Untitled Webstream'));
        $webstream->setDbDescription('');
        $webstream->setDbUrl('http://');
        $webstream->setDbLength('00:30:00');
        $webstream->setDbName(_('Untitled Webstream'));
        $webstream->setDbCreatorId($userInfo->id);
        $webstream->setDbUtime(new DateTime('now', new DateTimeZone('UTC')));
        $webstream->setDbMtime(new DateTime('now', new DateTimeZone('UTC')));

        // clear the session in case an old playlist was open: CC-4196
        Application_Model_Library::changePlaylist(null, null);

        $this->view->obj = new Application_Model_Webstream($webstream);
        $this->view->action = 'new';
        $this->view->html = $this->view->render('webstream/webstream.phtml');
    }

    public function editAction()
    {
        $request = $this->getRequest();

        $id = $request->getParam('id');
        if (is_null($id)) {
            throw new Exception("Missing parameter 'id'");
        }

        $webstream = CcWebstreamQuery::create()->findPK($id);
        if ($webstream) {
            Application_Model_Library::changePlaylist($id, 'stream');
        }

        $obj = new Application_Model_Webstream($webstream);

        $user = Application_Model_User::getCurrentUser();
        $isAdminOrPM = $user->isUserType([UTYPE_SUPERADMIN, UTYPE_ADMIN, UTYPE_PROGRAM_MANAGER]);

        if (!$isAdminOrPM && $webstream->getDbCreatorId() != $user->getId()) {
            $this->view->objType = 'webstream';
            $this->view->type = 'webstream';
            $this->view->obj = $obj;
            $this->view->id = $id;
            $this->view->html = $this->view->render('playlist/permission-denied.phtml');

            return;
        }

        $this->view->obj = $obj;
        $this->view->type = 'webstream';
        $this->view->id = $id;
        $this->view->action = 'edit';
        $this->view->html = $this->view->render('webstream/webstream.phtml');
    }

    public function deleteAction()
    {
        $request = $this->getRequest();
        $id = $request->getParam('ids');

        if (!$this->isAuthorized($id)) {
            header('Status: 401 Not Authorized');

            return;
        }

        $type = 'stream';
        Application_Model_Library::changePlaylist(null, $type);

        $webstream = CcWebstreamQuery::create()->findPK($id)->delete();

        $this->view->obj = null;
        $this->view->action = 'delete';
        $this->view->html = $this->view->render('webstream/webstream.phtml');
    }

    /*TODO : make a user object be passed a parameter into this function so
        that it does not have to be fetched multiple times.*/
    public function isAuthorized($webstream_id)
    {
        $user = Application_Model_User::getCurrentUser();
        if ($user->isUserType([UTYPE_SUPERADMIN, UTYPE_ADMIN, UTYPE_PROGRAM_MANAGER])) {
            return true;
        }

        if ($user->isHost()) {
            // not creating a webstream
            if ($webstream_id != -1) {
                $webstream = CcWebstreamQuery::create()->findPK($webstream_id);
                /*we are updating a playlist. Ensure that if the user is a
                    host/dj, that he has the correct permission.*/
                $user = Application_Model_User::getCurrentUser();

                // only allow when webstream belongs to the DJ
                return $webstream->getDbCreatorId() == $user->getId();
            }

            /*we are creating a new stream. Don't need to check whether the
                DJ/Host owns the stream*/
            return true;
        }
        Logging::info($user);

        return false;
    }

    public function saveAction()
    {
        $request = $this->getRequest();

        $id = $request->getParam('id');

        $parameters = [];
        foreach (['id', 'length', 'name', 'description', 'url'] as $p) {
            $parameters[$p] = trim($request->getParam($p));
        }

        if (!$this->isAuthorized($id)) {
            header('Status: 401 Not Authorized');

            return;
        }

        [$analysis, $mime, $mediaUrl, $di] = Application_Model_Webstream::analyzeFormData($parameters);

        try {
            if (Application_Model_Webstream::isValid($analysis)) {
                $streamId = Application_Model_Webstream::save($parameters, $mime, $mediaUrl, $di);

                Application_Model_Library::changePlaylist($streamId, 'stream');

                $this->view->statusMessage = "<div class='success'>" . _('Webstream saved.') . '</div>';
                $this->view->streamId = $streamId;
                $this->view->length = $di->format('%Hh %Im');
            } else {
                throw new Exception('isValid returned false');
            }
        } catch (Exception $e) {
            Logging::debug($e->getMessage());
            $this->view->statusMessage = "<div class='errors'>" . _('Invalid form values.') . '</div>';
            $this->view->streamId = -1;
            $this->view->analysis = $analysis;
        }
    }
}
