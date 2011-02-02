<?php

class SearchController extends Zend_Controller_Action
{
    protected $search_sess = null;

	private function addGroup($group_id) {

		$form = new Application_Form_AdvancedSearch();

		$form->addGroup($group_id, 1);
		$group = $form->getSubForm('group_'.$group_id);
		
		return $group->__toString();
	}
	
	private function addFieldToGroup($group_id, $row_id) {
		
		$form = new Application_Form_AdvancedSearch();

		$form->addGroup($group_id);
		$group = $form->getSubForm('group_'.$group_id);

		$group->addRow($row_id);
	
		return $group->__toString();
	}

    public function init()
    {
        if(!Zend_Auth::getInstance()->hasIdentity())
        {
            $this->_redirect('login/index');
        }

        $ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext->addActionContext('newfield', 'json')
					->addActionContext('newgroup', 'json')
					->initContext();

		$this->search_sess = new Zend_Session_Namespace("search");
    }

    public function indexAction()
    {
		$this->_helper->layout->setLayout('search');

		$this->view->headScript()->appendFile('/js/contextmenu/jjmenu.js','text/javascript');
		$this->view->headLink()->appendStylesheet('/css/contextmenu.css');

		$this->_helper->actionStack('contents', 'library');
		$this->_helper->actionStack('display', 'search');
		$this->_helper->actionStack('index', 'playlist');
    }

    public function displayAction()
    {
		$this->view->headScript()->appendFile('/js/airtime/library/advancedsearch.js','text/javascript');
		$this->view->headLink()->appendStylesheet('/css/library_search.css');

		$this->_helper->viewRenderer->setResponseSegment('search'); 

		$request = $this->getRequest();

		$form = new Application_Form_AdvancedSearch();
		$this->view->form = $form;

		// Form has not been submitted - displayed using layouts
		if (!$request->isPost()) {

			$form->addGroup(1, 1);

			$this->search_sess->next_group = 2;
			$this->search_sess->next_row[1] = 2;

			return;
		}

		$this->view->md = $request->getPost();
	
		// Form has been submitted
		$form->preValidation($request->getPost());
		
		if (!$form->isValid($request->getPost())) {
			return;
		}

		// valid form was submitted set as search criteria.
		$this->view->md = $form->getValues();
		$this->search_sess->md = $form->getValues();

		//make sure to start on first page of new results.
		unset($this->search_sess->page);
    }

    public function newfieldAction()
    {
        $group_id = $this->_getParam('group', 1);
		$row_id = $this->search_sess->next_row[$group_id];

		$this->view->html = $this->addFieldToGroup($group_id, $row_id);
		$this->view->row = $row_id;

		$this->search_sess->next_row[$group_id] = $row_id + 1;
    }

    public function newgroupAction()
    {
        $group_id = $this->search_sess->next_group;		
		
		$this->view->html = $this->addGroup($group_id);

		$this->search_sess->next_group = $group_id + 1;
		$this->search_sess->next_row[$group_id] = 2;
    }


}







