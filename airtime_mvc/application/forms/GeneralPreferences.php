<?php

require_once 'customfilters/ImageSize.php';

class Application_Form_GeneralPreferences extends Zend_Form_SubForm
{

    public function init()
    {
        $maxLens = Application_Model_Show::getMaxLengths();
        $this->setEnctype(Zend_Form::ENCTYPE_MULTIPART);

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

        // Station Logo
        $stationLogoUpload = new Zend_Form_Element_File('stationLogo');
        $stationLogoUpload->setLabel(_('Station Logo:'))
            ->setDescription(_("Note: Anything larger than 600x600 will be resized."))
            ->setRequired(false)
            ->addValidator('Count', false, 1)
            ->addValidator('Extension', false, 'jpg,jpeg,png,gif')
            ->setMaxFileSize(1000000)
            ->addFilter('ImageSize');
        $stationLogoUpload->setAttrib('accept', 'image/*');
        $this->addElement($stationLogoUpload);

        $stationLogoRemove = new Zend_Form_Element_Button('stationLogoRemove');
        $stationLogoRemove->setLabel(_('Remove'));
        $stationLogoRemove->setAttrib('class', 'btn');
        $stationLogoRemove->setAttrib('id', 'logo-remove-btn');
        $stationLogoRemove->setAttrib('onclick', 'removeLogo();');
        $this->addElement($stationLogoRemove);

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
