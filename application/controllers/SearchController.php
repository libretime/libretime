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
        $ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext->addActionContext('newfield', 'json')
					->addActionContext('newgroup', 'json')
                    ->addActionContext('index', 'json')
                    ->addActionContext('display', 'json')
					->initContext();

		$this->search_sess = new Zend_Session_Namespace("search");
    }

    public function indexAction()
    {
		$data = $this->_getParam('data');
		$form = new Application_Form_AdvancedSearch();
	
		// Form has been submitted
		$form->preValidation($data);
		
		//if (!$form->isValid($data)) {
            //$this->view->form = $form->__toString();
			//return;
		//}

		// valid form was submitted set as search criteria.
		$this->search_sess->md = $data;
    }

    public function displayAction()
    {
		
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







