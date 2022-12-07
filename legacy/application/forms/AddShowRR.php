<?php

declare(strict_types=1);

class Application_Form_AddShowRR extends Zend_Form_SubForm
{
    public function init()
    {
        // Add record element
        $this->addElement('checkbox', 'add_show_record', [
            'label' => _('Record from Line In?'),
            'required' => false,
        ]);

        // Add record element
        $this->addElement('checkbox', 'add_show_rebroadcast', [
            'label' => _('Rebroadcast?'),
            'required' => false,
        ]);
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
