<?php

class Application_Form_SmartBlockCriteria extends Zend_Form_SubForm
{
    private $timePeriodCriteriaOptions;
    private $sortOptions;
    private $limitOptions;
    private $trackTypeOptions;

    private function getTimePeriodCriteriaOptions()
    {
        if (!isset($this->timePeriodCriteriaOptions)) {
            $this->timePeriodCriteriaOptions = [
                '0' => _('Select unit of time'),
                'minute' => _('minute(s)'),
                'hour' => _('hour(s)'),
                'day' => _('day(s)'),
                'week' => _('week(s)'),
                'month' => _('month(s)'),
                'year' => _('year(s)'),
            ];
        }

        return $this->timePeriodCriteriaOptions;
    }

    private function getLimitOptions()
    {
        if (!isset($this->limitOptions)) {
            $this->limitOptions = [
                'hours' => _('hours'),
                'minutes' => _('minutes'),
                'items' => _('items'),
                'remaining' => _('time remaining in show'),
            ];
        }

        return $this->limitOptions;
    }

    private function getSortOptions()
    {
        if (!isset($this->sortOptions)) {
            $this->sortOptions = [
                'random' => _('Randomly'),
                'newest' => _('Newest'),
                'oldest' => _('Oldest'),
                'mostrecentplay' => _('Most recently played'),
                'leastrecentplay' => _('Least recently played'),
            ];
        }

        return $this->sortOptions;
    }

    private function getTracktypeOptions()
    {
        if (!isset($this->trackTypeOptions)) {
            $tracktypes = Application_Model_Tracktype::getTracktypes();
            $names[] = _('Select Track Type');
            foreach ($tracktypes as $arr => $a) {
                $names[$a['id']] = $tracktypes[$arr]['type_name'];
            }
        }

        return $names;
    }

    public function init() {}

    // converts UTC timestamp citeria into user timezone strings.
    private function convertTimestamps(&$criteria)
    {
        $columns = ['utime', 'mtime', 'lptime'];

        foreach ($columns as $column) {
            if (isset($criteria[$column])) {
                foreach ($criteria[$column] as &$constraint) {
                    // convert to appropriate timezone timestamps only if the modifier is not a relative time
                    if (!in_array($constraint['modifier'], ['before', 'after', 'between'])) {
                        $constraint['value'] = Application_Common_DateHelper::UTCStringToUserTimezoneString($constraint['value']);
                        if (isset($constraint['extra'])) {
                            $constraint['extra'] = Application_Common_DateHelper::UTCStringToUserTimezoneString($constraint['extra']);
                        }
                    }
                }
            }
        }
    }

    /*
     * This function takes a blockID as param and creates the data structure for the form displayed with the view
     * smart-block-criteria.phtml
     *
     * A description of the dataflow. First it loads the block and determines if it is a static or dynamic smartblock.
     * Next it adds a radio selector for static or dynamic type.
     * Then it loads the criteria via the getCriteria() function, which returns an array for each criteria.
     *
     *
     */

