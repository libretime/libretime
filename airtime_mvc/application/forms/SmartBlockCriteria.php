<?php
class Application_Form_SmartBlockCriteria extends Zend_Form_SubForm
{
    
    public function init(){
        
    }
    
    public function startForm($p_blockId)
    {
        $criteriaOptions = array(
            0 => "Select criteria",
            "album_title" => "Album",
            "bit_rate" => "Bit Rate",
            "bpm" => "Bpm",
            "comments" => "Comments",
            "composer" => "Composer",
            "conductor" => "Conductor",
            "artist_name" => "Creator",
            "disc_number" => "Disc Number",
            "genre" => "Genre",
            "isrc_number" => "ISRC",
            "label" => "Label",
            "language" => "Language",
            "mtime" => "Last Modified",
            "lptime" => "Last Played",
            "length" => "Length",
            "lyricist" => "Lyricist",
            "mood" => "Mood",
            "name" => "Name",
            "orchestra" => "Orchestra",
            "rating" => "Rating",
            "sample_rate" => "Sample Rate",
            "track_title" => "Title",
            "track_number" => "Track Number",
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
            "lptime" => "n",
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
            "rating" => "n",
            "sample_rate" => "n",
            "track_title" => "s",
            "track_number" => "n",
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
        
        // load type
        $out = CcBlockQuery::create()->findPk($p_blockId);
        if ($out->getDbType() == "static") {
            $blockType = 0;
        } else {
            $blockType = 1;
        }
        
        
        $spType = new Zend_Form_Element_Radio('sp_type');
        $spType->setLabel('Set smart playlist type:')
               ->setDecorators(array('viewHelper'))
               ->setMultiOptions(array(
                    'static' => 'Static',
                    'dynamic' => 'Dynamic'
                ))
               ->setValue($blockType);
        $this->addElement($spType);
        
        $bl = new Application_Model_Block($p_blockId);
        $storedCrit = $bl->getCriteria();
       
        /* $modRoadMap stores the number of modifier rows each
         * criteria row has. We need to know this so we display the
         * the form elements properly
         */
        $modRowMap = array();
        
        $openSmartBlockOption = false;
        if (!empty($storedCrit)) {
            $openSmartBlockOption = true;
        }
        
        $criteriaKeys = array();
        if (isset($storedCrit["crit"])) {
            $criteriaKeys = array_keys($storedCrit["crit"]);
        }
        $numElements = count($criteriaOptions);
        for ($i = 0; $i < $numElements; $i++) {
            $criteriaType = "";
            
            if (isset($criteriaKeys[$i])) {
                $critCount = count($storedCrit["crit"][$criteriaKeys[$i]]);
            } else {
                $critCount = 1;
            }
            
            $modRowMap[$i] = $critCount-1;
            
            /* Loop through all criteria with the same field
             * Ex: all criteria for 'Album'
             */
            for ($j = 0; $j < $critCount; $j++) {
                /****************** CRITERIA ***********/
                if ($j < 1) {
                    $criteria = new Zend_Form_Element_Select("sp_criteria_field_".$i);
                    $criteria->setAttrib('class', 'input_select sp_input_select')
                             ->setValue('Select criteria')
                             ->setDecorators(array('viewHelper'))
                             ->setMultiOptions($criteriaOptions);
                    if ($i != 0 && !isset($criteriaKeys[$i])) {
                        $criteria->setAttrib('disabled', 'disabled');
                    }
                    
                    if (isset($criteriaKeys[$i])) {
                        $criteriaType = $criteriaTypes[$storedCrit["crit"][$criteriaKeys[$i]][$j]["criteria"]];
                        $criteria->setValue($storedCrit["crit"][$criteriaKeys[$i]][$j]["criteria"]);
                    }
                    $this->addElement($criteria);
                    
                    /****************** MODIFIER ***********/
                    $criteriaModifers = new Zend_Form_Element_Select("sp_criteria_modifier_".$i);
                    $criteriaModifers->setValue('Select modifier')
                                     ->setAttrib('class', 'input_select sp_input_select')
                                     ->setDecorators(array('viewHelper'));
                    if ($i != 0 && !isset($criteriaKeys[$i])) {
                        $criteriaModifers->setAttrib('disabled', 'disabled');
                    }
                    if (isset($criteriaKeys[$i])) {
                        if($criteriaType == "s"){
                            $criteriaModifers->setMultiOptions($stringCriteriaOptions);
                        }else{
                            $criteriaModifers->setMultiOptions($numericCriteriaOptions);
                        }
                        $criteriaModifers->setValue($storedCrit["crit"][$criteriaKeys[$i]][$j]["modifier"]);
                    }else{
                        $criteriaModifers->setMultiOptions(array('0' => 'Select modifier'));
                    }
                    $this->addElement($criteriaModifers);
                    
                    /****************** VALUE ***********/
                    $criteriaValue = new Zend_Form_Element_Text("sp_criteria_value_".$i);
                    $criteriaValue->setAttrib('class', 'input_text sp_input_text')
                                  ->setDecorators(array('viewHelper'));
                    if ($i != 0 && !isset($criteriaKeys[$i])){
                        $criteriaValue->setAttrib('disabled', 'disabled');
                    }
                    if (isset($criteriaKeys[$i])) {
                        $criteriaValue->setValue($storedCrit["crit"][$criteriaKeys[$i]][$j]["value"]);
                    }
                    $this->addElement($criteriaValue);
                    
                    /****************** EXTRA ***********/
                    $criteriaExtra = new Zend_Form_Element_Text("sp_criteria_extra_".$i);
                    $criteriaExtra->setAttrib('class', 'input_text sp_extra_input_text')
                                  ->setDecorators(array('viewHelper'));
                    if (isset($criteriaKeys[$i]) && isset($storedCrit["crit"][$criteriaKeys[$i]][$j]["extra"])) {
                        $criteriaExtra->setValue($storedCrit["crit"][$criteriaKeys[$i]][$j]["extra"]);
                        $criteriaValue->setAttrib('class', 'input_text sp_extra_input_text');
                    }else{
                        $criteriaExtra->setAttrib('disabled', 'disabled');
                    }
                    $this->addElement($criteriaExtra);
                    
                /* This is where the additional modifier rows get defined
                 * The additional row count starts at 0 and gets appended
                 * to the parent field name
                 */
                } else if (count($storedCrit["crit"][$criteriaKeys[$i]]) > 1) {
                    $n = $j - 1;
                    $criteria = new Zend_Form_Element_Select("sp_criteria_field_".$i."_".$n);
                    $criteria->setAttrib('class', 'input_select sp_input_select sp-invisible')
                             ->setValue('Select criteria')
                             ->setDecorators(array('viewHelper'))
                             ->setMultiOptions($criteriaOptions)
                             ->setValue($storedCrit["crit"][$criteriaKeys[$i]][$j]["criteria"]);
                    $this->addElement($criteria);
                    
                    $criteriaModifers = new Zend_Form_Element_Select("sp_criteria_modifier_".$i."_".$n);
                    $criteriaModifers->setValue('Select modifier')
                                     ->setAttrib('class', 'input_select sp_input_select')
                                     ->setDecorators(array('viewHelper'));
                    if($criteriaType == "s"){
                        $criteriaModifers->setMultiOptions($stringCriteriaOptions);
                    }else{
                        $criteriaModifers->setMultiOptions($numericCriteriaOptions);
                    }
                    $criteriaModifers->setValue($storedCrit["crit"][$criteriaKeys[$i]][$j]["modifier"]);
                    $this->addElement($criteriaModifers);
                    
                    $criteriaValue = new Zend_Form_Element_Text("sp_criteria_value_".$i."_".$n);
                    $criteriaValue->setAttrib('class', 'input_text sp_input_text')
                                  ->setDecorators(array('viewHelper'))
                                  ->setValue($storedCrit["crit"][$criteriaKeys[$i]][$j]["value"]);
                    $this->addElement($criteriaValue);
                    
                    $criteriaExtra = new Zend_Form_Element_Text("sp_criteria_extra_".$i."_".$n);
                    $criteriaExtra->setAttrib('class', 'input_text sp_extra_input_text')
                                  ->setDecorators(array('viewHelper'));
                    if (isset($storedCrit["crit"][$criteriaKeys[$i]][$j]["extra"])) {
                        $criteriaExtra->setValue($storedCrit["crit"][$criteriaKeys[$i]][$j]["extra"]);
                        $criteriaValue->setAttrib('class', 'input_text sp_extra_input_text');
                    }else{
                        $criteriaExtra->setAttrib('disabled', 'disabled');
                    }
                    $this->addElement($criteriaExtra);
                }
                        
            }//for
            
        }//for
        
        $limit = new Zend_Form_Element_Select('sp_limit_options');
        $limit->setAttrib('class', 'sp_input_select')
              ->setDecorators(array('viewHelper'))
              ->setMultiOptions($limitOptions);
        if (isset($storedCrit["limit"])) {
            $limit->setValue($storedCrit["limit"]["modifier"]);
        }
        $this->addElement($limit);
        
        $limitValue = new Zend_Form_Element_Text('sp_limit_value');
        $limitValue->setAttrib('class', 'sp_input_text_limit')
                   ->setLabel('Limit to')
                   ->setDecorators(array('viewHelper'));
        $this->addElement($limitValue);
        if (isset($storedCrit["limit"])) {
            $limitValue->setValue($storedCrit["limit"]["value"]);
        }
        
        //getting block content candidate count that meets criteria
        $bl = new Application_Model_Block($p_blockId);
        $files = $bl->getListofFilesMeetCriteria();
        
        $save = new Zend_Form_Element_Button('save_button');
        $save->setAttrib('class', 'ui-button ui-state-default sp-button');
        $save->setAttrib('title', 'Save playlist');
        $save->setIgnore(true);
        $save->setLabel('Save');
        $save->setDecorators(array('viewHelper'));
        $this->addElement($save);
        
        $generate = new Zend_Form_Element_Button('generate_button');
        $generate->setAttrib('class', 'ui-button ui-state-default sp-button');
        $generate->setAttrib('title', 'Generate playlist content');
        $generate->setIgnore(true);
        $generate->setLabel('Generate');
        $generate->setDecorators(array('viewHelper'));
        $this->addElement($generate);
        
        $shuffle = new Zend_Form_Element_Button('shuffle_button');
        $shuffle->setAttrib('class', 'ui-button ui-state-default sp-button');
        $shuffle->setAttrib('title', 'Shuffle playlist content');
        $shuffle->setIgnore(true);
        $shuffle->setLabel('Shuffle');
        $shuffle->setDecorators(array('viewHelper'));
        $this->addElement($shuffle);

        $this->setDecorators(array(
                array('ViewScript', array('viewScript' => 'form/smart-block-criteria.phtml', "openOption"=> $openSmartBlockOption,
                        'criteriasLength' => count($criteriaOptions), 'poolCount' => $files['count'], 'modRowMap' => $modRowMap))
        ));
    }
    
}
