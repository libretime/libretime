<?php

class Application_Form_EditAudioMD extends Zend_Form
{
    
    public function init() {}
    
    public function startForm($p_id)
    {
        $baseUrl = Application_Common_OsPath::getBaseDir();
         // Set the method for the display form to POST
        $this->setMethod('post');

        $file_id = new Zend_Form_Element_Hidden('file_id');
        $file_id->setValue($p_id);
        $file_id->addDecorator('HtmlTag', array('tag' => 'div', 'style' => 'display:none'));
        $file_id->removeDecorator('Label');
        $file_id->setAttrib('class', 'obj_id');
        $this->addElement($file_id);

        // Add title field
        $track_title = new Zend_Form_Element_Text('track_title');
        $track_title->class = 'input_text';
        $track_title->setLabel(_('Title:'))
            ->setFilters(array('StringTrim'))
            ->setValidators(array(
                new Zend_Validate_StringLength(array('max' => 512))
            ));
        $this->addElement($track_title);

        // Add artist field
        $artist_name = new Zend_Form_Element_Text('artist_name');
        $artist_name->class = 'input_text';
        $artist_name->setLabel(_('Creator:'))
            ->setFilters(array('StringTrim'))
            ->setValidators(array(
                new Zend_Validate_StringLength(array('max' => 512))
            ));
        $this->addElement($artist_name);

        // Add album field
        $album_title = new Zend_Form_Element_Text('album_title');
        $album_title->class = 'input_text';
        $album_title->setLabel(_('Album:'))
            ->setFilters(array('StringTrim'))
            ->setValidators(array(
                new Zend_Validate_StringLength(array('max' => 512))
            ));
        $this->addElement($album_title);

        // Add track number field
        $track_number = new Zend_Form_Element('track_number');
        $track_number->class = 'input_text';
        $track_number->setLabel('Track Number:')
            ->setFilters(array('StringTrim'))
            ->setValidators(array(new Zend_Validate_Int()));
        $this->addElement($track_number);

        // Add genre field
        $genre = new Zend_Form_Element('genre');
        $genre->class = 'input_text';
        $genre->setLabel(_('Genre:'))
            ->setFilters(array('StringTrim'))
            ->setValidators(array(
                new Zend_Validate_StringLength(array('max' => 64))
            ));
        $this->addElement($genre);

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
        $label = new Zend_Form_Element('label');
        $label->class = 'input_text';
        $label->setLabel(_('Label:'))
            ->setFilters(array('StringTrim'))
            ->setValidators(array(
                new Zend_Validate_StringLength(array('max' => 512))
            ));
        $this->addElement($label);

        // Add composer field
        $composer = new Zend_Form_Element('composer');
        $composer->class = 'input_text';
        $composer->setLabel(_('Composer:'))
            ->setFilters(array('StringTrim'))
            ->setValidators(array(
                new Zend_Validate_StringLength(array('max' => 512))
            ));
        $this->addElement($composer);

        // Add conductor field
        $conductor = new Zend_Form_Element('conductor');
        $conductor->class = 'input_text';
        $conductor->setLabel(_('Conductor:'))
            ->setFilters(array('StringTrim'))
            ->setValidators(array(
                new Zend_Validate_StringLength(array('max' => 512))
            ));
        $this->addElement($conductor);

        // Add mood field
        $mood = new Zend_Form_Element('mood');
        $mood->class = 'input_text';
        $mood->setLabel(_('Mood:'))
            ->setFilters(array('StringTrim'))
            ->setValidators(array(
                new Zend_Validate_StringLength(array('max' => 64))
            ));
        $this->addElement($mood);

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
        $copyright = new Zend_Form_Element('copyright');
        $copyright->class = 'input_text';
        $copyright->setLabel(_('Copyright:'))
            ->setFilters(array('StringTrim'))
            ->setValidators(array(
                new Zend_Validate_StringLength(array('max' => 512))
            ));
        $this->addElement($copyright);

        // Add isrc number field
        $isrc_number = new Zend_Form_Element('isrc_number');
        $isrc_number->class = 'input_text';
        $isrc_number->setLabel(_('ISRC Number:'))
            ->setFilters(array('StringTrim'))
            ->setValidators(array(
                new Zend_Validate_StringLength(array('max' => 512))
            ));
        $this->addElement($isrc_number);

        // Add website field
        $info_url = new Zend_Form_Element('info_url');
        $info_url->class = 'input_text';
        $info_url->setLabel(_('Website:'))
            ->setFilters(array('StringTrim'))
            ->setValidators(array(
                new Zend_Validate_StringLength(array('max' => 512))
            ));
        $this->addElement($info_url);

        // Add language field
        $language = new Zend_Form_Element('language');
        $language->class = 'input_text';
        $language->setLabel(_('Language:'))
            ->setFilters(array('StringTrim'))
            ->setValidators(array(
                new Zend_Validate_StringLength(array('max' => 512))
            ));
        $this->addElement($language);

        // Add the submit button
        $this->addElement('button', 'editmdsave', array(
            'ignore'     => true,
            'class'      => 'btn md-save right-floated',
            'label'      => _('OK'),
            'decorators' => array(
                'ViewHelper'
            )
        ));

        // Add the submit button
        $this->addElement('button', 'editmdcancel', array(
            'ignore'   => true,
            'class'    => 'btn md-cancel right-floated',
            'label'    => _('Cancel'),
            'decorators' => array(
                'ViewHelper'
            )
        ));

        $this->addDisplayGroup(array('editmdcancel', 'editmdsave'), 'submitButtons', array(
            'decorators' => array(
                'FormElements',
                'DtDdWrapper'
                )
        ));
    }

    public function makeReadOnly()
    {
        foreach ($this as $element) {
            $element->setAttrib('readonly', 'readonly');
        }
    }

    public function removeActionButtons()
    {
        $this->removeElement('editmdsave');
        $this->removeElement('editmdcancel');
    }

}
