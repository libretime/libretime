<?php

class ShowbuilderController extends Zend_Controller_Action
{

    public function init()
    {
        $ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext->addActionContext('schedule-move', 'json')
                    ->addActionContext('schedule-add', 'json')
                    ->addActionContext('schedule-remove', 'json')
                    ->addActionContext('builder-dialog', 'json')
                    ->addActionContext('builder-feed', 'json')
                    ->initContext();
    }

    public function indexAction() {

        $this->_helper->layout->setLayout('builder');

        $this->view->headScript()->appendFile($this->view->baseUrl('/js/airtime/library/events/library_showbuilder.js'),'text/javascript');
        $this->view->headScript()->appendFile($this->view->baseUrl('/js/airtime/library/preview.js'), 'text/javascript');
        
        $this->_helper->actionStack('library', 'library');
        $this->_helper->actionStack('builder', 'showbuilder');
    }

    public function builderAction() {

        $this->_helper->viewRenderer->setResponseSegment('builder');

        $request = $this->getRequest();
        $baseUrl = $request->getBaseUrl();

        $now = time();
        $from = $request->getParam("from", $now);
        $to = $request->getParam("to", $now+(24*60*60));

        $start = DateTime::createFromFormat("U", $from, new DateTimeZone("UTC"));
        $start->setTimezone(new DateTimeZone(date_default_timezone_get()));
        $end = DateTime::createFromFormat("U", $to, new DateTimeZone("UTC"));
        $end->setTimezone(new DateTimeZone(date_default_timezone_get()));

        $form = new Application_Form_ShowBuilder();
        $form->populate(array(
            'sb_date_start' => $start->format("Y-m-d"),
            'sb_time_start' => $start->format("H:i"),
            'sb_date_end' => $end->format("Y-m-d"),
            'sb_time_end' => $end->format("H:i")
        ));

        $this->view->sb_form = $form;

        $offset = date("Z") * -1;
        $this->view->headScript()->appendScript("var serverTimezoneOffset = {$offset}; //in seconds");
        $this->view->headScript()->appendFile($baseUrl.'/js/timepicker/jquery.ui.timepicker.js','text/javascript');
        $this->view->headScript()->appendFile($baseUrl.'/js/airtime/showbuilder/builder.js','text/javascript');
        $this->view->headScript()->appendFile($baseUrl.'/js/airtime/showbuilder/main_builder.js','text/javascript');

        $this->view->headLink()->appendStylesheet($baseUrl.'/css/jquery.ui.timepicker.css');
        $this->view->headLink()->appendStylesheet($baseUrl.'/css/showbuilder.css');
    }

    public function builderDialogAction() {

        $request = $this->getRequest();
        $id = $request->getParam("id");

        $instance = CcShowInstancesQuery::create()->findPK($id);

        if (is_null($instance)) {
            $this->view->error = "show does not exist";
            return;
        }

        $start = $instance->getDbStarts(null);
        $start->setTimezone(new DateTimeZone(date_default_timezone_get()));
        $end = $instance->getDbEnds(null);
        $end->setTimezone(new DateTimeZone(date_default_timezone_get()));

        $show_name = $instance->getCcShow()->getDbName();
        $start_time = $start->format("Y-m-d H:i:s");
        $end_time = $end->format("Y-m-d H:i:s");

        $this->view->title = "{$show_name}:    {$start_time} - {$end_time}";
        $this->view->start = $instance->getDbStarts("U");
        $this->view->end = $instance->getDbEnds("U");

        $this->view->dialog = $this->view->render('showbuilder/builderDialog.phtml');
    }

