<?php

declare(strict_types=1);

class Application_Form_WatchedDirPreferences extends Zend_Form_SubForm
{
    public function init()
    {
        $this->setDecorators([
            ['ViewScript', ['viewScript' => 'form/preferences_watched_dirs.phtml']],
        ]);

        $this->addElement('text', 'storageFolder', [
            'class' => 'input_text',
            'label' => _('Import Folder:'),
            'required' => false,
            'filters' => ['StringTrim'],
            'value' => '',
            'decorators' => [
                'ViewHelper',
            ],
        ]);

        $this->addElement('text', 'watchedFolder', [
            'class' => 'input_text',
            'label' => _('Watched Folders:'),
            'required' => false,
            'filters' => ['StringTrim'],
            'value' => '',
            'decorators' => [
                'ViewHelper',
            ],
        ]);
    }

    public function verifyChosenFolder($p_form_element_id)
    {
        $element = $this->getElement($p_form_element_id);

        if (!is_dir($element->getValue())) {
            $element->setErrors([_('Not a valid Directory')]);

            return false;
        }
        $element->setValue('');

        return true;
    }
}
