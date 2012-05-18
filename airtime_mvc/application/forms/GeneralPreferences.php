<?php

class Application_Form_GeneralPreferences extends Zend_Form_SubForm
{
    private $isSaas;	
	
    public function init()
    {
        $isSaas = Application_Model_Preference::GetPlanLevel() == 'disabled'?false:true;
        $this->isSaas = $isSaas;
        Logging::log($this->isSaas==true?"true":"false"); 
        
        $this->setDecorators(array(
            array('ViewScript', array('viewScript' => 'form/preferences_general.phtml', "isSaas" => $isSaas))
        ));

        $defaultFade = Application_Model_Preference::GetDefaultFade();
        if($defaultFade == ""){
            $defaultFade = '0.500000';
        }

        //Station name
        $this->addElement('text', 'stationName', array(
            'class'      => 'input_text',
            'label'      => 'Station Name',
            'required'   => false,
            'filters'    => array('StringTrim'),
            'value' => Application_Model_Preference::GetValue("station_name"),
            'decorators' => array(
                'ViewHelper'
            )
        ));

        //Default station fade
        $this->addElement('text', 'stationDefaultFade', array(
            'class'      => 'input_text',
            'label'      => 'Default Fade (s):',
            'required'   => false,
            'filters'    => array('StringTrim'),
            'validators' => array(array('regex', false,
                array('/^[0-9]{1,2}(\.\d{1,6})?$/',
                'messages' => 'enter a time in seconds 0{.000000}'))),
            'value' => $defaultFade,
            'decorators' => array(
                'ViewHelper'
            )
        ));

        $third_party_api = new Zend_Form_Element_Radio('thirdPartyApi');
        $third_party_api->setLabel('Allow Remote Websites To Access "Schedule" Info?<br> (Enable this to make front-end widgets work.)');
        $third_party_api->setMultiOptions(array("Disabled",
                                            "Enabled"));
        $third_party_api->setValue(Application_Model_Preference::GetAllow3rdPartyApi());
        $third_party_api->setDecorators(array('ViewHelper'));
        $this->addElement($third_party_api);

        /* Form Element for setting the Timezone */
        $timezone = new Zend_Form_Element_Select("timezone");
        $timezone->setLabel("Timezone");
        $timezone->setMultiOptions($this->getTimezones());
        $timezone->setValue(Application_Model_Preference::GetTimezone());
        $timezone->setDecorators(array('ViewHelper'));
        $this->addElement($timezone);

        /* Form Element for setting which day is the start of the week */
        $week_start_day = new Zend_Form_Element_Select("weekStartDay");
        $week_start_day->setLabel("Week Starts On");
        $week_start_day->setMultiOptions($this->getWeekStartDays());
        $week_start_day->setValue(Application_Model_Preference::GetWeekStartDay());
        $week_start_day->setDecorators(array('ViewHelper'));
        $this->addElement($week_start_day);
        
        //enable system emails.
        $this->addElement('checkbox', 'enableSystemEmail', array(
            'label' => 'Enable System Emails (Password Reset)',
            'required' => false,
            'value' => Application_Model_Preference::GetEnableSystemEmail(),
            'decorators' => array(
                'ViewHelper'
            )
        ));
        
        // Add end date element
        $systemEmail = new Zend_Form_Element_Text('systemEmail');
        $systemEmail->class = 'input_text';
        $systemEmail->setRequired(false)
            ->setValue(Application_Model_Preference::GetSystemEmail())
            ->setLabel('Airtime System Email')
            ->setFilters(array('StringTrim'))
            ->setValidators(array(
                        'NotEmpty',
                        'EmailAddress'
            ))
            ->setDecorators(array('ViewHelper'));
                        
        $systemEmail->addValidator('Callback', true, array(
            'callback' => function($value, $context) {
                if ($context['enableSystemEmail']) {
                    return true;
                }
                return false;
            },
            'messages' => array(
                Zend_Validate_Callback::INVALID_VALUE => 'Email must be enabled'
            ),
        ));
                        
               
        $this->addElement($systemEmail);
    }

    private function getTimezones(){
        $regions = array(
            'Africa' => DateTimeZone::AFRICA,
            'America' => DateTimeZone::AMERICA,
            'Antarctica' => DateTimeZone::ANTARCTICA,
            'Arctic' => DateTimeZone::ARCTIC,
            'Asia' => DateTimeZone::ASIA,
            'Atlantic' => DateTimeZone::ATLANTIC,
            'Australia' => DateTimeZone::AUSTRALIA,
            'Europe' => DateTimeZone::EUROPE,
            'Indian' => DateTimeZone::INDIAN,
            'Pacific' => DateTimeZone::PACIFIC
        );

        $tzlist = array();

        foreach ($regions as $name => $mask){
            $ids = DateTimeZone::listIdentifiers($mask);
            foreach ($ids as $id){
                $tzlist[$id] = str_replace("_", " ", $id);
            }
        }

        return $tzlist;
    }


    private function getWeekStartDays() {
    	$days = array(
    		'Sunday',
    		'Monday',
		    'Tuesday',
		    'Wednesday',
		    'Thursday',
		    'Friday',
		    'Saturday'
    	);
    	return $days;
    }
}

