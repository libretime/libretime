<?php

class Application_Form_PlaylistRules extends Zend_Form
{
	private $_suffixes;
	private $_populateHelp;
	/* We need to know if the criteria value will be a string
	 * or numeric value in order to populate the modifier
	* select list
	*/
	private $_criteriaTypes = array(
		""  => "",
		"AlbumTitle" => "s",
		"BitRate" => "n",
		"Bpm" => "n",
		"Composer" => "s",
		"Conductor" => "s",
		"Copyright" => "s",
		"Cuein" => "n",
		"Cueout" => "n",
		"ArtistName" => "s",
		"EncodedBy" => "s",
		"CreatedAt" => "n",
		"UpdatedAt" => "n",
		"LastPlayedTime" => "n",
		"Genre" => "s",
		"IsrcNumber" => "s",
		"Label" => "s",
		"Language" => "s",
		"Length" => "n",
		"Mime" => "s",
		"Mood" => "s",
		"ReplayGain" => "n",
		"SampleRate" => "n",
		"TrackTitle" => "s",
		"TrackNumber" => "n",
		"InfoUrl" => "s",
		"Year" => "n"
	);
	
	private function getCriteriaOptions()
    {
		return array(
        	""  => _("Select criteria"),
            "AlbumTitle" => _("Album"),
            "BitRate" => _("Bit Rate (Kbps)"),
            "Bpm" => _("BPM"),
            "Composer" => _("Composer"),
            "Conductor" => _("Conductor"),
            "Copyright" => _("Copyright"),
            "Cuein" => _("Cue In"),
            "Cueout" => _("Cue Out"),
            "ArtistName" => _("Creator"),
            "EncodedBy" => _("Encoded By"),
            "Genre" => _("Genre"),
            "IsrcNumber" => _("ISRC"),
            "Label" => _("Label"),
            "Language" => _("Language"),
            "UpdatedAt" => _("Last Modified"),
            "LastPlayedTime" => _("Last Played"),
            "Length" => _("Length"),
            "Mime" => _("Mime"),
            "Mood" => _("Mood"),
            "ReplayGain" => _("Replay Gain"),
            "SampleRate" => _("Sample Rate (kHz)"),
            "TrackTitle" => _("Title"),
            "TrackNumber" => _("Track Number"),
            "CreatedAt" => _("Uploaded"),
            "InfoUrl" => _("Website"),
            "Year" => _("Year")
        );
    }
    
    private function getOption($option = null)
    {
    	if (is_null($option)) {
    		return $this->criteriaOptions;
    	}
    	else {
    		return $this->criteriaOptions[$option];
    	}
    }
    
    private function getDefaultCriteriaOptions()
    {
    	return array(
    		0 => _("Select modifier")
    	);
    }
		
	private function getStringCriteriaOptions()
	{
		return array(
			0 => _("Select modifier"),
			1 => _("contains"),
			2 => _("does not contain"),
			3 => _("is"),
			4 => _("is not"),
			5 => _("starts with"),
			6 => _("ends with")
		);
	}
	
	private function getNumericCriteriaOptions()
	{
		return array(
			0  => _("Select modifier"),
			3 => _("is"),
			4 => _("is not"),
			7 => _("is greater than"),
			8 => _("is less than"),
			9 => _("is greater than or equal to"),
			10 => _("is less than or equal to"),
			11 => _("is in the range")
		);
	}
	
	private function getLimitOptions()
	{
		return array(
			"hours"   => _("hours"),
			"minutes" => _("minutes"),
			"items"   => _("items")
		);
	}
	
	public function __construct()
	{
		parent::__construct();
		
		$this->_suffixes = array();
		$this->_populateHelp = array();
	}

    public function init()
    {
    	$this->setDecorators(array(
    		array('ViewScript', array(
    				'viewScript' => 'form/playlist-rules.phtml',
    				'suffixes' => &$this->_suffixes
    			))
    	));
    	
    	$repeatTracks = new Zend_Form_Element_Checkbox('pl_repeat_tracks');
    	$repeatTracks
    		->setDecorators(array('ViewHelper'))
    		->setLabel(_('Allow Repeat Tracks:'));
    	$this->addElement($repeatTracks);
    	
    	$myTracks = new Zend_Form_Element_Checkbox('pl_my_tracks');
    	$myTracks
    		->setDecorators(array('ViewHelper'))
    		->setLabel(_('Only My Tracks:'));
    	$this->addElement($myTracks);
    	
    	$limit = new Zend_Form_Element_Select('pl_limit_options');
    	$limit
    		->setAttrib('class', 'sp_input_select')
	    	->setDecorators(array('ViewHelper'))
	    	->setMultiOptions($this->getLimitOptions());
    	$this->addElement($limit);
    	
    	$limitValue = new Zend_Form_Element_Text('pl_limit_value');
    	$limitValue
    		->setAttrib('class', 'sp_input_text_limit')
	    	->setLabel(_('Limit to'))
	    	->setDecorators(array('ViewHelper'));
    	$this->addElement($limitValue);
    }
    
