<?php

declare(strict_types=1);

class Application_Form_PasswordChange extends Zend_Form
{
    public function init()
    {
        $this->setDecorators([
            ['ViewScript', ['viewScript' => 'form/password-change.phtml']],
        ]);

        $notEmptyValidator = Application_Form_Helper_ValidationTypes::overrideNotEmptyValidator();
        $stringLengthValidator = Application_Form_Helper_ValidationTypes::overrideStringLengthValidator(6, 80);

        $this->addElement('password', 'password', [
            'label' => _('Password'),
            'required' => true,
            'filters' => ['stringTrim'],
            'validators' => [
                $notEmptyValidator,
                $stringLengthValidator,
            ],
            'decorators' => [
                'ViewHelper',
            ],
        ]);

        $this->addElement('password', 'password_confirm', [
            'label' => _('Confirm new password'),
            'required' => true,
            'filters' => ['stringTrim'],
            'validators' => [
                new Zend_Validate_Callback(function ($value, $context) {
                    return $value == $context['password'];
                }),
            ],
            'errorMessages' => [_('Password confirmation does not match your password.')],
            'decorators' => [
                'ViewHelper',
            ],
        ]);

        $this->addElement('submit', 'submit', [
            'label' => _('Save'),
            'ignore' => true,
            'class' => 'ui-button ui-widget ui-state-default ui-button-text-only center',
            'decorators' => [
                'ViewHelper',
            ],
        ]);
    }
}
