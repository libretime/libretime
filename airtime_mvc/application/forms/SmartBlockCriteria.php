<?php
class Application_Form_SmartBlockCriteria extends Zend_Form_SubForm
{
    private $criteriaOptions;
    private $stringCriteriaOptions;
    private $numericCriteriaOptions;
    private $sortOptions;
    private $limitOptions;

    /* We need to know if the criteria value will be a string
     * or numeric value in order to populate the modifier
     * select list
     */
    private $criteriaTypes = array(
        0              => "",
        "album_title"  => "s",
        "bit_rate"     => "n",
        "bpm"          => "n",
        "composer"     => "s",
        "conductor"    => "s",
        "copyright"    => "s",
        "cuein"        => "n",
        "cueout"       => "n",
        "artist_name"  => "s",
        "encoded_by"   => "s",
        "utime"        => "n",
        "mtime"        => "n",
        "lptime"       => "n",
        "genre"        => "s",
        "isrc_number"  => "s",
        "label"        => "s",
        "language"     => "s",
        "length"       => "n",
        "mime"         => "s",
        "mood"         => "s",
        "owner_id"     => "s",
        "replay_gain"  => "n",
        "sample_rate"  => "n",
        "track_title"  => "s",
        "track_number" => "n",
        "info_url"     => "s",
        "year"         => "n"
    );

    private function getCriteriaOptions($option = null)
    {
        if (!isset($this->criteriaOptions)) {
            $this->criteriaOptions = array(
                0              => _("Select criteria"),
                "album_title"  => _("Album"),
                "bit_rate"     => _("Bit Rate (Kbps)"),
                "bpm"          => _("BPM"),
                "composer"     => _("Composer"),
                "conductor"    => _("Conductor"),
                "copyright"    => _("Copyright"),
                "cuein"        => _("Cue In"),
                "cueout"       => _("Cue Out"),
                "artist_name"  => _("Creator"),
                "encoded_by"   => _("Encoded By"),
                "genre"        => _("Genre"),
                "isrc_number"  => _("ISRC"),
                "label"        => _("Label"),
                "language"     => _("Language"),
                "mtime"        => _("Last Modified"),
                "lptime"       => _("Last Played"),
                "length"       => _("Length"),
                "mime"         => _("Mime"),
                "mood"         => _("Mood"),
                "owner_id"     => _("Owner"),
                "replay_gain"  => _("Replay Gain"),
                "sample_rate"  => _("Sample Rate (kHz)"),
                "track_title"  => _("Title"),
                "track_number" => _("Track Number"),
                "utime"        => _("Uploaded"),
                "info_url"     => _("Website"),
                "year"         => _("Year")
            );
        }

        if (is_null($option)) return $this->criteriaOptions;
        else return $this->criteriaOptions[$option];
    }

    private function getStringCriteriaOptions()
    {
        if (!isset($this->stringCriteriaOptions)) {
            $this->stringCriteriaOptions = array(
                "0"                => _("Select modifier"),
                "contains"         => _("contains"),
                "does not contain" => _("does not contain"),
                "is"               => _("is"),
                "is not"           => _("is not"),
                "starts with"      => _("starts with"),
                "ends with"        => _("ends with")
            );
        }
        return $this->stringCriteriaOptions;
    }

    private function getNumericCriteriaOptions()
    {
        if (!isset($this->numericCriteriaOptions)) {
            $this->numericCriteriaOptions = array(
                "0"               => _("Select modifier"),
                "is"              => _("is"),
                "is not"          => _("is not"),
                "is greater than" => _("is greater than"),
                "is less than"    => _("is less than"),
                "is in the range" => _("is in the range")
            );
        }
        return $this->numericCriteriaOptions;
    }

    private function getLimitOptions()
    {
        if (!isset($this->limitOptions)) {
            $this->limitOptions = array(
                "hours"   => _("hours"),
                "minutes" => _("minutes"),
                "items"   => _("items")
            );
        }
        return $this->limitOptions;
    }
        private function getSortOptions()
    {
        if (!isset($this->sortOptions)) {
            $this->sortOptions = array(
                "random"   => _("Randomly"),
                "newest" => _("Newest"),
                "oldest"   => _("Oldest")
            );
        }
        return $this->sortOptions;
    }


