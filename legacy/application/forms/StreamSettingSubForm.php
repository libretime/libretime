<?php

declare(strict_types=1);

class Application_Form_StreamSettingSubForm extends Zend_Form_SubForm
{
    private $prefix;
    private $setting;

    public static $customizable;

    public function init()
    {
    }

    public function setPrefix($prefix)
    {
        $this->prefix = $prefix;
    }

    public function setSetting($setting)
    {
        $this->setting = $setting;
    }

    public function startForm()
    {
        $prefix = 's' . $this->prefix;
        $stream_number = $this->prefix;
        $setting = $this->setting;

        $this->setIsArray(true);
        $this->setElementsBelongTo($prefix . '_data');

        $enable = new Zend_Form_Element_Checkbox('enable');
        $enable
            ->setLabel(_('Enabled:'))
            ->setAttrib('disabled', true)
            ->setValue($setting[$prefix . '_enable'] == 'true' ? 1 : 0)
            ->setDecorators(['ViewHelper']);
        $this->addElement($enable);

        $mobile = new Zend_Form_Element_Checkbox('mobile');
        $mobile
            ->setLabel(_('Mobile:'))
            ->setAttrib('disabled', true)
            ->setValue($setting[$prefix . '_mobile'])
            ->setDecorators(['ViewHelper']);
        $this->addElement($mobile);

        $type = new Zend_Form_Element_Text('type');
        $type
            ->setLabel(_('Stream Type:'))
            ->setAttrib('readonly', true)
            ->setValue(isset($setting[$prefix . '_type']) ? $setting[$prefix . '_type'] : 0)
            ->setDecorators(['ViewHelper']);
        $this->addElement($type);

        $bitrate = new Zend_Form_Element_Text('bitrate');
        $bitrate
            ->setLabel(_('Bit Rate:'))
            ->setAttrib('readonly', true)
            ->setValue(isset($setting[$prefix . '_bitrate']) ? $setting[$prefix . '_bitrate'] : 0)
            ->setDecorators(['ViewHelper']);
        $this->addElement($bitrate);

        $output = new Zend_Form_Element_Text('output');
        $output
            ->setLabel(_('Service Type:'))
            ->setAttrib('readonly', true)
            ->setValue(isset($setting[$prefix . '_output']) ? $setting[$prefix . '_output'] : 'icecast')
            ->setDecorators(['ViewHelper']);
        $this->addElement($output);

        $channels = new Zend_Form_Element_Text('channels');
        $channels
            ->setLabel(_('Channels:'))
            ->setAttrib('readonly', true)
            ->setValue(isset($setting[$prefix . '_channels']) ? $setting[$prefix . '_channels'] : 'stereo')
            ->setDecorators(['ViewHelper']);
        $this->addElement($channels);

        $host = new Zend_Form_Element_Text('host');
        $host
            ->setLabel(_('Server'))
            ->setAttrib('readonly', true)
            ->setValue(isset($setting[$prefix . '_host']) ? $setting[$prefix . '_host'] : '')
            ->setDecorators(['ViewHelper']);
        $host->setAttrib('alt', 'domain');
        $this->addElement($host);

        $port = new Zend_Form_Element_Text('port');
        $port
            ->setLabel(_('Port'))
            ->setAttrib('readonly', true)
            ->setValue(isset($setting[$prefix . '_port']) ? $setting[$prefix . '_port'] : '')
            ->setDecorators(['ViewHelper']);
        $this->addElement($port);

        $mount = new Zend_Form_Element_Text('mount');
        $mount
            ->setLabel(_('Mount Point'))
            ->setAttrib('readonly', true)
            ->setValue(isset($setting[$prefix . '_mount']) ? $setting[$prefix . '_mount'] : '')
            ->setDecorators(['ViewHelper']);
        $mount->setAttrib('alt', 'regular_text');
        $this->addElement($mount);

        $name = new Zend_Form_Element_Text('name');
        $name
            ->setLabel(_('Name'))
            ->setAttrib('readonly', true)
            ->setValue(isset($setting[$prefix . '_name']) ? $setting[$prefix . '_name'] : '')
            ->setDecorators(['ViewHelper']);
        $this->addElement($name);

        $genre = new Zend_Form_Element_Text('genre');
        $genre
            ->setAttrib('readonly', true)
            ->setLabel(_('Genre'))
            ->setValue(isset($setting[$prefix . '_genre']) ? $setting[$prefix . '_genre'] : '')
            ->setDecorators(['ViewHelper']);
        $this->addElement($genre);

        $url = new Zend_Form_Element_Text('url');
        $url
            ->setLabel(_('URL'))
            ->setAttrib('readonly', true)
            ->setValue(isset($setting[$prefix . '_url']) ? $setting[$prefix . '_url'] : '')
            ->setDecorators(['ViewHelper']);
        $url->setAttrib('alt', 'url');
        $this->addElement($url);

        $description = new Zend_Form_Element_Text('description');
        $description
            ->setLabel(_('Description'))
            ->setAttrib('readonly', true)
            ->setValue(isset($setting[$prefix . '_description']) ? $setting[$prefix . '_description'] : '')
            ->setDecorators(['ViewHelper']);
        $this->addElement($description);

        $public_url = new Zend_Form_Element_Text('public_url');
        $public_url
            ->setLabel(_('Stream URL'))
            ->setValue($setting[$prefix . '_public_url'] ?? '');
        $this->addElement($public_url);

        $liquidsoap_error_msg = '<div class="stream-status status-info"><p>' . _('Getting information from the server...') . '</p></div>';

        $this->setDecorators([
            ['ViewScript', [
                'viewScript' => 'form/stream-setting-form.phtml',
                'stream_number' => $stream_number,
                'enabled' => $enable->getValue(),
                'liquidsoap_error_msg' => $liquidsoap_error_msg,
            ]],
        ]);
    }
}
