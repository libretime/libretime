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
					->addActionContext('move-show', 'json')
					->addActionContext('resize-show', 'json')		
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

		if(!is_null($weekday)) {
			$weekday = array($weekday);
		}

		$userInfo = Zend_Auth::getInstance()->getStorage()->read();

		$show = new Show($userInfo->id, $userInfo->type);
		$this->view->events = $show->getFullCalendarEvents($start, $end, $weekday);
    }

    public function addShowDialogAction()
    {
        $request = $this->getRequest();
        $form = new Application_Form_AddShow();
 
        if ($request->isPost()) {
            if ($form->isValid($request->getPost())) {  
    
				$userInfo = Zend_Auth::getInstance()->getStorage()->read();

				$show = new Show($userInfo->id, $userInfo->type);
				$overlap = $show->addShow($form->getValues());

				if(isset($overlap)) {
					$this->view->overlap = $overlap;
					$this->view->form = $form->__toString();
				}

				return;
			}     
        }
		$this->view->form = $form->__toString();
    }

    public function moveShowAction()
    {
        $deltaDay = $this->_getParam('day');
		$deltaMin = $this->_getParam('min');
		$showId = $this->_getParam('showId');

		$userInfo = Zend_Auth::getInstance()->getStorage()->read();

		$show = new Show($userInfo->id, $userInfo->type);

		$overlap = $show->moveShow($showId, $deltaDay, $deltaMin);

		if(isset($overlap))
			$this->view->overlap = $overlap;
    }

    public function resizeShowAction()
    {
        $deltaDay = $this->_getParam('day');
		$deltaMin = $this->_getParam('min');
		$showId = $this->_getParam('showId');

		$userInfo = Zend_Auth::getInstance()->getStorage()->read();

		$show = new Show($userInfo->id, $userInfo->type);

		$overlap = $show->resizeShow($showId, $deltaDay, $deltaMin);

		if(isset($overlap))
			$this->view->overlap = $overlap;
    }


}











