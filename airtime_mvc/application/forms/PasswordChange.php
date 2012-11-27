<?php

/**
 */
class Application_Form_PasswordChange extends Zend_Form
{
    public function init()
    {
        $this->setDecorators(array(
                array('ViewScript', array('viewScript' => 'form/password-change.phtml'))
        ));
        
        $notEmptyValidator = Application_Form_Helper_ValidationTypes::overrideNotEmptyValidator();

        $this->addElement('password', 'password', array(
            'label' => _('Password'),
            'required' => true,
            'filters' => array('stringTrim'),
            'validators' => array($notEmptyValidator,
                array('stringLength', false, array(6, 80)),
            ),
            'decorators' => array(
                'ViewHelper'
            )
        ));

        $this->addElement('password', 'password_confirm', array(
            'label' => _('Confirm new password'),
            'required' => true,
            'filters' => array('stringTrim'),
            'validators' => array(
                new Zend_Validate_Callback(function ($value, $context) {
                    return $value == $context['password'];
                }),
            ),
            'errorMessages' => array(_("Password confirmation does not match your password.")),
            'decorators' => array(
                'ViewHelper'
            )
        ));

        $this->addElement('submit', 'submit', array(
            'label' => _('Get new password'),
            'ignore' => true,
            'class' => 'ui-button ui-widget ui-state-default ui-button-text-only center',
            'decorators' => array(
                'ViewHelper'
            )
        ));
    }
}
