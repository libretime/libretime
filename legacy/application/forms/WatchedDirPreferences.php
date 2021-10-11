<?php

class Application_Form_WatchedDirPreferences extends Zend_Form_SubForm
{

    public function init()
    {
        $this->setDecorators(array(
            array('ViewScript', array('viewScript' => 'form/preferences_watched_dirs.phtml'))
        ));

        $this->addElement('text', 'storageFolder', array(
            'class'      => 'input_text',
            'label'      => _('Import Folder:'),
            'required'   => false,
            'filters'    => array('StringTrim'),
            'value' => '',
            'decorators' => array(
                'ViewHelper'
            )
        ));

        $this->addElement('text', 'watchedFolder', array(
            'class'      => 'input_text',
            'label'      => _('Watched Folders:'),
            'required'   => false,
            'filters'    => array('StringTrim'),
            'value' => '',
            'decorators' => array(
                'ViewHelper'
            )
        ));
    }

    public function verifyChosenFolder($p_form_element_id)
    {
        $element = $this->getElement($p_form_element_id);

        if (!is_dir($element->getValue())) {
            $element->setErrors(array(_('Not a valid Directory')));

            return false;
        } else {
            $element->setValue("");

            return true;
        }

    }

}
