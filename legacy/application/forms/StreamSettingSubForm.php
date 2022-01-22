<?php

class Application_Form_StreamSettingSubForm extends Zend_Form_SubForm
{
    private $prefix;
    private $setting;
    private $stream_types;
    private $stream_bitrates;

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

    public function setStreamTypes($stream_types)
    {
        $this->stream_types = $stream_types;
    }

    public function setStreamBitrates($stream_bitrates)
    {
        $this->stream_bitrates = $stream_bitrates;
    }

    public function startForm()
    {
        $prefix = 's' . $this->prefix;
        $stream_number = $this->prefix;
        $setting = $this->setting;
        $stream_types = $this->stream_types;
        $stream_bitrates = $this->stream_bitrates;

        $streamDefaults = Application_Model_StreamSetting::getDefaults($prefix);
        // If we're not using custom stream settings, use the defaults
        $useDefaults = !Application_Model_Preference::getUsingCustomStreamSettings();

        $this->setIsArray(true);
        $this->setElementsBelongTo($prefix . '_data');

        $enable = new Zend_Form_Element_Checkbox('enable');
        $enable->setLabel(_('Enabled:'))
            ->setValue($setting[$prefix . '_enable'] == 'true' ? 1 : 0)
            ->setDecorators(['ViewHelper']);
        $this->addElement($enable);
        static::$customizable[] = $enable->getName();

        $mobile = new Zend_Form_Element_Checkbox('mobile');
        $mobile->setLabel(_('Mobile:'));
        $mobile->setValue($setting[$prefix . '_mobile']);
        $mobile->setDecorators(['ViewHelper']);
        $this->addElement($mobile);
        static::$customizable[] = $mobile->getName();

        $type = new Zend_Form_Element_Select('type');
        $type->setLabel(_('Stream Type:'))
            ->setMultiOptions($stream_types)
            ->setValue(isset($setting[$prefix . '_type']) ? $setting[$prefix . '_type'] : 0)
            ->setDecorators(['ViewHelper']);
        $this->addElement($type);
        static::$customizable[] = $type->getName();

        $bitrate = new Zend_Form_Element_Select('bitrate');
        $bitrate->setLabel(_('Bit Rate:'))
            ->setMultiOptions($stream_bitrates)
            ->setValue(isset($setting[$prefix . '_bitrate']) ? $setting[$prefix . '_bitrate'] : 0)
            ->setDecorators(['ViewHelper']);
        $this->addElement($bitrate);
        static::$customizable[] = $bitrate->getName();

        $output = new Zend_Form_Element_Select('output');
        $output->setLabel(_('Service Type:'))
            ->setMultiOptions(['icecast' => 'Icecast', 'shoutcast' => 'SHOUTcast'])
            ->setValue($useDefaults ? $streamDefaults['output'] :
                               (isset($setting[$prefix . '_output']) ? $setting[$prefix . '_output'] : 'icecast'))
            ->setDecorators(['ViewHelper']);
        $this->addElement($output);

        $channels = new Zend_Form_Element_Select('channels');
        $channels->setLabel(_('Channels:'))
            ->setMultiOptions(['mono' => _('1 - Mono'), 'stereo' => _('2 - Stereo')])
            ->setValue(isset($setting[$prefix . '_channels']) ? $setting[$prefix . '_channels'] : 'stereo')
            ->setDecorators(['ViewHelper']);
        $this->addElement($channels);
        static::$customizable[] = $channels->getName();

        $host = new Zend_Form_Element_Text('host');
        $host->setLabel(_('Server'))
            ->setValue($useDefaults ? $streamDefaults['host'] :
                               (isset($setting[$prefix . '_host']) ? $setting[$prefix . '_host'] : ''))
            ->setValidators([
                ['regex', false, ['/^[0-9a-zA-Z-_.]+$/', 'messages' => _('Invalid character entered')]], ])
            ->setDecorators(['ViewHelper']);
        $host->setAttrib('alt', 'domain');
        $this->addElement($host);

        $port = new Zend_Form_Element_Text('port');
        $port->setLabel(_('Port'))
            ->setValue($useDefaults ? $streamDefaults['port'] :
                               (isset($setting[$prefix . '_port']) ? $setting[$prefix . '_port'] : ''))
            ->setValidators([new Zend_Validate_Between(['min' => 0, 'max' => 99999])])
            ->addValidator('regex', false, ['pattern' => '/^[0-9]+$/', 'messages' => ['regexNotMatch' => _('Only numbers are allowed.')]])
            ->setDecorators(['ViewHelper']);
        $this->addElement($port);

        $pass = new Zend_Form_Element_Text('pass');
        $pass->setLabel(_('Password'))
            ->setValue($useDefaults ? $streamDefaults['pass'] :
                               (isset($setting[$prefix . '_pass']) ? $setting[$prefix . '_pass'] : ''))
            ->setValidators([
                ['regex', false, ['/^[^ &<>]+$/', 'messages' => _('Invalid character entered')]], ])
            ->setDecorators(['ViewHelper']);
        $pass->setAttrib('alt', 'regular_text');
        $this->addElement($pass);

        $genre = new Zend_Form_Element_Text('genre');
        $genre->setLabel(_('Genre'))
            ->setValue(isset($setting[$prefix . '_genre']) ? $setting[$prefix . '_genre'] : '')
            ->setDecorators(['ViewHelper']);
        $this->addElement($genre);

        $url = new Zend_Form_Element_Text('url');
        $url->setLabel(_('URL'))
            ->setValue(isset($setting[$prefix . '_url']) ? $setting[$prefix . '_url'] : '')
            ->setValidators([
                ['regex', false, ['/^[0-9a-zA-Z\-_.:\/]+$/', 'messages' => _('Invalid character entered')]], ])
            ->setDecorators(['ViewHelper']);
        $url->setAttrib('alt', 'url');
        $this->addElement($url);

        $name = new Zend_Form_Element_Text('name');
        $name->setLabel(_('Name'))
            ->setValue(isset($setting[$prefix . '_name']) ? $setting[$prefix . '_name'] : '')
            ->setDecorators(['ViewHelper']);
        $this->addElement($name);

        $description = new Zend_Form_Element_Text('description');
        $description->setLabel(_('Description'))
            ->setValue(isset($setting[$prefix . '_description']) ? $setting[$prefix . '_description'] : '')
            ->setDecorators(['ViewHelper']);
        $this->addElement($description);

        $mount = new Zend_Form_Element_Text('mount');
        $mount->setLabel(_('Mount Point'))
            ->setValue($useDefaults ? $streamDefaults['mount'] :
                               (isset($setting[$prefix . '_mount']) ? $setting[$prefix . '_mount'] : ''))
            ->setValidators([
                ['regex', false, ['/^[^ &<>]+$/', 'messages' => _('Invalid character entered')]], ])
            ->setDecorators(['ViewHelper']);
        $mount->setAttrib('alt', 'regular_text');
        $this->addElement($mount);

        $user = new Zend_Form_Element_Text('user');
        $user->setLabel(_('Username'))
            ->setValue($useDefaults ? $streamDefaults['user'] :
                               (isset($setting[$prefix . '_user']) ? $setting[$prefix . '_user'] : ''))
            ->setValidators([
                ['regex', false, ['/^[^ &<>]+$/', 'messages' => _('Invalid character entered')]], ])
            ->setDecorators(['ViewHelper']);
        $user->setAttrib('alt', 'regular_text');
        $this->addElement($user);

        $adminUser = new Zend_Form_Element_Text('admin_user');
        $adminUser->setLabel(_('Admin User'))
            ->setValue(Application_Model_StreamSetting::getAdminUser($prefix))
            ->setValidators([
                ['regex', false, ['/^[^ &<>]+$/', 'messages' => _('Invalid character entered')]], ])
            ->setDecorators(['ViewHelper']);
        $adminUser->setAttrib('alt', 'regular_text');
        $this->addElement($adminUser);

        $adminPass = new Zend_Form_Element_Password('admin_pass');
        $adminPass->setLabel(_('Admin Password'))
            ->setValue(Application_Model_StreamSetting::getAdminPass($prefix))
            ->setValidators([
                ['regex', false, ['/^[^ &<>]+$/', 'messages' => _('Invalid character entered')]], ])
            ->setDecorators(['ViewHelper']);
        $adminPass->setAttrib('alt', 'regular_text');
        $this->addElement($adminPass);

        $liquidsoap_error_msg = '<div class="stream-status status-info"><p>' . _('Getting information from the server...') . '</p></div>';

        $this->setDecorators([
            ['ViewScript', ['viewScript' => 'form/stream-setting-form.phtml',
                'stream_number' => $stream_number,
                'enabled' => $enable->getValue(),
                'liquidsoap_error_msg' => $liquidsoap_error_msg, ]],
        ]);
    }

