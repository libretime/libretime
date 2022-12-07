<?php

declare(strict_types=1);

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
        $this->setDecorators([
            ['ViewScript', ['viewScript' => 'preference/stream-setting.phtml']],
        ]);

        $setting = $this->setting;

        $stream_format = new Zend_Form_Element_Radio('streamFormat');
        $stream_format
            ->setLabel(_('Stream Label:'))
            ->setMultiOptions([
                _('Artist - Title'),
                _('Show - Artist - Title'),
                _('Station name - Show name'),
            ])
            ->setValue(Application_Model_Preference::GetStreamLabelFormat())
            ->setDecorators(['ViewHelper']);
        $this->addElement($stream_format);

        $offAirMeta = new Zend_Form_Element_Text('offAirMeta');
        $offAirMeta
            ->setLabel(_('Off Air Metadata'))
            ->setValue(Application_Model_Preference::getOffAirMeta())
            ->setDecorators(['ViewHelper']);
        $this->addElement($offAirMeta);

        $enable_replay_gain = new Zend_Form_Element_Checkbox('enableReplayGain');
        $enable_replay_gain
            ->setLabel(_('Enable Replay Gain'))
            ->setValue(Application_Model_Preference::GetEnableReplayGain())
            ->setDecorators(['ViewHelper']);
        $this->addElement($enable_replay_gain);

        $replay_gain = new Zend_Form_Element_Hidden('replayGainModifier');
        $replay_gain
            ->setLabel(_('Replay Gain Modifier'))
            ->setValue(Application_Model_Preference::getReplayGainModifier())
            ->setAttribs(['style' => 'border: 0; color: #f6931f; font-weight: bold;'])
            ->setDecorators(['ViewHelper']);
        $this->addElement($replay_gain);

        $output_sound_device = new Zend_Form_Element_Checkbox('output_sound_device');
        $output_sound_device
            ->setLabel(_('Hardware Audio Output:'))
            ->setAttrib('readonly', true)
            ->setRequired(false)
            ->setValue(($setting['output_sound_device'] == 'true') ? 1 : 0)
            ->setDecorators(['ViewHelper']);
        $this->addElement($output_sound_device);

        $output_sound_device_type = new Zend_Form_Element_Select('output_sound_device_type');
        $output_sound_device_type
            ->setLabel(_('Output Type'))
            ->setAttrib('readonly', true)
            ->setValue(isset($setting['output_sound_device_type']) ? $setting['output_sound_device_type'] : 0)
            ->setDecorators(['ViewHelper']);
        $this->addElement($output_sound_device_type);
    }
}