    public function startForm($p_blockId, $p_isValid = false)
    {
        // load type
        $out = CcBlockQuery::create()->findPk($p_blockId);
        if ($out->getDbType() == 'dynamic') {
            $blockType = 0;
        } else {
            $blockType = 1;
        }

        $spType = new Zend_Form_Element_Radio('sp_type');
        $spType->setLabel(_('Type:'))
            ->setDecorators(['viewHelper'])
            ->setMultiOptions([
                'dynamic' => _('Dynamic'),
                'static' => _('Static'),
            ])
            ->setValue($blockType);
        $this->addElement($spType);

        $bl = new Application_Model_Block($p_blockId);
        $storedCrit = $bl->getCriteriaGrouped();
        Logging::info($storedCrit);

        // need to convert criteria to be displayed in the user's timezone if there's some timestamp type.
        self::convertTimestamps($storedCrit['crit']);

        /* $modRoadMap stores the number of same criteria
         * Ex: 3 Album titles, and 2 Track titles
         * We need to know this so we display the form elements properly
         */
        $modRowMap = [];

        $openSmartBlockOption = false;
        if (!empty($storedCrit)) {
            $openSmartBlockOption = true;
        }

        // this returns a number indexed array for each criteria found in the database
        $criteriaKeys = [];
        if (isset($storedCrit['crit'])) {
            $criteriaKeys = array_keys($storedCrit['crit']);
        }
        // the way the everything is currently built it setups 25 smartblock criteria forms and then disables them
        // but this creates 29 elements
        $numElements = count(BlockCriteria::displayCriteria());
        // loop through once for each potential criteria option ie album, composer, track
        // criteria from different groups are separated already by the getCriteriaGrouped call

        for ($i = 0; $i < $numElements; ++$i) {
            $criteriaType = '';

            // if there is a criteria found then count the number of rows for this specific criteria ie > 1 track title
            // need to refactor this to maintain separation based upon criteria grouping
            if (isset($criteriaKeys[$i])) {
                // Logging::info($criteriaKeys[$i]);
                Logging::info($storedCrit['crit'][$criteriaKeys[$i]]);
                $critCount = count($storedCrit['crit'][$criteriaKeys[$i]]);
            } else {
                $critCount = 1;
            }
            // the challenge is that we need to increment the element for a new group
            // within the same criteria but not the reference point i in the array
            // and for these secondary groups they will have a differe$storedCrit["crit"][$criteriaKeys[$i]]nt j reference point
            // store the number of items with the same key in the ModRowMap
            $modRowMap[$i] = $critCount;

            /* Loop through all criteria with the same field
             * Ex: all criteria for 'Album'
             */
            for ($j = 0; $j < $critCount; ++$j) {
                // CRITERIA
                // hide the criteria drop down select on any rows after the first
                if ($j > 0) {
                    $invisible = ' sp-invisible';
                } else {
                    $invisible = '';
                }

                $criteria = new Zend_Form_Element_Select('sp_criteria_field_' . $i . '_' . $j);
                $criteria->setAttrib('class', 'input_select sp_input_select' . $invisible)
                    ->setValue('Select criteria')
                    ->setDecorators(['viewHelper'])
                    ->setMultiOptions(BlockCriteria::displayCriteria());
                // if this isn't the first criteria and there isn't an entry for it already disable it
                if ($i != 0 && !isset($criteriaKeys[$i])) {
                    $criteria->setAttrib('disabled', 'disabled');
                }
                // add the numbering to the form ie the i loop for each specific criteria and
                // the j loop starts at 0 and grows for each item matching the same criteria
                // look up the criteria type using the criteriaTypes function from above based upon the criteria value
                if (isset($criteriaKeys[$i])) {
                    $bCriteria = BlockCriteria::get($storedCrit['crit'][$criteriaKeys[$i]][$j]['criteria']);
                    $criteriaType = $bCriteria->type;
                    $criteria->setValue($bCriteria->key);
                }
                $this->addElement($criteria);

                // MODIFIER
                // every element has an optional modifier dropdown select

                $criteriaModifers = new Zend_Form_Element_Select('sp_criteria_modifier_' . $i . '_' . $j);
                $criteriaModifers->setValue('Select modifier')
                    ->setAttrib('class', 'input_select sp_input_select')
                    ->setDecorators(['viewHelper']);
                if ($i != 0 && !isset($criteriaKeys[$i])) {
                    $criteriaModifers->setAttrib('disabled', 'disabled');
                }
                // determine the modifier based upon criteria type which is looked up based upon an array
                if (isset($criteriaKeys[$i])) {
                    $criteriaModifers->setMultiOptions($bCriteria->displayModifiers());
                    $criteriaModifers->setValue($storedCrit['crit'][$criteriaKeys[$i]][$j]['modifier']);
                } else {
                    $criteriaModifers->setMultiOptions(CriteriaModifier::mapToDisplay([]));
                }
                $this->addElement($criteriaModifers);

                // VALUE
                // The challenge here is that datetime
                if (isset($criteriaKeys[$i])) {
                    $modifierTest = (string) $storedCrit['crit'][$criteriaKeys[$i]][$j]['modifier'];
                    if (
                        isset($criteriaType) && $criteriaType == ModifierType::TRACK_TYPE
                        && preg_match('/is|is not/', $modifierTest) == 1
                    ) {
                        $criteriaValue = new Zend_Form_Element_Select('sp_criteria_value_' . $i . '_' . $j);
                        $criteriaValue->setAttrib('class', 'input_select sp_input_select')->setDecorators(['viewHelper']);

                        if (isset($criteriaKeys[$i])) {  // do if $relativeTT above
                            $criteriaValue->setAttrib('enabled', 'enabled');
                        } else {
                            $criteriaValue->setAttrib('disabled', 'disabled');
                        }
                    } else {
                        $criteriaValue = new Zend_Form_Element_Text('sp_criteria_value_' . $i . '_' . $j);
                        $criteriaValue->setAttrib('class', 'input_text sp_input_text')->setDecorators(['viewHelper']);
                        if ($i != 0 && !isset($criteriaKeys[$i])) {
                            $criteriaValue->setAttrib('disabled', 'disabled');
                        }
                    }
                } else {
                    $criteriaValue = new Zend_Form_Element_Text('sp_criteria_value_' . $i . '_' . $j);
                    $criteriaValue->setAttrib('class', 'input_text sp_input_text')->setDecorators(['viewHelper']);
                    if ($i != 0 && !isset($criteriaKeys[$i])) {
                        $criteriaValue->setAttrib('disabled', 'disabled');
                    }
                }
                if (isset($criteriaKeys[$i])) {
                    // Need to parse relative dates in a special way to populate select box down below
                    // this is used below to test whether the datetime select should be shown or hidden
                    $relativeDateTime = false;
                    $modifierTest = (string) $storedCrit['crit'][$criteriaKeys[$i]][$j]['modifier'];
                    if (
                        isset($criteriaType) && $criteriaType == ModifierType::DATE
                        && preg_match('/before|after|between/', $modifierTest) == 1
                    ) {
                        // set relativeDatetime boolean to true so that the datetime select is displayed below
                        $relativeDateTime = true;
                        $criteriaValue->setValue(filter_var($storedCrit['crit'][$criteriaKeys[$i]][$j]['value'], FILTER_SANITIZE_NUMBER_INT));
                    } elseif (
                        isset($criteriaType) && $criteriaType == ModifierType::TRACK_TYPE
                        && preg_match('/is|is not/', $modifierTest) == 1
                    ) {
                        // set relativeDatetime boolean to true so that the datetime select is displayed below
                        $relativeDateTime = false;

                        $tracktypeSelectValue = preg_replace('/[0-9]+/', '', $storedCrit['crit'][$criteriaKeys[$i]][$j]['value']);
                        $tracktypeSelectValue = trim(preg_replace('/\W\w+\s*(\W*)$/', '$1', $tracktypeSelectValue));
                        $criteriaValue->setMultiOptions($this->getTracktypeOptions());

                        if ($storedCrit['crit'][$criteriaKeys[$i]][$j]['value']) {
                            $criteriaValue->setValue($storedCrit['crit'][$criteriaKeys[$i]][$j]['value']);
                        } else {
                            $criteriaValue->setMultiOptions(['0' => _('Select track type')]);
                            $criteriaValue->setMultiOptions($this->getTracktypeOptions());
                            $criteriaValue->setValue($tracktypeSelectValue);
                        }
                        $criteriaValue->setAttrib('enabled', 'enabled');
                    } else {
                        $criteriaValue->setValue($storedCrit['crit'][$criteriaKeys[$i]][$j]['value']);
                    }
                }
                $this->addElement($criteriaValue);

                // DATETIME SELECT
                $criteriaDatetimeSelect = new Zend_Form_Element_Select('sp_criteria_datetime_select_' . $i . '_' . $j);
                $criteriaDatetimeSelect->setAttrib('class', 'input_select sp_input_select')
                    ->setDecorators(['viewHelper']);
                if (isset($criteriaKeys[$i]) && $relativeDateTime) {
                    $criteriaDatetimeSelect->setAttrib('enabled', 'enabled');
                } else {
                    $criteriaDatetimeSelect->setAttrib('disabled', 'disabled');
                }
                // check if the value is stored and it is a relative datetime field
                if (
                    isset($criteriaKeys[$i], $storedCrit['crit'][$criteriaKeys[$i]][$j]['value'], $criteriaType)
                    && $criteriaType == ModifierType::DATE
                    && preg_match('/before|after|between/', $modifierTest) == 1
                ) {
                    // need to remove any leading numbers stored in the database
                    $dateTimeSelectValue = preg_replace('/[0-9]+/', '', $storedCrit['crit'][$criteriaKeys[$i]][$j]['value']);
                    // need to strip white from front and ago from the end to match with the value of the time unit select dropdown
                    $dateTimeSelectValue = trim(preg_replace('/\W\w+\s*(\W*)$/', '$1', $dateTimeSelectValue));
                    $criteriaDatetimeSelect->setMultiOptions($this->getTimePeriodCriteriaOptions());
                    $criteriaDatetimeSelect->setValue($dateTimeSelectValue);
                    $criteriaDatetimeSelect->setAttrib('enabled', 'enabled');
                } else {
                    $criteriaDatetimeSelect->setMultiOptions(['0' => _('Select unit of time')]);
                    $criteriaDatetimeSelect->setMultiOptions($this->getTimePeriodCriteriaOptions());
                }

                $this->addElement($criteriaDatetimeSelect);

                // EXTRA
                $criteriaExtra = new Zend_Form_Element_Text('sp_criteria_extra_' . $i . '_' . $j);
                $criteriaExtra->setAttrib('class', 'input_text sp_extra_input_text')
                    ->setDecorators(['viewHelper']);
                if (isset($criteriaKeys[$i], $storedCrit['crit'][$criteriaKeys[$i]][$j]['extra'])) {
                    // need to check if this is a relative date time value
                    if (isset($criteriaType) && $criteriaType == ModifierType::DATE && $modifierTest == 'between') {
                        // the criteria value will be a number followed by time unit and ago so set input to number part
                        $criteriaExtra->setValue(filter_var($storedCrit['crit'][$criteriaKeys[$i]][$j]['extra'], FILTER_SANITIZE_NUMBER_INT));
                    } else {
                        $criteriaExtra->setValue($storedCrit['crit'][$criteriaKeys[$i]][$j]['extra']);
                    }
                    $criteriaValue->setAttrib('class', 'input_text sp_extra_input_text');
                } else {
                    $criteriaExtra->setAttrib('disabled', 'disabled');
                }
                $this->addElement($criteriaExtra);
                // DATETIME SELECT EXTRA

                $criteriaExtraDatetimeSelect = new Zend_Form_Element_Select('sp_criteria_extra_datetime_select_' . $i . '_' . $j);
                $criteriaExtraDatetimeSelect->setAttrib('class', 'input_select sp_input_select')
                    ->setDecorators(['viewHelper']);

                if (
                    isset($criteriaKeys[$i], $storedCrit['crit'][$criteriaKeys[$i]][$j]['extra'])
                    && $modifierTest == 'between'
                ) {
                    // need to remove the leading numbers stored in the database
                    $extraDateTimeSelectValue = preg_replace('/[0-9]+/', '', $storedCrit['crit'][$criteriaKeys[$i]][$j]['extra']);
                    // need to strip white from front and ago from the end to match with the value of the time unit select dropdown
                    $extraDateTimeSelectValue = trim(preg_replace('/\W\w+\s*(\W*)$/', '$1', $extraDateTimeSelectValue));
                    $criteriaExtraDatetimeSelect->setMultiOptions($this->getTimePeriodCriteriaOptions());
                    // Logging::info('THIS IS-'.$extraDateTimeSelectValue.'-IT');
                    $criteriaExtraDatetimeSelect->setValue($extraDateTimeSelectValue);
                    $criteriaExtraDatetimeSelect->setAttrib('enabled', 'enabled');
                } else {
                    $criteriaExtraDatetimeSelect->setMultiOptions(['0' => _('Select unit of time')]);
                    $criteriaExtraDatetimeSelect->setMultiOptions($this->getTimePeriodCriteriaOptions());
                    $criteriaExtraDatetimeSelect->setAttrib('disabled', 'disabled');
                }
                $this->addElement($criteriaExtraDatetimeSelect);
            } // for
        } // for

        $repeatTracks = new Zend_Form_Element_Checkbox('sp_repeat_tracks');
        $repeatTracks->setDecorators(['viewHelper'])
            ->setLabel(_('Allow Repeated Tracks:'));
        if (isset($storedCrit['repeat_tracks'])) {
            $repeatTracks->setChecked($storedCrit['repeat_tracks']['value'] == 1 ? true : false);
        }
        $this->addElement($repeatTracks);

        $overflowTracks = new Zend_Form_Element_Checkbox('sp_overflow_tracks');
        $overflowTracks->setDecorators(['viewHelper'])
            ->setLabel(_('Allow last track to exceed time limit:'));
        if (isset($storedCrit['overflow_tracks'])) {
            $overflowTracks->setChecked($storedCrit['overflow_tracks']['value'] == 1);
        }
        $this->addElement($overflowTracks);

        $sort = new Zend_Form_Element_Select('sp_sort_options');
        $sort->setAttrib('class', 'sp_input_select')
            ->setDecorators(['viewHelper'])
            ->setLabel(_('Sort Tracks:'))
            ->setMultiOptions($this->getSortOptions());
        if (isset($storedCrit['sort'])) {
            $sort->setValue($storedCrit['sort']['value']);
        }
        $this->addElement($sort);

        $limit = new Zend_Form_Element_Select('sp_limit_options');
        $limit->setAttrib('class', 'sp_input_select')
            ->setDecorators(['viewHelper'])
            ->setMultiOptions($this->getLimitOptions());
        if (isset($storedCrit['limit'])) {
            $limit->setValue($storedCrit['limit']['modifier']);
        }
        $this->addElement($limit);

        $limitValue = new Zend_Form_Element_Text('sp_limit_value');
        $limitValue->setAttrib('class', 'sp_input_text_limit')
            ->setLabel(_('Limit to:'))
            ->setDecorators(['viewHelper']);
        $this->addElement($limitValue);
        if (isset($storedCrit['limit'])) {
            $limitValue->setValue($storedCrit['limit']['value']);
        } else {
            // setting default to 1 hour
            $limitValue->setValue(1);
        }

        $generate = new Zend_Form_Element_Button('generate_button');
        $generate->setAttrib('class', 'sp-button btn');
        $generate->setAttrib('title', _('Generate playlist content and save criteria'));
        $generate->setIgnore(true);
        if ($blockType == 0) {
            $generate->setLabel(_('Preview'));
        } else {
            $generate->setLabel(_('Generate'));
        }
        $generate->setDecorators(['viewHelper']);
        $this->addElement($generate);

        $shuffle = new Zend_Form_Element_Button('shuffle_button');
        $shuffle->setAttrib('class', 'sp-button btn');
        $shuffle->setAttrib('title', _('Shuffle playlist content'));
        $shuffle->setIgnore(true);
        $shuffle->setLabel(_('Shuffle'));
        $shuffle->setDecorators(['viewHelper']);
        $this->addElement($shuffle);

        $this->setDecorators([
            ['ViewScript', [
                'viewScript' => 'form/smart-block-criteria.phtml', 'openOption' => $openSmartBlockOption,
                'criteriasLength' => $numElements, 'modRowMap' => $modRowMap,
            ]],
        ]);
    }

