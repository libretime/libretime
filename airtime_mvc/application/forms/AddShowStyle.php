<?php

class Application_Form_AddShowStyle extends Zend_Form_SubForm
{

    public function init()
    {
       // Add show background-color input
        $this->addElement('text', 'add_show_background_color', array(
            'label'      => 'Background Colour:',
            'class'      => 'input_text',
            'filters'    => array('StringTrim')
        ));

        $bg = $this->getElement('add_show_background_color');

        $bg->setDecorators(array(array('ViewScript', array(
            'viewScript' => 'form/add-show-style.phtml',
            'class'      => 'big'
        ))));
        
        $bg->setValidators(array(
            'Hex',
            array('stringLength', false, array(6, 6))
        ));
                

	// Add show color input
        $this->addElement('text', 'add_show_color', array(
            'label'      => 'Text Colour:',
            'class'      => 'input_text',
            'filters'    => array('StringTrim')
        ));

        $c = $this->getElement('add_show_color');

        $c->setDecorators(array(array('ViewScript', array(
            'viewScript' => 'form/add-show-style.phtml',
            'class'      => 'big'
        ))));
        
        $c->setValidators(array(
                'Hex',
                array('stringLength', false, array(6, 6))
        ));
    }

    public function disable(){
        $elements = $this->getElements();
        foreach ($elements as $element)
        {
            if ($element->getType() != 'Zend_Form_Element_Hidden')
            {
                $element->setAttrib('disabled','disabled');
            }
        }
    }

}

