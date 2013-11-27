<?php

class Application_Form_Webstream extends Zend_Form
{
	public function init() {
		
		$this->setDecorators(
			array(
				array('ViewScript', array('viewScript' => 'form/webstream.phtml'))
			)
		);
		
		$id = new Zend_Form_Element_Hidden('id');
		$id->setValidators(array(
			new Zend_Validate_Int()
		));
		$this->addElement($id);
		
		$name = new Zend_Form_Element_Text('name');
		$name->setLabel(_('Firstname:'));
		$name->setAttrib('class', 'input_text');
		$name->addFilter('StringTrim');
		$this->addElement($name);
		
		$description = new Zend_Form_Element_Text('description');
		$description->setLabel(_('Description:'));
		$description->setAttrib('class', 'input_text_area');
		$description->addFilter('StringTrim');
		$description->setValidators(array(
        	new Zend_Validate_StringLength(array('max' => 512)),
        ));
		$this->addElement($description);
		
		$url = new Zend_Form_Element_Text('url');
		$url->setLabel(_('Stream URL:'));
		$url->setAttrib('class', 'input_text');
		$url->setRequired(true);
		$url->addFilter('StringTrim');
		$url->addFilter(new Filter_WebstreamRedirect());
		$url->setValidators(array(
        	new Validate_WebstreamUrl(),
        ));
		$this->addElement($url);
		
		$hours = new Zend_Form_Element_Text('hours');
		$hours->setLabel(_('h'));
		$hours->setAttrib('class', 'input_text');
		$hours->addFilter('StringTrim');
		$hours->setValidators(array(
			new Zend_Validate_Int(),
		));
		$this->addElement($hours);
		
		$min = new Zend_Form_Element_Text('mins');
		$min->setLabel(_('m'));
		$min->setAttrib('class', 'input_text');
		$min->addFilter('StringTrim');
		$min->setValidators(array(
			new Zend_Validate_Int(),
		));
		$this->addElement($min);
	}
}