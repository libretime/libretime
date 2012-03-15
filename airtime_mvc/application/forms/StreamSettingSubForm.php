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

        $disable_all = false;
        if(Application_Model_Preference::GetEnableStreamConf() == "false"){
            $disable_all = true;
        }

        $enable = new Zend_Form_Element_Checkbox('enable');
        $enable->setLabel('Enabled:')
                            ->setValue($setting[$prefix.'_enable'] == 'true' ? 1 : 0)
                            ->setDecorators(array('ViewHelper'));
        if($disable_all){
            $enable->setAttrib("disabled", "disabled");
        }
        $this->addElement($enable);

        $type = new Zend_Form_Element_Select('type');
        $type->setLabel("Stream Type:")
                ->setMultiOptions($stream_types)
                ->setValue(isset($setting[$prefix.'_type'])?$setting[$prefix.'_type']:0)
                ->setDecorators(array('ViewHelper'));
        if($disable_all){
            $type->setAttrib("disabled", "disabled");
        }
        $this->addElement($type);

        $bitrate = new Zend_Form_Element_Select('bitrate');
        $bitrate->setLabel("Bit Rate:")
                ->setMultiOptions($stream_bitrates)
                ->setValue(isset($setting[$prefix.'_bitrate'])?$setting[$prefix.'_bitrate']:0)
                ->setDecorators(array('ViewHelper'));
        if($disable_all){
            $bitrate->setAttrib("disabled", "disabled");
        }
        $this->addElement($bitrate);

        $output = new Zend_Form_Element_Select('output');
        $output->setLabel("Service Type:")
                ->setMultiOptions(array("icecast"=>"Icecast", "shoutcast"=>"SHOUTcast"))
                ->setValue(isset($setting[$prefix.'_output'])?$setting[$prefix.'_output']:"icecast")
                ->setDecorators(array('ViewHelper'));
        if($disable_all){
            $output->setAttrib("disabled", "disabled");
        }
        $this->addElement($output);

        $host = new Zend_Form_Element_Text('host');
        $host->setLabel("Server")
                ->setValue(isset($setting[$prefix.'_host'])?$setting[$prefix.'_host']:"")
                ->setValidators(array(
                        array('regex', false, array('/^[0-9a-zA-Z-_.]+$/', 'messages' => 'Invalid character entered'))))
                ->setDecorators(array('ViewHelper'));
        if($disable_all){
            $host->setAttrib("disabled", "disabled");
        }
        $host->setAttrib('alt', 'domain');
        $this->addElement($host);

        $port = new Zend_Form_Element_Text('port');
        $port->setLabel("Port")
                ->setValue(isset($setting[$prefix.'_port'])?$setting[$prefix.'_port']:"")
                ->setValidators(array(new Zend_Validate_Between(array('min'=>0, 'max'=>99999))))
                ->addValidator('regex', false, array('pattern'=>'/^[0-9]+$/', 'messages'=>array('regexNotMatch'=>'Only numbers are allowed.')))
                ->setDecorators(array('ViewHelper'));
        if($disable_all){
            $port->setAttrib("disabled", "disabled");
        }
        $this->addElement($port);

        $pass = new Zend_Form_Element_Text('pass');
        $pass->setLabel("Password")
                ->setValue(isset($setting[$prefix.'_pass'])?$setting[$prefix.'_pass']:"")
                ->setValidators(array(
                        array('regex', false, array('/^[^ &<>]+$/', 'messages' => 'Invalid character entered'))))
                ->setDecorators(array('ViewHelper'));
        if($disable_all){
            $pass->setAttrib("disabled", "disabled");
        }
        $pass->setAttrib('alt', 'regular_text');
        $this->addElement($pass);

        $genre = new Zend_Form_Element_Text('genre');
        $genre->setLabel("Genre")
                ->setValue(isset($setting[$prefix.'_genre'])?$setting[$prefix.'_genre']:"")
                ->setDecorators(array('ViewHelper'));
        if($disable_all){
            $genre->setAttrib("disabled", "disabled");
        }
        $this->addElement($genre);

        $url = new Zend_Form_Element_Text('url');
        $url->setLabel("URL")
                ->setValue(isset($setting[$prefix.'_url'])?$setting[$prefix.'_url']:"")
                ->setValidators(array(
                        array('regex', false, array('/^[0-9a-zA-Z\-_.:\/]+$/', 'messages' => 'Invalid character entered'))))
                ->setDecorators(array('ViewHelper'));
        if($disable_all){
            $url->setAttrib("disabled", "disabled");
        }
        $url->setAttrib('alt', 'url');
        $this->addElement($url);

        $description = new Zend_Form_Element_Text('description');
        $description->setLabel("Name/Description")
                ->setValue(isset($setting[$prefix.'_description'])?$setting[$prefix.'_description']:"")
                ->setDecorators(array('ViewHelper'));
        if($disable_all){
            $description->setAttrib("disabled", "disabled");
        }
        $this->addElement($description);

        $mount = new Zend_Form_Element_Text('mount');
        $mount->setLabel("Mount Point")
                ->setValue(isset($setting[$prefix.'_mount'])?$setting[$prefix.'_mount']:"")
                ->setValidators(array(
                        array('regex', false, array('/^[^ &<>]+$/', 'messages' => 'Invalid character entered'))))
                ->setDecorators(array('ViewHelper'));
        if($disable_all){
            $mount->setAttrib("disabled", "disabled");
        }
        $mount->setAttrib('alt', 'regular_text');
        $this->addElement($mount);

        $user = new Zend_Form_Element_Text('user');
        $user->setLabel("Username")
                ->setValue(isset($setting[$prefix.'_user'])?$setting[$prefix.'_user']:"")
                ->setValidators(array(
                        array('regex', false, array('/^[^ &<>]+$/', 'messages' => 'Invalid character entered'))))
                ->setDecorators(array('ViewHelper'));
        if($disable_all){
            $user->setAttrib("disabled", "disabled");
        }
        $user->setAttrib('alt', 'regular_text');
        $this->addElement($user);
        
        $liquidsopa_error_msg = '<div class="stream-status status-info"><h3>Getting information from the server...</h3></div>';
        
        $this->setDecorators(array(
            array('ViewScript', array('viewScript' => 'form/stream-setting-form.phtml', "stream_number"=>$stream_number, "enabled"=>$enable->getValue(), "liquidsoap_error_msg"=>$liquidsopa_error_msg))
        ));
    }

    public function isValid ($data){
        $f_data = $data['s'.$this->prefix."_data"];
        $isValid = parent::isValid($f_data);
        if($f_data['enable'] == 1){
            if($f_data['host'] == ''){
                $element = $this->getElement("host");
                $element->addError("Server cannot be empty.");
                $isValid = false;
            }
            if($f_data['port'] == ''){
                $element = $this->getElement("port");
                $element->addError("Port cannot be empty.");
                $isValid = false;
            }
            if($f_data['output'] == 'icecast'){
                if($f_data['mount'] == ''){
                    $element = $this->getElement("mount");
                    $element->addError("Mount cannot be empty with Icecast server.");
                    $isValid = false;
                }
            }
        }
        return $isValid;
    }
}
