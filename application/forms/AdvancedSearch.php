<?php

class Application_Form_AdvancedSearch extends Zend_Form
{

    public function init()
    {
		$this->addElement('hidden', 'search_next_id', array(
			'value' => 2
		));
		$this->getElement('search_next_id')->removeDecorator('Label')->removeDecorator('HtmlTag');

		// Add the submit button
        $this->addElement('submit', 'submit', array(
            'ignore'   => true,
            'label'    => 'Submit',
			'order'    => '-1'
        ));
    }

	public function preValidation(array $data) {

		function findId($name) {
			$t = explode("_", $name);
			return $t[1];
		}

		// array_filter callback
		function findFields($field) {
		  return strpos($field, 'row') !== false;
		}

		$fields = array_filter(array_keys($data), 'findFields');

		foreach ($fields as $field) {
			// use id to set new order
			$id = findId($field);
			$this->addNewField($data, $id);
		}

		
	}

	public function addNewField($data, $id) {

		$sub = new Application_Form_AdvancedSearchRow($id);

		$values = array("metadata_".$id => $data["row_".$id]["metadata_".$id], 
						"match_".$id => $data["row_".$id]["match_".$id], 
						"search_".$id => $data["row_".$id]["search_".$id]);

		$sub->setDefaults($values);

		$this->addSubForm($sub, 'row_'.$id, $id);
		$this->getSubForm('row_'.$id)->removeDecorator('DtDdWrapper');
	}


}

