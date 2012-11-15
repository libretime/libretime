<?php

class Application_Form_AddUser extends Zend_Form
{

    public function init()
    {
        /*
        $this->addElementPrefixPath('Application_Validate',
                                    '../application/validate',
                                    'validate');
                                    * */

        $this->setAttrib('id', 'user_form');
        
        $hidden = new Zend_Form_Element_Hidden('user_id');
        $hidden->setDecorators(array('ViewHelper'));
        $this->addElement($hidden);

        $login = new Zend_Form_Element_Text('login');
        $login->setLabel(_('Username:'));
        $login->setAttrib('class', 'input_text');
        $login->setRequired(true);
        $login->addFilter('StringTrim');
        //$login->addValidator('UserNameValidate');
        $this->addElement($login);

        $password = new Zend_Form_Element_Password('password');
        $password->setLabel(_('Password:'));
        $password->setAttrib('class', 'input_text');
        $password->setRequired(true);
        $password->addFilter('StringTrim');
        $password->addValidator('NotEmpty');
        $this->addElement($password);

        $firstName = new Zend_Form_Element_Text('first_name');
        $firstName->setLabel(_('Firstname:'));
        $firstName->setAttrib('class', 'input_text');
        $firstName->addFilter('StringTrim');
        $firstName->addValidator('NotEmpty');
        $this->addElement($firstName);

        $lastName = new Zend_Form_Element_Text('last_name');
        $lastName->setLabel(_('Lastname:'));
        $lastName->setAttrib('class', 'input_text');
        $lastName->addFilter('StringTrim');
        $lastName->addValidator('NotEmpty');
        $this->addElement($lastName);

        $email = new Zend_Form_Element_Text('email');
        $email->setLabel(_('Email:'));
        $email->setAttrib('class', 'input_text');
        $email->addFilter('StringTrim');
        $email->setRequired(true);
        $email->addValidator('EmailAddress');
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
        $jabber->addValidator('EmailAddress');
        $this->addElement($jabber);

        $select = new Zend_Form_Element_Select('type');
        $select->setLabel(_('User Type:'));
        $select->setAttrib('class', 'input_select');
        $select->setAttrib('style', 'width: 40%');
        $select->setMultiOptions(array(
                "G" => _("Guest"),
                "H" => _("DJ"),
                "P" => _("Program Manager"),
                "A" => _("Admin")
            ));
        $select->setRequired(true);
        $this->addElement($select);

        $saveBtn = new Zend_Form_Element_Button('save_user');
        $saveBtn->setAttrib('class', 'btn btn-small right-floated');
        $saveBtn->setIgnore(true);
        $saveBtn->setLabel(_('Save'));
        $this->addElement($saveBtn);
    }

    public function validateLogin($data)
    {
        if (strlen($data['user_id']) == 0) {
            $count = CcSubjsQuery::create()->filterByDbLogin($data['login'])->count();

            if ($count != 0) {
                $this->getElement('login')->setErrors(array(_("Login name is not unique.")));

                return false;
            }
        }

        return true;
    }
}
