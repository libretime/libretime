<?php

class ShowbuilderController extends Zend_Controller_Action
{

    public function init()
    {
        $ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext->addActionContext('schedule', 'json')
                    ->initContext();
    }

    public function indexAction() {

        $request = $this->getRequest();

        $user_id = $request->getParam("uid", 0);
        $show_instance_id = $request->getParam("sid", 0);

        try {
            $user = new Application_Model_User($user_id);
            $show_instance = new Application_Model_ShowInstance($show_instance_id);
        }
        catch(Exception $e) {
            $this->_helper->redirector('denied', 'error');
        }

        //user is allowed to schedule this show.
        if ($user->isAdmin() || $user->isHost($show_instance->getShowId())) {
            $this->_helper->layout->setLayout('builder');

            $this->_helper->actionStack('library', 'library');
            $this->_helper->actionStack('builder', 'showbuilder');
        }
        else {
            $this->_helper->redirector('denied', 'error');
        }
    }

    public function builderAction() {

        $request = $this->getRequest();
        $baseUrl = $request->getBaseUrl();

        $this->view->headScript()->appendFile($baseUrl.'/js/fullcalendar/fullcalendar_orig.js','text/javascript');
        $this->view->headScript()->appendFile($baseUrl.'/js/airtime/showbuilder/builder.js','text/javascript');

        $this->view->headLink()->appendStylesheet($baseUrl.'/css/fullcalendar.css');

        $this->_helper->viewRenderer->setResponseSegment('builder');
    }

    public function eventFeedAction() {

    }

    public function scheduleAction() {

    }
}