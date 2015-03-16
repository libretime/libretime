<?php

class Application_Form_EmbeddablePlayer extends Zend_Form_SubForm
{
    public function init()
    {
        $this->setDecorators(array(
            array('ViewScript', array('viewScript' => 'form/embeddableplayer.phtml'))
        ));

        $displayTrackMetadata = new Zend_Form_Element_Checkbox('player_display_track_metadata');
        $displayTrackMetadata->setValue(true);
        $displayTrackMetadata->setLabel(_('Display track metadata?'));
        $this->addElement($displayTrackMetadata);

        $streamURL = new Zend_Form_Element_Radio('player_stream_url');
        $urlOptions = Array();
        foreach(Application_Model_StreamSetting::getEnabledStreamUrls() as $type => $url) {
            if ($type == "opus") continue;
            $urlOptions[$url] = $type;
        }
        $streamURL->setMultiOptions(
            $urlOptions
        );
        $streamURL->setValue(array_keys($urlOptions)[0]);
        $streamURL->setLabel(_('Select stream:'));
        $streamURL->setAttrib('codec', array_values($urlOptions)[0]);
        $streamURL->setAttrib('numberOfEnabledStreams', sizeof($urlOptions));
        $this->addElement($streamURL);

        $url = $streamURL->getValue();
        $codec = $streamURL->getAttrib('codec');

        $embedSrc = new Zend_Form_Element_Text('player_embed_src');
        $embedSrc->setAttrib("readonly", "readonly");
        $embedSrc->setValue('<iframe frameborder="0" src="'.Application_Common_HTTPHelper::getStationUrl().'/embeddableplayer/embed-code?url='.$url.'&codec='.$codec.'"></iframe>');
        $embedSrc->removeDecorator('label');
        $this->addElement($embedSrc);

    }
}