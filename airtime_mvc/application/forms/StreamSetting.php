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
            $output_sound_device->setLabel('Hardware Audio Ouput')
                                ->setRequired(false)
                                ->setValue(($setting['output_sound_device'] == "true")?1:0)
                                ->setDecorators(array('ViewHelper'));
            if (Application_Model_Preference::GetEnableStreamConf() == "false"){
                $output_sound_device->setAttrib("readonly", true);
            }
            $this->addElement($output_sound_device);
        }
        
        $icecast_vorbis_metadata = new Zend_Form_Element_Checkbox('icecast_vorbis_metadata');
        $icecast_vorbis_metadata->setLabel('Icecast Vorbis Meatadata')
                                ->setRequired(false)
                                ->setValue(($setting['icecast_vorbis_metadata'] == "true")?1:0)
                                ->setDecorators(array('ViewHelper'));
        if (Application_Model_Preference::GetEnableStreamConf() == "false"){
            $icecast_vorbis_metadata->setAttrib("readonly", true);
        }
        $this->addElement($icecast_vorbis_metadata);
    }

    public function isValid($data){
        $this->populate(array("output_sound_device"=>$data['output_sound_device'], "icecast_vorbis_metadata"=>$data['icecast_vorbis_metadata']));
        return true;
    }
}
