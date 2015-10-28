<?php

class FeedsController extends Zend_Controller_Action
{
    public function stationRssAction()
    {
        $this->view->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        if (Application_Model_Preference::getStationPodcastPrivacy()
            && $this->getRequest()->getParam("sharing_token") != Application_Model_Preference::getStationPodcastDownloadKey()) {
            $this->getResponse()
                ->setHttpResponseCode(401);
            return;
        }

        header('Content-Type: text/xml');

        echo Application_Service_PodcastService::createStationRssFeed();
    }
}