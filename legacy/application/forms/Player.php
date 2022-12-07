<?php

declare(strict_types=1);

define('OPUS', 'opus');

class Application_Form_Player extends Zend_Form_SubForm
{
    public function init()
    {
        $this->setDecorators([
            ['ViewScript', ['viewScript' => 'form/player.phtml']],
        ]);

        $title = new Zend_Form_Element_Text('player_title');
        $title->setValue(_('Now Playing'));
        $title->setLabel(_('Title:'));
        $title->setDecorators([
            'ViewHelper',
            'Errors',
            'Label',
        ]);
        $title->addDecorator('Label', ['class' => 'player-title']);
        $this->addElement($title);

        $streamMode = new Zend_Form_Element_Radio('player_stream_mode');
        $streamMode->setLabel(_('Select Stream:'));
        $streamMode->setMultiOptions(
            [
                'auto' => _('Auto detect the most appropriate stream to use.'),
                'manual' => _('Select a stream:'),
            ]
        );
        $streamMode->setValue('auto');
        $this->addElement($streamMode);

        $streamURL = new Zend_Form_Element_Radio('player_stream_url');
        $opusStreamCount = 0;
        $urlOptions = [];
        foreach (Application_Model_StreamSetting::getEnabledStreamData() as $stream => $data) {
            $urlOptions[$stream] = strtoupper($data['codec']) . ' - ' . $data['bitrate'] . 'kbps';
            if ($data['mobile']) {
                $urlOptions[$stream] .= _(' - Mobile friendly');
            }
            if ($data['codec'] == OPUS) {
                ++$opusStreamCount;
                $urlOptions[$stream] .= _(' - The player does not support Opus streams.');
            }
        }
        $streamURL->setMultiOptions(
            $urlOptions
        );

        // Set default value to the first non-opus stream we find
        foreach ($urlOptions as $o => $v) {
            if (strpos(strtolower($v), OPUS) !== false) {
                continue;
            }
            $streamURL->setValue($o);

            break;
        }

        $streamURL->setAttrib('numberOfEnabledStreams', count($urlOptions) - $opusStreamCount);
        $streamURL->removeDecorator('label');
        $this->addElement($streamURL);

        $embedSrc = new Zend_Form_Element_Textarea('player_embed_src');
        $embedSrc->setAttrib('readonly', 'readonly');
        $embedSrc->setAttrib('class', 'embed-player-text-box');
        $embedSrc->setAttrib('cols', '40')
            ->setAttrib('rows', '4');
        $embedSrc->setLabel(_('Embeddable code:'));
        $embedSrc->setDescription(_("Copy this code and paste it into your website's HTML to embed the player in your site."));
        $embedSrc->setValue('<iframe frameborder="0" width="280" height="216" src="' . Config::getPublicUrl() . 'embed/player?stream=auto&title=Now Playing"></iframe>');
        $this->addElement($embedSrc);

        $previewLabel = new Zend_Form_Element_Text('player_preview_label');
        $previewLabel->setLabel(_('Preview:'));
        $previewLabel->setDecorators([
            'ViewHelper',
            'Errors',
            'Label',
        ]);
        $previewLabel->addDecorator('Label', ['class' => 'preview-label']);
        $this->addElement($previewLabel);
    }
}
