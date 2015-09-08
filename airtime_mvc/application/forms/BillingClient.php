<?php
require_once 'Zend/Locale.php';

class Application_Form_BillingClient extends Zend_Form
{
    public function init()
    {        
        /*$this->setDecorators(array(
                array('ViewScript', array('viewScript' => 'form/billing-purchase.phtml'))));*/
        $client = Billing::getClientDetails();
        $this->setAttrib("id", "clientdetails_form");
        
        $notEmptyValidator = Application_Form_Helper_ValidationTypes::overrideNotEmptyValidator();
        $emailValidator = Application_Form_Helper_ValidationTypes::overrideEmailAddressValidator();
        
        $firstname = new Zend_Form_Element_Text('firstname');
        $firstname->setLabel(_pro('First Name:'))
            ->setValue($client["firstname"])
            ->setAttrib('class', 'input_text')
            ->setRequired(true)
            ->addValidator($notEmptyValidator)
            ->addFilter('StringTrim');
        $this->addElement($firstname);

        $lastname = new Zend_Form_Element_Text('lastname');
        $lastname->setLabel(_pro('Last Name:'))
            ->setValue($client["lastname"])
            ->setAttrib('class', 'input_text')
            ->setRequired(true)
            ->addValidator($notEmptyValidator)
            ->addFilter('StringTrim');
        $this->addElement($lastname);

        $companyname = new Zend_Form_Element_Text('companyname');
        $companyname->setLabel(_pro('Company Name:'))
            ->setValue($client["companyname"])
            ->setAttrib('class', 'input_text')
            ->setRequired(false)
            ->addValidator($notEmptyValidator)
            ->addFilter('StringTrim');
        $this->addElement($companyname);

        $email = new Zend_Form_Element_Text('email');
        $email->setLabel(_pro('Email Address:'))
            ->setValue($client["email"])
            ->setAttrib('class', 'input_text')
            ->setRequired(true)
            ->setAttrib('readonly', 'readonly')
            ->addValidator($emailValidator)
            ->addFilter('StringTrim');
        $this->addElement($email);

        $address1 = new Zend_Form_Element_Text('address1');
        $address1->setLabel(_pro('Address 1:'))
            ->setValue($client["address1"])
            ->setAttrib('class', 'input_text')
            ->setRequired(true)
            ->addValidator($notEmptyValidator)
            ->addFilter('StringTrim');
        $this->addElement($address1);

        $address2 = new Zend_Form_Element_Text('address2');
        $address2->setLabel(_pro('Address 2:'))
            ->setValue($client["address2"])
            ->setAttrib('class', 'input_text')
            ->addFilter('StringTrim');
        $this->addElement($address2);

        $city = new Zend_Form_Element_Text('city');
        $city->setLabel(_pro('City:'))
            ->setValue($client["city"])
            ->setAttrib('class', 'input_text')
            ->setRequired(true)
            ->addValidator($notEmptyValidator)
            ->addFilter('StringTrim');
        $this->addElement($city);

        //TODO: get list from whmcs?
        $state = new Zend_Form_Element_Text('state');
        $state->setLabel(_pro('State/Region:'))
            ->setValue($client["state"])
            ->setAttrib('class', 'input_text')
            ->setRequired(true)
            ->addValidator($notEmptyValidator)
            ->addFilter('StringTrim');
        $this->addElement($state);

        $postcode = new Zend_Form_Element_Text('postcode');
        $postcode->setLabel(_pro('Zip Code / Postal Code:'))
            ->setValue($client["postcode"])
            ->setAttrib('class', 'input_text')
            ->setRequired(true)
            ->addValidator($notEmptyValidator)
            ->addFilter('StringTrim');
        $this->addElement($postcode);

        $locale = new Zend_Locale('en_US');
        $countries = $locale->getTranslationList('Territory', 'en', 2);
        asort($countries, SORT_LOCALE_STRING);
        
        $country = new Zend_Form_Element_Select('country');
        $country->setLabel(_pro('Country:'))
            ->setValue($client["country"])
            ->setAttrib('class', 'input_text')
            ->setMultiOptions($countries)
            ->setRequired(true)
            ->addValidator($notEmptyValidator)
            ->addFilter('StringTrim');
        $this->addElement($country);

        $phonenumber = new Zend_Form_Element_Text('phonenumber');
        $phonenumber->setLabel(_pro('Phone Number:'))
            ->setValue($client["phonenumber"])
            ->setAttrib('class', 'input_text')
            ->setRequired(true)
            ->addValidator($notEmptyValidator)
            ->addFilter('StringTrim');
        $this->addElement($phonenumber);

        $securityqid = new Zend_Form_Element_Select('securityqid');
        $securityqid->setLabel(_pro('Please choose a security question:'))
            ->setValue($client["securityqid"])
            ->setAttrib('class', 'input_text')
            ->setRequired(true)
            ->setMultiOptions(array(
                "1" => _("What is the name of your favorite childhood friend?"),
                "3" => _("What school did you attend for sixth grade?"),
                "4" => _("In what city did you meet your spouse/significant other?"),
                "5" => _("What street did you live on in third grade?"),
                "6" => _("What is the first name of the boy or girl that you first kissed?"),
                "7" => _("In what city or town was your first job?")));
        $this->addElement($securityqid);

        $securityqans = new Zend_Form_Element_Text('securityqans');
        $securityqans->setLabel(_pro('Please enter an answer:'))
            ->setValue($client["securityqans"])
            ->setAttrib('class', 'input_text')
            ->setRequired(true)
            ->addValidator($notEmptyValidator)
            ->addFilter('StringTrim');
        $this->addElement($securityqans);

        foreach ($client["customfields"] as $field) {
            if ($field["id"] == "7") {
                $vatvalue = $field["value"];
            } elseif ($field["id"] == "71") {
                $subscribevalue = $field["value"];
            }
        }

        $vat = new Zend_Form_Element_Text("7");
        $vat->setLabel(_pro('VAT/Tax ID (EU only)'))
            ->setBelongsTo('customfields')
            ->setValue($vatvalue)
            ->setAttrib('class', 'input_text')
            //->setRequired(true)
            //->addValidator($notEmptyValidator)
            ->addFilter('StringTrim');
        $this->addElement($vat);

        $subscribe = new Zend_Form_Element_Checkbox('71');
        $subscribe->setLabel(_pro('Subscribe to Sourcefabric newsletter'))
            ->setValue($subscribevalue)
            ->setBelongsTo('customfields')
            ->setAttrib('class', 'billing-details-checkbox')
            ->setRequired(true)
            ->addValidator($notEmptyValidator)
            ->addFilter('StringTrim');
        $this->addElement($subscribe);

        $password = new Zend_Form_Element_Password('password2');
        $password->setLabel(_pro('Password:'));
        $password->setAttrib('class', 'input_text');
        $password->setValue("xxxxxx");
        $password->setRequired(true);
        $password->addFilter('StringTrim');
        $password->addValidator($notEmptyValidator);
        $this->addElement($password);

        $passwordVerify = new Zend_Form_Element_Password('password2verify');
        $passwordVerify->setLabel(_pro('Verify Password:'));
        $passwordVerify->setAttrib('class', 'input_text');
        $passwordVerify->setValue("xxxxxx");
        $passwordVerify->setRequired(true);
        $passwordVerify->addFilter('StringTrim');
        //$passwordVerify->addValidator($notEmptyValidator);
        $passwordVerify->addValidator('Identical', false, array('token' => 'password2'));
        $passwordVerify->addValidator($notEmptyValidator);
        $this->addElement($passwordVerify);

        $submit = new Zend_Form_Element_Submit("submit");
        $submit->setIgnore(true)
                ->setLabel(_pro("Save"));
        $this->addElement($submit);
    }
}