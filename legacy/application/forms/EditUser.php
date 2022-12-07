<?php

declare(strict_types=1);

class Application_Form_EditUser extends Zend_Form
{
    public function init()
    {
        /*
        $this->addElementPrefixPath('Application_Validate',
                                    '../application/validate',
                                    'validate');
                                    * */

        $currentUser = Application_Model_User::getCurrentUser();
        $currentUserId = $currentUser->getId();
        $userData = Application_Model_User::GetUserData($currentUserId);
        $notEmptyValidator = Application_Form_Helper_ValidationTypes::overrideNotEmptyValidator();
        $emailValidator = Application_Form_Helper_ValidationTypes::overrideEmailAddressValidator();
        $notDemoValidator = new Application_Validate_NotDemoValidate();

        $this->setDecorators([
            ['ViewScript', ['viewScript' => 'form/edit-user.phtml', 'currentUser' => $currentUser->getLogin()]],
        ]);
        $this->setAttrib('id', 'current-user-form');

        $csrf_namespace = new Zend_Session_Namespace('csrf_namespace');
        $csrf_element = new Zend_Form_Element_Hidden('csrf');
        $csrf_element->setValue($csrf_namespace->authtoken)->setRequired('true')->removeDecorator('HtmlTag')->removeDecorator('Label');
        $this->addElement($csrf_element);

        $hidden = new Zend_Form_Element_Hidden('cu_user_id');
        $hidden->setDecorators(['ViewHelper']);
        $hidden->setValue($userData['id']);
        $this->addElement($hidden);

        $login = new Zend_Form_Element_Text('cu_login');
        $login->setLabel(_('Username:'));
        $login->setValue($userData['login']);
        $login->setAttrib('class', 'input_text');
        $login->setAttrib('readonly', 'readonly');
        $login->setRequired(true);
        $login->addValidator($notEmptyValidator);
        $login->addFilter('StringTrim');
        $login->setDecorators(['viewHelper']);
        $this->addElement($login);

        $password = new Zend_Form_Element_Password('cu_password');
        $password->setLabel(_('Password:'));
        $password->setAttrib('class', 'input_text');
        $password->setRequired(true);
        $password->addFilter('StringTrim');
        $password->addValidator($notEmptyValidator);
        $password->setDecorators(['viewHelper']);
        $this->addElement($password);

        $passwordVerify = new Zend_Form_Element_Password('cu_passwordVerify');
        $passwordVerify->setLabel(_('Verify Password:'));
        $passwordVerify->setAttrib('class', 'input_text');
        $passwordVerify->setRequired(true);
        $passwordVerify->addFilter('StringTrim');
        $passwordVerify->addValidator($notEmptyValidator);
        $passwordVerify->addValidator($notDemoValidator);
        $passwordVerify->setDecorators(['viewHelper']);
        $this->addElement($passwordVerify);

        $firstName = new Zend_Form_Element_Text('cu_first_name');
        $firstName->setLabel(_('Firstname:'));
        $firstName->setValue($userData['first_name']);
        $firstName->setAttrib('class', 'input_text');
        $firstName->addFilter('StringTrim');
        $firstName->setDecorators(['viewHelper']);
        $this->addElement($firstName);

        $lastName = new Zend_Form_Element_Text('cu_last_name');
        $lastName->setLabel(_('Lastname:'));
        $lastName->setValue($userData['last_name']);
        $lastName->setAttrib('class', 'input_text');
        $lastName->addFilter('StringTrim');
        $lastName->setDecorators(['viewHelper']);
        $this->addElement($lastName);

        $email = new Zend_Form_Element_Text('cu_email');
        $email->setLabel(_('Email:'));
        $email->setValue($userData['email']);
        $email->setAttrib('class', 'input_text');
        $email->addFilter('StringTrim');
        $email->setRequired(true);
        $email->addValidator($notEmptyValidator);
        $email->addValidator($emailValidator);
        $email->setDecorators(['viewHelper']);
        $this->addElement($email);

        $cellPhone = new Zend_Form_Element_Text('cu_cell_phone');
        $cellPhone->setLabel(_('Mobile Phone:'));
        $cellPhone->setValue($userData['cell_phone']);
        $cellPhone->setAttrib('class', 'input_text');
        $cellPhone->addFilter('StringTrim');
        $cellPhone->setDecorators(['viewHelper']);
        $this->addElement($cellPhone);

        $skype = new Zend_Form_Element_Text('cu_skype');
        $skype->setLabel(_('Skype:'));
        $skype->setValue($userData['skype_contact']);
        $skype->setAttrib('class', 'input_text');
        $skype->addFilter('StringTrim');
        $skype->setDecorators(['viewHelper']);
        $this->addElement($skype);

        $jabber = new Zend_Form_Element_Text('cu_jabber');
        $jabber->setLabel(_('Jabber:'));
        $jabber->setValue($userData['jabber_contact']);
        $jabber->setAttrib('class', 'input_text');
        $jabber->addFilter('StringTrim');
        $jabber->addValidator($emailValidator);
        $jabber->setDecorators(['viewHelper']);
        $this->addElement($jabber);

        $locale = new Zend_Form_Element_Select('cu_locale');
        $locale->setLabel(_('Language:'));
        $locale->setMultiOptions(Application_Model_Locale::getLocales());
        $locale->setValue(Application_Model_Preference::GetUserLocale());
        $locale->setDecorators(['ViewHelper']);
        $this->addElement($locale);

        $stationTz = Application_Model_Preference::GetDefaultTimezone();
        $userTz = Application_Model_Preference::GetUserTimezone();

        $timezone = new Zend_Form_Element_Select('cu_timezone');
        $timezone->setLabel(_('Interface Timezone:'));
        $timezone->setMultiOptions(Application_Common_Timezone::getTimezones());
        $timezone->setValue($userTz == $stationTz ? null : $userTz);
        $timezone->setDecorators(['ViewHelper']);
        $this->addElement($timezone);
    }

    public function validateLogin($p_login, $p_userId)
    {
        $count = CcSubjsQuery::create()
            ->filterByDbLogin($p_login)
            ->filterByDbId($p_userId, Criteria::NOT_EQUAL)
            ->count();

        if ($count != 0) {
            $this->getElement('cu_login')->setErrors([_('Login name is not unique.')]);

            return false;
        }

        return true;
    }

    // We need to add the password identical validator here in case
    // Zend version is less than 1.10.5
    public function isValid($data)
    {
        if (isset($data['cu_password'])) {
            $passwordIdenticalValidator = Application_Form_Helper_ValidationTypes::overridePasswordIdenticalValidator(
                $data['cu_password']
            );
            $this->getElement('cu_passwordVerify')->addValidator($passwordIdenticalValidator);
        }

        return parent::isValid($data);
    }
}
