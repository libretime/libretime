<?php

class PodcastController extends Zend_Controller_Action
{
    public function init()
    {
        $CC_CONFIG = Config::getConfig();
        $baseUrl = Application_Common_OsPath::getBaseDir();

        $headScript = $this->view->headScript();
        AirtimeTableView::injectTableJavaScriptDependencies($headScript, $baseUrl, $CC_CONFIG['airtime_version']);

        $this->view->headScript()->appendFile($baseUrl . 'js/airtime/library/library.js?' . $CC_CONFIG['airtime_version'], 'text/javascript');
        $this->view->headScript()->appendFile($baseUrl . 'js/airtime/library/events/library_showbuilder.js?' . $CC_CONFIG['airtime_version'], 'text/javascript');

        $this->view->headScript()->appendFile($baseUrl . 'js/airtime/widgets/table.js?' . $CC_CONFIG['airtime_version'], 'text/javascript');
        $this->view->headScript()->appendFile($baseUrl . 'js/airtime/library/podcast.js?' . $CC_CONFIG['airtime_version'], 'text/javascript');

        $this->view->headLink()->appendStylesheet($baseUrl . 'css/datatables/css/ColVis.css?' . $CC_CONFIG['airtime_version']);
        $this->view->headLink()->appendStylesheet($baseUrl . 'css/datatables/css/dataTables.colReorder.min.css?' . $CC_CONFIG['airtime_version']);

        $this->view->headLink()->appendStylesheet($baseUrl . 'css/station_podcast.css?' . $CC_CONFIG['airtime_version']);
        $this->view->headLink()->appendStylesheet($baseUrl . 'css/dashboard.css?' . $CC_CONFIG['airtime_version']);
    }

    /**
     * Renders the Station podcast view.
     */
    public function stationAction()
    {
        $stationPodcastId = Application_Model_Preference::getStationPodcastId();
        $podcast = Application_Service_PodcastService::getPodcastById($stationPodcastId);
        $this->view->podcast = json_encode($podcast);
        $this->view->form = new Application_Form_StationPodcast();
    }
}
