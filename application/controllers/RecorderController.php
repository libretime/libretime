<?php

class RecorderController extends Zend_Controller_Action
{

    public function init()
    {
        $ajaxContext = $this->_helper->getHelper('contextSwitch');
        $ajaxContext->addActionContext('get-show-schedule', 'json')
                    ->addActionContext('get-uploaded-file', 'json')
                    ->initContext();
    }

    public function indexAction()
    {
        // action body
    }

    public function getShowScheduleAction()
    {
        $today_timestamp = date("Y-m-d H:i:s");
        $this->view->shows = Show::getShows($today_timestamp, null, $excludeInstance=NULL, $onlyRecord=TRUE);
    }

}



