<?php

declare(strict_types=1);

class Application_Form_DangerousPreferences extends Zend_Form_SubForm
{
    public function init()
    {
        $this->setDecorators([
            ['ViewScript', ['viewScript' => 'form/preferences_danger.phtml']],
        ]);

        $clearLibrary = new Zend_Form_Element_Button('clear_library');
        $clearLibrary->setLabel(_('Delete All Tracks in Library'));
        // $submit->removeDecorator('Label');
        $clearLibrary->setAttribs(['class' => 'btn centered']);
        $clearLibrary->setAttrib('onclick', 'deleteAllFiles();');
        $clearLibrary->removeDecorator('DtDdWrapper');

        $this->addElement($clearLibrary);
    }
}