    // This is a simple function that determines if a modValue should enable a datetime
    public function enableDateTimeUnit($modValue)
    {
        return preg_match('/before|after|between/', $modValue) == 1;
    }

    public function preValidation($params)
    {
        $data = Application_Model_Block::organizeSmartPlaylistCriteria($params['data']);
        // add elements that needs to be added
        // set multioption for modifier according to criteria_field
        $modRowMap = [];
        if (!isset($data['criteria'])) {
            return $data;
        }

        foreach ($data['criteria'] as $critKey => $d) {
            $count = 1;
            foreach ($d as $modKey => $modInfo) {
                $critMod = $critKey . '_' . $modKey;
                $blockCriteria = BlockCriteria::get($modInfo['sp_criteria_field']);
                if ($modKey == 0) {
                    $eleCrit = $this->getElement('sp_criteria_field_' . $critMod);
                    $eleCrit->setValue($blockCriteria->display);
                    $eleCrit->setAttrib('disabled', null);

                    $eleMod = $this->getElement('sp_criteria_modifier_' . $critMod);

                    $eleMod->setMultiOptions($blockCriteria->displayModifiers());

                    $eleMod->setValue($modInfo['sp_criteria_modifier']);
                    $eleMod->setAttrib('disabled', null);

                    $eleDatetime = $this->getElement('sp_criteria_datetime_select_' . $critMod);
                    if ($this->enableDateTimeUnit($eleMod->getValue())) {
                        $eleDatetime->setAttrib('enabled', 'enabled');
                        $eleDatetime->setValue($modInfo['sp_criteria_datetime_select']);
                        $eleDatetime->setAttrib('disabled', null);
                    } else {
                        $eleDatetime->setAttrib('disabled', 'disabled');
                    }
                    $eleValue = $this->getElement('sp_criteria_value_' . $critMod);
                    $eleValue->setValue($modInfo['sp_criteria_value']);
                    $eleValue->setAttrib('disabled', null);

                    if (isset($modInfo['sp_criteria_extra'])) {
                        $eleExtra = $this->getElement('sp_criteria_extra_' . $critMod);
                        $eleExtra->setValue($modInfo['sp_criteria_extra']);
                        $eleValue->setAttrib('class', 'input_text sp_extra_input_text');
                        $eleExtra->setAttrib('disabled', null);
                    }
                    $eleExtraDatetime = $this->getElement('sp_criteria_extra_datetime_select_' . $critMod);
                    if ($eleMod->getValue() == 'between') {
                        $eleExtraDatetime->setAttrib('enabled', 'enabled');
                        $eleExtraDatetime->setValue($modInfo['sp_criteria_extra_datetime_select']);
                        $eleExtraDatetime->setAttrib('disabled', null);
                    } else {
                        $eleExtraDatetime->setAttrib('disabled', 'disabled');
                    }
                } else {
                    $criteria = new Zend_Form_Element_Select('sp_criteria_field_' . $critMod);
                    $criteria->setAttrib('class', 'input_select sp_input_select sp-invisible')
                        ->setValue('Select criteria')
                        ->setDecorators(['viewHelper'])
                        ->setMultiOptions(BlockCriteria::displayCriteria());

                    $criteria->setValue($blockCriteria->display);
                    $this->addElement($criteria);

                    // MODIFIER
                    $criteriaModifers = new Zend_Form_Element_Select('sp_criteria_modifier_' . $critMod);
                    $criteriaModifers->setValue('Select modifier')
                        ->setAttrib('class', 'input_select sp_input_select')
                        ->setDecorators(['viewHelper']);

                    $criteriaModifers->setMultiOptions($blockCriteria->displayModifiers());
                    $criteriaModifers->setValue($modInfo['sp_criteria_modifier']);
                    $this->addElement($criteriaModifers);

                    // VALUE
                    $criteriaValue = new Zend_Form_Element_Text('sp_criteria_value_' . $critMod);
                    $criteriaValue->setAttrib('class', 'input_text sp_input_text')
                        ->setDecorators(['viewHelper']);
                    $criteriaValue->setValue($modInfo['sp_criteria_value']);
                    $this->addElement($criteriaValue);
                    // DATETIME UNIT SELECT

                    $criteriaDatetimeSelect = new Zend_Form_Element_Select('sp_criteria_datetime_select_' . $critMod);
                    $criteriaDatetimeSelect->setAttrib('class', 'input_select sp_input_select')
                        ->setDecorators(['viewHelper']);
                    if ($this->enableDateTimeUnit($criteriaValue->getValue())) {
                        $criteriaDatetimeSelect->setAttrib('enabled', 'enabled');
                        $criteriaDatetimeSelect->setAttrib('disabled', null);
                        $criteriaDatetimeSelect->setValue($modInfo['sp_criteria_datetime_select']);
                        $this->addElement($criteriaDatetimeSelect);
                    } else {
                        $criteriaDatetimeSelect->setAttrib('disabled', 'disabled');
                    }
                    // EXTRA
                    $criteriaExtra = new Zend_Form_Element_Text('sp_criteria_extra_' . $critMod);
                    $criteriaExtra->setAttrib('class', 'input_text sp_extra_input_text')
                        ->setDecorators(['viewHelper']);
                    if (isset($modInfo['sp_criteria_extra'])) {
                        $criteriaExtra->setValue($modInfo['sp_criteria_extra']);
                        $criteriaValue->setAttrib('class', 'input_text sp_extra_input_text');
                    } else {
                        $criteriaExtra->setAttrib('disabled', 'disabled');
                    }
                    $this->addElement($criteriaExtra);

                    // EXTRA DATETIME UNIT SELECT

                    $criteriaExtraDatetimeSelect = new Zend_Form_Element_Select('sp_criteria_extra_datetime_select_' . $critMod);
                    $criteriaExtraDatetimeSelect->setAttrib('class', 'input_select sp_input_select')
                        ->setDecorators(['viewHelper']);
                    if ($criteriaValue->getValue() == 'between') {
                        $criteriaExtraDatetimeSelect->setAttrib('enabled', 'enabled');
                        $criteriaExtraDatetimeSelect->setAttrib('disabled', null);
                        $criteriaExtraDatetimeSelect->setValue($modInfo['sp_criteria_extra_datetime_select']);
                        $this->addElement($criteriaExtraDatetimeSelect);
                    } else {
                        $criteriaExtraDatetimeSelect->setAttrib('disabled', 'disabled');
                    }
                    ++$count;
                }
            }
            $modRowMap[$critKey] = $count;
        }

        $decorator = $this->getDecorator('ViewScript');
        $existingModRow = $decorator->getOption('modRowMap');
        foreach ($modRowMap as $key => $v) {
            $existingModRow[$key] = $v;
        }
        $decorator->setOption('modRowMap', $existingModRow);

        // reconstruct the params['criteria'] so we can populate the form
        $formData = [];
        foreach ($params['data'] as $ele) {
            $formData[$ele['name']] = $ele['value'];
        }

        $this->populate($formData);

        // Logging::info($formData);
        return $data;
    }

