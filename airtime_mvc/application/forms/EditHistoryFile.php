<?php

class Application_Form_EditHistoryFile extends Application_Form_EditHistory
{
	const ID_PREFIX = "his_file_";
	
	public function init() {
		
		parent::init();

		$this->setDecorators(
			array(
				array('ViewScript', array('viewScript' => 'form/edit-history-file.phtml'))
			)
		);
	}
	
	public function createFromTemplate($template, $required) {
	
		parent::createFromTemplate($template, $required);
		
		$templateSubForm = $this->getTemplateSubForm();
		
		//slightly hacky, but just need to check length for now.
		$el = $templateSubForm->getElement(self::ID_PREFIX.MDATA_KEY_DURATION);
		if (isset($el)) {
			$el->setAttrib("readonly", true);
		}
	}
}