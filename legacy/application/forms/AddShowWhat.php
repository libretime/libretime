<?php

declare(strict_types=1);

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
        $this->addElement('hidden', 'add_show_id', [
            'decorators' => ['ViewHelper'],
        ]);

        // Hidden element to indicate the instance id of the show
        // being edited.
        $this->addElement('hidden', 'add_show_instance_id', [
            'decorators' => ['ViewHelper'],
        ]);

        // Add name element
        $this->addElement('text', 'add_show_name', [
            'label' => _('Name:'),
            'class' => 'input_text',
            'required' => true,
            'filters' => ['StringTrim'],
            'value' => _('Untitled Show'),
            'validators' => [$notEmptyValidator, ['StringLength', false, [0, $maxLens['name']]]],
        ]);

        // Add URL element
        $this->addElement('text', 'add_show_url', [
            'label' => _('URL:'),
            'class' => 'input_text',
            'required' => false,
            'filters' => ['StringTrim'],
            'validators' => [$notEmptyValidator, ['StringLength', false, [0, $maxLens['url']]]],
        ]);

        // Add genre element
        $this->addElement('text', 'add_show_genre', [
            'label' => _('Genre:'),
            'class' => 'input_text',
            'required' => false,
            'filters' => ['StringTrim'],
            'validators' => [['StringLength', false, [0, $maxLens['genre']]]],
        ]);

        // Add the description element
        $this->addElement('textarea', 'add_show_description', [
            'label' => _('Description:'),
            'required' => false,
            'class' => 'input_text_area',
            'validators' => [['StringLength', false, [0, $maxLens['description']]]],
        ]);

        $descText = $this->getElement('add_show_description');

        $descText->setDecorators([['ViewScript', [
            'viewScript' => 'form/add-show-block.phtml',
            'class' => 'block-display',
        ]]]);

        // Add the instance description
        $this->addElement('textarea', 'add_show_instance_description', [
            'label' => _('Instance Description:'),
            'required' => false,
            'class' => 'input_text_area',
            'validators' => [['StringLength', false, [0, $maxLens['description']]]],
        ]);

        $instanceDesc = $this->getElement('add_show_instance_description');

        $instanceDesc->setDecorators([['ViewScript', [
            'viewScript' => 'form/add-show-block.phtml',
            'class' => 'block-display',
        ]]]);
        $instanceDesc->setAttrib('disabled', 'disabled');
    }

    /**
     * Enable the instance description when editing a show instance.
     */
    public function enableInstanceDesc()
    {
        $el = $this->getElement('add_show_instance_description');
        Logging::info($el);
        $el->setAttrib('disabled', null);
        $el->setAttrib('readonly', null);
    }

    public function disable()
    {
        $elements = $this->getElements();
        foreach ($elements as $element) {
            if ($element->getType() != 'Zend_Form_Element_Hidden') {
                $element->setAttrib('disabled', 'disabled');
            }
        }
    }

    public function makeReadonly()
    {
        $elements = $this->getElements();
        foreach ($elements as $element) {
            if ($element->getType() != 'Zend_Form_Element_Hidden') {
                $element->setAttrib('readonly', 'readonly');
            }
        }
    }
}
