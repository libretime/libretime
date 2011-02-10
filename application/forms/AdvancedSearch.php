<?php

class Application_Form_AdvancedSearch extends Zend_Form
{

    public function init()
    {
		// Add the add button
        $this->addElement('button', 'search_add_group', array(
            'ignore'   => true,
            'label'    => 'Add',
			'order'    => '-2'
        ));
		$this->getElement('search_add_group')->removeDecorator('DtDdWrapper');

		// Add the submit button
        $this->addElement('button', 'search_submit', array(
            'ignore'   => true,
            'label'    => 'Submit',
			'order'    => '-1'
        ));
		$this->getElement('search_submit')->removeDecorator('DtDdWrapper');
    }

	public function addGroup($group_id, $row_id=null) {

		$this->addSubForm(new Application_Form_AdvancedSearchGroup(), 'group_'.$group_id, $group_id);
		$this->getSubForm('group_'.$group_id)->removeDecorator('DtDdWrapper');

		if(!is_null($row_id)) {
			$subGroup = $this->getSubForm('group_'.$group_id);
			$subGroup->addRow($row_id);
		}
	}

	public function preValidation(array $data) {

		function findId($name) {
			$t = explode("_", $name);
			return $t[1];
		}

		function findFields($field) {
		  return strpos($field, 'group') !== false;
		}

		$groups = array_filter(array_keys($data), 'findFields');

		foreach ($groups as $group) {

			$group_id = findId($group);
			$this->addGroup($group_id);

			$subGroup = $this->getSubForm($group);

			foreach (array_keys($data[$group]) as $row) {

				$row_id = findId($row);
				$subGroup->addRow($row_id, $data[$group][$row]);
			}
		}
		
	}
}

