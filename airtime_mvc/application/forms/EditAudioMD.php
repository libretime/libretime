<?php

class Application_Form_EditAudioMD extends Zend_Form
{
    public function init()
    {
        $baseUrl = Application_Common_OsPath::getBaseDir();
         // Set the method for the display form to POST
        $this->setMethod('post');

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
            'filters'    => array('StringTrim')
        ));

        // Add genre field
        $this->addElement('text', 'genre', array(
            'label'      => _('Genre:'),
            'class'      => 'input_text',
            'filters'    => array('StringTrim')
        ));

        // Add year field
        $this->addElement('text', 'year', array(
            'label'      => _('Year:'),
            'class'      => 'input_text',
            'filters'    => array('StringTrim'),
            'validators' => array(
                array('date', false, array('YYYY-MM-DD')),
                array('date', false, array('YYYY-MM')),
                array('date', false, array('YYYY'))
            )
        ));

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
        $this->addElement('submit', 'submit', array(
            'ignore'   => true,
            'class'    => 'btn',
            'label'    => _('Save'),
            'decorators' => array(
                'ViewHelper'
            )
        ));

        // Add the submit button
        $this->addElement('button', 'cancel', array(
            'ignore'   => true,
            'class'    => 'btn md-cancel',
            'label'    => _('Cancel'),
            'onclick' => 'javascript:document.location.href="'.$baseUrl.'/Library"',
            'decorators' => array(
                'ViewHelper'
            )
        ));

        $this->addDisplayGroup(array('submit', 'cancel'), 'submitButtons', array(
                'decorators' => array(
                    'FormElements',
                    'DtDdWrapper'
                    )
        ));
    }

}
