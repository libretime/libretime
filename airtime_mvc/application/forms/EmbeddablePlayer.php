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
                "auto" => "Auto detect the most appropriate stream to use.",
                "manual" => "Select a stream:"
            )
        );
        $streamMode->setValue("auto");
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
            if (strpos(strtolower($v), "opus") !== false) {
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
        $embedSrc->setValue('<iframe frameborder="0" width="280" height="230" src="'.Application_Common_HTTPHelper::getStationUrl().'embeddableplayer/embed-code?stream=auto"></iframe>');
        $embedSrc->removeDecorator('label');
        $this->addElement($embedSrc);

        $previewLabel = new Zend_Form_Element_Text('player_preview_label');
        $previewLabel->setLabel("Preview:");
        $this->addElement($previewLabel);

    }
}