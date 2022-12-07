<?php

declare(strict_types=1);

require_once 'customvalidators/ConditionalNotEmpty.php';

class Application_Form_AddShowLiveStream extends Zend_Form_SubForm
{
    public function init()
    {
        $cb_airtime_auth = new Zend_Form_Element_Checkbox('cb_airtime_auth');
        $cb_airtime_auth->setLabel(sprintf(_('Use %s Authentication:'), PRODUCT_NAME))
            ->setChecked(true)
            ->setRequired(false);
        $this->addElement($cb_airtime_auth);

        $cb_custom_auth = new Zend_Form_Element_Checkbox('cb_custom_auth');
        $cb_custom_auth->setLabel(_('Use Custom Authentication:'))
            ->setRequired(false);
        $this->addElement($cb_custom_auth);

        // custom username
        $custom_username = new Zend_Form_Element_Text('custom_username');
        $custom_username->setAttrib('class', 'input_text')
            ->setAttrib('autocomplete', 'off')
            ->setAllowEmpty(true)
            ->setLabel(_('Custom Username'))
            ->setFilters(['StringTrim'])
            ->setValidators([
                new ConditionalNotEmpty(['cb_custom_auth' => '1']),
            ]);
        $this->addElement($custom_username);

        // custom password
        $custom_password = new Zend_Form_Element_Password('custom_password');
        $custom_password->setAttrib('class', 'input_text')
            ->setAttrib('autocomplete', 'off')
            ->setAttrib('renderPassword', 'true')
            ->setAllowEmpty(true)
            ->setLabel(_('Custom Password'))
            ->setFilters(['StringTrim'])
            ->setValidators([
                new ConditionalNotEmpty(['cb_custom_auth' => '1']),
            ]);
        $this->addElement($custom_password);

        $showSourceParams = parse_url(Application_Model_Preference::GetLiveDJSourceConnectionURL());

        // Show source connection url parameters
        $showSourceHost = new Zend_Form_Element_Text('show_source_host');
        $showSourceHost->setAttrib('readonly', true)
            ->setLabel(_('Host:'))
            ->setValue(isset($showSourceParams['host']) ? $showSourceParams['host'] : '');
        $this->addElement($showSourceHost);

        $showSourcePort = new Zend_Form_Element_Text('show_source_port');
        $showSourcePort->setAttrib('readonly', true)
            ->setLabel(_('Port:'))
            ->setValue(isset($showSourceParams['port']) ? $showSourceParams['port'] : '');
        $this->addElement($showSourcePort);

        $showSourceMount = new Zend_Form_Element_Text('show_source_mount');
        $showSourceMount->setAttrib('readonly', true)
            ->setLabel(_('Mount:'))
            ->setValue(isset($showSourceParams['path']) ? $showSourceParams['path'] : '');
        $this->addElement($showSourceMount);

        $this->setDecorators(
            [
                ['ViewScript', ['viewScript' => 'form/add-show-live-stream.phtml']],
            ]
        );
    }

    public function isValid($data)
    {
        $isValid = parent::isValid($data);

        if ($data['cb_custom_auth'] == 1) {
            if (trim($data['custom_username']) == '') {
                $element = $this->getElement('custom_username');
                $element->addError(_('Username field cannot be empty.'));
                $isValid = false;
            }
            if (trim($data['custom_password']) == '') {
                $element = $this->getElement('custom_password');
                $element->addError(_('Password field cannot be empty.'));
                $isValid = false;
            }
        }

        return $isValid;
    }

    public function disable()
    {
        $elements = $this->getElements();
        foreach ($elements as $element) {
            if ($element->getType() != 'Zend_Form_Element_Hidden') {
                $element->setAttrib('disabled', 'disabled');
            }
        }
    }
}
