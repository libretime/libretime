<?php

class Application_Form_AddShowStyle extends Zend_Form_SubForm
{

    public function init()
    {
       // Add show background-color input
        $this->addElement('text', 'add_show_background_color', array(
            'label'      => 'Background Colour:',
            'filters'    => array('StringTrim')
        ));

	// Add show color input
        $this->addElement('text', 'add_show_color', array(
            'label'      => 'Text Colour',
            'filters'    => array('StringTrim')
        ));
    }


}

