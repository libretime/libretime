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
    
    public function startFrom(){
        $setting = $this->setting;
        $output_sound_device = new Zend_Form_Element_Checkbox('output_sound_device');
        $output_sound_device->setLabel('Enabled')
                            ->setRequired(false)
                            ->setValue(($setting['output_sound_device'] == "true")?1:0)
                            ->setDecorators(array('ViewHelper'));
        if(Application_Model_Preference::GetDisableStreamConf() == "true"){
            $output_sound_device->setAttrib("readonly", true);
        }
        $this->addElement($output_sound_device);
    }
    
    public function isValid($data){
        $this->populate(array("output_sound_device"=>$data));
        return true;
    }
}