    private function getModifierOptions($criteria) {
    	
    	$type = $this->_criteriaTypes[$criteria];
    	
    	switch ($type) {
    		
    		case "n":
    			return $this->getNumericCriteriaOptions();
    			break;
    		case "s":
    			return $this->getStringCriteriaOptions();
    			break;
    		default:
    			return $this->getDefaultCriteriaOptions();
    	}
    }
    
    private function buildRuleCriteria($suffix) {
    	
    	$criteria = new Zend_Form_Element_Select("sp_criteria_field_{$suffix}");
    	$criteria
	    	->setAttrib('class', 'input_select sp_input_select rule_criteria')
	    	->setValue('Select criteria')
	    	->setDecorators(array('viewHelper'))
	    	->setMultiOptions($this->getCriteriaOptions());
    	 
    	$this->addElement($criteria);
    	
    	return $criteria->getId();
    }
    
    private function buildRuleModifier($suffix, $options) {
    	
    	$criteriaModifers = new Zend_Form_Element_Select("sp_criteria_modifier_{$suffix}");
    	$criteriaModifers
	    	->setValue('Select modifier')
	    	->setAttrib('class', 'input_select sp_input_select rule_modifier')
	    	->setDecorators(array('viewHelper'));
    	 
    	$criteriaModifers->setMultiOptions($options);
    	
    	$this->addElement($criteriaModifers);
    	
    	return $criteriaModifers->getId();
    }
    
    private function buildRuleInput($suffix) {
    	
    	$criteriaValue = new Zend_Form_Element_Text("sp_criteria_value_{$suffix}");
    	$criteriaValue
	    	->setAttrib('class', 'input_text sp_input_text')
	    	->setDecorators(array('viewHelper'));
    	 
    	$this->addElement($criteriaValue);
    	
    	return $criteriaValue->getId();
    }
    
    private function buildRuleExtra($suffix) {
    	
    	$criteriaExtra = new Zend_Form_Element_Text("sp_criteria_extra_{$suffix}");
    	$criteriaExtra
	    	->setAttrib('class', 'input_text sp_extra_input_text')
	    	->setDecorators(array('viewHelper'));
    	 
    	$this->addElement($criteriaExtra);
    	
    	return $criteriaExtra->getId();
    }
    
    private function buildRuleCriteriaRow($info = null) {
    	$suffix = mt_rand(10000, 99999);
    	
    	if (is_null($info)) {
    		$criteria = "";
    	}
    	else {
    		$criteria = $info["criteria"];
    	}
    	
    	$options = self::getModifierOptions($criteria);
    	
    	$critKey = self::buildRuleCriteria($suffix);
    	$modKey = self::buildRuleModifier($suffix, $options);
    	$inputKey = self::buildRuleInput($suffix);
    	
    	if (isset($info)) {
    		
    		if (isset($info["criteria"])) {
    			$this->_populateHelp[$critKey] = $info["criteria"];
    		}
    		
    		if (isset($info["modifier"])) {
    			$this->_populateHelp[$modKey] = $info["modifier"];
    		}

    		if (isset($info["input1"])) {
    			$this->_populateHelp[$inputKey] = $info["input1"];
    		}
    	}
    	
    	//this extra field is only required for range conditions.
    	if (isset($info) && intval($info["modifier"]) === 11) {
    		$extraKey = self::buildRuleExtra($suffix);
    		
    		if (isset($info["input2"])) {
    			$this->_populateHelp[$extraKey] = $info["input2"];
    		}
    	}
    	
    	return $suffix;
    }
    
    public function buildCriteriaOptions($criteria = null)
    {
    	Logging::info($criteria);
    	
    	if (is_null($criteria)) {
    		$this->_suffixes[0][0] = self::buildRuleCriteriaRow();
    		return;
    	}
    	
    	for ($i = 0; $i < count($criteria); $i++) {
    		for ($j = 0; $j < count($criteria[$i]); $j++) {
    			$this->_suffixes[$i][$j] = self::buildRuleCriteriaRow($criteria[$i][$j]);
    		}
    	}
    	
    	return $this->_suffixes;
    }
    
    public function getPopulateHelp()
    {
    	return $this->_populateHelp;
    }
}