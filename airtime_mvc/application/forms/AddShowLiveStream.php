<?php
require_once 'customvalidators/ConditionalNotEmpty.php';

class Application_Form_AddShowLiveStream extends Zend_Form_SubForm
{

    public function init()
    {
        $description1 = "This follows the same security pattern for the shows: if no users are explicitly set for the show, then anyone with a valid airtime login can connect to the stream, otherwise if there are users assigned to the show, then only those users can connect.";
        $cb_airtime_auth = new Zend_Form_Element_Checkbox("cb_airtime_auth");
        $cb_airtime_auth->setLabel("Connect using Airtime username & password")
                          ->setDescription($description1)
                          ->setRequired(false)
                          ->setDecorators(array('ViewHelper'));
        $this->addElement($cb_airtime_auth);
        
        $description2 = "Specifiy custom athentification which will work for only the show.";
        $cb_custom_auth = new Zend_Form_Element_Checkbox("cb_custom_auth");
        $cb_custom_auth  ->setLabel("Custom")
                            ->setDescription($description2)
                            ->setRequired(false)
                            ->setDecorators(array('ViewHelper'));
        $this->addElement($cb_custom_auth);
        
        //custom username
        $custom_username = new Zend_Form_Element_Text('custom_username');
        $custom_username->setAttrib('class', 'input_text')
                        ->setAttrib('autocomplete', 'off')
                        ->setAllowEmpty(true)
                        ->setLabel('Custom Username')
                        ->setFilters(array('StringTrim'))
                        ->setValidators(array(
                            new ConditionalNotEmpty(array("cb_custom_auth"=>"1"))))
                        ->setDecorators(array('ViewHelper'));
        $this->addElement($custom_username);
        
        //custom password
        $custom_password = new Zend_Form_Element_Password('custom_password');
        $custom_password->setAttrib('class', 'input_text')
                        ->setAttrib('autocomplete', 'off')
                        ->setAttrib('renderPassword','true')
                        ->setAllowEmpty(true)
                        ->setLabel('Custom Password')
                        ->setFilters(array('StringTrim'))
                        ->setValidators(array(
                            new ConditionalNotEmpty(array("cb_custom_auth"=>"1"))))
                        ->setDecorators(array('ViewHelper'));
        $this->addElement($custom_password);
        
        $this->setDecorators(array(
            array('ViewScript', array('viewScript' => 'form/add-show-live-stream.phtml', "connection_url"=>Application_Model_Preference::GetLiveDJSourceConnectionURL()))
        ));
    }
}