    public function init()
    {
    }
    
    /*
     * converts UTC timestamp citeria into user timezone strings.
     */
    private function convertTimestamps(&$criteria)
    {
    	$columns = array("utime", "mtime", "lptime");
    	
    	foreach ($columns as $column) {
    		
    		if (isset($criteria[$column])) {
    			
    			foreach ($criteria[$column] as &$constraint) {
    				
    				$constraint['value'] =
    				Application_Common_DateHelper::UTCStringToUserTimezoneString($constraint['value']);
    				 
    				if (isset($constraint['extra'])) {
    					$constraint['extra'] =
    					Application_Common_DateHelper::UTCStringToUserTimezoneString($constraint['extra']);
    				}
    			}
    		}
    	}
    }

    public function startForm($p_blockId, $p_isValid = false)
    {
        // load type
        $out = CcBlockQuery::create()->findPk($p_blockId);
        if ($out->getDbType() == "static") {
            $blockType = 0;
        } else {
            $blockType = 1;
        }

        $spType = new Zend_Form_Element_Radio('sp_type');
        $spType->setLabel(_('Type:'))
               ->setDecorators(array('viewHelper'))
               ->setMultiOptions(array(
                    'static' => _('Static'),
                    'dynamic' => _('Dynamic')
                ))
               ->setValue($blockType);
        $this->addElement($spType);

        $bl = new Application_Model_Block($p_blockId);
        $storedCrit = $bl->getCriteria();
        
        //need to convert criteria to be displayed in the user's timezone if there's some timestamp type.
        self::convertTimestamps($storedCrit["crit"]);

        /* $modRoadMap stores the number of same criteria
         * Ex: 3 Album titles, and 2 Track titles
         * We need to know this so we display the form elements properly
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
        $numElements = count($this->getCriteriaOptions());
        for ($i = 0; $i < $numElements; $i++) {
            $criteriaType = "";

            if (isset($criteriaKeys[$i])) {
                $critCount = count($storedCrit["crit"][$criteriaKeys[$i]]);
            } else {
                $critCount = 1;
            }

            $modRowMap[$i] = $critCount;

            /* Loop through all criteria with the same field
             * Ex: all criteria for 'Album'
             */
            for ($j = 0; $j < $critCount; $j++) {
                /****************** CRITERIA ***********/
                if ($j > 0) {
                    $invisible = ' sp-invisible';
                } else {
                    $invisible = '';
                }

                $criteria = new Zend_Form_Element_Select("sp_criteria_field_".$i."_".$j);
                $criteria->setAttrib('class', 'input_select sp_input_select'.$invisible)
                         ->setValue('Select criteria')
                         ->setDecorators(array('viewHelper'))
                         ->setMultiOptions($this->getCriteriaOptions());
                if ($i != 0 && !isset($criteriaKeys[$i])) {
                    $criteria->setAttrib('disabled', 'disabled');
                }

                if (isset($criteriaKeys[$i])) {
                    $criteriaType = $this->criteriaTypes[$storedCrit["crit"][$criteriaKeys[$i]][$j]["criteria"]];
                    $criteria->setValue($storedCrit["crit"][$criteriaKeys[$i]][$j]["criteria"]);
                }
                $this->addElement($criteria);

                /****************** MODIFIER ***********/
                $criteriaModifers = new Zend_Form_Element_Select("sp_criteria_modifier_".$i."_".$j);
                $criteriaModifers->setValue('Select modifier')
                                 ->setAttrib('class', 'input_select sp_input_select')
                                 ->setDecorators(array('viewHelper'));
                if ($i != 0 && !isset($criteriaKeys[$i])) {
                    $criteriaModifers->setAttrib('disabled', 'disabled');
                }
                if (isset($criteriaKeys[$i])) {
                    if ($criteriaType == "s") {
                        $criteriaModifers->setMultiOptions($this->getStringCriteriaOptions());
                    } else {
                        $criteriaModifers->setMultiOptions($this->getNumericCriteriaOptions());
                    }
                    $criteriaModifers->setValue($storedCrit["crit"][$criteriaKeys[$i]][$j]["modifier"]);
                } else {
                    $criteriaModifers->setMultiOptions(array('0' => _('Select modifier')));
                }
                $this->addElement($criteriaModifers);

                /****************** VALUE ***********/
                $criteriaValue = new Zend_Form_Element_Text("sp_criteria_value_".$i."_".$j);
                $criteriaValue->setAttrib('class', 'input_text sp_input_text')
                              ->setDecorators(array('viewHelper'));
                if ($i != 0 && !isset($criteriaKeys[$i])) {
                    $criteriaValue->setAttrib('disabled', 'disabled');
                }
                if (isset($criteriaKeys[$i])) {
                    $criteriaValue->setValue($storedCrit["crit"][$criteriaKeys[$i]][$j]["value"]);
                }
                $this->addElement($criteriaValue);

                /****************** EXTRA ***********/
                $criteriaExtra = new Zend_Form_Element_Text("sp_criteria_extra_".$i."_".$j);
                $criteriaExtra->setAttrib('class', 'input_text sp_extra_input_text')
                              ->setDecorators(array('viewHelper'));
                if (isset($criteriaKeys[$i]) && isset($storedCrit["crit"][$criteriaKeys[$i]][$j]["extra"])) {
                    $criteriaExtra->setValue($storedCrit["crit"][$criteriaKeys[$i]][$j]["extra"]);
                    $criteriaValue->setAttrib('class', 'input_text sp_extra_input_text');
                } else {
                    $criteriaExtra->setAttrib('disabled', 'disabled');
                }
                $this->addElement($criteriaExtra);

            }//for

        }//for