    public function isValid($params)
    {
        $isValid = true;
        $data = $this->preValidation($params);
        $allCriteria = BlockCriteria::criteriaMap();

        // things we need to check
        // 1. limit value shouldn't be empty and has upperbound of 24 hrs
        // 2. sp_criteria or sp_criteria_modifier shouldn't be 0
        // 3. validate formate according to DB column type
        $multiplier = 1;

        // validation start
        if ($data['etc']['sp_limit_options'] == 'hours') {
            $multiplier = 60;
        }
        if ($data['etc']['sp_limit_options'] == 'hours' || $data['etc']['sp_limit_options'] == 'mins') {
            $element = $this->getElement('sp_limit_value');
            if ($data['etc']['sp_limit_value'] == '' || floatval($data['etc']['sp_limit_value']) <= 0) {
                $element->addError(_('Limit cannot be empty or smaller than 0'));
                $isValid = false;
            } else {
                $mins = floatval($data['etc']['sp_limit_value']) * $multiplier;
                if ($mins > 1440) {
                    $element->addError(_('Limit cannot be more than 24 hrs'));
                    $isValid = false;
                }
            }
        } else {
            $element = $this->getElement('sp_limit_value');
            if ($data['etc']['sp_limit_value'] == '' || floatval($data['etc']['sp_limit_value']) <= 0) {
                $element->addError(_('Limit cannot be empty or smaller than 0'));
                $isValid = false;
            } elseif (!ctype_digit($data['etc']['sp_limit_value'])) {
                $element->addError(_('The value should be an integer'));
                $isValid = false;
            } elseif (intval($data['etc']['sp_limit_value']) > 500) {
                $element->addError(_('500 is the max item limit value you can set'));
                $isValid = false;
            }
        }

        if (isset($data['criteria'])) {
            foreach ($data['criteria'] as $rowKey => $row) {
                foreach ($row as $key => $d) {
                    $element = $this->getElement('sp_criteria_field_' . $rowKey . '_' . $key);
                    // check for not selected select box
                    if ($d['sp_criteria_field'] == '0' || $d['sp_criteria_modifier'] == '0') {
                        $element->addError(_('You must select Criteria and Modifier'));
                        $isValid = false;
                    } else {
                        $column = CcFilesPeer::getTableMap()->getColumnByPhpName($allCriteria[$d['sp_criteria_field']]->peer);
                        // validation on type of column
                        if (in_array($d['sp_criteria_field'], ['length', 'cuein', 'cueout'])) {
                            if (!preg_match('/^(\d{2}):(\d{2}):(\d{2})/', $d['sp_criteria_value'])) {
                                $element->addError(_("'Length' should be in '00:00:00' format"));
                                $isValid = false;
                            }
                        }
                        // this looks up the column type for the criteria the modified time, upload time etc.
                        elseif ($column->getType() == PropelColumnTypes::TIMESTAMP) {
                            // need to check for relative modifiers first - bypassing currently
                            if (in_array($d['sp_criteria_modifier'], ['before', 'after', 'between'])) {
                                if (!preg_match('/^[1-9][0-9]*$|0/', $d['sp_criteria_value'])) {
                                    $element->addError(_('Only non-negative integer numbers are allowed (e.g 1 or 5) for the text value'));
                                    // TODO validate this on numeric input with whatever parsing also do for extra
                                    // if the modifier is before ago or between we skip validation until we confirm format
                                    $isValid = false;
                                } elseif (isset($d['sp_criteria_datetime_select']) && $d['sp_criteria_datetime_select'] == '0') {
                                    $element->addError(_('You must select a time unit for a relative datetime.'));
                                    $isValid = false;
                                }
                            } else {
                                if (!preg_match('/(\d{4})-(\d{2})-(\d{2})/', $d['sp_criteria_value'])) {
                                    $element->addError(_('The value should be in timestamp format (e.g. 0000-00-00 or 0000-00-00 00:00:00)'));
                                    $isValid = false;
                                } else {
                                    $result = Application_Common_DateHelper::checkDateTimeRangeForSQL($d['sp_criteria_value']);
                                    if (!$result['success']) {
                                        // check for if it is in valid range( 1753-01-01 ~ 12/31/9999 )
                                        $element->addError($result['errMsg']);
                                        $isValid = false;
                                    }
                                }
                            }
                            if (isset($d['sp_criteria_extra'])) {
                                if ($d['sp_criteria_modifier'] == 'between') {
                                    // validate that the input value only contains a number if using relative date times
                                    if (!preg_match('/^[1-9][0-9]*$|0/', $d['sp_criteria_extra'])) {
                                        $element->addError(_('Only non-negative integer numbers are allowed for a relative date time'));
                                        $isValid = false;
                                    }
                                    // also need to check to make sure they chose a time unit from the dropdown
                                    elseif ($d['sp_criteria_extra_datetime_select'] == '0') {
                                        $element->addError(_('You must select a time unit for a relative datetime.'));
                                        $isValid = false;
                                    }
                                } else {
                                    if (!preg_match('/(\d{4})-(\d{2})-(\d{2})/', $d['sp_criteria_extra'])) {
                                        $element->addError(_('The value should be in timestamp format (e.g. 0000-00-00 or 0000-00-00 00:00:00)'));
                                        $isValid = false;
                                    } else {
                                        $result = Application_Common_DateHelper::checkDateTimeRangeForSQL($d['sp_criteria_extra']);
                                        if (!$result['success']) {
                                            // check for if it is in valid range( 1753-01-01 ~ 12/31/9999 )
                                            $element->addError($result['errMsg']);
                                            $isValid = false;
                                        }
                                    }
                                }
                            }
                        } elseif (
                            $column->getType() == PropelColumnTypes::INTEGER
                            && $d['sp_criteria_field'] != 'owner_id'
                        ) {
                            if (!is_numeric($d['sp_criteria_value'])) {
                                $element->addError(_('The value has to be numeric'));
                                $isValid = false;
                            }
                            // length check
                            if ($d['sp_criteria_value'] >= 2 ** 31) {
                                $element->addError(_('The value should be less then 2147483648'));
                                $isValid = false;
                            }
                            // Unselected track type
                            if ($d['sp_criteria_field'] == 'track_type_id' && $d['sp_criteria_value'] == 0) {
                                $element->addError(_('The value cannot be empty'));
                                $isValid = false;
                            }
                        } elseif ($column->getType() == PropelColumnTypes::VARCHAR) {
                            if (strlen($d['sp_criteria_value']) > $column->getSize()) {
                                $element->addError(sprintf(_('The value should be less than %s characters'), $column->getSize()));
                                $isValid = false;
                            }
                        }
                    }

                    if ($d['sp_criteria_value'] == '') {
                        $element->addError(_('Value cannot be empty'));
                        $isValid = false;
                    }
                } // end foreach
            } // for loop
        } // if

        return $isValid;
    }
}
