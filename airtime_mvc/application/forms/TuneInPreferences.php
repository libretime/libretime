<?php
require_once 'customvalidators/ConditionalNotEmpty.php';

class Application_Form_TuneInPreferences extends Zend_Form_SubForm
{
    public function init()
    {
        $this->setDecorators(array(
            array('ViewScript', array('viewScript' => 'form/preferences_tunein.phtml'))
        ));

        $enableTunein = new Zend_Form_Element_Checkbox("enable_tunein");
        $enableTunein->setLabel(_("Push metadata to your station on TuneIn?"));
        $enableTunein->setValue(Application_Model_Preference::getTuneinEnabled());
        $enableTunein->setAttrib("class", "block-display");
        $this->addElement($enableTunein);

        // TODO: figure out how to make this validator work
        $validator = new ConditionalNotEmpty(array(
            'enable_tunein' => 1
        ));

        $tuneinStationId = new Zend_Form_Element_Text("tunein_station_id");
        $tuneinStationId->setLabel(_("Station ID:"));
        $tuneinStationId->setValue(Application_Model_Preference::getTuneinStationId());
        $tuneinStationId->setAttrib("class", "input_text");
        $tuneinStationId->addValidator($validator);
        $this->addElement($tuneinStationId);

        $tuneinPartnerKey = new Zend_Form_Element_Text("tunein_partner_key");
        $tuneinPartnerKey->setLabel(_("Partner Key:"));
        $tuneinPartnerKey->setValue(Application_Model_Preference::getTuneinPartnerKey());
        $tuneinPartnerKey->setAttrib("class", "input_text");
        $tuneinPartnerKey->addValidator($validator);
        $this->addElement($tuneinPartnerKey);

        $tuneinPartnerId = new Zend_Form_Element_Text("tunein_partner_id");
        $tuneinPartnerId->setLabel(_("Partner Id:"));
        $tuneinPartnerId->setValue(Application_Model_Preference::getTuneinPartnerId());
        $tuneinPartnerId->setAttrib("class", "input_text");
        $tuneinPartnerId->addValidator($validator);
        $this->addElement($tuneinPartnerId);
    }
}