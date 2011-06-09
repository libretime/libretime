<?php

class Application_Form_SupportPreferences extends Zend_Form_SubForm
{

    public function init()
    {
        $this->setDecorators(array(
            array('ViewScript', array('viewScript' => 'form/preferences_support.phtml'))
        ));

        // Phone number
        $this->addElement('text', 'Phone', array(
            'class'      => 'input_text',
            'label'      => 'Phone:',
            'required'   => false,
            'filters'    => array('StringTrim'),
            'value' => Application_Model_Preference::GetPhone(),
            'decorators' => array(
                'ViewHelper'
            )
        ));

        //Email
        $this->addElement('text', 'Email', array(
            'class'      => 'input_text',
            'label'      => 'Email:',
            'required'   => false,
            'filters'    => array('StringTrim'),
            'value' => Application_Model_Preference::GetEmail(),
            'decorators' => array(
                'ViewHelper'
            )
        ));

         // Station Web Site
        $this->addElement('text', 'StationWebSite', array(
            'label'      => 'Station Web Site:',
            'required'   => false,
            'class'      => 'input_text',
            'value' => Application_Model_Preference::GetStationWebSite(),
            'decorators' => array(
                'ViewHelper'
            )
		));

        //enable support feedback
        $this->addElement('checkbox', 'SupportFeedback', array(
            'label'      => 'Support feedback enabled',
            'required'   => false,
            'value' => Application_Model_Preference::GetSupportFeedback(),
            'decorators' => array(
                'ViewHelper'
            )
		));
		
		//add register button if not registered
		if( !Application_Model_Preference::GetRegistered() ){
	       $this->addElement('submit', 'Register', array(
	            'class'    => 'ui-button ui-state-default',
	            'ignore'   => true,
	            'label'    => 'Register',
	            'decorators' => array(
	                'ViewHelper'
	            )
	        ));      
		}
    }


}