        $repeatTracks = new Zend_Form_Element_Checkbox('sp_repeat_tracks');
        $repeatTracks->setDecorators(array('viewHelper'))
                     ->setLabel(_('Allow Repeated Tracks:'));
        if (isset($storedCrit["repeat_tracks"])) {
                $repeatTracks->setChecked($storedCrit["repeat_tracks"]["value"] == 1?true:false);
        }
        $this->addElement($repeatTracks);

        $sort = new Zend_Form_Element_Select('sp_sort_options');
        $sort->setAttrib('class', 'sp_input_select')
              ->setDecorators(array('viewHelper'))
              ->setLabel(_("Sort Tracks:"))
              ->setMultiOptions($this->getSortOptions());
        if (isset($storedCrit["sort"])) {
            $sort->setValue($storedCrit["sort"]["value"]);
        }
        $this->addElement($sort);
        
        $limit = new Zend_Form_Element_Select('sp_limit_options');
        $limit->setAttrib('class', 'sp_input_select')
              ->setDecorators(array('viewHelper'))
              ->setMultiOptions($this->getLimitOptions());
        if (isset($storedCrit["limit"])) {
            $limit->setValue($storedCrit["limit"]["modifier"]);
        }
        $this->addElement($limit);

        $limitValue = new Zend_Form_Element_Text('sp_limit_value');
        $limitValue->setAttrib('class', 'sp_input_text_limit')
                   ->setLabel(_('Limit to:'))
                   ->setDecorators(array('viewHelper'));
        $this->addElement($limitValue);
        if (isset($storedCrit["limit"])) {
            $limitValue->setValue($storedCrit["limit"]["value"]);
        } else {
            // setting default to 1 hour
            $limitValue->setValue(1);
        }

        //getting block content candidate count that meets criteria
        $bl = new Application_Model_Block($p_blockId);
        if ($p_isValid) {
            $files = $bl->getListofFilesMeetCriteria();
            $showPoolCount = true;
        } else {
            $files = null;
            $showPoolCount = false;
        }

        $generate = new Zend_Form_Element_Button('generate_button');
        $generate->setAttrib('class', 'sp-button btn');
        $generate->setAttrib('title', _('Generate playlist content and save criteria'));
        $generate->setIgnore(true);
        $generate->setLabel(_('Generate'));
        $generate->setDecorators(array('viewHelper'));
        $this->addElement($generate);

