<?php

declare(strict_types=1);

class Application_Form_AddShowWho extends Zend_Form_SubForm
{
    public function init()
    {
        // Add hosts autocomplete
        $this->addElement('text', 'add_show_hosts_autocomplete', [
            'label' => _('Search Users:'),
            'class' => 'input_text ui-autocomplete-input',
            'required' => false,
        ]);

        $options = [];
        $hosts = Application_Model_User::getHosts();

        foreach ($hosts as $host) {
            $options[$host['index']] = $host['label'];
        }

        // Add hosts selection
        $hosts = new Zend_Form_Element_MultiCheckbox('add_show_hosts');
        $hosts->setLabel(_('DJs:'))
            ->setMultiOptions($options);

        $this->addElement($hosts);
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
}
