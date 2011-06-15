<?php

class Application_Form_RegisterAirtime extends Zend_Form
{

    public function init()
    {
        $this->setAction('/Preference/registersubmit');
        $this->setMethod('post');
        
		$country_list = Application_Model_Preference::GetCountryList();
		
        $this->setDecorators(array(
            array('ViewScript', array('viewScript' => 'form/register-dialog.phtml')),
            array('File', array('viewScript' => 'form/register-dialog.phtml', 'placement' => false)))
        );
        
        // Station Name
        $stnName = new Zend_Form_Element_Text("stnName");
        $stnName->setLabel("Station Name:")
                ->setRequired(true)
                ->setValue(Application_Model_Preference::GetStationName())
                ->setDecorators(array('ViewHelper'));
        $this->addElement($stnName);
        
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

		// county list dropdown
		$this->addElement('select', 'Country', array(
			'label'		=> 'Country:',
			'required'	=> false,
			'value'		=> Application_Model_Preference::GetStationCountry(),
			'multiOptions'	=> $country_list,
			'decorators' => array(
                'ViewHelper'
            )
		));
		
		// Station city
        $this->addElement('text', 'City', array(
            'label'      => 'City:',
            'required'   => false,
            'class'      => 'input_text',
            'value' => Application_Model_Preference::GetStationCity(),
            'decorators' => array(
                'ViewHelper'
            )
		));
		
		// Station Description
		$description = new Zend_Form_Element_Textarea('Description');
		$description->class = 'input_text_area';
		$description->setLabel('Station Description:')
					->setRequired(false)
					->setValue(Application_Model_Preference::GetStationDescription())
					->setDecorators(array('ViewHelper'))
					->setAttrib('ROWS','2')
					->setAttrib('COLS','58');
		$this->addElement($description);
		
		// Station Logo
		$upload = new Zend_Form_Element_File('Logo');
		$upload->setLabel('Station Logo:')
				->setRequired(false)
				->setDecorators(array('File'))
				->addValidator('Count', false, 1)
				->addValidator('Extension', false, 'jpg,png,gif')
				->addValidator('ImageSize', false, array(
					'minwidth'	=> 180,
					'minheight'	=> 180,
					'maxwidth'	=> 1000,
					'maxheight'	=>	1000));
		$this->addElement($upload);
		
        //enable support feedback
        $this->addElement('checkbox', 'SupportFeedback', array(
            'label'      => 'Send support feedback',
            'required'   => false,
            'value' => Application_Model_Preference::GetSupportFeedback(),
            'decorators' => array(
                'ViewHelper'
            )
		));

		// checkbox for publicise
		$checkboxPublicise = new Zend_Form_Element_Checkbox("Publicise");
        $checkboxPublicise->setLabel('Publicise my station on Sourcefabric.org')
                          ->setRequired(false)
                          ->setDecorators(array('ViewHelper'))
                          ->setValue(Application_Model_Preference::GetPublicise());
        if(!Application_Model_Preference::GetSupportFeedback()){
            $checkboxPublicise->setAttrib("disabled", "disabled");
        }
        $this->addElement($checkboxPublicise);
		
		// text area for sending detail
        $this->addElement('textarea', 'SendInfo', array(
        	'class'		=> 'sending_textarea',
        	'required'   => false,
            'filters'    => array('StringTrim'),
        	'readonly'	=> true,
			'rows'		=> 24,        
            'value' => Application_Model_Preference::GetSystemInfo(),
            'decorators' => array(
                'ViewHelper'
            )
        ));
    }
}

