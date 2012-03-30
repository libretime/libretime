<?php

class Application_Form_AddShowRR extends Zend_Form_SubForm
{

    public function init()
    {
        // Add record element
		$this->addElement('checkbox', 'add_show_record', array(
            'label'      => 'Record from Line In?',
            'required'   => false,
		));

        // Add record element
		$this->addElement('checkbox', 'add_show_rebroadcast', array(
            'label'      => 'Rebroadcast?',
            'required'   => false,
		));
    }

    public function disable(){
        $elements = $this->getElements();
        foreach ($elements as $element)
        {
            if ($element->getType() != 'Zend_Form_Element_Hidden')
            {
                $element->setAttrib('readonly',true);
                $element->setAttribs(array('style' => 'color: #B1B1B1; '));
            }
        }
    }

}

