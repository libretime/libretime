<?php

class Application_Form_StreamSetting extends Zend_Form
{
    private $setting;

    public function init()
    {

    }

    public function setSetting($setting)
    {
        $this->setting = $setting;
    }

    public function startFrom()
    {
        $this->setDecorators(array(
                                 array('ViewScript', array('viewScript' => 'preference/stream-setting.phtml'))
                             ));

        $setting = $this->setting;

        $icecast_vorbis_metadata = new Zend_Form_Element_Checkbox('icecast_vorbis_metadata');
        $icecast_vorbis_metadata->setLabel(_('Icecast Vorbis Metadata'))
                                ->setRequired(false)
                                ->setValue(($setting['icecast_vorbis_metadata'] == "true")?1:0)
                                ->setDecorators(array('ViewHelper'));
        if (Application_Model_Preference::GetEnableStreamConf() == "false") {
            $icecast_vorbis_metadata->setAttrib("readonly", true);
        }
        $this->addElement($icecast_vorbis_metadata);

        $stream_format = new Zend_Form_Element_Radio('streamFormat');
        $stream_format->setLabel(_('Stream Label:'));
        $stream_format->setMultiOptions(array(_("Artist - Title"),
                                            _("Show - Artist - Title"),
                                            _("Station name - Show name")));
        $stream_format->setValue(Application_Model_Preference::GetStreamLabelFormat());
        $stream_format->setDecorators(array('ViewHelper'));
        $this->addElement($stream_format);
        
        $offAirMeta = new Zend_Form_Element_Text('offAirMeta');
        $offAirMeta->setLabel(_('Off Air Metadata'))
                   ->setValue(Application_Model_StreamSetting::getOffAirMeta())
                   ->setDecorators(array('ViewHelper'));
        $this->addElement($offAirMeta);
        
        $enable_replay_gain = new Zend_Form_Element_Checkbox("enableReplayGain");
        $enable_replay_gain->setLabel(_("Enable Replay Gain"))
                           ->setValue(Application_Model_Preference::GetEnableReplayGain())
                           ->setDecorators(array('ViewHelper'));
        $this->addElement($enable_replay_gain);
        
        $replay_gain = new Zend_Form_Element_Hidden("replayGainModifier");
        $replay_gain->setLabel(_("Replay Gain Modifier"))
        ->setValue(Application_Model_Preference::getReplayGainModifier())
        ->setAttribs(array('style' => "border: 0; color: #f6931f; font-weight: bold;"))
        ->setDecorators(array('ViewHelper'));
        $this->addElement($replay_gain);

        $custom = Application_Model_Preference::getUsingCustomStreamSettings();
        $customSettings = new Zend_Form_Element_Radio('customStreamSettings');
        $customSettings->setLabel(_('Streaming Server:'));
        $customSettings->setMultiOptions(array(_("Airtime Pro Streaming"), _("Custom / 3rd Party Streaming")));
        $customSettings->setValue(!empty($custom) ? $custom : 0);
        $this->addElement($customSettings);
    }

    public function isValid($data)
    {
        if (isset($data['output_sound_device'])) {
            $d = array();
            $d["output_sound_device"] = $data['output_sound_device'];
            $d["icecast_vorbis_metadata"] = $data['icecast_vorbis_metadata'];
            if (isset($data['output_sound_device_type'])) {
                $d["output_sound_device_type"] = $data['output_sound_device_type'];
            }
            $d["streamFormat"] = $data['streamFormat'];
            $this->populate($d);
        }
        $isValid = parent::isValid($data);

        return $isValid;
    }
}
