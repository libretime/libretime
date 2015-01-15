<?php

class Application_Form_GeneralPreferences extends Zend_Form_SubForm
{

    public function init()
    {
        $maxLens = Application_Model_Show::getMaxLengths();

        $notEmptyValidator = Application_Form_Helper_ValidationTypes::overrideNotEmptyValidator();
        $rangeValidator = Application_Form_Helper_ValidationTypes::overrideBetweenValidator(0, 59.9);
        $this->setDecorators(array(
            array('ViewScript', array('viewScript' => 'form/preferences_general.phtml'))
        ));

        $defaultFadeIn = Application_Model_Preference::GetDefaultFadeIn();
        $defaultFadeOut = Application_Model_Preference::GetDefaultFadeOut();

        //Station name
        $this->addElement('text', 'stationName', array(
            'class' => 'input_text',
            'label' => _('Station Name'),
            'required' => false,
            'filters' => array('StringTrim'),
            'value' => Application_Model_Preference::GetStationName(),
        ));

        // Station description
        $stationDescription = new Zend_Form_Element_Textarea("stationDescription");
        $stationDescription->setLabel(_('Station Description'));
        $stationDescription->setValue(Application_Model_Preference::GetStationDescription());
        $stationDescription->setRequired(false);
        $stationDescription->setValidators(array(array('StringLength', false, array(0, $maxLens['description']))));
        $stationDescription->setAttrib('rows', 4);
        $this->addElement($stationDescription);

        //Default station crossfade duration
        $this->addElement('text', 'stationDefaultCrossfadeDuration', array(
            'class' => 'input_text',
            'label' => _('Default Crossfade Duration (s):'),
            'required' => true,
            'filters' => array('StringTrim'),
            'validators' => array(
                    $rangeValidator,
                    $notEmptyValidator,
                    array('regex', false, array('/^[0-9]+(\.\d+)?$/', 'messages' => _('Please enter a time in seconds (eg. 0.5)')))
            ),
            'value' => Application_Model_Preference::GetDefaultCrossfadeDuration(),
        ));

        //Default station fade in
        $this->addElement('text', 'stationDefaultFadeIn', array(
            'class' => 'input_text',
            'label' => _('Default Fade In (s):'),
            'required' => true,
            'filters' => array('StringTrim'),
            'validators' => array(
                $rangeValidator,
                $notEmptyValidator,
                array('regex', false, array('/^[0-9]+(\.\d+)?$/', 'messages' => _('Please enter a time in seconds (eg. 0.5)')))
            ),
            'value' => $defaultFadeIn,
        ));

        //Default station fade out
        $this->addElement('text', 'stationDefaultFadeOut', array(
            'class' => 'input_text',
            'label' => _('Default Fade Out (s):'),
            'required' => true,
            'filters' => array('StringTrim'),
            'validators' => array(
                $rangeValidator,
                $notEmptyValidator,
                array('regex', false, array('/^[0-9]+(\.\d+)?$/', 'messages' => _('Please enter a time in seconds (eg. 0.5)')))
            ),
            'value' => $defaultFadeOut,
        ));

        $third_party_api = new Zend_Form_Element_Radio('thirdPartyApi');
        $third_party_api->setLabel(_('Public Airtime API'));
        $third_party_api->setDescription(_('Required for embeddable schedule widget.'));
        $third_party_api->setMultiOptions(array(
                                            _("Disabled"),
                                            _("Enabled"),
                                        ));
        $third_party_api->setValue(Application_Model_Preference::GetAllow3rdPartyApi());
        $third_party_api->setDescription(_('Enabling this feature will allow Airtime to provide schedule data
                                            to external widgets that can be embedded in your website. Enable this
                                            feature to reveal the embeddable code.'));
        $third_party_api->setSeparator(' '); //No <br> between radio buttons
        //$third_party_api->addDecorator(new Zend_Form_Decorator_Label(array('tag' => 'dd', 'class' => 'radio-inline-list')));
        $third_party_api->addDecorator('HtmlTag', array('tag' => 'dd',
                                                        'id'=>"thirdPartyApi-element",
                                                        'class' => 'radio-inline-list',
                                        ));
        $this->addElement($third_party_api);

        // Add the description element
        $this->addElement('textarea', 'widgetCode', array(
            'label' => 'Javascript Code:',
            'required' => false,
            'readonly' => true,
            'style' => 'font-family: Consolas, "Liberation Mono", Courier,
                monospace;',
            'value' => self::getWidgetCode(),
        ));

        $locale = new Zend_Form_Element_Select("locale");
        $locale->setLabel(_("Default Language"));
        $locale->setMultiOptions(Application_Model_Locale::getLocales());
        $locale->setValue(Application_Model_Preference::GetDefaultLocale());
        $this->addElement($locale);

        /* Form Element for setting the Timezone */
        $timezone = new Zend_Form_Element_Select("timezone");
        $timezone->setLabel(_("Station Timezone"));
        $timezone->setMultiOptions(Application_Common_Timezone::getTimezones());
        $timezone->setValue(Application_Model_Preference::GetDefaultTimezone());
        $this->addElement($timezone);

        /* Form Element for setting which day is the start of the week */
        $week_start_day = new Zend_Form_Element_Select("weekStartDay");
        $week_start_day->setLabel(_("Week Starts On"));
        $week_start_day->setMultiOptions($this->getWeekStartDays());
        $week_start_day->setValue(Application_Model_Preference::GetWeekStartDay());
        $this->addElement($week_start_day);
    }

    private static function getWidgetCode() {
        
        $host = $_SERVER['SERVER_NAME'];
        $code = <<<CODE
<!-- READ THESE INSTRUCTIONS CAREFULLY:
    Step 1 of 2: Paste these next 4 lines in the <head> section of your HTML page -->
<script src="https://$host/widgets/js/jquery-1.6.1.min.js" type="text/javascript"></script>
<script src="https://$host/widgets/js/jquery-ui-1.8.10.custom.min.js" type="text/javascript"></script>
<script src="https://$host/widgets/js/jquery.showinfo.js" type="text/javascript"></script>
<link rel="stylesheet" href="https://$host/widgets/css/airtime-widgets.css"></link>

<!-- Step 2 of 2: Paste these remaining lines in the <body> section of your HTML page -->
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
    var d = new Date().getDay();
    $('#scheduleTabs').tabs({selected: d === 0 ? 6 : d-1, fx: { opacity: 'toggle' }});               
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