    public function isValid($data)
    {
        $f_data = $data['s' . $this->prefix . '_data'];
        $isValid = parent::isValid($f_data);
        // XXX: A couple of ugly workarounds here, but I guess that's what you get when you
        // combine an already-complex POST and GET into a single action...
        if (Application_Model_Preference::getUsingCustomStreamSettings() && $f_data) {
            if ($f_data['enable'] == 1 && isset($f_data['host'])) {
                if ($f_data['host'] == '') {
                    $element = $this->getElement('host');
                    $element->addError(_('Server cannot be empty.'));
                    $isValid = false;
                }
                if ($f_data['port'] == '') {
                    $element = $this->getElement('port');
                    $element->addError(_('Port cannot be empty.'));
                    $isValid = false;
                }
                if ($f_data['output'] == 'icecast') {
                    if ($f_data['mount'] == '') {
                        $element = $this->getElement('mount');
                        $element->addError(_('Mount cannot be empty with Icecast server.'));
                        $isValid = false;
                    }
                }
            }
        }

        return $isValid;
    }

    public function toggleState()
    {
        $elements = $this->getElements();
        foreach ($elements as $element) {
            if (Application_Model_Preference::getUsingCustomStreamSettings()) {
                $element->setAttrib('disabled', null);
            } elseif (!(in_array($element->getName(), static::$customizable)
                    || $element->getType() == 'Zend_Form_Element_Hidden')) {
                $element->setAttrib('disabled', 'disabled');
            }
        }
    }
}
