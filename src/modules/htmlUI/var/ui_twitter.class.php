<?php
class uiTwitter {
    private $Base;
    
    private $settings = array(
        'bitly-login'  => 'campcaster',
        'bitly-apikey' => 'R_2f812152bfc21035468350273ec8ff43' 
    );
    
    /**
     * Time in sec
     *
     * @var int
     */
    private $runtime = 10;

    public function __construct(&$uiBase)
    {
        $this->Base =& $uiBase;
        $this->loadSettings();
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
                'element'   => 'twitter-account-fieldset-open',
                'type'      => 'static',
                'text'      => '<fieldset style="width: 300px;">'
            ),
            array(
                'element'   => 'twitter-account-label',
                'type'      => 'static',
                'text'      => '<legend style="font-weight: bold;">Twitter info</legend>'
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
                'element'   => 'twitter-account-fieldset-close',
                'type'      => 'static',
                'text'      => '</fieldset>'
            ),
            array(
                'element'   => 'twitter-config-fieldset-open',
                'type'      => 'static',
                'text'      => '<fieldset style="width: 300px;">'
            ),
            array(
                'element'   => 'twitter-config-label',
                'type'      => 'static',
                'text'      => '<legend style="font-weight: bold;"></small>Tweet configuration<small></legend>'
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
                'rule'      => 'regex',
                'element'   => 'twitter-url',
                'format'    => UI_REGEX_URL,
                'rulemsg'   => 'The URL seems not to be valid. You need to use http(s):// prefix.'
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
                'element'   => 'twitter-config-fieldset-close',
                'type'      => 'static',
                'text'      => '</fieldset>'
            ),
                        array(
                'element'   => 'twitter-shortener-fieldset-open',
                'type'      => 'static',
                'text'      => '<fieldset style="width: 300px;">'
            ),
            array(
                'element'   => 'twitter-shortener-label',
                'type'      => 'static',
                'text'      => '<legend style="font-weight: bold;"></small>URL shortener<small></legend>'
            ),
            array(
                'element'   => 'twitter-shortener-provider',
                'type'      => 'select',
                'label'     => 'Provider',
                'options'   => array(
                    'bit.ly'        => 'bit.ly',
                    'tinyurl.com'   => 'tinyurl.com',               
                ),
                'isPref'    => true
            ),
            array(
                'element'   => 'twitter-bitly-login',
                'type'      => 'text',
                'label'     => 'bit.ly username',
                'isPref'    => true
            ),
            array(
                'element'   => 'twitter-bitly-apikey',
                'type'      => 'text',
                'label'     => 'bit.ly API key',
                'isPref'    => true
            ),
            array(
                'element'   => 'twitter-shortener-fieldset-close',
                'type'      => 'static',
                'text'      => '</fieldset>'
            ),
            array(
                'element'   => 'Submit',
                'type'      => 'submit',
                'label'     => 'Submit',
            )
        );
        return $formmask; 
    }
    
    private function loadSettings()
    {
        $mask = uiTwitter::getSettingFormMask();
        
        foreach($mask as $key => $val) {
            if (isset($val['isPref']) && $val['isPref']) {
                $element = preg_replace('/^twitter-/', '', $val['element'], 1);
                $p = $this->Base->gb->loadGroupPref($this->Base->sessid, 'StationPrefs', $val['element']);
                if (is_string($p)) {
                    $this->settings[$element] = $p;
                }
            }
        }
    }
    
    public function getSettingsForm()
    {
        $mask = uiTwitter::getSettingFormMask();
        $form = new HTML_QuickForm('twitter', UI_STANDARD_FORM_METHOD, UI_HANDLER);#
        
        foreach($mask as $key => $val) {
            if (isset($val['isPref']) && $val['isPref'] && !$val['hiddenPref']) {
                $element = preg_replace('/^twitter-/', '', $val['element']);
                $p = $this->settings[$element];
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
                        $this->Base->_retMsg('Error while saving twitter settings.');
                } elseif (!$val['hiddenPref']) {
                    $this->Base->gb->delGroupPref($this->Base->sessid,  'StationPrefs', $val['element']);
                }
            }
        }
        
        $this->Base->_retMsg('Twitter settings saved.');
    }
    
    public function getFeed($p_useSampledata = false)
    {        
        if ($p_useSampledata) {
            $whatsplaying = array(
                "tracktitle"     => "Gimme Shelter",
                "trackartist"    => "The Rolling Stones",
                "playlisttitle"  => "The Blues Hour"
            );   
        } else {
            $whatsplaying = $this->getWhatsplaying($this->settings['offset']);
        }
        
        if (!$whatsplaying) {
            return;   
        }

        ////////////////////////////////////////////////////////////////////////
        // create twitter tweet sample
        // TWEET PREFIX
        if (!empty($this->settings['prefix'])) {
            $tweetprefix = $this->settings['prefix'] . " ";
        } else {
            $tweetprefix = "";
        }
        // TWEET SUFFIX
        if (!empty($this->settings['suffix'])) {
            $tweetsuffix = " " . $this->settings['suffix'];
        } else {
            $tweetsuffix = "";
        }
        if (!empty($this->settings['url'])) {
            $tweetsuffix = $tweetsuffix . " " . self::shortUrl($this->settings['url']);
        }
        // TWEET BODY
        $tweetbody = array();
        if ($this->settings['has_tracktitle']) { $tweetbody[] = $whatsplaying['tracktitle']; }
        if ($this->settings['has_trackartist']) { $tweetbody[] = $whatsplaying['trackartist']; }
        if ($this->settings['has_playlisttitle']) { $tweetbody[] = $whatsplaying['playlisttitle']; }
        if ($this->settings['has_stationname']) { $tweetbody[] = $this->Base->STATIONPREFS['stationName']; }
        
        $tweetbody = implode (". ",$tweetbody);
        
        // chop body to fit if necessary
        if ((strlen($tweetprefix) + strlen($tweetbody) + strlen($tweetsuffix)) > 140) {
            $tweetbody = substr($tweetbody, 0, (140 - (strlen($tweetprefix) + strlen($tweetsuffix) + 3))) . "...";
        }
        
        $tweet = $tweetprefix . $tweetbody . $tweetsuffix;
            
        return $tweet;

    }
    
    public function shortUrl($p_url)
    {
        switch ($this->settings['shortener-provider']) {
            case 'tinyurl.com':
                $short = file_get_contents('http://tinyurl.com/api-create.php?url='.$p_url);
                break;
                
            case 'bit.ly':
                $short = file_get_contents("http://api.bit.ly/shorten?version=2.0.1&longUrl={$p_url}&format=text&login={$this->settings['bitly-login']}&apiKey={$this->settings['bitly-apikey']}");
                break;
        }
        
        return $short;
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
        $twitter = new twitter();
        $twitter->username = $this->settings['login'];
        $twitter->password = $this->settings['password'];
        
        if ($res = $twitter->update($p_feed)) {
            $this->Base->gb->saveGroupPref($this->Base->sessid, 'StationPrefs', 'twitter-lastupdate', time());
            return $res;
        }
        return false;
    }
    
    public function needsUpdate()
    {
        if (time() -  $this->Base->gb->loadGroupPref($this->Base->sessid, 'StationPrefs', 'twitter-lastupdate') + $this->runtime > $this->settings['interval']) {
            return true;
        }
        return false;
    }
    
    public function twitterify($p_string)
    {
        $string = preg_replace("#(^|[\n ])([\w]+?://[\w]+[^ \"\n\r\t< ]*)#", "\\1<a href=\"\\2\" target=\"_blank\">\\2</a>", $p_string);
        $string = preg_replace("#(^|[\n ])((www|ftp)\.[^ \"\t\n\r< ]*)#", "\\1<a href=\"http://\\2\" target=\"_blank\">\\2</a>", $string);
        $string = preg_replace("/@(\w+)/", "<a href=\"http://www.twitter.com/\\1\" target=\"_blank\">@\\1</a>", $string);
        $string = preg_replace("/#(\w+)/", "<a href=\"http://search.twitter.com/search?q=\\1\" target=\"_blank\">#\\1</a>", $string);
        
        return $string;
    }
    
    public function isActive()
    {
        return $this->settings['is_active'];   
    }
}
