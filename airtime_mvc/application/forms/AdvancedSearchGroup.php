<?php

class Application_Form_AdvancedSearchGroup extends Zend_Form_SubForm
{
    public function init()
    {
		// Add the add button
        $this->addElement('button', 'search_add_row', array(
            'ignore'   => true,
            'label'    => 'Add',
			'order'    => '-2'
        ));
		$this->getElement('search_add_row')->removeDecorator('DtDdWrapper');

        // Add the add button
        $this->addElement('button', 'search_remove_group', array(
            'ignore'   => true,
            'label'    => 'Remove',
			'order'    => '-1'
        ));
		$this->getElement('search_remove_group')->removeDecorator('DtDdWrapper');
    }

	public function addRow($row_id, $data=null) {

		$this->addSubForm(new Application_Form_AdvancedSearchRow(), 'row_'.$row_id, $row_id);
		$row = $this->getSubForm('row_'.$row_id);
		$row->removeDecorator('DtDdWrapper');

		if(!is_null($data)) {
			$row->setDefaults($data);
		}
	}


}