        $shuffle = new Zend_Form_Element_Button('shuffle_button');
        $shuffle->setAttrib('class', 'sp-button btn');
        $shuffle->setAttrib('title', _('Shuffle playlist content'));
        $shuffle->setIgnore(true);
        $shuffle->setLabel(_('Shuffle'));
        $shuffle->setDecorators(array('viewHelper'));
        $this->addElement($shuffle);

        $this->setDecorators(array(
                array('ViewScript', array('viewScript' => 'form/smart-block-criteria.phtml', "openOption"=> $openSmartBlockOption,
                        'criteriasLength' => count($this->getCriteriaOptions()), 'poolCount' => $files['count'], 'modRowMap' => $modRowMap,
                        'showPoolCount' => $showPoolCount))
        ));
    }

    public function preValidation($params)
    {
        $data = Application_Model_Block::organizeSmartPlaylistCriteria($params['data']);
        // add elelments that needs to be added
        // set multioption for modifier according to criteria_field
        $modRowMap = array();
        if (!isset($data['criteria'])) {
            return $data;
        }

        foreach ($data['criteria'] as $critKey=>$d) {
            $count = 1;
            foreach ($d as $modKey=>$modInfo) {
                if ($modKey == 0) {
                    $eleCrit = $this->getElement("sp_criteria_field_".$critKey."_".$modKey);
                    $eleCrit->setValue($this->getCriteriaOptions($modInfo['sp_criteria_field']));
                    $eleCrit->setAttrib("disabled", null);

                    $eleMod = $this->getElement("sp_criteria_modifier_".$critKey."_".$modKey);
                    $criteriaType = $this->criteriaTypes[$modInfo['sp_criteria_field']];
                    if ($criteriaType == "s") {
                        $eleMod->setMultiOptions($this->getStringCriteriaOptions());
                    } elseif ($criteriaType == "n") {
                        $eleMod->setMultiOptions($this->getNumericCriteriaOptions());
                    } else {
                        $eleMod->setMultiOptions(array('0' => _('Select modifier')));
                    }
                    $eleMod->setValue($modInfo['sp_criteria_modifier']);
                    $eleMod->setAttrib("disabled", null);

                    $eleValue = $this->getElement("sp_criteria_value_".$critKey."_".$modKey);
                    $eleValue->setValue($modInfo['sp_criteria_value']);
                    $eleValue->setAttrib("disabled", null);

                    if (isset($modInfo['sp_criteria_extra'])) {
                        $eleExtra = $this->getElement("sp_criteria_extra_".$critKey."_".$modKey);
                        $eleExtra->setValue($modInfo['sp_criteria_extra']);
                        $eleValue->setAttrib('class', 'input_text sp_extra_input_text');
                        $eleExtra->setAttrib("disabled", null);
                    }

                } else {
                    $criteria = new Zend_Form_Element_Select("sp_criteria_field_".$critKey."_".$modKey);
                    $criteria->setAttrib('class', 'input_select sp_input_select sp-invisible')
                    ->setValue('Select criteria')
                    ->setDecorators(array('viewHelper'))
                    ->setMultiOptions($this->getCriteriaOptions());

                    $criteriaType = $this->criteriaTypes[$modInfo['sp_criteria_field']];
                    $criteria->setValue($this->getCriteriaOptions($modInfo['sp_criteria_field']));
                    $this->addElement($criteria);

                    /****************** MODIFIER ***********/
                    $criteriaModifers = new Zend_Form_Element_Select("sp_criteria_modifier_".$critKey."_".$modKey);
                    $criteriaModifers->setValue('Select modifier')
                    ->setAttrib('class', 'input_select sp_input_select')
                    ->setDecorators(array('viewHelper'));

                    if ($criteriaType == "s") {
                        $criteriaModifers->setMultiOptions($this->getStringCriteriaOptions());
                    } elseif ($criteriaType == "n") {
                        $criteriaModifers->setMultiOptions($this->getNumericCriteriaOptions());
                    } else {
                        $criteriaModifers->setMultiOptions(array('0' => _('Select modifier')));
                    }
                    $criteriaModifers->setValue($modInfo['sp_criteria_modifier']);
                    $this->addElement($criteriaModifers);

                    /****************** VALUE ***********/
                    $criteriaValue = new Zend_Form_Element_Text("sp_criteria_value_".$critKey."_".$modKey);
                    $criteriaValue->setAttrib('class', 'input_text sp_input_text')
                    ->setDecorators(array('viewHelper'));
                    $criteriaValue->setValue($modInfo['sp_criteria_value']);
                    $this->addElement($criteriaValue);

                    /****************** EXTRA ***********/
                    $criteriaExtra = new Zend_Form_Element_Text("sp_criteria_extra_".$critKey."_".$modKey);
                    $criteriaExtra->setAttrib('class', 'input_text sp_extra_input_text')
                    ->setDecorators(array('viewHelper'));
                    if (isset($modInfo['sp_criteria_extra'])) {
                        $criteriaExtra->setValue($modInfo['sp_criteria_extra']);
                        $criteriaValue->setAttrib('class', 'input_text sp_extra_input_text');
                    } else {
                        $criteriaExtra->setAttrib('disabled', 'disabled');
                    }
                    $this->addElement($criteriaExtra);
                    $count++;
                }
            }
            $modRowMap[$critKey] = $count;
        }

        $decorator = $this->getDecorator("ViewScript");
        $existingModRow = $decorator->getOption("modRowMap");
        foreach ($modRowMap as $key=>$v) {
            $existingModRow[$key] = $v;
        }
        $decorator->setOption("modRowMap", $existingModRow);

        // reconstruct the params['criteria'] so we can populate the form
        $formData = array();
        foreach ($params['data'] as $ele) {
            $formData[$ele['name']] = $ele['value'];
        }

        $this->populate($formData);

        return $data;
    }

    public function isValid($params)
    {
        $isValid = true;
        $data = $this->preValidation($params);
        $criteria2PeerMap = array(
            0 => "Select criteria",
            "album_title" => "DbAlbumTitle",
            "artist_name" => "DbArtistName",
            "bit_rate" => "DbBitRate",
            "bpm" => "DbBpm",
            "composer" => "DbComposer",
            "conductor" => "DbConductor",
            "copyright" => "DbCopyright",
            "cuein" => "DbCuein",
            "cueout" => "DbCueout",
            "encoded_by" => "DbEncodedBy",
            "utime" => "DbUtime",
            "mtime" => "DbMtime",
            "lptime" => "DbLPtime",
            "genre" => "DbGenre",
            "info_url" => "DbInfoUrl",
            "isrc_number" => "DbIsrcNumber",
            "label" => "DbLabel",
            "language" => "DbLanguage",
            "length" => "DbLength",
            "mime" => "DbMime",
            "mood" => "DbMood",
            "owner_id" => "DbOwnerId",
            "replay_gain" => "DbReplayGain",
            "sample_rate" => "DbSampleRate",
            "track_title" => "DbTrackTitle",
            "track_number" => "DbTrackNumber",
            "year" => "DbYear"
        );

        // things we need to check
        // 1. limit value shouldn't be empty and has upperbound of 24 hrs
        // 2. sp_criteria or sp_criteria_modifier shouldn't be 0
        // 3. validate formate according to DB column type
        $multiplier = 1;
        $result = 0;

        // validation start
        if ($data['etc']['sp_limit_options'] == 'hours') {
            $multiplier = 60;
        }
        if ($data['etc']['sp_limit_options'] == 'hours' || $data['etc']['sp_limit_options'] == 'mins') {
            $element = $this->getElement("sp_limit_value");
            if ($data['etc']['sp_limit_value'] == "" || floatval($data['etc']['sp_limit_value']) <= 0) {
                $element->addError(_("Limit cannot be empty or smaller than 0"));
                $isValid = false;
            } else {
                $mins = floatval($data['etc']['sp_limit_value']) * $multiplier;
                if ($mins > 1440) {
                    $element->addError(_("Limit cannot be more than 24 hrs"));
                    $isValid = false;
                }
            }
        } else {
            $element = $this->getElement("sp_limit_value");
            if ($data['etc']['sp_limit_value'] == "" || floatval($data['etc']['sp_limit_value']) <= 0) {
                $element->addError(_("Limit cannot be empty or smaller than 0"));
                $isValid = false;
            } elseif (!ctype_digit($data['etc']['sp_limit_value'])) {
                $element->addError(_("The value should be an integer"));
                $isValid = false;
            } elseif (intval($data['etc']['sp_limit_value']) > 500) {
                $element->addError(_("500 is the max item limit value you can set"));
                $isValid = false;
            }
        }

        if (isset($data['criteria'])) {
            foreach ($data['criteria'] as $rowKey=>$row) {
                foreach ($row as $key=>$d) {
                    $element = $this->getElement("sp_criteria_field_".$rowKey."_".$key);
                    // check for not selected select box
                    if ($d['sp_criteria_field'] == "0" || $d['sp_criteria_modifier'] == "0") {
                        $element->addError(_("You must select Criteria and Modifier"));
                        $isValid = false;
                    } else {
                        $column = CcFilesPeer::getTableMap()->getColumnByPhpName($criteria2PeerMap[$d['sp_criteria_field']]);
                        // validation on type of column
                        if (in_array($d['sp_criteria_field'], array('length', 'cuein', 'cueout'))) {
                            if (!preg_match("/^(\d{2}):(\d{2}):(\d{2})/", $d['sp_criteria_value'])) {
                                $element->addError(_("'Length' should be in '00:00:00' format"));
                                $isValid = false;
                            }
                        } elseif ($column->getType() == PropelColumnTypes::TIMESTAMP) {
                            if (!preg_match("/(\d{4})-(\d{2})-(\d{2})/", $d['sp_criteria_value'])) {
                                $element->addError(_("The value should be in timestamp format (e.g. 0000-00-00 or 0000-00-00 00:00:00)"));
                                $isValid = false;
                            } else {
                                $result = Application_Common_DateHelper::checkDateTimeRangeForSQL($d['sp_criteria_value']);
                                if (!$result["success"]) {
                                    // check for if it is in valid range( 1753-01-01 ~ 12/31/9999 )
                                    $element->addError($result["errMsg"]);
                                    $isValid = false;
                                }
                            }

                            if (isset($d['sp_criteria_extra'])) {
                                if (!preg_match("/(\d{4})-(\d{2})-(\d{2})/", $d['sp_criteria_extra'])) {
                                    $element->addError(_("The value should be in timestamp format (e.g. 0000-00-00 or 0000-00-00 00:00:00)"));
                                    $isValid = false;
                                } else {
                                    $result = Application_Common_DateHelper::checkDateTimeRangeForSQL($d['sp_criteria_extra']);
                                    if (!$result["success"]) {
                                        // check for if it is in valid range( 1753-01-01 ~ 12/31/9999 )
                                        $element->addError($result["errMsg"]);
                                        $isValid = false;
                                    }
                                }
                            }
                        } elseif ($column->getType() == PropelColumnTypes::INTEGER &&
                            $d['sp_criteria_field'] != 'owner_id') {
                            if (!is_numeric($d['sp_criteria_value'])) {
                                $element->addError(_("The value has to be numeric"));
                                $isValid = false;
                            }
                            // length check
                            if ($d['sp_criteria_value'] >= pow(2,31)) {
                                $element->addError(_("The value should be less then 2147483648"));
                                $isValid = false;
                            }
                        } elseif ($column->getType() == PropelColumnTypes::VARCHAR) {
                            if (strlen($d['sp_criteria_value']) > $column->getSize()) {
                                $element->addError(sprintf(_("The value should be less than %s characters"), $column->getSize()));
                                $isValid = false;
                            }
                        }
                    }

                    if ($d['sp_criteria_value'] == "") {
                        $element->addError(_("Value cannot be empty"));
                        $isValid = false;
                    }
                }//end foreach
            }//for loop
        }//if

        return $isValid;
    }
}
