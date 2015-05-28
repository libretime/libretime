<?php

class Application_Form_DangerousPreferences extends Zend_Form_SubForm {

    public function init() {

        $this->setDecorators(array(
            array('ViewScript', array('viewScript' => 'form/preferences_danger.phtml'))
        ));

        $clearLibrary = new Zend_Form_Element_Button('clear_library');
        $clearLibrary->setLabel(_('Delete All Tracks in Library'));
        //$submit->removeDecorator('Label');
        $clearLibrary->setAttribs(array('class'=>'btn centered'));
        $clearLibrary->setAttrib('onclick', 'deleteAllFiles();');
        $clearLibrary->removeDecorator('DtDdWrapper');

        $this->addElement($clearLibrary);
    }

}
