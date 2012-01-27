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
    }

    public function builderAction() {

        $request = $this->getRequest();
        $baseUrl = $request->getBaseUrl();

        $this->view->headScript()->appendScript("var serverTimezoneOffset = ".date("Z")."; //in seconds");
        $this->view->headScript()->appendFile($baseUrl.'/js/timepicker/jquery.ui.timepicker.js','text/javascript');
        $this->view->headScript()->appendFile($baseUrl.'/js/airtime/showbuilder/builder.js','text/javascript');

        $this->view->headLink()->appendStylesheet($baseUrl.'/css/jquery.ui.timepicker.css');

        $this->_helper->viewRenderer->setResponseSegment('builder');
    }

    public function builderFeedAction() {

        $request = $this->getRequest();
        $current_time = time();

        $starts_epoch = $request->getParam("start", $current_time);
        //default ends is 24 hours after starts.
        $ends_epoch = $request->getParam("end", $current_time + (60*60*24));

        $startsDT = DateTime::createFromFormat("U", $starts_epoch, new DateTimeZone("UTC"));
        $endsDT = DateTime::createFromFormat("U", $ends_epoch, new DateTimeZone("UTC"));

        Logging::log("showbuilder starts {$startsDT->format("Y-m-d H:i:s")}");
        Logging::log("showbuilder ends {$endsDT->format("Y-m-d H:i:s")}");

        $showBuilder = new Application_Model_ShowBuilder($startsDT, $endsDT);

        $this->view->schedule = $showBuilder->GetItems();
    }

    public function scheduleAction() {

        $request = $this->getRequest();

        $instance = $request->getParam("instance", null);
        $id = $request->getParam("id", null);
        $starts_epoch = $request->getParam("start", null);
        $file_id = $request->getParam("file", null);

        $startDT = DateTime::createFromFormat("U", $starts_epoch, new DateTimeZone("UTC"));

        //invalid request
        if (is_null($start)) {
            return;
        }

        //updating a scheduled item.
        if (isset($id)) {
            $schedItem = CcScheduleQuery::create()->findPK($id);
            $duration = $schedItem->getDbEnds('U') - $schedItem->getDbStarts('U');

            $endDT = DateTime::createFromFormat("U", $starts_epoch + $duration, new DateTimeZone("UTC"));

            $oldInstance = $schedItem->getDbInstanceId();

            if ($instance === $oldInstance) {
                CcScheduleQuery::create()
                ->filterByDbInstanceId($oldInstance)
                ->find();
            }
            //file was dragged out of the show into another show or scheduled not within a show.
            else {
            }


        }
        else {
            $schedItem = new CcSchedule();
        }

        $schedItem->setDbStarts($startDT);
        $schedItem->setDbEnds($endDT);
        $schedItem->save();
    }
}