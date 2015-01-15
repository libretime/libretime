<?php

class Application_Form_EditAudioMD extends Zend_Form
{
    
    public function init() {}
    
    public function startForm($p_id)
    {
        $baseUrl = Application_Common_OsPath::getBaseDir();
         // Set the method for the display form to POST
        $this->setMethod('post');

        $this->addElement('hidden', 'file_id', array(
            'value' => $p_id
        ));
        // Add title field
        $this->addElement('text', 'track_title', array(
            'label'      => _('Title:'),
            'class'      => 'input_text',
            'filters'    => array('StringTrim'),
        ));

        // Add artist field
        $this->addElement('text', 'artist_name', array(
            'label'      => _('Creator:'),
            'class'      => 'input_text',
            'filters'    => array('StringTrim'),
        ));

        // Add album field
        $this->addElement('text', 'album_title', array(
            'label'      => _('Album:'),
            'class'      => 'input_text',
            'filters'    => array('StringTrim')
        ));

        // Add track number field
        $this->addElement('text', 'track_number', array(
            'label'      => _('Track:'),
            'class'      => 'input_text',
            'filters'    => array('StringTrim'),
        ));

        // Add genre field
        $this->addElement('text', 'genre', array(
            'label'      => _('Genre:'),
            'class'      => 'input_text',
            'filters'    => array('StringTrim')
        ));

        // Add year field
        $year = new Zend_Form_Element_Text('year');
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
        $this->addElement('text', 'label', array(
            'label'      => _('Label:'),
            'class'      => 'input_text',
            'filters'    => array('StringTrim')
        ));

        // Add composer field
        $this->addElement('text', 'composer', array(
            'label'      => _('Composer:'),
            'class'      => 'input_text',
            'filters'    => array('StringTrim')
        ));

        // Add conductor field
        $this->addElement('text', 'conductor', array(
            'label'      => _('Conductor:'),
            'class'      => 'input_text',
            'filters'    => array('StringTrim')
        ));

        // Add mood field
        $this->addElement('text', 'mood', array(
            'label'      => _('Mood:'),
            'class'      => 'input_text',
            'filters'    => array('StringTrim')
        ));

        // Add bmp field
        $bpm = new Zend_Form_Element_Text('bpm');
        $bpm->class = 'input_text';
        $bpm->setLabel(_('BPM:'))
            ->setFilters(array('StringTrim'))
            ->setValidators(array(
                        new Zend_Validate_StringLength(array('min'=>0,'max' => 8)),
                        new Zend_Validate_Digits()));
        $this->addElement($bpm);

        // Add copyright field
        $this->addElement('text', 'copyright', array(
            'label'      => _('Copyright:'),
            'class'      => 'input_text',
            'filters'    => array('StringTrim')
        ));

        // Add isrc number field
        $this->addElement('text', 'isrc_number', array(
            'label'      => _('ISRC Number:'),
            'class'      => 'input_text',
            'filters'    => array('StringTrim')
        ));

        // Add website field
        $this->addElement('text', 'info_url', array(
            'label'      => _('Website:'),
            'class'      => 'input_text',
            'filters'    => array('StringTrim')
        ));

        // Add language field
        $this->addElement('text', 'language', array(
            'label'      => _('Language:'),
            'class'      => 'input_text',
            'filters'    => array('StringTrim')
        ));

        // Add the submit button
        $this->addElement('button', 'editmdsave', array(
            'ignore'   => true,
            'class'    => 'btn',
            'label'    => _('Save'),
            'decorators' => array(
                'ViewHelper'
            )
        ));

        // Add the submit button
        $this->addElement('button', 'editmdcancel', array(
            'ignore'   => true,
            'class'    => 'btn md-cancel',
            'label'    => _('Cancel'),
            'decorators' => array(
                'ViewHelper'
            )
        ));

        $this->addDisplayGroup(array('editmdsave', 'editmdcancel'), 'submitButtons', array(
                'decorators' => array(
                    'FormElements',
                    'DtDdWrapper'
                    )
        ));
    }

}
