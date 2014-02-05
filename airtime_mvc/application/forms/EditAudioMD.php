<?php

class Application_Form_EditAudioMD extends Zend_Form
{
    
    public function init() {
    	
        $this->addElement('hidden', 'MDATA_ID', array(
            //'value' => $p_id
        ));
        // Add title field
        $this->addElement('text', 'MDATA_KEY_TITLE', array(
            'label' => _('Title:'),
            'class' => 'input_text',
            'filters' => array('StringTrim'),
        ));

        // Add artist field
        $this->addElement('text', 'MDATA_KEY_CREATOR', array(
            'label' => _('Creator:'),
            'class' => 'input_text',
            'filters' => array('StringTrim'),
        ));

        // Add album field
        $this->addElement('text', 'MDATA_KEY_SOURCE', array(
            'label' => _('Album:'),
            'class' => 'input_text',
            'filters' => array('StringTrim')
        ));

        // Add track number field
        $this->addElement('text', 'MDATA_KEY_TRACKNUMBER', array(
            'label' => _('Track:'),
            'class' => 'input_text',
            'filters' => array('StringTrim'),
        ));

        // Add genre field
        $this->addElement('text', 'MDATA_KEY_GENRE', array(
            'label' => _('Genre:'),
            'class' => 'input_text',
            'filters' => array('StringTrim')
        ));

        // Add year field
        $year = new Zend_Form_Element_Text('MDATA_KEY_YEAR');
        $year->class = 'input_text';
        $year->setLabel(_('Year:'))
            ->setFilters(array('StringTrim'))
            ->setValidators(array(
                new Zend_Validate_StringLength(array('max' => 10)),
                Application_Form_Helper_ValidationTypes::overrrideDateValidator("YYYY-MM-DD"),
                Application_Form_Helper_ValidationTypes::overrrideDateValidator("YYYY-MM"),
                Application_Form_Helper_ValidationTypes::overrrideDateValidator("YYYY")
            ));
        $this->addElement($year);

        // Add label field
        $this->addElement('text', 'MDATA_KEY_LABEL', array(
            'label' => _('Label:'),
            'class' => 'input_text',
            'filters' => array('StringTrim')
        ));

        // Add composer field
        $this->addElement('text', 'MDATA_KEY_COMPOSER', array(
            'label' => _('Composer:'),
            'class' => 'input_text',
            'filters' => array('StringTrim')
        ));

        // Add conductor field
        $this->addElement('text', 'MDATA_KEY_CONDUCTOR', array(
            'label' => _('Conductor:'),
            'class' => 'input_text',
            'filters' => array('StringTrim')
        ));

        // Add mood field
        $this->addElement('text', 'MDATA_KEY_MOOD', array(
            'label' => _('Mood:'),
            'class' => 'input_text',
            'filters' => array('StringTrim')
        ));

        // Add bmp field
        $bpm = new Zend_Form_Element_Text('MDATA_KEY_BPM');
        $bpm->class = 'input_text';
        $bpm->setLabel(_('BPM:'))
            ->setFilters(array('StringTrim'))
            ->setValidators(array(
                 new Zend_Validate_StringLength(array('min'=> 0,'max' => 8)),
                 new Zend_Validate_Digits()));
        $this->addElement($bpm);

        // Add copyright field
        $this->addElement('text', 'MDATA_KEY_COPYRIGHT', array(
            'label' => _('Copyright:'),
            'class' => 'input_text',
            'filters' => array('StringTrim')
        ));

        // Add isrc number field
        $this->addElement('text', 'MDATA_KEY_ISRC', array(
            'label' => _('ISRC Number:'),
            'class' => 'input_text',
            'filters' => array('StringTrim')
        ));

        // Add website field
        $this->addElement('text', 'MDATA_KEY_URL', array(
            'label' => _('Website:'),
            'class' => 'input_text',
            'filters' => array('StringTrim')
        ));

        // Add language field
        $this->addElement('text', 'MDATA_KEY_LANGUAGE', array(
            'label' => _('Language:'),
            'class' => 'input_text',
            'filters' => array('StringTrim')
        ));
    }
}
