<?php

class Application_Form_GeneralPreferences extends Zend_Form_SubForm
{

    public function init()
    {
        $notEmptyValidator = Application_Form_Helper_ValidationTypes::overrideNotEmptyValidator();
        $rangeValidator = Application_Form_Helper_ValidationTypes::overrideBetweenValidator(0, 59.9);
        $this->setDecorators(array(
            array('ViewScript', array('viewScript' => 'form/preferences_general.phtml'))
        ));

        $defaultFadeIn = Application_Model_Preference::GetDefaultFadeIn();
        $defaultFadeOut = Application_Model_Preference::GetDefaultFadeOut();
       
        //Station name
        $this->addElement('text', 'stationName', array(
            'class'      => 'input_text',
            'label'      => _('Station Name'),
            'required'   => false,
            'filters'    => array('StringTrim'),
            'value' => Application_Model_Preference::GetStationName(),
            'decorators' => array(
                'ViewHelper'
            )
        ));
        
        //Default station fade in
        $this->addElement('text', 'stationDefaultCrossfadeDuration', array(
        		'class'      => 'input_text',
        		'label'      => _('Default Crossfade Duration (s):'),
        		'required'   => true,
        		'filters'    => array('StringTrim'),
        		'validators' => array(
        				array(
        						$rangeValidator,
        						$notEmptyValidator,
        						'regex', false, array('/^[0-9]{1,2}(\.\d{1})?$/', 'messages' => _('enter a time in seconds 0{.0}'))
        				)
        		),
        		'value' => Application_Model_Preference::GetDefaultCrossfadeDuration(),
        		'decorators' => array(
        				'ViewHelper'
        		)
        ));

        //Default station fade in
        $this->addElement('text', 'stationDefaultFadeIn', array(
            'class'      => 'input_text',
            'label'      => _('Default Fade In (s):'),
            'required'   => true,
            'filters'    => array('StringTrim'),
            'validators' => array(
                array(
                    $rangeValidator,
                    $notEmptyValidator,
                    'regex', false, array('/^[0-9]{1,2}(\.\d{1})?$/', 'messages' => _('enter a time in seconds 0{.0}'))
                )
            ),
            'value' => $defaultFadeIn,
            'decorators' => array(
                'ViewHelper'
            )
        ));
        
        //Default station fade out
        $this->addElement('text', 'stationDefaultFadeOut', array(
        		'class'      => 'input_text',
        		'label'      => _('Default Fade Out (s):'),
        		'required'   => true,
        		'filters'    => array('StringTrim'),
        		'validators' => array(
        				array(
        						$rangeValidator,
        						$notEmptyValidator,
        						'regex', false, array('/^[0-9]{1,2}(\.\d{1})?$/', 'messages' => _('enter a time in seconds 0{.0}'))
        				)
        		),
        		'value' => $defaultFadeOut,
        		'decorators' => array(
        				'ViewHelper'
        		)
        ));

        $third_party_api = new Zend_Form_Element_Radio('thirdPartyApi');
        $third_party_api->setLabel(
            sprintf(_('Allow Remote Websites To Access "Schedule" Info?%s (Enable this to make front-end widgets work.)'), '<br>'));
        $third_party_api->setMultiOptions(array(_("Disabled"),
                                            _("Enabled")));
        $third_party_api->setValue(Application_Model_Preference::GetAllow3rdPartyApi());
        $third_party_api->setDecorators(array('ViewHelper'));
        $this->addElement($third_party_api);
        //
         // Add the description element
        $this->addElement('textarea', 'widgetCode', array(
            'label'      => 'Javascript Code:',
            'required'   => false,
            'readonly'   => true,
            'style'      => 'font-family: Consolas, "Liberation Mono", Courier, 
                monospace;',
            'class'      => 'input_text_area',
            'value' => self::getWidgetCode(), //$_SERVER["SERVER_NAME"],
            'decorators' => array(
                'ViewHelper'
            )
        ));

        $locale = new Zend_Form_Element_Select("locale");
        $locale->setLabel(_("Default Interface Language"));
        $locale->setMultiOptions(Application_Model_Locale::getLocales());
        $locale->setValue(Application_Model_Preference::GetDefaultLocale());
        $locale->setDecorators(array('ViewHelper'));
        $this->addElement($locale);

        /* Form Element for setting the Timezone */
        $timezone = new Zend_Form_Element_Select("timezone");
        $timezone->setLabel(_("Station Timezone"));
        $timezone->setMultiOptions(Application_Common_Timezone::getTimezones());
        $timezone->setValue(Application_Model_Preference::GetDefaultTimezone());
        $timezone->setDecorators(array('ViewHelper'));
        $this->addElement($timezone);

        /* Form Element for setting which day is the start of the week */
        $week_start_day = new Zend_Form_Element_Select("weekStartDay");
        $week_start_day->setLabel(_("Week Starts On"));
        $week_start_day->setMultiOptions($this->getWeekStartDays());
        $week_start_day->setValue(Application_Model_Preference::GetWeekStartDay());
        $week_start_day->setDecorators(array('ViewHelper'));
        $this->addElement($week_start_day);
    }

    private static function getWidgetCode() {
        
        $host = $_SERVER['SERVER_NAME'];
        $code = <<<CODE
<script src="http://$host/widgets/js/jquery-1.6.1.min.js" type="text/javascript"></script>
<script src="http://$host/widgets/js/jquery-ui-1.8.10.custom.min.js" type="text/javascript"></script>
<script src="http://$host/widgets/js/jquery.showinfo.js" type="text/javascript"></script>

<div id="headerLiveHolder" style="border: 1px solid #999999; padding: 10px;"></div>
<div id="onAirToday"></div>
<div id="scheduleTabs"></div>

<script type="text/javascript">
$(document).ready(function() {
    $("#headerLiveHolder").airtimeLiveInfo({
        sourceDomain: "http://$host",
        updatePeriod: 20 //seconds
    });

    $("#onAirToday").airtimeShowSchedule({
        sourceDomain: "http://$host",
        updatePeriod: 5, //seconds
        showLimit: 10
    });

    $("#scheduleTabs").airtimeWeekSchedule({
        sourceDomain:"http://$host",
        updatePeriod: 600 //seconds
    });
});
</script>
CODE;

        return $code;
    }

    private function getWeekStartDays()
    {
        $days = array(
            _('Sunday'),
            _('Monday'),
            _('Tuesday'),
            _('Wednesday'),
            _('Thursday'),
            _('Friday'),
            _('Saturday')
        );

        return $days;
    }
}
