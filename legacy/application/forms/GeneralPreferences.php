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
        $this->setDecorators([
            ['ViewScript', ['viewScript' => 'form/preferences_general.phtml']],
        ]);

        $defaultFadeIn = Application_Model_Preference::GetDefaultFadeIn();
        $defaultFadeOut = Application_Model_Preference::GetDefaultFadeOut();

        // Station name
        $this->addElement('text', 'stationName', [
            'class' => 'input_text',
            'label' => _('Station Name'),
            'required' => false,
            'filters' => ['StringTrim'],
            'value' => Application_Model_Preference::GetStationName(),
        ]);

        // Station description
        $stationDescription = new Zend_Form_Element_Textarea('stationDescription');
        $stationDescription->setLabel(_('Station Description'));
        $stationDescription->setValue(Application_Model_Preference::GetStationDescription());
        $stationDescription->setRequired(false);
        $stationDescription->setValidators([['StringLength', false, [0, $maxLens['description']]]]);
        $stationDescription->setAttrib('rows', 4);
        $this->addElement($stationDescription);

        // Station Logo
        $stationLogoUpload = new Zend_Form_Element_File('stationLogo');
        $stationLogoUpload->setLabel(_('Station Logo:'))
            ->setDescription(_('Note: Anything larger than 600x600 will be resized.'))
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

        // Default station crossfade duration
        $this->addElement('text', 'stationDefaultCrossfadeDuration', [
            'class' => 'input_text',
            'label' => _('Default Crossfade Duration (s):'),
            'required' => true,
            'filters' => ['StringTrim'],
            'validators' => [
                $rangeValidator,
                $notEmptyValidator,
                ['regex', false, ['/^[0-9]+(\.\d+)?$/', 'messages' => _('Please enter a time in seconds (eg. 0.5)')]],
            ],
            'value' => Application_Model_Preference::GetDefaultCrossfadeDuration(),
        ]);

        // Default station fade in
        $this->addElement('text', 'stationDefaultFadeIn', [
            'class' => 'input_text',
            'label' => _('Default Fade In (s):'),
            'required' => true,
            'filters' => ['StringTrim'],
            'validators' => [
                $rangeValidator,
                $notEmptyValidator,
                ['regex', false, ['/^[0-9]+(\.\d+)?$/', 'messages' => _('Please enter a time in seconds (eg. 0.5)')]],
            ],
            'value' => $defaultFadeIn,
        ]);

        // Default station fade out
        $this->addElement('text', 'stationDefaultFadeOut', [
            'class' => 'input_text',
            'label' => _('Default Fade Out (s):'),
            'required' => true,
            'filters' => ['StringTrim'],
            'validators' => [
                $rangeValidator,
                $notEmptyValidator,
                ['regex', false, ['/^[0-9]+(\.\d+)?$/', 'messages' => _('Please enter a time in seconds (eg. 0.5)')]],
            ],
            'value' => $defaultFadeOut,
        ]);

        $tracktypeDefault = new Zend_Form_Element_Select('tracktypeDefault');
        $tracktypeDefault->setLabel(_('Track Type Upload Default'));
        $tracktypeDefault->setMultiOptions(Application_Model_Library::getTracktypes());
        $tracktypeDefault->setValue(Application_Model_Preference::GetTrackTypeDefault());
        $this->addElement($tracktypeDefault);

        // add intro playlist select here
        $introPlaylistSelect = new Zend_Form_Element_Select('introPlaylistSelect');
        $introPlaylistSelect->setLabel(_('Intro Autoloading Playlist'));
        $introPlaylistSelect->setMultiOptions(Application_Model_Library::getPlaylistNames(true));
        $introPlaylistSelect->setValue(Application_Model_Preference::GetIntroPlaylist());
        $this->addElement($introPlaylistSelect);

        $outroPlaylistSelect = new Zend_Form_Element_Select('outroPlaylistSelect');
        $outroPlaylistSelect->setLabel(_('Outro Autoloading Playlist'));
        $outroPlaylistSelect->setMultiOptions(Application_Model_Library::getPlaylistNames(true));
        $outroPlaylistSelect->setValue(Application_Model_Preference::GetOutroPlaylist());
        $this->addElement($outroPlaylistSelect);

        $podcast_album_override = new Zend_Form_Element_Radio('podcastAlbumOverride');
        $podcast_album_override->setLabel(_('Overwrite Podcast Episode Metatags'));
        $podcast_album_override->setMultiOptions([
            _('Disabled'),
            _('Enabled'),
        ]);
        $podcast_album_override->setValue(Application_Model_Preference::GetPodcastAlbumOverride());
        $podcast_album_override->setDescription(_('Enabling this feature will cause podcast episode tracks to have their Artist, Title, and Album metatags set from podcast feed values. Note that enabling this feature is recommended in order to ensure reliable scheduling of episodes via smartblocks.'));
        $podcast_album_override->setSeparator(' '); // No <br> between radio buttons
        $podcast_album_override->addDecorator('HtmlTag', [
            'tag' => 'dd',
            'id' => 'podcastAlbumOverride-element',
            'class' => 'radio-inline-list',
        ]);
        $this->addElement($podcast_album_override);

        $podcast_auto_smartblock = new Zend_Form_Element_Radio('podcastAutoSmartblock');
        $podcast_auto_smartblock->setLabel(_('Generate a smartblock and a playlist upon creation of a new podcast'));
        $podcast_auto_smartblock->setMultiOptions([
            _('Disabled'),
            _('Enabled'),
        ]);
        $podcast_auto_smartblock->setValue(Application_Model_Preference::GetPodcastAutoSmartblock());
        $podcast_auto_smartblock->setDescription(_('If this option is enabled, a new smartblock and playlist matching the newest track of a podcast will be generated immediately upon creation of a new podcast. Note that the "Overwrite Podcast Episode Metatags" feature must also be enabled in order for smartblocks to reliably find episodes.'));
        $podcast_auto_smartblock->setSeparator(' '); // No <br> between radio buttons
        $podcast_auto_smartblock->addDecorator('HtmlTag', [
            'tag' => 'dd',
            'id' => 'podcastAutoSmartblock-element',
            'class' => 'radio-inline-list',
        ]);
        $this->addElement($podcast_auto_smartblock);

        // TODO add and insert Podcast Smartblock and Playlist autogenerate options

        $third_party_api = new Zend_Form_Element_Radio('thirdPartyApi');
        $third_party_api->setLabel(_('Public LibreTime API'));
        $third_party_api->setDescription(_('Required for embeddable schedule widget.'));
        $third_party_api->setMultiOptions([
            _('Disabled'),
            _('Enabled'),
        ]);
        $third_party_api->setValue(Application_Model_Preference::GetAllow3rdPartyApi());
        $third_party_api->setDescription(_('Enabling this feature will allow LibreTime to provide schedule data
                                            to external widgets that can be embedded in your website.'));
        $third_party_api->setSeparator(' '); // No <br> between radio buttons
        // $third_party_api->addDecorator(new Zend_Form_Decorator_Label(array('tag' => 'dd', 'class' => 'radio-inline-list')));
        $third_party_api->addDecorator('HtmlTag', [
            'tag' => 'dd',
            'id' => 'thirdPartyApi-element',
            'class' => 'radio-inline-list',
        ]);
        $this->addElement($third_party_api);

        $locale = new Zend_Form_Element_Select('locale');
        $locale->setLabel(_('Default Language'));
        $locale->setMultiOptions(Application_Model_Locale::getLocales());
        $locale->setValue(Application_Model_Preference::GetDefaultLocale());
        $this->addElement($locale);

        // Form Element for setting the Timezone
        $timezone = new Zend_Form_Element_Select('timezone');
        $timezone->setLabel(_('Station Timezone'));
        $timezone->setAttrib('disabled', 'true');
        $timezone->setMultiOptions(Application_Common_Timezone::getTimezones());
        $timezone->setValue(Application_Model_Preference::GetDefaultTimezone());
        $this->addElement($timezone);

        // Form Element for setting which day is the start of the week
        $week_start_day = new Zend_Form_Element_Select('weekStartDay');
        $week_start_day->setLabel(_('Week Starts On'));
        $week_start_day->setMultiOptions($this->getWeekStartDays());
        $week_start_day->setValue(Application_Model_Preference::GetWeekStartDay());
        $this->addElement($week_start_day);

        $radioPageLoginButton = new Zend_Form_Element_Checkbox('radioPageLoginButton');
        $radioPageLoginButton->setDecorators([
            'ViewHelper',
            'Errors',
            'Label',
        ]);
        $displayRadioPageLoginButtonValue = Application_Model_Preference::getRadioPageDisplayLoginButton();
        if ($displayRadioPageLoginButtonValue == '') {
            $displayRadioPageLoginButtonValue = true;
        }
        $radioPageLoginButton->addDecorator('Label', ['class' => 'enable-tunein']);
        $radioPageLoginButton->setLabel(_('Display login button on your Radio Page?'));
        $radioPageLoginButton->setValue($displayRadioPageLoginButtonValue);
        $this->addElement($radioPageLoginButton);

        // add a checkbox for completely disabling the radio page
        $radioPageDisabled = new Zend_Form_Element_Checkbox('radioPageDisabled');
        $radioPageDisabled->setDecorators([
            'ViewHelper',
            'Errors',
            'Label',
        ]);
        $radioPageDisabledValue = Application_Model_Preference::getRadioPageDisabled();
        if ($radioPageDisabledValue == '') {
            $radioPageDisabledValue = false;
        }
        $radioPageDisabled->addDecorator('Label', ['class' => 'enable-tunein']);
        $radioPageDisabled->setLabel(_('Disable your public Radio Page and redirect to login?'));
        $radioPageDisabled->setValue($radioPageDisabledValue);
        $this->addElement($radioPageDisabled);

        $feature_preview_mode = new Zend_Form_Element_Radio('featurePreviewMode');
        $feature_preview_mode->setLabel(_('Feature Previews'));
        $feature_preview_mode->setMultiOptions([
            _('Disabled'),
            _('Enabled'),
        ]);
        $feature_preview_mode->setValue(Application_Model_Preference::GetFeaturePreviewMode());
        $feature_preview_mode->setDescription(_('Enable this to opt-in to test new features.'));
        $feature_preview_mode->setSeparator(' '); // No <br> between radio buttons
        $feature_preview_mode->addDecorator('HtmlTag', [
            'tag' => 'dd',
            'id' => 'featurePreviewMode-element',
            'class' => 'radio-inline-list',
        ]);
        $this->addElement($feature_preview_mode);
    }

    private function getWeekStartDays()
    {
        return [
            _('Sunday'),
            _('Monday'),
            _('Tuesday'),
            _('Wednesday'),
            _('Thursday'),
            _('Friday'),
            _('Saturday'),
        ];
    }
}
