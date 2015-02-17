<?php

class Application_Form_EmbeddablePlayer extends Zend_Form_SubForm
{
    public function init()
    {
        $this->setDecorators(array(
            array('ViewScript', array('viewScript' => 'form/embeddableplayer.html'))
        ));

        $displayTrackMetadata = new Zend_Form_Element_Checkbox('display_track_metadata');
        $displayTrackMetadata->setValue(true);
        $displayTrackMetadata->setLabel(_('Display track metadata?'));
        $this->addElement($displayTrackMetadata);

        $streamURL = new Zend_Form_Element_Radio('stream_url');
        $streamURL->setMultiOptions(array(
            'AAC' => 'http://127.0.0.1:8000/airtime_a',
            'MP3' => 'http://127.0.0.1:8000/airtime_b'
        ));
        $streamURL->setLabel(_('Select stream:'));
        $this->addElement($streamURL);

        $submit = new Zend_Form_Element_Submit('submit');
        $this->addElement($submit);
    }
}