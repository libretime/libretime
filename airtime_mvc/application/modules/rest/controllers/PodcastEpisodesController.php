<?php

class Rest_PodcastEpisodesController extends Zend_Rest_Controller
{
    public function init()
    {
        $this->view->layout()->disableLayout();

        // Remove reliance on .phtml files to render requests
        $this->_helper->viewRenderer->setNoRender(true);
    }

    public function indexAction()
    {
        Logging::info("episodes index");
        $id = $this->getId();
        Logging::info($id);
        if (!$id) {
            return;
        }
    }

    public function getAction()
    {
        Logging::info("episodes get");
        $id = $this->getId();
        if (!$id) {
            return;
        }

        try {

        } catch (Exception $e) {

        }
    }

    public function postAction()
    {
        Logging::info("episodes post");
    }

    public function putAction()
    {
        Logging::info("episodes put");
    }

    public function deleteAction()
    {
        Logging::info("delete - episodes");
        $id = $this->getId();
        Logging::info($id);
        if (!$id) {
            return;
        }
    }

    private function getId()
    {
        if (!$id = $this->_getParam('episode_id', false)) {
            $resp = $this->getResponse();
            $resp->setHttpResponseCode(400);
            $resp->appendBody("ERROR: No podcast ID specified.");
            return false;
        }
        return $id;
    }

    private function unknownErrorResponse()
    {
        $resp = $this->getResponse();
        $resp->setHttpResponseCode(400);
        $resp->appendBody("An unknown error occurred.");
    }

}
