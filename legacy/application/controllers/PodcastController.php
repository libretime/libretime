<?php

declare(strict_types=1);

class PodcastController extends Zend_Controller_Action
{
    public function init()
    {
        $headScript = $this->view->headScript();
        AirtimeTableView::injectTableJavaScriptDependencies($headScript);

        $this->view->headScript()->appendFile(Assets::url('js/airtime/library/library.js'), 'text/javascript');
        $this->view->headScript()->appendFile(Assets::url('js/airtime/library/events/library_showbuilder.js'), 'text/javascript');

        $this->view->headScript()->appendFile(Assets::url('js/airtime/widgets/table.js'), 'text/javascript');
        $this->view->headScript()->appendFile(Assets::url('js/airtime/library/podcast.js'), 'text/javascript');

        $this->view->headLink()->appendStylesheet(Assets::url('css/datatables/css/ColVis.css'));
        $this->view->headLink()->appendStylesheet(Assets::url('css/datatables/css/dataTables.colReorder.min.css'));

        $this->view->headLink()->appendStylesheet(Assets::url('css/station_podcast.css'));
        $this->view->headLink()->appendStylesheet(Assets::url('css/dashboard.css'));
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
