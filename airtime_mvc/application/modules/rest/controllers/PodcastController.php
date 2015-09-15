<?php

class Rest_PodcastController extends Zend_Rest_Controller
{
    public function init()
    {
        $this->view->layout()->disableLayout();

        // Remove reliance on .phtml files to render requests
        $this->_helper->viewRenderer->setNoRender(true);
    }

    public function indexAction()
    {

    }

    public function getAction()
    {
        $id = $this->getId();
        if (!$id) {
            return;
        }

        try {
            //https://github.com/aaronsnoswell/itunes-podcast-feed/blob/master/feed.php

        } catch (Exception $e) {

        }
    }

    public function postAction()
    {

    }

    public function putAction()
    {

    }

    public function deleteAction()
    {

    }

    private function getId()
    {
        if (!$id = $this->_getParam('id', false)) {
            $resp = $this->getResponse();
            $resp->setHttpResponseCode(400);
            $resp->appendBody("ERROR: No podcast ID specified.");
            return false;
        }
        return $id;
    }

}
