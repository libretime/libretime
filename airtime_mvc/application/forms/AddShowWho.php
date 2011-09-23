<?php

class Application_Form_AddShowWho extends Zend_Form_SubForm
{

    public function init()
    {
        // Add hosts autocomplete
        $this->addElement('text', 'add_show_hosts_autocomplete', array(
            'label'      => 'Search Users:',
            'class'      => 'input_text ui-autocomplete-input',
            'required'   => false
		)); 

		$options = array();
		$hosts = Application_Model_User::getHosts();

		foreach ($hosts as $host) {
			$options[$host['value']] = $host['label'];
		}

		//Add hosts selection
		$hosts = new Zend_Form_Element_MultiCheckbox('add_show_hosts');
		$hosts->setLabel('DJs:')
			->setMultiOptions($options);

		$this->addElement($hosts);
    }


}