    public function builderFeedAction() {

        $request = $this->getRequest();
        $current_time = time();

        $starts_epoch = $request->getParam("start", $current_time);
        //default ends is 24 hours after starts.
        $ends_epoch = $request->getParam("end", $current_time + (60*60*24));
        $show_filter = intval($request->getParam("showFilter", 0));
        $my_shows = intval($request->getParam("myShows", 0));

        $startsDT = DateTime::createFromFormat("U", $starts_epoch, new DateTimeZone("UTC"));
        $endsDT = DateTime::createFromFormat("U", $ends_epoch, new DateTimeZone("UTC"));

        Logging::log("showbuilder starts {$startsDT->format("Y-m-d H:i:s")}");
        Logging::log("showbuilder ends {$endsDT->format("Y-m-d H:i:s")}");

        $opts = array("myShows" => $my_shows, "showFilter" => $show_filter);
        $showBuilder = new Application_Model_ShowBuilder($startsDT, $endsDT, $opts);

        $this->view->schedule = $showBuilder->GetItems();
    }

    public function scheduleAddAction() {

        $request = $this->getRequest();
        $mediaItems = $request->getParam("mediaIds", array());
        $scheduledIds = $request->getParam("schedIds", array());

        try {
            $scheduler = new Application_Model_Scheduler();
            $scheduler->scheduleAfter($scheduledIds, $mediaItems);
        }
        catch (OutDatedScheduleException $e) {
            $this->view->error = $e->getMessage();
            Logging::log($e->getMessage());
            Logging::log("{$e->getFile()}");
            Logging::log("{$e->getLine()}");
        }
        catch (Exception $e) {
            $this->view->error = $e->getMessage();
            Logging::log($e->getMessage());
            Logging::log("{$e->getFile()}");
            Logging::log("{$e->getLine()}");
        }
    }

    public function scheduleRemoveAction()
    {
        $request = $this->getRequest();
        $items = $request->getParam("items", array());

        try {
            $scheduler = new Application_Model_Scheduler();
            $scheduler->removeItems($items);
        }
        catch (OutDatedScheduleException $e) {
            $this->view->error = $e->getMessage();
            Logging::log($e->getMessage());
            Logging::log("{$e->getFile()}");
            Logging::log("{$e->getLine()}");
        }
        catch (Exception $e) {
            $this->view->error = $e->getMessage();
            Logging::log($e->getMessage());
            Logging::log("{$e->getFile()}");
            Logging::log("{$e->getLine()}");
        }
    }

    public function scheduleMoveAction() {

        $request = $this->getRequest();
        $selectedItem = $request->getParam("selectedItem");
        $afterItem = $request->getParam("afterItem");

        try {
            $scheduler = new Application_Model_Scheduler();
            $scheduler->moveItem($selectedItem, $afterItem);
        }
        catch (OutDatedScheduleException $e) {
            $this->view->error = $e->getMessage();
            Logging::log($e->getMessage());
            Logging::log("{$e->getFile()}");
            Logging::log("{$e->getLine()}");
        }
        catch (Exception $e) {
            $this->view->error = $e->getMessage();
            Logging::log($e->getMessage());
            Logging::log("{$e->getFile()}");
            Logging::log("{$e->getLine()}");
        }
    }

    public function scheduleReorderAction() {

        $request = $this->getRequest();

        $showInstance = $request->getParam("instanceId");
    }

    /*
     * make sure any incoming requests for scheduling are ligit.
     *
     * @param array $items, an array containing pks of cc_schedule items.
     */
    private function filterSelected($items) {

        $allowed = array();
        $user = Application_Model_User::GetCurrentUser();
        $type = $user->getType();

        //item must be within the host's show.
        if ($type === UTYPE_HOST) {

            $hosted = CcShowHostsQuery::create()
               ->filterByDbHost($user->getId())
               ->find();

            $allowed_shows = array();
            foreach ($hosted as $host) {
               $allowed_shows[] = $host->getDbShow();
            }

            for ($i = 0; $i < count($items); $i++) {

                $instance = $items[$i]["instance"];

                if (in_array($instance, $allowed_shows)) {
                    $allowed[] = $items[$i];
                }
            }

            $this->view->shows = $res;
        }
        //they can schedule anything.
        else if ($type === UTYPE_ADMIN || $type === UTYPE_PROGRAM_MANAGER) {
            $allowed = $items;
        }

        return $allowed;
    }
}