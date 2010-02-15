<?php
class uiTwitter {
    private $Base;

    public function __construct(&$uiBase)
    {
        $this->Base =& $uiBase;
    }
    
    private static function getSettingFormMask()
    {
        $formmask = array(
            array(
                'element'   => 'act',
                'type'      => 'hidden',
                'constant'  => 'twitter.saveSettings'
            ),
            array(
                'element'   => 'twitter-is_active',
                'type'      => 'checkbox',
                'label'     => 'Activate posts',
                'isPref'    => true
            ),
            array(
                'element'   => 'twitter-login',
                'type'      => 'text',
                'label'     => 'Twitter user account',
                'isPref'    => true
            ),
            array(
                'element'   => 'twitter-password',
                'type'      => 'password',
                'label'     => 'Twitter password <small>(Input to change)</span>',
                'isPref'    => true,
                'hiddenPref' => true
            ),
            array(
                'element'   => 'twitter-password2',
                'type'      => 'password',
                'label'     => 'Repeat password',
            ),
            array(
                'rule'      => 'compare',
                'element'   => array('twitter-password','twitter-password2'),
                'rulemsg'   => 'The passwords do not match.'
            ),
            array(
                'element'   => 'twitter-prefix',
                'type'      => 'text',
                'label'     => 'Prefix',
                'isPref'    => true
            ),
            array(
                'element'   => 'twitter-has_tracktitle',
                'type'      => 'checkbox',
                'label'     => 'Track title',
                'isPref'    => true
            ),
            array(
                'element'   => 'twitter-has_trackartist',
                'type'      => 'checkbox',
                'label'     => 'Track artist',
                'isPref'    => true
            ),
            array(
                'element'   => 'twitter-has_playlisttitle',
                'type'      => 'checkbox',
                'label'     => 'Playlist title',
                'isPref'    => true
            ),
            array(
                'element'   => 'twitter-has_stationname',
                'type'      => 'checkbox',
                'label'     => 'Station name',
                'isPref'    => true
            ),
            array(
                'element'   => 'twitter-suffix',
                'type'      => 'text',
                'label'     => 'Suffix',
                'isPref'    => true
            ),
            array(
                'element'   => 'twitter-url',
                'type'      => 'text',
                'label'     => 'URL (optional)',
                'isPref'    => true
            ),
            array(
                'element'   => 'twitter-offset',
                'type'      => 'select',
                'label'     => 'Tweet what\'s...',
                'options'   => array(
                    "0"  => "playing now",
                    "3000"  => "in five minutes",
                    "6000" => "in ten minutes",
                    "9000" => "in 15 minutes",
                    "1800" => "in 30 minutes",
                    "3600" => "in one hour",
                ),
                'isPref'    => true
            ),
            array(
                'element'   => 'twitter-interval',
                'type'      => 'select',
                'label'     => 'Tweet every...',
                'options'   => array(
                    "60"    => "minute",
                    "180"    => "three minutes",
                    "300"    => "five minutes",
                    "600"   => "ten minutes",
                    "900"   => "15 minutes",
                    "1800"   => "30 minutes",
                    "3600"   => "hour",
                    "21600"  => "6 hours",
                    "43200" => "24 hours",
                ),
                'isPref'    => true
            ),
            array(
                'element'   => 'Submit',
                'type'      => 'submit',
                'label'     => 'Submit',
            )
        );
        return $formmask; 
    }
    
    private function getSettings()
    {
        static $settings;
        
        if (is_array($settings)) {
            return $settings;   
        }
        
        $settings = array();
        $mask = uiTwitter::getSettingFormMask();
        
        foreach($mask as $key => $val) {
            if (isset($val['isPref']) && $val['isPref'] && !$val['hiddenPref']) {
                $element = isset($val['element']) ? $val['element'] : null;
                $p = $this->Base->gb->loadGroupPref($this->Base->sessid, 'StationPrefs', $element);
                if (is_string($p)) {
                    $settings[$element] = $p;
                }
            }
        }
          
        return $settings;
    }
    
    public function getSettingsForm()
    {
        $mask = uiTwitter::getSettingFormMask();
        $form = new HTML_QuickForm('twitter', UI_STANDARD_FORM_METHOD, UI_HANDLER);#
        $settings = $this->getSettings();
        
        foreach($mask as $key => $val) {
            if (isset($val['isPref']) && $val['isPref']) {
                $element = isset($val['element']) ? $val['element'] : null;
                $p = $settings[$element];
                if (is_string($p)) {
                    $mask[$key]['default'] = $p;
                }
            }
        }
        uiBase::parseArrayToForm($form, $mask);
        $renderer = new HTML_QuickForm_Renderer_Array(true, true);
        $form->accept($renderer);
        return $renderer->toArray();
    }
    
