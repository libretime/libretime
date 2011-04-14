<?php

class Application_Form_AddShowRR extends Zend_Form_SubForm
{

    public function init()
    {
        // Add record element
		$this->addElement('checkbox', 'add_show_record', array(
            'label'      => 'Record?',
            'required'   => false,
		));

        // Add record element
		$this->addElement('checkbox', 'add_show_rebroadcast', array(
            'label'      => 'Rebroadcast?',
            'required'   => false,
		));
    }


}

