<?php

class ShowbuilderController extends Zend_Controller_Action
{

    public function init()
    {
        $ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext->addActionContext('schedule', 'json')
                    ->addActionContext('builder-feed', 'json')
                    ->initContext();
    }

    public function indexAction() {

        $this->_helper->layout->setLayout('builder');

        $this->view->headScript()->appendFile($this->view->baseUrl('/js/airtime/library/events/library_showbuilder.js'),'text/javascript');

        $this->_helper->actionStack('library', 'library');
        $this->_helper->actionStack('builder', 'showbuilder');

        //user is allowed to schedule this show.
        //if ($user->isAdmin() || $user->isHost($show_instance->getShowId())) {
        //}
    }

    public function builderAction() {

        $request = $this->getRequest();
        $baseUrl = $request->getBaseUrl();

        $this->view->headScript()->appendFile($baseUrl.'/js/timepicker/jquery.ui.timepicker.js','text/javascript');

        $this->view->headScript()->appendScript("var serverTimezoneOffset = ".date("Z")."; //in seconds");
        $this->view->headScript()->appendFile($baseUrl.'/js/datatables/js/jquery.dataTables.js','text/javascript');
        $this->view->headScript()->appendFile($baseUrl.'/js/datatables/plugin/dataTables.ColVis.js','text/javascript');
        $this->view->headScript()->appendFile($baseUrl.'/js/datatables/plugin/dataTables.ColReorderResize.js','text/javascript');
        $this->view->headScript()->appendFile($baseUrl.'/js/airtime/showbuilder/builder.js','text/javascript');

        $this->view->headLink()->appendStylesheet($baseUrl.'/css/jquery.ui.timepicker.css');

        $this->_helper->viewRenderer->setResponseSegment('builder');
    }

    public function builderFeedAction() {

        $request = $this->getRequest();
        $current_time = microtime(true);

        $starts_epoch = $request->getParam("start", $current_time);
        //default ends is 24 hours after starts.
        $ends_epoch = $request->getParam("end", $current_time + (60*60*24));

        $startsDT = DateTime::createFromFormat("U.u", $starts_epoch, new DateTimeZone("UTC"));
        $endsDT = DateTime::createFromFormat("U.u", $ends_epoch, new DateTimeZone("UTC"));

        $scheduled_items = Application_Model_Schedule::GetItems($startsDT->format("Y-m-d H:i:s.u"), $endsDT->format("Y-m-d H:i:s.u"), false);

        foreach ($scheduled_items as &$item) {
            $itemStartsDT = DateTime::createFromFormat("Y-m-d H:i:s.u", $item["starts"], new DateTimeZone("UTC"));
            $itemEndsDT = DateTime::createFromFormat("Y-m-d H:i:s.u", $item["ends"], new DateTimeZone("UTC"));
        }

        $this->view->schedule = $scheduled_items;
    }

    public function scheduleAction() {

        $request = $this->getRequest();

        $show_instance_id = $request->getParam("sid", 0);
        $scheduled_item_id = $request->getParam("time", 0);
        $scheduled_start = $request->getParam("start", 0);

        //snap to previous/next default.
        $scheduled_type = $request->getParam("type", 0);

    }
}