    public function saveSettings()
    {
        if ($this->Base->_validateForm($_REQUEST, uiTwitter::getSettingFormMask()) !== TRUE) {
            $this->Base->_retMsg('An error has occured on validating the form.');
            return FALSE;
        }
        
        $mask = uiTwitter::getSettingFormMask();
        $form = new HTML_QuickForm('twitter', UI_STANDARD_FORM_METHOD, UI_HANDLER);
        uiBase::parseArrayToForm($form, $mask);
        $formdata = $form->exportValues();
        
        foreach ($mask as $key => $val) {
            if (isset($val['isPref']) && $val['isPref']) {
                if (!empty($formdata[$val['element']])) {
                	$result = $this->Base->gb->saveGroupPref($this->Base->sessid, 'StationPrefs', $val['element'], $formdata[$val['element']]);
                    if (PEAR::isError($result))
                        $this->_retMsg('Error while saving twitter settings.');
                } else {
                    $this->Base->gb->delGroupPref($this->Base->sessid,  'StationPrefs', $val['element']);
                }
            }
        }
        
        $this->Base->_retMsg('Twitter settings saved.');
    }
    
    public function getFeed($p_withSample = false)
    {
        $settings = $this->getSettings();
        $whatsplaying = $this->getWhatsplaying($settings['twitter-offset']);
        
        if (!$p_withSample && !$whatsplaying) {
            return;   
        }
        
        $whatsplaying = @array_merge(
            array(
                "tracktitle"     => "Gimme Shelter",
                "trackartist"    => "The Rolling Stones",
                "playlisttitle"  => "The Blues Hour"
            ),
            $whatsplaying
        );

        ////////////////////////////////////////////////////////////////////////
        // create twitter tweet sample
        // TWEET PREFIX
        if (!empty($settings['twitter-prefix'])) {
            $tweetprefix = $settings['twitter-prefix'] . " ";
        } else {
            $tweetprefix = "";
        }
        // TWEET SUFFIX
        if (!empty($settings['twitter-suffix'])) {
            $tweetsuffix = " " . $settings['twitter-suffix'];
        } else {
            $tweetsuffix = "";
        }
        if (!empty($settings['twitter-url'])) {
            $tweetsuffix = $tweetsuffix . " " . self::GetTinyUrl($settings['twitter-url']);
        }
        // TWEET BODY
        $tweetbody = array();
        if ($settings['twitter-has_tracktitle']) { $tweetbody[] = $whatsplaying['tracktitle']; }
        if ($settings['twitter-has_trackartist']) { $tweetbody[] = $whatsplaying['trackartist']; }
        if ($settings['twitter-has_playlisttitle']) { $tweetbody[] = $whatsplaying['playlisttitle']; }
        if ($settings['twitter-has_stationname']) { $tweetbody[] = $this->Base->STATIONPREFS['stationName']; }
        
        $tweetbody = implode (". ",$tweetbody);
        
        // chop body to fit if necessary
        if ((strlen($tweetprefix) + strlen($tweetbody) + strlen($tweetsuffix)) > 140) {
            $tweetbody = substr($tweetbody, 0, (140 - (strlen($tweetprefix) + strlen($tweetsuffix) + 3))) . "...";
        }
        
        $tweet = $tweetprefix . $tweetbody . $tweetsuffix;
                
        return $tweet;

    }
    
    public function getTinyUrl($p_url)
    {
        $tiny = file_get_contents('http://tinyurl.com/api-create.php?url='.$p_url);
        return $tiny;
    }
    
    public function getWhatsplaying($p_offset)
    {
        $timestamp = time() + $p_offset;
        $xmldatetime = strftime('%Y%m%dT%H:%M:%S', $timestamp);

        $pl = $this->Base->SCHEDULER->displayScheduleMethod($xmldatetime, $xmldatetime);

        if (!is_array($pl) || !count($pl)) {
            return FALSE;
        }

        $pl = current($pl);
        //  subtract difference to UTC
        $offset = strftime('%H:%M:%S', $timestamp - uiScheduler::datetimeToTimestamp($pl['start']) - 3600 * strftime('%H', 0));

        $clip = $this->Base->gb->displayPlaylistClipAtOffset($this->Base->sessid, $pl['playlistId'], $offset, $distance, $_SESSION['langid'], UI_DEFAULT_LANGID);

        if (!$clip['gunid']) {
            return FALSE;
        }
        
        return array(
            'tracktitle' => $this->Base->gb->getMetadataValue(BasicStor::IdFromGunid($clip['gunid']), UI_MDATA_KEY_TITLE, $this->Base->sessid),
            'trackartist' => $this->Base->gb->getMetadataValue(BasicStor::IdFromGunid($clip['gunid']), UI_MDATA_KEY_CREATOR, $this->Base->sessid),
            'playlisttitle' => $this->Base->gb->getMetadataValue(BasicStor::IdFromGunid($pl['playlistId']), UI_MDATA_KEY_TITLE, $this->Base->sessid),
        );
    }
    
    public function sendFeed($p_feed)
    {
        $settings = $this->getSettings();
        
        $twitter = new twitter();
        $twitter->username = $settings['twitter-login'];
        $twitter->password = $settings['twitter-password'];
        
        if ($twitter->update($p_feed)) {
            $this->Base->gb->saveGroupPref($this->Base->sessid, 'StationPrefs', 'twitter-lastupdate', time());
            return true;
        }
        return false;
    }
    
    public function needsUpdate()
    {
        $settings = $this->getSettings();
        if (time() -  $this->Base->gb->loadGroupPref($this->Base->sessid, 'StationPrefs', 'twitter-lastupdate') > $settings['twitter-interval']) {
            return true;
        }
        return false;
    }
}