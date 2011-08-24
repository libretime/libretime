<?php
class Application_Form_StreamSettingSubForm extends Zend_Form_SubForm{
    private $prefix;
    private $setting;
    private $stream_types;
    private $stream_bitrates;
    
    public function init()
    {
        
    }
    
    public function setPrefix($prefix){
        $this->prefix = $prefix;
    }
    
    public function setSetting($setting){
        $this->setting = $setting;
    }
    
    public function setStreamTypes($stream_types){
        $this->stream_types = $stream_types;
    }
    
    public function setStreamBitrates($stream_bitrates){
        $this->stream_bitrates = $stream_bitrates;
    }
    
    public function startForm(){
        $prefix = "s".$this->prefix;
        $stream_number = $this->prefix;
        $setting = $this->setting;
        $stream_types = $this->stream_types;
        $stream_bitrates = $this->stream_bitrates;
        
        $this->setIsArray(true);
        $this->setElementsBelongTo($prefix."_data");
        
        $enable = new Zend_Form_Element_Checkbox('enable');
        $enable->setLabel('Enabled:')
                            ->setValue($setting[$prefix.'_output'] != 'disabled'?1:0)
                            ->setDecorators(array('ViewHelper'));
        $this->addElement($enable);
        
        $type = new Zend_Form_Element_Select('type');
        $type->setLabel("Type:")
                ->setMultiOptions($stream_types)
                ->setValue(isset($setting[$prefix.'_type'])?$setting[$prefix.'_type']:0)
                ->setDecorators(array('ViewHelper'));
        $this->addElement($type);
        
        $bitrate = new Zend_Form_Element_Select('bitrate');
        $bitrate->setLabel("Bitrate:")
                ->setMultiOptions($stream_bitrates)
                ->setValue(isset($setting[$prefix.'_bitrate'])?$setting[$prefix.'_bitrate']:0)
                ->setDecorators(array('ViewHelper'));
        $this->addElement($bitrate);
        
        $output = new Zend_Form_Element_Select('output');
        $output->setLabel("Output to:")
                ->setMultiOptions(array("icecast"=>"Icecast", "shoutcast"=>"Shoutcast"))
                ->setValue(isset($setting[$prefix.'_output'])?$setting[$prefix.'_output']:"icecast")
                ->setDecorators(array('ViewHelper'));
        $this->addElement($output);
        
        $host = new Zend_Form_Element_Text('host');
        $host->setLabel("Server")
                ->setValue(isset($setting[$prefix.'_host'])?$setting[$prefix.'_host']:"")
                ->setDecorators(array('ViewHelper'));
        $this->addElement($host);
        
        $port = new Zend_Form_Element_Text('port');
        $port->setLabel("Port")
                ->setValue(isset($setting[$prefix.'_port'])?$setting[$prefix.'_port']:"")
                ->setDecorators(array('ViewHelper'));
        $this->addElement($port);
        
        $pass = new Zend_Form_Element_Text('pass');
        $pass->setLabel("Password")
                ->setValue(isset($setting[$prefix.'_pass'])?$setting[$prefix.'_pass']:"")
                ->setDecorators(array('ViewHelper'));
        $this->addElement($pass);
        
        $genre = new Zend_Form_Element_Text('genre');
        $genre->setLabel("Genre")
                ->setValue(isset($setting[$prefix.'_genre'])?$setting[$prefix.'_genre']:"")
                ->setDecorators(array('ViewHelper'));
        $this->addElement($genre);
        
        $url = new Zend_Form_Element_Text('url');
        $url->setLabel("URL")
                ->setValue(isset($setting[$prefix.'_url'])?$setting[$prefix.'_url']:"")
                ->setDecorators(array('ViewHelper'));
        $this->addElement($url);
        
        $description = new Zend_Form_Element_Text('description');
        $description->setLabel("Name/Description")
                ->setValue(isset($setting[$prefix.'_description'])?$setting[$prefix.'_description']:"")
                ->setDecorators(array('ViewHelper'));
        $this->addElement($description);
        
        $mount_info = array();
        if(isset($setting[$prefix.'_mount'])){
            $mount_info = explode('.',$setting[$prefix.'_mount']);
        }
        $mount = new Zend_Form_Element_Text('mount');
        $mount->setLabel("Mount Point")
                ->setValue(isset($mount_info[0])?$mount_info[0]:"")
                ->setDecorators(array('ViewHelper'));
        $this->addElement($mount);
        
        $user = new Zend_Form_Element_Text('user');
        $user->setLabel("Username")
                ->setValue(isset($setting[$prefix.'_user'])?$setting[$prefix.'_user']:"")
                ->setDecorators(array('ViewHelper'));
        $this->addElement($user);
        
        $this->setDecorators(array(
            array('ViewScript', array('viewScript' => 'form/stream-setting-form.phtml', "stream_number"=>$stream_number))
        ));
    }
    
    public function isValid ($data){
        $isValid = parent::isValid($data);
        if($data['enable'] == 1){
            if($data['host'] == ''){
                $element = $this->getElement("host");
                $element->addError("Server cannot be empty.");
                $isValid = false;
            }
            if($data['port'] == ''){
                $element = $this->getElement("port");
                $element->addError("Port cannot be empty.");
                $isValid = false;
            }
            if($data['output'] == 'icecast'){
                if($data['mount'] == ''){
                    $element = $this->getElement("mount");
                    $element->addError("Mount cannot be empty with Icecast server.");
                    $isValid = false;
                }
            }
        }
        return $isValid;
    }
}