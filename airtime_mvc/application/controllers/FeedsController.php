<?php

class FeedsController extends Zend_Controller_Action
{
    public function stationRssAction()
    {
        $this->view->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        header('Content-Type: text/xml');

        echo Application_Service_PodcastService::createStationRssFeed();
    }
}