<?php

/**
 */
class Application_Form_PasswordRestore extends Zend_Form
{
    public function init()
    {
        $this->addElement('text', 'email', array(
            'label' => 'E-mail',
            'required' => true,
            'filters' => array(
                'stringTrim',
            ),
        ));

        $this->addElement('submit', 'submit', array(
            'label' => 'Restore password',
            'ignore' => true,
            'class' => 'ui-button ui-state-default'
        ));
    }
}