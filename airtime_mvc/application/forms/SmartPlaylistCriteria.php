<?php
class Application_Form_SmartPlaylistCriteria extends Zend_Form_SubForm
{
    public function init()
    {
        
        $criteriaOptions = array(
            0 => "Select criteria",
            "track_title" => "Title",
            "artist_name" => "Artist",
            "bit_rate" => "Bit Rate",
            "sample_rate" => "Sample Rate",
            "length" => "Length",
            "album_title" => "Album",
            "genre" => "Genre",
            "year" => "Year",
            "track_num" => "Track Number",
            "bmp" => "Bpm",
            "rating" => "Rating",
            "disc_number" => "Disc Number",
            "mood" => "Mood",
            "label" => "Label",
            "composer" => "Composer",
            "lyricist" => "Lyricist",
            "name" => "Name",
            "isrc_number" => "ISRC Number",
            "language" => "Language",
            "utime" => "Date Added",
            "mtime" => "Date Modified",
            "comments" => "Comments",
            "orchestra" => "Orchestra",
            "composer" => "Composer",
            "conductor" => "Conductor",
            "radio_station_name" => "Radio Station Name",
            "soundcloud_id" => "Soundcloud Upload"    
        );
        
        $stringCriteriaOptions = array(
            0 => "Select modifier",
            "contains" => "contains",
            "does not contain" => "does not contain",
            "is" => "is",
            "is not" => "is not",
            "starts with" => "starts with",
            "ends with" => "ends with"
        );
        
        $numericCriteriaOptions = array(
            0 => "Select modifier",
            "is" => "is",
            "is not" => "is not",
            "is greater than" => "is greater than",
            "is less than" => "is less than",
            "is in the range" => "is in the range"
        );
        
        $limitOptions = array(
            "hours",
            "minutes",
            "items"
        );

        $this->setDecorators(array(
        array('ViewScript', array('viewScript' => 'form/smart-playlist-criteria.phtml',
              'criteriasLength' => count($criteriaOptions)))
        ));
        
        $spType = new Zend_Form_Element_Radio('sp_type');
        $spType->setLabel('Set smart playlist type:');
        $spType->setDecorators(array('viewHelper'));
        $spType->setMultiOptions(array(
            'static' => 'Static',
            'dynamic' => 'Dynamic'
        ));
        $spType->setValue('Static');
        $this->addElement($spType);
        
        $numElements = count($criteriaOptions);
        for($i = 1; $i <= $numElements; $i++) {
            $criteria = new Zend_Form_Element_Select('sp_criteria_'.$i);
            $criteria->setAttrib('class', 'input_select');
            $criteria->setValue(0);
            $criteria->setDecorators(array('viewHelper'));
            $criteria->setMultiOptions($criteriaOptions);
            $this->addElement($criteria);
            
            $criteriaModifers = new Zend_Form_Element_Select('sp_criteria_modifier_'.$i);
            $criteriaModifers->setValue(0);
            $criteriaModifers->setAttrib('class', 'input_select');
            $criteriaModifers->setDecorators(array('viewHelper'));
            $criteriaModifers->setMultiOptions($stringCriteriaOptions);
            $this->addElement($criteriaModifers);
        
            $criteriaValue = new Zend_Form_Element_Text('sp_criteria_value_'.$i);
            $criteriaValue->setAttrib('class', 'input_text');
            $criteriaValue->setDecorators(array('viewHelper'));
            $this->addElement($criteriaValue);
        }
        
        $limitCheck = new Zend_Form_Element_Checkbox('sp_limit_check');
        $limitCheck->setLabel('Limit to');
        $limitCheck->setDecorators(array('viewHelper'));
        $limitCheck->setValue(true);
        $this->addElement($limitCheck);
        
        $limit = new Zend_Form_Element_Select('sp_limit_options');
        $limit->setAttrib('class', 'input_select');
        $limit->setDecorators(array('viewHelper'));
        $limit->setMultiOptions($limitOptions);
        $this->addElement($limit);
        
        $limitValue = new Zend_Form_Element_Text('sp_limit_value');
        $limitValue->setAttrib('class', 'input_text');
        $limitValue->setDecorators(array('viewHelper'));
        $this->addElement($limitValue);
        
        $save = new Zend_Form_Element_Button('save_button');
        $save->setAttrib('class', 'ui-button ui-state-default right-floated');
        $save->setIgnore(true);
        $save->setLabel('Generate');
        $this->addElement($save);
    }
}