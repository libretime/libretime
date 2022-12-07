<?php

declare(strict_types=1);

class Application_Form_SetupLanguageTimezone extends Zend_Form_SubForm
{
    public function init()
    {
        $this->setDecorators([
            ['ViewScript', ['viewScript' => 'form/setup-lang-timezone.phtml']],
        ]);

        $csrf_namespace = new Zend_Session_Namespace('csrf_namespace');
        $csrf_element = new Zend_Form_Element_Hidden('csrf');
        $csrf_element->setValue($csrf_namespace->authtoken)->setRequired('true')->removeDecorator('HtmlTag')->removeDecorator('Label');
        $this->addElement($csrf_element);

        $language = new Zend_Form_Element_Select('setup_language');
        $language->setLabel(_('Station Language'));
        $language->setMultiOptions(Application_Model_Locale::getLocales());
        $this->addElement($language);

        $timezone = new Zend_Form_Element_Select('setup_timezone');
        $timezone->setLabel(_('Station Timezone'));
        $timezone->setMultiOptions(Application_Common_Timezone::getTimezones());
        $this->addElement($timezone);
    }
}
