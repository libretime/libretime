<?php

class Application_Form_PlaylistRules extends Zend_Form
{
	/* We need to know if the criteria value will be a string
	 * or numeric value in order to populate the modifier
	* select list
	*/
	private $criteriaTypes = array(
		""             => "",
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
                ""             => _("Select criteria"),
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

        if (is_null($option)) {
        	return $this->criteriaOptions;
        }
        else {
        	return $this->criteriaOptions[$option];
        }
    }
		
	private function getStringCriteriaOptions()
	{
		return array(
			""                => _("Select modifier"),
			"contains"         => _("contains"),
			"does not contain" => _("does not contain"),
			"is"               => _("is"),
			"is not"           => _("is not"),
			"starts with"      => _("starts with"),
			"ends with"        => _("ends with")
		);
	}
	
	private function getNumericCriteriaOptions()
	{
		return array(
			""               => _("Select modifier"),
			"is"              => _("is"),
			"is not"          => _("is not"),
			"is greater than" => _("is greater than"),
			"is less than"    => _("is less than"),
			"is in the range" => _("is in the range")
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

    public function init()
    {
    	$this->setDecorators(array(
    		array('ViewScript', array('viewScript' => 'form/playlist-rules.phtml'))
    	));
    	
    	$spType = new Zend_Form_Element_Radio('pl_type');
    	$spType
    		->setLabel(_('Type:'))
	    	->setDecorators(array('ViewHelper'))
	    	->setMultiOptions(array(
    			0 => _('Static'),
    			1 => _('Dynamic')
    	));
    	$this->addElement($spType);
    	
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
}