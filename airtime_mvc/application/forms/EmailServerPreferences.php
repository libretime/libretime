<?php
require_once 'customvalidators/ConditionalNotEmpty.php';

class Application_Form_EmailServerPreferences extends Zend_Form_SubForm
{

    public function init()
    {
        $this->setDecorators(array(
            array('ViewScript', array('viewScript' => 'form/preferences_email_server.phtml'))
        ));

        // Enable system emails
        $this->addElement('checkbox', 'enableSystemEmail', array(
            'label' => _('Enable System Emails (Password Reset)'),
            'required' => false,
            'value' => Application_Model_Preference::GetEnableSystemEmail(),
            'decorators' => array(
                'ViewHelper'
            )
        ));

        $this->addElement('text', 'systemEmail', array(
            'class' => 'input_text',
            'label' => _("Reset Password 'From' Email"),
            'value' => Application_Model_Preference::GetSystemEmail(),
            'readonly' => true,
            'decorators' => array('viewHelper')
        ));

        $this->addElement('checkbox', 'configureMailServer', array(
            'label' => _('Configure Mail Server'),
            'required' => false,
            'value' => Application_Model_Preference::GetMailServerConfigured(),
            'decorators' => array (
                'viewHelper'
            )
        ));

        $this->addElement('checkbox', 'msRequiresAuth', array(
            'label' => _('Requires Authentication'),
            'required' => false,
            'value' => Application_Model_Preference::GetMailServerRequiresAuth(),
            'decorators' => array(
                'viewHelper'
            )
        ));

        $this->addElement('text', 'mailServer', array(
            'class' => 'input_text',
            'label' => _('Mail Server'),
            'value' => Application_Model_Preference::GetMailServer(),
            'readonly' => true,
            'decorators' => array('viewHelper'),
            'allowEmpty' => false,
            'validators' => array(
                new ConditionalNotEmpty(array(
                    'configureMailServer' => '1'
                ))
            )
        ));

        $this->addElement('text', 'email', array(
            'class' => 'input_text',
            'label' => _('Email Address'),
            'value' => Application_Model_Preference::GetMailServerEmailAddress(),
            'readonly' => true,
            'decorators' => array('viewHelper'),
            'allowEmpty' => false,
            'validators' => array(
                new ConditionalNotEmpty(array(
                    'configureMailServer' => '1',
                    'msRequiresAuth' => '1'
                ))
            )
        ));

        $this->addElement('password', 'ms_password', array(
            'class' => 'input_text',
            'label' => _('Password'),
            'value' => Application_Model_Preference::GetMailServerPassword(),
            'readonly' => true,
            'decorators' => array('viewHelper'),
            'allowEmpty' => false,
            'validators' => array(
                new ConditionalNotEmpty(array(
                    'configureMailServer' => '1',
                    'msRequiresAuth' => '1'
                ))
            ),
            'renderPassword' => true
        ));

        $port = new Zend_Form_Element_Text('port');
        $port->class = 'input_text';
        $port->setRequired(false)
            ->setValue(Application_Model_Preference::GetMailServerPort())
            ->setLabel(_('Port'))
            ->setAttrib('readonly', true)
            ->setDecorators(array('viewHelper'));

        $this->addElement($port);

    }

}
