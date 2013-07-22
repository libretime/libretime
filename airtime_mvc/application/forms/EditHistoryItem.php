<?php

class Application_Form_EditHistoryItem extends Zend_Form
{
	public function init() {

	    $file_id = new Zend_Form_Element_Hidden('his_file_id');
	    $file_id->setValidators(array(
	            new Zend_Validate_Int()
	    ));
	    $this->addElement($file_id);


	    /* Title form element */
	    $title = new Zend_Form_Element_Text('his_file_title');
	    $title->setLabel(_('Title:'));
	    $title->setAttrib('class', 'input_text');
	    $title->addFilter('StringTrim');
	    //$title->setDecorators(array('viewHelper'));
	    $this->addElement($title);

	}
}