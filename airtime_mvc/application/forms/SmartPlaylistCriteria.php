<?php
class Application_Form_SmartPlaylistCriteria extends Zend_Form_SubForm
{
    public function init(){
        
    }
    public function startForm($p_playlistId)
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
            "disc_number" => "Disc Number",
            "genre" => "Genre",
            "isrc_number" => "ISRC",
            "label" => "Label",
            "language" => "Language",
            "mtime" => "Last Modified",
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
            "utime" => "Uploaded",
            "year" => "Year"               
        );
        
        $criteriaTypes = array(
            0 => "",
            "album_title" => "s",
            "artist_name" => "s",
            "bit_rate" => "n",
            "bpm" => "n",
            "comments" => "s",
            "composer" => "s",
            "conductor" => "s",
            "utime" => "n",
            "mtime" => "n",
            "disc_number" => "n",
            "genre" => "s",
            "isrc_number" => "s",
            "label" => "s",
            "language" => "s",
            "length" => "n",
            "lyricist" => "s",
            "mood" => "s",
            "name" => "s",
            "orchestra" => "s",
            "radio_station_name" => "s",
            "rating" => "n",
            "sample_rate" => "n",
            "soundcloud_id" => "n",
            "track_title" => "s",
            "track_num" => "n",
            "year" => "n"
        );
        
        $stringCriteriaOptions = array(
            "0" => "Select modifier",
            "contains" => "contains",
            "does not contain" => "does not contain",
            "is" => "is",
            "is not" => "is not",
            "starts with" => "starts with",
            "ends with" => "ends with"
        );
        
        $numericCriteriaOptions = array(
            "0" => "Select modifier",
            "is" => "is",
            "is not" => "is not",
            "is greater than" => "is greater than",
            "is less than" => "is less than",
            "is in the range" => "is in the range"
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
        
        // load criteria from db
        $c = new Criteria();
        $c->add(CcPlaylistcriteriaPeer::PLAYLIST_ID, $p_playlistId);
        $out = CcPlaylistcriteriaPeer::doSelect($c);
        
        $storedCrit = array();
        foreach ($out as $crit) {
            $criteria = $crit->getDbCriteria();
            $modifier = $crit->getDbModifier();
            $value = $crit->getDbValue();
            $extra = $crit->getDbExtra();
        
            if($criteria == "limit"){
                $storedCrit["limit"] = array("value"=>$value, "modifier"=>$modifier);
            }else{
                $storedCrit["crit"][] = array("criteria"=>$criteria, "value"=>$value, "modifier"=>$modifier, "extra"=>$extra);
            }
        }
        
        $numElements = count($criteriaOptions);
        for ($i = 0; $i < $numElements; $i++) {
            $criteriaType = "";
            $criteria = new Zend_Form_Element_Select('sp_criteria_field_'.$i);
            $criteria->setAttrib('class', 'input_select')
                     ->setValue('Select criteria')
                     ->setDecorators(array('viewHelper'))
                     ->setMultiOptions($criteriaOptions);
            if ($i != 0 && !isset($storedCrit["crit"][$i])){
                $criteria->setAttrib('disabled', 'disabled');
            }
            if (isset($storedCrit["crit"][$i])) {
                $criteriaType = $criteriaTypes[$storedCrit["crit"][$i]["criteria"]];
                $criteria->setValue($storedCrit["crit"][$i]["criteria"]);
            }
            $this->addElement($criteria);
            
            $criteriaModifers = new Zend_Form_Element_Select('sp_criteria_modifier_'.$i);
            $criteriaModifers->setValue('Select modifier')
                             ->setAttrib('class', 'input_select')
                             ->setDecorators(array('viewHelper'));
            if ($i != 0 && !isset($storedCrit["crit"][$i])){
                $criteriaModifers->setAttrib('disabled', 'disabled');
            }
            if (isset($storedCrit["crit"][$i])) {
                if($criteriaType == "s"){
                    $criteriaModifers->setMultiOptions($stringCriteriaOptions);
                }else{
                    $criteriaModifers->setMultiOptions($numericCriteriaOptions);
                }
                $criteriaModifers->setValue($storedCrit["crit"][$i]["modifier"]);
            }else{
                $criteriaModifers->setMultiOptions(array('0' => 'Select modifier'));
            }
            $this->addElement($criteriaModifers);
        
            $criteriaValue = new Zend_Form_Element_Text('sp_criteria_value_'.$i);
            $criteriaValue->setAttrib('class', 'input_text')
                          ->setDecorators(array('viewHelper'));
            if ($i != 0 && !isset($storedCrit["crit"][$i])){
                $criteriaValue->setAttrib('disabled', 'disabled');
            }
            if (isset($storedCrit["crit"][$i])) {
                $criteriaValue->setValue($storedCrit["crit"][$i]["value"]);
            }
            $this->addElement($criteriaValue);
            
            $criteriaExtra = new Zend_Form_Element_Text('sp_criteria_extra_'.$i);
            $criteriaExtra->setAttrib('class', 'input_text')
                          ->setDecorators(array('viewHelper'));
            if (isset($storedCrit["crit"][$i]["extra"])) {
                $criteriaExtra->setValue($storedCrit["crit"][$i]["extra"]);
            }else{
                $criteriaExtra->setAttrib('disabled', 'disabled');
            }
            $this->addElement($criteriaExtra);
        }
        
        $limit = new Zend_Form_Element_Select('sp_limit_options');
        $limit->setAttrib('class', 'input_select');
        $limit->setDecorators(array('viewHelper'));
        $limit->setMultiOptions($limitOptions);
        if (isset($storedCrit["limit"])) {
            $limit->setValue($storedCrit["limit"]["modifier"]);
        }else{
            $limit->setAttrib('disabled', 'disabled');
        }
        $this->addElement($limit);
        
        $limitValue = new Zend_Form_Element_Text('sp_limit_value');
        $limitValue->setAttrib('class', 'input_text');
        $limitValue->setLabel('Limit to');
        $limitValue->setDecorators(array('viewHelper'));
        $this->addElement($limitValue);
        if (isset($storedCrit["limit"])) {
            $limitValue->setValue($storedCrit["limit"]["value"]);
        }else{
            $limitValue->setAttrib('disabled', 'disabled');
        }
        
        $save = new Zend_Form_Element_Button('save_button');
        $save->setAttrib('class', 'ui-button ui-state-default right-floated');
        $save->setIgnore(true);
        $save->setLabel('Generate');
        $this->addElement($save);
    }
    
    public function loadCriteria($p_playlistId)
    {
        $c = new Criteria();
        $c->add(CcPlaylistcriteriaPeer::PLAYLIST_ID, $p_playlistId);
        $out = CcPlaylistcriteriaPeer::doSelect($c);
        
        $i = 0;
        foreach ($out as $crit) {
            $criteria = $crit->getDbCriteria();
            $modifier = $crit->getDbModifier();
            $value = $crit->getDbValue();
            $extra = $crit->getDbExtra();
            
            if($criteria == "limit"){
                Zend_Form::getElement("sp_limit_options")->setValue($modifier);
                Zend_Form::getElement("sp_limit_value")->setValue($value);
            }else{
                Zend_Form::getElement("sp_criteria_$i")->setValue($criteria);
                Zend_Form::getElement("sp_criteria_modifier_$i")->setValue($criteria);
                Zend_Form::getElement("sp_criteria_value_$i")->setValue($criteria);
                Zend_Form::getElement("sp_criteria_extra_$i")->setValue($criteria);
                $i++;
            }
        }
    }
}