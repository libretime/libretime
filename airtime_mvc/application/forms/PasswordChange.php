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
        
        $this->addElement('password', 'password', array(
            'label' => 'Password',
            'required' => true,
            'filters' => array('stringTrim'),
            'validators' => array(
                array('stringLength', false, array(6, 80)),
            ),
            'decorators' => array(
                'ViewHelper'
            )
        ));

        $this->addElement('password', 'password_confirm', array(
            'label' => 'Confirm new password',
            'required' => true,
            'filters' => array('stringTrim'),
            'validators' => array(
                new Zend_Validate_Callback(function ($value, $context) {
                    return $value == $context['password'];
                }),
            ),
            'errorMessages' => array("Password confirmation does not match your password."),
            'decorators' => array(
                'ViewHelper'
            )
        ));

        $this->addElement('submit', 'submit', array(
            'label' => 'Get new password',
            'ignore' => true,
            'class' => 'ui-button ui-widget ui-state-default ui-button-text-only center',
            'decorators' => array(
                'ViewHelper'
            )
        ));
    }
}
