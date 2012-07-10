<?php
class Application_Form_SmartPlaylistCriteria extends Zend_Form_SubForm
{
    public function init()
    {
        //temporary solution
        //get criteria ids
        $ids = array(1,2,3);
        
        $this->setDecorators(array(
            array('ViewScript', array('viewScript' => 'form/smart-playlist-criteria.phtml', 'ids' => $ids))
        ));
        
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
            "contains",
            "does not contain",
            "is",
            "is not",
            "starts with",
            "ends with"
        );
        
        $numericCriteriaOptions = array(
            "is",
            "is not",
            "is greater than",
            "is less than",
            "is in the range"
        );
        
        $limitOptions = array(
            "hours",
            "minutes",
            "items"
        );
        
        $spType = new Zend_Form_Element_Radio('sp_type');
        $spType->setLabel('Set smart playlist type:');
        $spType->setDecorators(array('viewHelper'));
        $spType->setMultiOptions(array(
            'Static',
            'Dynamic'
        ));
        $spType->setValue('Static');
        $this->addElement($spType);
        
        foreach($ids as $id) {
            $criteria = new Zend_Form_Element_Select('sp_criteria_'.$id);
            $criteria->setAttrib('id', $id);
            $criteria->setAttrib('class', 'input_select');
            $criteria->setDecorators(array('viewHelper'));
            $criteria->setMultiOptions($criteriaOptions);
            $this->addElement($criteria);
        }
        
        foreach($ids as $id) {
            $criteriaOptions = new Zend_Form_Element_Select('sp_criteria_options_'.$id);
            $criteriaOptions->setAttrib('id', $id);
            $criteriaOptions->setAttrib('class', 'input_select');
            $criteriaOptions->setDecorators(array('viewHelper'));
            $criteriaOptions->setMultiOptions($stringCriteriaOptions);
            $this->addElement($criteriaOptions);
        }
        
        foreach($ids as $id) {
            $criteriaValue = new Zend_Form_Element_Text('sp_criteria_value_'.$id);
            $criteriaValue->setAttrib('id', $id);
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
    }
}