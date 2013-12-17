<?php

class Application_Form_AddShowWhat extends Zend_Form_SubForm
{
    public function init()
    {
        $notEmptyValidator = Application_Form_Helper_ValidationTypes::overrideNotEmptyValidator();
        // retrieves the length limit for each char field
        // and store to assoc array
        $maxLens = Application_Model_Show::getMaxLengths();

        // Hidden element to indicate whether the show is new or
        // whether we are updating an existing show.
        $this->addElement('hidden', 'add_show_id', array(
            'decorators' => array('ViewHelper')
        ));

        // Hidden element to indicate the instance id of the show
        // being edited.
        $this->addElement('hidden', 'add_show_instance_id', array(
            'decorators' => array('ViewHelper')
        ));

        // Add name element
        $this->addElement('text', 'add_show_name', array(
            'label'      => _('Name:'),
            'class'      => 'input_text',
            'required'   => true,
            'filters'    => array('StringTrim'),
            'value'        => _('Untitled Show'),
            'validators' => array($notEmptyValidator, array('StringLength', false, array(0, $maxLens['name'])))
        ));

         // Add URL element
        $this->addElement('text', 'add_show_url', array(
            'label'      => _('URL:'),
            'class'      => 'input_text',
            'required'   => false,
            'filters'    => array('StringTrim'),
            'validators' => array($notEmptyValidator, array('StringLength', false, array(0, $maxLens['url'])))
        ));

         // Add genre element
        $this->addElement('text', 'add_show_genre', array(
            'label'      => _('Genre:'),
            'class'      => 'input_text',
            'required'   => false,
            'filters'    => array('StringTrim'),
            'validators' => array(array('StringLength', false, array(0, $maxLens['genre'])))
        ));

         // Add the description element
        $this->addElement('textarea', 'add_show_description', array(
            'label'      => _('Description:'),
            'required'   => false,
            'class'      => 'input_text_area',
            'validators' => array(array('StringLength', false, array(0, $maxLens['description'])))
        ));

        $descText = $this->getElement('add_show_description');

        $descText->setDecorators(array(array('ViewScript', array(
            'viewScript' => 'form/add-show-block.phtml',
            'class'      => 'block-display'
        ))));

    }

    public function disable()
    {
        $elements = $this->getElements();
        foreach ($elements as $element) {
            if ($element->getType() != 'Zend_Form_Element_Hidden') {
                $element->setAttrib('disabled','disabled');
            }
        }
    }

    public function makeReadonly()
    {
        $elements = $this->getElements();
        foreach ($elements as $element) {
            if ($element->getType() != 'Zend_Form_Element_Hidden') {
                $element->setAttrib('readonly','readonly');
            }
        }
    }
}
