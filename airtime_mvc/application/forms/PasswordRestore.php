<?php

/**
 */
class Application_Form_PasswordRestore extends Zend_Form
{
    public function init()
    {
        $this->setDecorators(array(
                array('ViewScript', array('viewScript' => 'form/password-restore.phtml'))
        ));
        
        $this->addElement('text', 'email', array(
            'label' => 'E-mail',
            'required' => true,
            'filters' => array(
                'stringTrim',
            ),
            'decorators' => array(
                'ViewHelper'
            )
        ));

        $this->addElement('submit', 'submit', array(
            'label' => 'Restore password',
            'ignore' => true,
            'class' => 'ui-button ui-widget ui-state-default ui-button-text-only center',
            'decorators' => array(
                'ViewHelper'
            )
        ));
    }
}