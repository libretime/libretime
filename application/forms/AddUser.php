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
                        
        $hidden = new Zend_Form_Element_Hidden('user_id');
        $hidden->setDecorators(array('ViewHelper'));
        $this->addElement($hidden);
              
        $login = new Zend_Form_Element_Text('login');
        $login->setLabel('Username:');
        $login->setAttrib('class', 'input_text');
        $login->setRequired(true);
        $login->addFilter('StringTrim');
        //$login->addValidator('UserNameValidate');
        $this->addElement($login);
        
        $password = new Zend_Form_Element_Password('password');
        $password->setLabel('Password:');
        $password->setAttrib('class', 'input_text');
        $password->setRequired(true);
        $password->addFilter('StringTrim');
        $password->addValidator('NotEmpty');
        $this->addElement($password);
    
        $firstName = new Zend_Form_Element_Text('first_name');
        $firstName->setLabel('Firstname:');
        $firstName->setAttrib('class', 'input_text');
        $firstName->setRequired(true);
        $firstName->addFilter('StringTrim');
        $firstName->addValidator('NotEmpty');
        $this->addElement($firstName);
        
        $lastName = new Zend_Form_Element_Text('last_name');
        $lastName->setLabel('Lastname:');
        $lastName->setAttrib('class', 'input_text');
        $lastName->setRequired(true);
        $lastName->addFilter('StringTrim');
        $lastName->addValidator('NotEmpty');
        $this->addElement($lastName);
        
        $email = new Zend_Form_Element_Text('email');
        $email->setLabel('Email:');
        $email->setAttrib('class', 'input_text');
        $email->addFilter('StringTrim');
        $email->addValidator('EmailAddress');
        $this->addElement($email);

        $skype = new Zend_Form_Element_Text('skype');
        $skype->setLabel('Skype:');
        $skype->setAttrib('class', 'input_text');
        $skype->addFilter('StringTrim');
        $this->addElement($skype);

        $jabber = new Zend_Form_Element_Text('jabber');
        $jabber->setLabel('Jabber:');
        $jabber->setAttrib('class', 'input_text');
        $jabber->addFilter('StringTrim');
        $jabber->addValidator('EmailAddress');
        $this->addElement($jabber);

        $select = new Zend_Form_Element_Select('type');
        $select->setLabel('User Type:');
        $select->setAttrib('class', 'input_select');
        $select->setAttrib('style', 'width: 40%');
        $select->setMultiOptions(array(
                "G" => "Guest",
                "H" => "Host",
                "A" => "Admin"
            ));
        $select->setRequired(true);
        $this->addElement($select);
        
        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setAttrib('class', 'ui-button ui-state-default right-floated');
        $submit->setIgnore(true);
        $submit->setLabel('Submit');
        $this->addElement($submit);
    }
    
    public function validateLogin($data){
        
        if (strlen($data['user_id']) == 0){
            $count = CcSubjsQuery::create()->filterByDbLogin($data['login'])->count();
            
            if ($count != 0){
                $this->getElement('login')->setErrors(array("login name is not unique."));
                return false;
            }
        }
        
        return true;
    }
}

