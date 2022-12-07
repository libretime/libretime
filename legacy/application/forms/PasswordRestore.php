<?php

declare(strict_types=1);

class Application_Form_PasswordRestore extends Zend_Form
{
    public function init()
    {
        $this->setDecorators([
            ['ViewScript', ['viewScript' => 'form/password-restore.phtml']],
        ]);

        $this->addElement('text', 'email', [
            'label' => _('Email'),
            'required' => true,
            'filters' => [
                'stringTrim',
            ],
            'decorators' => [
                'ViewHelper',
            ],
        ]);

        $this->addElement('text', 'username', [
            'label' => _('Username'),
            'required' => false,
            'filters' => [
                'stringTrim',
            ],
            'decorators' => [
                'ViewHelper',
            ],
        ]);

        $this->addElement('submit', 'submit', [
            'label' => _('Reset password'),
            'ignore' => true,
            'class' => 'ui-button ui-widget ui-state-default ui-button-text-only center',
            'decorators' => [
                'ViewHelper',
            ],
        ]);

        $cancel = new Zend_Form_Element_Button('cancel');
        $cancel->class = 'ui-button ui-widget ui-state-default ui-button-text-only center';
        $cancel->setLabel(_('Back'))
            ->setIgnore(true)
            ->setAttrib('onclick', 'window.location = ' . Zend_Controller_Front::getInstance()->getBaseUrl('login'))
            ->setDecorators(['ViewHelper']);
        $this->addElement($cancel);
    }
}
