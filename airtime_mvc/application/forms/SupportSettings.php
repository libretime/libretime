<?php

require_once 'customfilters/ImageSize.php';

class Application_Form_SupportSettings extends Zend_Form
{

    public function init()
    {
        $country_list = Application_Model_Preference::GetCountryList();
        $notEmptyValidator = Application_Form_Helper_ValidationTypes::overrideNotEmptyValidator();

        $this->setDecorators(array(
            array('ViewScript', array('viewScript' => 'form/support-setting.phtml')),
            array('File', array('viewScript' => 'form/support-setting.phtml', 'placement' => false)))
        );

        //Station name
        $this->addElement('text', 'stationName', array(
            'class'      => 'input_text',
            'label'      => _('Station Name'),
            'required'   => true,
            'filters'    => array('StringTrim'),
            'validators'  => array($notEmptyValidator),
            'value' => Application_Model_Preference::GetStationName(),
            'decorators' => array(
                'ViewHelper'
            )
        ));

        // Phone number
        $this->addElement('text', 'Phone', array(
            'class'      => 'input_text',
            'label'      => _('Phone:'),
            'required'   => false,
            'filters'    => array('StringTrim'),
            'value' => Application_Model_Preference::GetPhone(),
            'decorators' => array(
                'ViewHelper'
            )
        ));

        //Email
        $this->addElement('text', 'Email', array(
            'class'      => 'input_text',
            'label'      => _('Email:'),
            'required'   => false,
            'filters'    => array('StringTrim'),
            'value' => Application_Model_Preference::GetEmail(),
            'decorators' => array(
                'ViewHelper'
            )
        ));

         // Station Web Site
        $this->addElement('text', 'StationWebSite', array(
            'label'      => _('Station Web Site:'),
            'required'   => false,
            'class'      => 'input_text',
            'value' => Application_Model_Preference::GetStationWebSite(),
            'decorators' => array(
                'ViewHelper'
            )
        ));

        // county list dropdown
        $this->addElement('select', 'Country', array(
            'label'        => _('Country:'),
            'required'    => false,
            'value'        => Application_Model_Preference::GetStationCountry(),
            'multiOptions'    => $country_list,
            'decorators' => array(
                'ViewHelper'
            )
        ));

        // Station city
        $this->addElement('text', 'City', array(
            'label'      => _('City:'),
            'required'   => false,
            'class'      => 'input_text',
            'value' => Application_Model_Preference::GetStationCity(),
            'decorators' => array(
                'ViewHelper'
            )
        ));

        // Station Description
        $description = new Zend_Form_Element_Textarea('Description');
        $description->class = 'input_text_area';
        $description->setLabel(_('Station Description:'))
                    ->setRequired(false)
                    ->setValue(Application_Model_Preference::GetStationDescription())
                    ->setDecorators(array('ViewHelper'))
                    ->setAttrib('ROWS','2')
                    ->setAttrib('COLS','58');
        $this->addElement($description);

        // Station Logo
        $upload = new Zend_Form_Element_File('Logo');
        $upload->setLabel(_('Station Logo:'))
                ->setRequired(false)
                ->setDecorators(array('File'))
                ->addValidator('Count', false, 1)
                ->addValidator('Extension', false, 'jpg,jpeg,png,gif')
                ->addFilter('ImageSize');
        $upload->setAttrib('accept', 'image/*');
        $this->addElement($upload);

            //enable support feedback
            $this->addElement('checkbox', 'SupportFeedback', array(
                'label'      => _('Send support feedback'),
                'required'   => false,
                'value' => Application_Model_Preference::GetSupportFeedback(),
                'decorators' => array(
                    'ViewHelper'
                )
            ));

            // checkbox for publicise
            $checkboxPublicise = new Zend_Form_Element_Checkbox("Publicise");
            $checkboxPublicise->setLabel(sprintf(_('Promote my station on %s'), COMPANY_SITE))
                              ->setRequired(false)
                              ->setDecorators(array('ViewHelper'))
                              ->setValue(Application_Model_Preference::GetPublicise());
            if (Application_Model_Preference::GetSupportFeedback() == '0') {
                $checkboxPublicise->setAttrib("disabled", "disabled");
            }
            $this->addElement($checkboxPublicise);

            // text area for sending detail
            $this->addElement('textarea', 'SendInfo', array(
                'class'        => 'sending_textarea',
                'required'   => false,
                'filters'    => array('StringTrim'),
                'readonly'    => true,
                'cols'     => 61,
                'rows'        => 5,
                'value' => Application_Model_Preference::GetSystemInfo(false, true),
                'decorators' => array(
                    'ViewHelper'
                )
            ));

            $privacyPolicyAnchorOpen = "<a id='link_to_privacy' href='" . PRIVACY_POLICY_URL 
                . "' onclick='window.open(this.href); return false;'>";
            // checkbox for privacy policy
            $checkboxPrivacy = new Zend_Form_Element_Checkbox("Privacy");
            $checkboxPrivacy->setLabel(
                sprintf(_('By checking this box, I agree to %s\'s %sprivacy policy%s.'),
                    COMPANY_NAME,
                    $privacyPolicyAnchorOpen,
                    "</a>"))
                ->setDecorators(array('ViewHelper'));
            $this->addElement($checkboxPrivacy);

        // submit button
        $submit = new Zend_Form_Element_Submit("submit");
        $submit->class = 'btn right-floated';
        $submit->setIgnore(true)
                ->setLabel(_("Save"))
                ->setDecorators(array('ViewHelper'));
        $this->addElement($submit);
    }

    // overriding isValid function
    public function isValid ($data)
    {
        $isValid = parent::isValid($data);

        if (isset($data["Privacy"])) {
            $checkPrivacy = $this->getElement('Privacy');
            if ($data["SupportFeedback"] == "1" && $data["Privacy"] != "1") {
                $checkPrivacy->addError(_("You have to agree to privacy policy."));
                $isValid = false;
            }
        }

        return $isValid;
    }
}
