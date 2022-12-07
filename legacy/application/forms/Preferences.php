<?php

declare(strict_types=1);

class Application_Form_Preferences extends Zend_Form
{
    public function init()
    {
        $this->setDecorators([
            ['ViewScript', ['viewScript' => 'form/preferences.phtml']],
        ]);

        $general_pref = new Application_Form_GeneralPreferences();

        // $this->addElement('hash', 'csrf', array(
        //     'salt' => 'unique',
        //     'decorators' => array(
        //         'ViewHelper'
        //     )
        // ));

        $csrf_namespace = new Zend_Session_Namespace('csrf_namespace');
        $csrf_element = new Zend_Form_Element_Hidden('csrf');
        $csrf_element->setValue($csrf_namespace->authtoken)->setRequired('true')->removeDecorator('HtmlTag')->removeDecorator('Label');
        $this->addElement($csrf_element);

        $this->addSubForm($general_pref, 'preferences_general');

        // tunein form
        $tuneinPreferences = new Application_Form_TuneInPreferences();
        $this->addSubForm($tuneinPreferences, 'preferences_tunein');

        $danger_pref = new Application_Form_DangerousPreferences();
        $this->addSubForm($danger_pref, 'preferences_danger');

        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setLabel(_('Save'));
        // $submit->removeDecorator('Label');
        $submit->setAttribs(['class' => 'btn right-floated']);
        $submit->removeDecorator('DtDdWrapper');

        $this->addElement($submit);
    }
}
