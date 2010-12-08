<?php

class ScheduleController extends Zend_Controller_Action
{

    public function init()
    {
        if(!Zend_Auth::getInstance()->hasIdentity())
        {
            $this->_redirect('login/index');
		}

		$ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext->addActionContext('event-feed', 'json')
					->addActionContext('add-show-dialog', 'json')
					->addActionContext('add-show', 'json')	
                    ->initContext();
    }

    public function indexAction()
    {
        $this->view->headScript()->appendFile('/js/fullcalendar/fullcalendar.min.js','text/javascript');
    	$this->view->headScript()->appendFile('/js/campcaster/schedule/schedule.js','text/javascript');

		$this->view->headLink()->appendStylesheet('/css/fullcalendar.css');
		$this->view->headLink()->appendStylesheet('/css/schedule.css');
    }

    public function eventFeedAction()
    {
        $start = $this->_getParam('start', null);
		$end = $this->_getParam('end', null);
		$weekday = $this->_getParam('weekday', null);

		$userInfo = Zend_Auth::getInstance()->getStorage()->read();

		$show = new Show($userInfo->type);
		$this->view->events = $show->getFullCalendarEvents($start, $end, $weekday);
    }

    public function addShowDialogAction()
    {
        $request = $this->getRequest();
        $form = new Application_Form_AddShow();
 
        if ($request->isPost()) {
            if ($form->isValid($request->getPost())) {  
    
				$userInfo = Zend_Auth::getInstance()->getStorage()->read();

				$show = new Show($userInfo->type);
				$show->addShow($form->getValues());
				return;
			}     
        }
		$this->view->form = $form->__toString();
    }

    function addShow()
    {
		//name, description, hosts, allDay, repeats,
		//start_time, duration, start_date, end_date, dofw

		/*
        $name = $this->_getParam('name', 'Default Name');
		$description = $this->_getParam('description', '');
		$hosts = $this->_getParam('hosts');
		$allDay = $this->_getParam('all_day', false);
		$repeats = $this->_getParam('repeats', false);
		$startTime = $this->_getParam('start_time');
		$duration = $this->_getParam('duration');
		$startDate = $this->_getParam('start_date');
		$endDate = $this->_getParam('end_date', null);
		$dofw = $this->_getParam('dofw');

		if($repeats === false)
			$endDate = $startDate;

		$repeats = $repeats ? 1 : 0;
		*/

		//$userInfo = Zend_Auth::getInstance()->getStorage()->read();

		//$show = new Show($userInfo->type);
		//$show->addShow($name, $startDate, $endDate, $startTime, $duration, $repeats, $dofw, $description);
    }


}







