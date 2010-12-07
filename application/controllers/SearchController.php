<?php

class SearchController extends Zend_Controller_Action
{

	protected $form;

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
    }

    public function indexAction()
    {
        // action body
    }

    public function displayAction()
    {
		$this->view->headScript()->appendFile('/js/campcaster/library/advancedsearch.js','text/javascript');
		$this->view->headLink()->appendStylesheet('/css/library_search.css');

		$this->form = new Application_Form_AdvancedSearch();
		$form = $this->form;

		// Form has not been submitted - pass to view and return
		if (!$this->getRequest()->isPost()) {
			$sub = new Application_Form_AdvancedSearchRow(1);
			$form->addSubForm($sub, 'row_1');
			$form->getSubForm('row_1')->removeDecorator('DtDdWrapper');

			$this->view->form = $form;
			return;
		}

		// Form has been submitted - run data through preValidation()
		$form->preValidation($_POST);
		
		if (!$form->isValid($_POST)) {
			$this->view->form = $form;
			return;
		}

		// Form is valid
		$this->view->form = $form;

		$info = $form->getValues();		
		$this->view->files = StoredFile::searchFiles($info);
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





