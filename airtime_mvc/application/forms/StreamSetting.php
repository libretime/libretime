<?php

class Application_Form_StreamSetting extends Zend_Form_SubForm
{

    public function init()
    {
        $this->setDecorators(array(
            array('ViewScript', array('viewScript' => 'form/stream_setting.phtml'))
        ));
    }
}

