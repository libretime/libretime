<?php

declare(strict_types=1);

class Application_Form_AddUser extends Zend_Form
{
    public function init()
    {
        /*
        $this->addElementPrefixPath('Application_Validate',
                                    '../application/validate',
                                    'validate');
                                    * */
        $notEmptyValidator = Application_Form_Helper_ValidationTypes::overrideNotEmptyValidator();
        $emailValidator = Application_Form_Helper_ValidationTypes::overrideEmailAddressValidator();
        $notDemoValidator = new Application_Validate_NotDemoValidate();

        $this->setAttrib('id', 'user_form');

        $hidden = new Zend_Form_Element_Hidden('user_id');
        $hidden->setDecorators(['ViewHelper']);
        $this->addElement($hidden);

        $this->addElement('hash', 'csrf', [
            'salt' => 'unique',
        ]);

        $login = new Zend_Form_Element_Text('login');
        $login->setLabel(_('Username:'));
        $login->setAttrib('class', 'input_text');
        $login->setRequired(true);
        $login->addValidator($notEmptyValidator);
        $login->addFilter('StringTrim');
        // $login->addValidator('UserNameValidate');
        $this->addElement($login);

        $password = new Zend_Form_Element_Password('password');
        $password->setLabel(_('Password:'));
        $password->setAttrib('class', 'input_text');
        $password->setRequired(true);
        $password->addFilter('StringTrim');
        $password->addValidator($notEmptyValidator);
        $this->addElement($password);

        $passwordVerify = new Zend_Form_Element_Password('passwordVerify');
        $passwordVerify->setLabel(_('Verify Password:'));
        $passwordVerify->setAttrib('class', 'input_text');
        $passwordVerify->setRequired(true);
        $passwordVerify->addFilter('StringTrim');
        $passwordVerify->addValidator($notEmptyValidator);
        $passwordVerify->addValidator($notDemoValidator);
        $this->addElement($passwordVerify);

        $firstName = new Zend_Form_Element_Text('first_name');
        $firstName->setLabel(_('Firstname:'));
        $firstName->setAttrib('class', 'input_text');
        $firstName->addFilter('StringTrim');
        $this->addElement($firstName);

        $lastName = new Zend_Form_Element_Text('last_name');
        $lastName->setLabel(_('Lastname:'));
        $lastName->setAttrib('class', 'input_text');
        $lastName->addFilter('StringTrim');
        $this->addElement($lastName);

        $email = new Zend_Form_Element_Text('email');
        $email->setLabel(_('Email:'));
        $email->setAttrib('class', 'input_text');
        $email->addFilter('StringTrim');
        $email->setRequired(true);
        $email->addValidator($notEmptyValidator);
        $email->addValidator($emailValidator);
        $this->addElement($email);

        $cellPhone = new Zend_Form_Element_Text('cell_phone');
        $cellPhone->setLabel(_('Mobile Phone:'));
        $cellPhone->setAttrib('class', 'input_text');
        $cellPhone->addFilter('StringTrim');
        $this->addElement($cellPhone);

        $skype = new Zend_Form_Element_Text('skype');
        $skype->setLabel(_('Skype:'));
        $skype->setAttrib('class', 'input_text');
        $skype->addFilter('StringTrim');
        $this->addElement($skype);

        $jabber = new Zend_Form_Element_Text('jabber');
        $jabber->setLabel(_('Jabber:'));
        $jabber->setAttrib('class', 'input_text');
        $jabber->addFilter('StringTrim');
        $jabber->addValidator($emailValidator);
        $this->addElement($jabber);

        $select = new Zend_Form_Element_Select('type');
        $select->setLabel(_('User Type:'));
        $select->setAttrib('class', 'input_select');
        $select->setAttrib('style', 'width: 40%');
        $select->setMultiOptions([
            'G' => _('Guest'),
            'H' => _('DJ'),
            'P' => _('Program Manager'),
            'A' => _('Admin'),
        ]);
        $select->setRequired(false);
        $this->addElement($select);

        $saveBtn = new Zend_Form_Element_Button('save_user');
        $saveBtn->setAttrib('class', 'btn right-floated');
        $saveBtn->setIgnore(true);
        $saveBtn->setLabel(_('Save'));
        $this->addElement($saveBtn);
    }

    public function validateLogin($data)
    {
        if (strlen($data['user_id']) == 0) {
            $count = CcSubjsQuery::create()->filterByDbLogin($data['login'])->count();

            if ($count != 0) {
                $this->getElement('login')->setErrors([_('Login name is not unique.')]);

                return false;
            }
        }

        return true;
    }

    // We need to add the password identical validator here in case
    // Zend version is less than 1.10.5
    public function isValid($data)
    {
        $passwordIdenticalValidator = Application_Form_Helper_ValidationTypes::overridePasswordIdenticalValidator(
            $data['password']
        );
        $this->getElement('passwordVerify')->addValidator($passwordIdenticalValidator);

        return parent::isValid($data);
    }
}
