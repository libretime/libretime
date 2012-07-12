<?php
class Application_Form_SmartPlaylistCriteria extends Zend_Form_SubForm
{
    public function init()
    {
        
        $criteriaOptions = array(
            0 => "Select criteria",
            "album_title" => "Album",
            "artist_name" => "Artist",
            "bit_rate" => "Bit Rate",
            "bpm" => "Bpm",
            "comments" => "Comments",
            "composer" => "Composer",
            "conductor" => "Conductor",
            "utime" => "Date Added",
            "mtime" => "Date Modified",
            "disc_number" => "Disc Number",
            "genre" => "Genre",
            "isrc_number" => "ISRC Number",
            "label" => "Label",
            "language" => "Language",
            "length" => "Length",
            "lyricist" => "Lyricist",
            "mood" => "Mood",
            "name" => "Name",
            "orchestra" => "Orchestra",
            "radio_station_name" => "Radio Station Name",
            "rating" => "Rating",
            "sample_rate" => "Sample Rate",
            "soundcloud_id" => "Soundcloud Upload",
            "track_title" => "Title",
            "track_num" => "Track Number",
            "year" => "Year"               
        );
        
        $limitOptions = array(
            "hours" => "hours",
            "minutes" => "minutes",
            "items" => "items"
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
        for ($i = 0; $i < $numElements; $i++) {
            $criteria = new Zend_Form_Element_Select('sp_criteria_'.$i);
            $criteria->setAttrib('class', 'input_select');
            $criteria->setValue('Select criteria');
            $criteria->setDecorators(array('viewHelper'));
            $criteria->setMultiOptions($criteriaOptions);
            if ($i != 0){
                $criteria->setAttrib('disabled', 'disabled');
            }
            $this->addElement($criteria);
            
            $criteriaModifers = new Zend_Form_Element_Select('sp_criteria_modifier_'.$i);
            $criteriaModifers->setValue('Select modifier');
            $criteriaModifers->setAttrib('class', 'input_select');
            $criteriaModifers->setDecorators(array('viewHelper'));
            $criteriaModifers->setMultiOptions(array('0' => 'Select modifier'));
            if ($i != 0){
                $criteriaModifers->setAttrib('disabled', 'disabled');
            }
            $this->addElement($criteriaModifers);
        
            $criteriaValue = new Zend_Form_Element_Text('sp_criteria_value_'.$i);
            $criteriaValue->setAttrib('class', 'input_text');
            $criteriaValue->setDecorators(array('viewHelper'));
            if ($i != 0){
                $criteriaValue->setAttrib('disabled', 'disabled');
            }
            $this->addElement($criteriaValue);
        }
        
        $limit = new Zend_Form_Element_Select('sp_limit_options');
        $limit->setAttrib('class', 'input_select');
        $limit->setDecorators(array('viewHelper'));
        $limit->setMultiOptions($limitOptions);
        $this->addElement($limit);
        
        $limitValue = new Zend_Form_Element_Text('sp_limit_value');
        $limitValue->setAttrib('class', 'input_text');
        $limitValue->setLabel('Limit to');
        $limitValue->setDecorators(array('viewHelper'));
        $this->addElement($limitValue);
        
        $save = new Zend_Form_Element_Button('save_button');
        $save->setAttrib('class', 'ui-button ui-state-default right-floated');
        $save->setIgnore(true);
        $save->setLabel('Generate');
        $this->addElement($save);
    }
}