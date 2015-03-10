<?php

class Application_Form_EmbeddablePlayer extends Zend_Form_SubForm
{
    public function init()
    {
        $this->setDecorators(array(
            array('ViewScript', array('viewScript' => 'form/embeddableplayer.phtml'))
        ));

        $embedSrc = new Zend_Form_Element_Text('player_embed_src');
        $embedSrc->setAttrib("readonly", "readonly");
        $embedSrc->setAttrib("class", "player_embed_src");
        $embedSrc->setValue('<iframe frameborder="0" src="http://localhost/embeddableplayer/embed-code"></iframe>');
        $embedSrc->removeDecorator('label');
        $this->addElement($embedSrc);

        $displayTrackMetadata = new Zend_Form_Element_Checkbox('player_display_track_metadata');
        $displayTrackMetadata->setValue(true);
        $displayTrackMetadata->setLabel(_('Display track metadata?'));
        $this->addElement($displayTrackMetadata);

        $streamURL = new Zend_Form_Element_Radio('player_stream_url');
        $urlOptions = Array();
        foreach(Application_Model_StreamSetting::getEnabledStreamUrls() as $type => $url) {
            $urlOptions[$url] = $type;
        }
        $streamURL->setMultiOptions(
            $urlOptions
        );
        $streamURL->setValue(0);
        $streamURL->setLabel(_('Select stream:'));
        $this->addElement($streamURL);

    }
}