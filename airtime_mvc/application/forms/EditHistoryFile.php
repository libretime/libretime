<?php

class Application_Form_EditHistoryFile extends Zend_Form
{
	public function init() {

		/*
		$this->setDecorators(
			array(
				array('ViewScript', array('viewScript' => 'form/edit-history-file.phtml'))
			)
		);
		*/

		$this->setMethod('post');


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

		/* Creator form element */
		$creator = new Zend_Form_Element_Text('his_file_creator');
		$creator->setLabel(_('Creator:'));
		$creator->setAttrib('class', 'input_text');
		$creator->addFilter('StringTrim');
		//$creator->setDecorators(array('viewHelper'));
		$this->addElement($creator);

		/* Composer form element */
		$composer = new Zend_Form_Element_Text('his_file_composer');
		$composer->setLabel(_('Composer:'));
		$composer->setAttrib('class', 'input_text');
		$composer->addFilter('StringTrim');
		//$composer->setDecorators(array('viewHelper'));
		$this->addElement($composer);

		/* Copyright form element */
		$copyright = new Zend_Form_Element_Text('his_file_copyright');
		$copyright->setLabel(_('Copyright:'));
		$copyright->setAttrib('class', 'input_text');
		$copyright->addFilter('StringTrim');
		//$copyright->setDecorators(array('viewHelper'));
		$this->addElement($copyright);

		// Add the submit button
		$this->addElement('button', 'his_file_save', array(
			'ignore'   => true,
			'class'    => 'btn his_file_save',
			'label'    => _('Save'),
			'decorators' => array(
				'ViewHelper'
			)
		));

		// Add the cancel button
		$this->addElement('button', 'his_file_cancel', array(
			'ignore'   => true,
			'class'    => 'btn his_file_cancel',
			'label'    => _('Cancel'),
			'decorators' => array(
				'ViewHelper'
			)
		));

		$this->addDisplayGroup(
			array(
				'his_file_save',
				'his_file_cancel'
			),
			'submitButtons',
			array(
				'decorators' => array(
					'FormElements',
					'DtDdWrapper'
				)
			)
		);
	}
}