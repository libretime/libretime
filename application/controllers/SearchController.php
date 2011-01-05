<?php

class SearchController extends Zend_Controller_Action
{

	protected $form;
	protected $search_sess = null;

    public function init()
    {
		if(!Zend_Auth::getInstance()->hasIdentity())
        {
            $this->_redirect('login/index');
        }

        $ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext->addActionContext('newfield', 'html')
					->initContext();

		$this->form = new Application_Form_AdvancedSearch();
		$this->search_sess = new Zend_Session_Namespace("search");
    }

    public function indexAction()
    {
		$this->_helper->layout->setLayout('search');

		$this->view->headScript()->appendFile('/js/airtime/onready/search.js','text/javascript');
		$this->view->headScript()->appendFile('/js/contextmenu/jjmenu.js','text/javascript');
		$this->view->headLink()->appendStylesheet('/css/contextmenu.css');

		$this->_helper->actionStack('contents', 'library');
		$this->_helper->actionStack('display', 'search');
		$this->_helper->actionStack('index', 'sideplaylist');
    }

    public function displayAction()
    {
		$this->view->headScript()->appendFile('/js/airtime/library/advancedsearch.js','text/javascript');
		$this->view->headLink()->appendStylesheet('/css/library_search.css');

		$this->_helper->viewRenderer->setResponseSegment('search'); 

		$request = $this->getRequest();

		$this->form = new Application_Form_AdvancedSearch();
		$form = $this->form;
		$this->view->form = $form;

		// Form has not been submitted - displayed using layouts
		if (!$request->isPost()) {

			$sub = new Application_Form_AdvancedSearchRow(1);
			$form->addSubForm($sub, 'row_1');
			$form->getSubForm('row_1')->removeDecorator('DtDdWrapper');

			return;
		}

		// Form has been submitted - run data through preValidation()
		$form->preValidation($request->getPost());
		
		if (!$form->isValid($request->getPost())) {
			return;
		}

		// valid form was submitted set as search criteria.
		$info = $form->getValues();
		$this->search_sess->md = $info;

		//make sure to start on first page of new results.
		unset($this->search_sess->page);
    }

    public function newfieldAction()
    {
		$id = $this->_getParam('id', 1);

		$this->form->addSubForm(new Application_Form_AdvancedSearchRow($id), 'row_'.$id, $id);

		$this->form->getSubForm('row_'.$id)->removeDecorator('DtDdWrapper');
		$e = $this->form->getSubForm('row_'.$id);
		
		$this->view->field = $e->__toString();
    }

}





