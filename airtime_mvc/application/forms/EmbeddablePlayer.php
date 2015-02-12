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

        $submit = new Zend_Form_Element_Submit('submit');
        $this->addElement($submit);
    }
}