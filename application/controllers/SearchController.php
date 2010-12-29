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
					->addActionContext('display', 'json')
					->initContext();

		$this->form = new Application_Form_AdvancedSearch();
		$this->search_sess = new Zend_Session_Namespace("search");
    }

    public function indexAction()
    {
		$this->_helper->layout->setLayout('search');

		$this->view->headScript()->appendFile('/js/campcaster/onready/search.js','text/javascript');

		$this->_helper->actionStack('display', 'search');
		$this->_helper->actionStack('contents', 'library');
		$this->_helper->actionStack('index', 'sideplaylist');
    }

    public function displayAction()
    {
		$this->view->headScript()->appendFile('/js/campcaster/library/advancedsearch.js','text/javascript');
		$this->view->headLink()->appendStylesheet('/css/library_search.css');

		$this->_helper->viewRenderer->setResponseSegment('search'); 

		$request = $this->getRequest();

		$this->form = new Application_Form_AdvancedSearch();
		$form = $this->form;

		// Form has not been submitted - displayed using layouts
		if (!$request->isPost()) {

			unset($this->search_sess->md);
			unset($this->search_sess->order);

			$sub = new Application_Form_AdvancedSearchRow(1);
			$form->addSubForm($sub, 'row_1');
			$form->getSubForm('row_1')->removeDecorator('DtDdWrapper');

			$this->view->form = $form;
			return;
		}

		// Form has been submitted - run data through preValidation()
		$form->preValidation($request->getPost());
		
		if (!$form->isValid($request->getPost())) {
			$this->view->form = $form->__toString();
			return;
		}

		// form was submitted, send back strings to json response.	
		$info = $form->getValues();
		$this->search_sess->md = $info;
		$order = isset($this->search_sess->order) ? $this->search_sess->order : NULL;

		$this->view->files = StoredFile::searchFiles($info, $order);

		if (count($this->view->files) > 0) {
			$this->view->results = $this->view->render('library/update.phtml');
		}
		else {
			$this->view->results = "<tr>No Results</tr>";
		}
		unset($this->view->files);
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





