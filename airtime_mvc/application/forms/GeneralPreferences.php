<?php

// this is not getting loaded by autloading since it has a classname
// that makes it clash with how zf1 expects to load plugins.
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


        // add intro playlist select here
        $introPlaylistSelect = new Zend_Form_Element_Select("introPlaylistSelect");
        $introPlaylistSelect->setLabel(_("Intro Autoloading Playlist"));
        $introPlaylistSelect->setMultiOptions(Application_Model_Library::getPlaylistNames(true));
        $introPlaylistSelect->setValue(Application_Model_Preference::GetIntroPlaylist());
        $this->addElement($introPlaylistSelect);

        $outroPlaylistSelect = new Zend_Form_Element_Select("outroPlaylistSelect");
        $outroPlaylistSelect->setLabel(_("Outro Autoloading Playlist"));
        $outroPlaylistSelect->setMultiOptions(Application_Model_Library::getPlaylistNames(true));
        $outroPlaylistSelect->setValue(Application_Model_Preference::GetOutroPlaylist());
        $this->addElement($outroPlaylistSelect);



        $podcast_album_override = new Zend_Form_Element_Radio('podcastAlbumOverride');
        $podcast_album_override->setLabel(_('Podcast Metadata Override'));
        $podcast_album_override->setMultiOptions(array(
            _("Disabled"),
            _("Enabled"),
        ));
        $podcast_album_override->setValue(Application_Model_Preference::GetPodcastAlbumOverride());
        $podcast_album_override->setDescription(_('Enabling this means that podcast tracks will get their metadata set from the podcast feed values'));
        $podcast_album_override->setSeparator(' '); //No <br> between radio buttons
        $podcast_album_override->addDecorator('HtmlTag', array('tag' => 'dd',
            'id'=>"podcastAlbumOverride-element",
            'class' => 'radio-inline-list',
        ));
        $this->addElement($podcast_album_override);

        $podcast_auto_smartblock = new Zend_Form_Element_Radio('podcastAutoSmartblock');
        $podcast_auto_smartblock->setLabel(_('Podcast Automatic Smartblock and Playlist'));
        $podcast_auto_smartblock->setMultiOptions(array(
            _("Disabled"),
            _("Enabled"),
        ));
        $podcast_auto_smartblock->setValue(Application_Model_Preference::GetPodcastAutoSmartblock());
        $podcast_auto_smartblock->setDescription(_('Enabling this means that a smartblock and playlist matching the newest track of a 
        podcast will be created when a new podcast is added. This depends upon the Podcast Album Override to work.'));
        $podcast_auto_smartblock->setSeparator(' '); //No <br> between radio buttons
        $podcast_auto_smartblock->addDecorator('HtmlTag', array('tag' => 'dd',
            'id'=>"podcastAutoSmartblock-element",
            'class' => 'radio-inline-list',
        ));
        $this->addElement($podcast_auto_smartblock);

        //TODO add and insert Podcast Smartblock and Playlist autogenerate options

        $third_party_api = new Zend_Form_Element_Radio('thirdPartyApi');
        $third_party_api->setLabel(_('Public Airtime API'));
        $third_party_api->setDescription(_('Required for embeddable schedule widget.'));
        $third_party_api->setMultiOptions(array(
                                            _("Disabled"),
                                            _("Enabled"),
                                        ));
        $third_party_api->setValue(Application_Model_Preference::GetAllow3rdPartyApi());
        $third_party_api->setDescription(_('Enabling this feature will allow Airtime to provide schedule data
                                            to external widgets that can be embedded in your website.'));
        $third_party_api->setSeparator(' '); //No <br> between radio buttons
        //$third_party_api->addDecorator(new Zend_Form_Decorator_Label(array('tag' => 'dd', 'class' => 'radio-inline-list')));
        $third_party_api->addDecorator('HtmlTag', array('tag' => 'dd',
                                                        'id'=>"thirdPartyApi-element",
                                                        'class' => 'radio-inline-list',
                                        ));
        $this->addElement($third_party_api);

        $allowedCorsUrlsValue = Application_Model_Preference::GetAllowedCorsUrls();
        $allowedCorsUrls = new Zend_Form_Element_Textarea('allowedCorsUrls');
        $allowedCorsUrls->setLabel(_('Allowed CORS URLs'));
        $allowedCorsUrls->setDescription(_('Remote URLs that are allowed to access this LibreTime instance in a browser. One URL per line.'));
        $allowedCorsUrls->setValue($allowedCorsUrlsValue);
        $this->addElement($allowedCorsUrls);

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

        $radioPageLoginButton = new Zend_Form_Element_Checkbox("radioPageLoginButton");
        $radioPageLoginButton->setDecorators(array(
            'ViewHelper',
            'Errors',
            'Label'
        ));
        $displayRadioPageLoginButtonValue = Application_Model_Preference::getRadioPageDisplayLoginButton();
        if ($displayRadioPageLoginButtonValue == "") {
            $displayRadioPageLoginButtonValue = true;
        }
        $radioPageLoginButton->addDecorator('Label', array("class" => "enable-tunein"));
        $radioPageLoginButton->setLabel(_("Display login button on your Radio Page?"));
        $radioPageLoginButton->setValue($displayRadioPageLoginButtonValue);
        $this->addElement($radioPageLoginButton);
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
