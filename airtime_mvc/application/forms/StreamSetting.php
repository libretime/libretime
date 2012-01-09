<?php

class Application_Form_StreamSetting extends Zend_Form
{
    private $setting;

    public function init()
    {

    }

    public function setSetting($setting){
        $this->setting = $setting;
    }

    public function startFrom() {
        $setting = $this->setting;
        if(Application_Model_Preference::GetPlanLevel() == 'disabled'){
            $output_sound_device = new Zend_Form_Element_Checkbox('output_sound_device');
            $output_sound_device->setLabel('Hardware Audio Output')
                                ->setRequired(false)
                                ->setValue(($setting['output_sound_device'] == "true")?1:0)
                                ->setDecorators(array('ViewHelper'));
            if (Application_Model_Preference::GetEnableStreamConf() == "false"){
                $output_sound_device->setAttrib("readonly", true);
            }
            $this->addElement($output_sound_device);

            $output_types = array("ALSA"=>"ALSA", "AO"=>"AO", "OSS"=>"OSS", "Portaudio"=>"Portaudio", "Pulseaudio"=>"Pulseaudio");
            $output_type = new Zend_Form_Element_Select('output_sound_device_type');
            $output_type->setLabel("Output Type")
                    ->setMultiOptions($output_types)
                    ->setValue($setting['output_sound_device_type'])
                    ->setDecorators(array('ViewHelper'));
            if($setting['output_sound_device'] != "true"){
                $output_type->setAttrib("disabled", "disabled");
            }
            $this->addElement($output_type);
        }

        # tooltip
        $description = 'VLC and mplayer have a serious bug when playing an OGG/VORBIS
                    stream that has metadata information enabled (stream metadata is the
                    track title, show name, etc displayed in the audio player): they will
                    disconnect from the stream after every song if this option is enabled.
                    If your listeners do not require support for these audio players,
                    then you should enable this option.';

        $icecast_vorbis_metadata = new Zend_Form_Element_Checkbox('icecast_vorbis_metadata');
        $icecast_vorbis_metadata->setLabel('Icecast Vorbis Metadata')
                                ->setDescription($description)
                                ->setRequired(false)
                                ->setValue(($setting['icecast_vorbis_metadata'] == "true")?1:0)
                                ->setDecorators(array('ViewHelper'));
        if (Application_Model_Preference::GetEnableStreamConf() == "false"){
            $icecast_vorbis_metadata->setAttrib("readonly", true);
        }
        $this->addElement($icecast_vorbis_metadata);

        $stream_format = new Zend_Form_Element_Radio('streamFormat');
        $stream_format->setLabel('Stream Label:');
        $stream_format->setMultiOptions(array("Artist - Title",
                                            "Show - Artist - Title",
                                            "Station name - Show name"));
        $stream_format->setValue(Application_Model_Preference::GetStreamLabelFormat());
        $stream_format->setDecorators(array('ViewHelper'));
        $this->addElement($stream_format);
    }

    public function isValid($data){
        if($data['output_sound_device']){
            $this->populate(array("output_sound_device"=>$data['output_sound_device'], "icecast_vorbis_metadata"=>$data['icecast_vorbis_metadata'],
                                "output_sound_device_type"=>$data['output_sound_device_type'], "streamFormat"=>$data['streamFormat']));
        }
        return true;
    }
}
