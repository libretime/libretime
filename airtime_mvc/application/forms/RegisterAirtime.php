<?php

class Application_Form_RegisterAirtime extends Zend_Form_SubForm
{

    public function init()
    {
        $this->setDecorators(array(
            array('ViewScript', array('viewScript' => 'form/register-dialog.phtml'))
        ));
        
        // checkbox for publicise
        $this->addElement('checkbox', 'Publicise', array(
            'label'      => 'Publicise my station on Sourcefabric.org',
            'required'   => false,
            'value' => Application_Model_Preference::GetSupportFeedback(),
            'decorators' => array(
                'ViewHelper'
            )
		));

        // Station Name
        $this->addElement('text', 'StationName', array(
            'label'      => 'Station Name:',
            'required'   => false,
            'class'      => 'input_text',
            'value' => Application_Model_Preference::GetStationName(),
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
        
        // text area for sending detail
        $this->addElement('textarea', 'SendInfo', array(
        	'class'		=> 'textarea',
        	'required'   => false,
            'filters'    => array('StringTrim'),
        	'cols'		=> 48,
        	'rows'		=> 20,
        	'readonly'	=> true,
            'value' => Application_Model_Preference::GetSystemInfo(),
            'decorators' => array(
                'ViewHelper'
            )
        ));
    }
}

