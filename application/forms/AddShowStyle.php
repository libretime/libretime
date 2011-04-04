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
    }


}

