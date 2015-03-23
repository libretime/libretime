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

        $streamMode = new Zend_Form_Element_Radio('player_stream_mode');
        $streamMode->setLabel(_('Select Stream:'));
        $streamMode->setMultiOptions(
            array(
                "a" => "Use a mobile stream if possible, when appropriate. Otherwise use the highest quality stream.",
                "b" => "Select a stream"
            )
        );
        $streamMode->setValue("a");
        $this->addElement($streamMode);

        $streamURL = new Zend_Form_Element_Radio('player_stream_url');
        $opusStreamCount = 0;
        $urlOptions = Array();
        foreach(Application_Model_StreamSetting::getEnabledStreamData() as $stream => $data) {
            $urlOptions[$stream] = strtoupper($data["codec"])." - ".$data["bitrate"]."kbps";
            if ($data["mobile"]) {
                $urlOptions[$stream] .= " - Mobile friendly";
            }
            if ($data["codec"] == "opus") {
                $opusStreamCount += 1;
                $urlOptions[$stream] .=" - The player does not support Opus streams.";
            }
        }
        $streamURL->setMultiOptions(
            $urlOptions
        );

        foreach ($urlOptions as $o => $v) {
            if (strpos($v, "opus") !== false) {
                continue;
            } else {
                $streamURL->setValue($o);
                break;
            }
        }

        $streamURL->setAttrib('numberOfEnabledStreams', sizeof($urlOptions)-$opusStreamCount);
        $streamURL->setAttrib("disabled", "disabled");
        $this->addElement($streamURL);

        $embedSrc = new Zend_Form_Element_Text('player_embed_src');
        $embedSrc->setAttrib("readonly", "readonly");
        $embedSrc->setAttrib("class", "embed-player-text-box");
        $embedSrc->setValue('<iframe frameborder="0" src="'.Application_Common_HTTPHelper::getStationUrl().'embeddableplayer/embed-code?stream-mode=a"></iframe>');
        $embedSrc->removeDecorator('label');
        $this->addElement($embedSrc);

        $previewLabel = new Zend_Form_Element_Text('player_preview_label');
        $previewLabel->setLabel("Preview:");
        $this->addElement($previewLabel);

    }
}