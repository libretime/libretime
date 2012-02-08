<?php
require_once 'customvalidators/ConditionalNotEmpty.php';

class Application_Form_AddShowLiveStream extends Zend_Form_SubForm
{

    public function init()
    {
        $this->setDecorators(array(
        array('ViewScript', array('viewScript' => 'form/add-show-live-stream.phtml'))
        ));

        $allow_live_stream = new Zend_Form_Element_Checkbox("allow_live_stream_override");
        $allow_live_stream->setLabel("Allow Live Stream Override")
                          ->setRequired(false)
                          ->setDecorators(array('ViewHelper'));
        $this->addElement($allow_live_stream);
         
        $description1 = "This follows the same security pattern for the shows: if no users are explicitly set for the show, then anyone with a valid airtime login can connect to the stream, otherwise if there are users assigned to the show, then only those users can connect.";
        $security_setting1 = new Zend_Form_Element_Checkbox("security_setting1");
        $security_setting1->setLabel("Connect using Airtime username & password")
                          ->setDescription($description1)
                          ->setRequired(false)
                          ->setDecorators(array('ViewHelper'));
        $this->addElement($security_setting1);
        
        $description2 = "Specifiy custom athentification which will work for only the show.";
        $security_setting2 = new Zend_Form_Element_Checkbox("security_setting2");
        $security_setting2  ->setLabel("Custom")
                            ->setDescription($description2)
                            ->setRequired(false)
                            ->setDecorators(array('ViewHelper'));
        $this->addElement($security_setting2);
        
        //custom username
        $custom_username = new Zend_Form_Element_Text('custom_username');
        $custom_username->setAttrib('class', 'input_text')
                        ->setAttrib('autocomplete', 'off')
                        ->setAllowEmpty(true)
                        ->setLabel('Custom Username')
                        ->setFilters(array('StringTrim'))
                        ->setValidators(array(
                            new ConditionalNotEmpty(array("security_setting2"=>"1"))))
                        //fix//->setValue(Application_Model_Preference::GetLiveSteamMasterUsername())
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
                            new ConditionalNotEmpty(array("security_setting2"=>"1"))))
                        //fix//->setValue(Application_Model_Preference::GetLiveSteamMasterUsername())
                        ->setDecorators(array('ViewHelper'));
        $this->addElement($custom_password);
    }
}