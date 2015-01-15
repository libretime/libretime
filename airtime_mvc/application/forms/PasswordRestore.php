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
            'label' => _('E-mail'),
            'required' => true,
            'filters' => array(
                'stringTrim',
            ),
            'decorators' => array(
                'ViewHelper'
            )
        ));

        $this->addElement('text', 'username', array(
            'label' => _('Username'),
            'required' => false,
            'filters' => array(
                'stringTrim',
            ),
            'decorators' => array(
                'ViewHelper'
            )
        ));

        $this->addElement('submit', 'submit', array(
            'label' => _('Restore password'),
            'ignore' => true,
            'class' => 'ui-button ui-widget ui-state-default ui-button-text-only center',
            'decorators' => array(
                'ViewHelper'
            )
        ));

        $cancel = new Zend_Form_Element_Button("cancel");
        $cancel->class = 'ui-button ui-widget ui-state-default ui-button-text-only center';
        $cancel->setLabel(_("Cancel"))
               ->setIgnore(True)
               ->setAttrib('onclick', 'redirectToLogin();')
               ->setDecorators(array('ViewHelper'));
        $this->addElement($cancel);
    }
}
