<?php
function errCallBack($err)
{
    echo "<pre>gm:\n".$err->getMessage()."\ndi:\n".$err->getDebugInfo()."\nui:\n".$err->getUserInfo()."\n";
    echo "<hr>BackTrace:\n";
    print_r($err->backtrace);
    echo "</pre>\n";
    exit;
}

/**
 *  uiBase class
 *
 *  LiveSupport HTML User Interface module
 *
 */
class uiBase
{
    // --- class constructor ---
    /**
     *  uiBase
     *
     *  Initialize a new Basis Class including:
     *  - database  initialation
     *  - GreenBox initialation
     *
     *  @param $config array, configurartion data
     */
    function uiBase(&$config)
    {
        $this->dbc = DB::connect($config['dsn'], TRUE);
        if (DB::isError($this->dbc)) {
            die($this->dbc->getMessage());
        }
        $this->dbc->setFetchMode(DB_FETCHMODE_ASSOC);
        $this->gb =& new GreenBox(&$this->dbc, $config);
        $this->config = $config;
        $this->sessid = $_REQUEST[$config['authCookieName']];
        $this->userid = $this->gb->getSessUserId($this->sessid);
        $this->login  = $this->gb->getSessLogin($this->sessid);
        $this->id =  $_REQUEST['id'] ? $_REQUEST['id'] : $this->gb->getObjId($this->login, $this->gb->storId);
        $this->InputTextStandardAttrib = array('size'     =>UI_INPUT_STANDARD_SIZE,
                                               'maxlength'=>UI_INPUT_STANDARD_MAXLENGTH);



    }

    // --- basic funtionality ---
    /**
     *  tra
     *
     *  Translate the given string using localisation files.
     *
     *  @param input string, string to translate
     *  @return string, translated string
     */
    function tra($input)
    {
        // just a dummy function yet
        $nr=func_num_args();
        if ($nr>1)
        for ($i=1; $i<$nr; $i++){
            $name  = '$'.$i;
            $val   = func_get_arg($i);
            $input = str_replace($name, $val, $input);
        }
        return $input;
    }


    /**
     *  _parseArr2Form
     *
     *  Add elements/rules/groups to an given HTML_QuickForm object
     *
     *  @param form object, reference to HTML_QuickForm object
     *  @param mask array, reference to array defining to form elements
     *  @param side string, side where the validation should beeing
     */
    function _parseArr2Form(&$form, &$mask, $side='client')
    {
        foreach($mask as $k=>$v) {
            ## add elements ########################
            if ($v['type']=='radio') {
                foreach($v['options'] as $rk=>$rv) {
                    $radio[] =& $form->createElement($v['type'], NULL, NULL, $rv, $rk, $v['attributes']);
                }
                $form->addGroup($radio, $v['element'], $this->tra($v['label']));
                unset($radio);

            } elseif ($v['type']=='select') {
                $elem[$v['element']] =& $form->createElement($v['type'], $v['element'], $this->tra($v['label']), $v['options'], $v['attributes']);
                $elem[$v['element']]->setMultiple($v['multiple']);
                if (isset($v['selected'])) $elem[$v['element']]->setSelected($v['selected']);
                if (!$v['groupit'])        $form->addElement($elem[$v['element']]);

            } elseif ($v['type']=='date') {
                $elem[$v['element']] =& $form->createElement($v['type'], $v['element'], $this->tra($v['label']), $v['options'], $v['attributes']);
                if (!$v['groupit'])     $form->addElement($elem[$v['element']]);

            } elseif ($v['type']=='checkbox' || $v['type']=='static') {
                $elem[$v['element']] =& $form->createElement($v['type'], $v['element'], $this->tra($v['label']), $v['text'], $v['attributes']);
                if (!$v['groupit'])     $form->addElement($elem[$v['element']]);

            } elseif (isset($v['type'])) {
                $elem[$v['element']] =& $form->createElement($v['type'], $v['element'], $this->tra($v['label']),
                                            ($v[type]=='text' || $v['type']=='file' || $v['type']=='password') ? array_merge($v['attributes'], array('size'=>UI_INPUT_STANDARD_SIZE, 'maxlength'=>UI_INPUT_STANDARD_MAXLENGTH)) :
                                            ($v['type']=='textarea' ? array_merge($v['attributes'], array('rows'=>UI_TEXTAREA_STANDART_ROWS, 'cols'=>UI_TEXTAREA_STANDART_COLS)) : $v['attributes'])
                                        );
                if (!$v['groupit'])     $form->addElement($elem[$v['element']]);
            }
            ## add required rule ###################
            if ($v['required']) {
                $form->addRule($v['element'], isset($v['requiredmsg'])?$this->tra($v['requiredmsg']):$this->tra('Missing value for $1', $this->tra($v['label'])), 'required', NULL, $side);
            }
            ## add constant value ##################
            if (isset($v['constant'])) {
                $form->setConstants(array($v['element']=>$v['constant']));
            }
            ## add default value ###################
            if (isset($v['default'])) {
                $form->setDefaults(array($v['element']=>$v['default']));
            }
            ## add other rules #####################
            if ($v['rule']) {
                $form->addRule($v['element'], isset($v['rulemsg']) ? $this->tra($v['rulemsg']) : $this->tra('$1 must be $2', $this->tra($v['element']), $this->tra($v['rule'])), $v['rule'] ,$v['format'], $side);
            }
            ## add group ###########################
            if (is_array($v['group'])) {
                foreach($v['group'] as $val) {
                    $groupthose[] =& $elem[$val];
                }
                $form->addGroup($groupthose, $v['name'], $this->tra($v['label']), $v['seperator'], $v['appendName']);
                if ($v['rule']) {
                    $form->addRule($v['name'], isset($v['rulemsg']) ? $this->tra($v['rulemsg']) : $this->tra('$1 must be $2', $this->tra($v['name'])), $v['rule'], $v['format'], $side);
                }
                if ($v['grouprule']) {
                    $form->addGroupRule($v['name'], $v['arg1'], $v['grouprule'], $v['format'], $v['howmany'], $side, $v['reset']);
                }
                unset($groupthose);
            }
            ## check error on type file ##########
            if ($v['type']=='file') {
                if ($_POST[$v['element']]['error']) {
                    $form->setElementError($v['element'], isset($v['requiredmsg']) ? $this->tra($v['requiredmsg']) : $this->tra('Missing value for $1', $this->tra($v['label'])));
                }
            }
        }

        reset($mask);
        $form->validate();
    }


    /**
     *  _dateArr2Str
     *
     *  Converts date-array from form into string
     *
     *  @param input array, reference to array of form-elements
     */
    function _dateArr2Str(&$input)
    {
        foreach ($input as $k=>$v){
            if (is_array($v) && isset($v['d']) && (isset($v['M']) || isset($v['m'])) && (isset($v['Y']) || isset($v['y']))){
                $input[$k] = $v['Y'].$v['y'].'-'.(strlen($v['M'].$v['m'])==2 ? $v['M'].$v['m'] : '0'.$v['M'].$v['m']).'-'.(strlen($v['d'])==2 ? $v['d'] : '0'.$v['d']);
            }
        }
    }


    /**
     *  _getInfo
     *
     *  Call getid3 library to analyze media file and show some results
     *
     *  @param $id int local ID of file
     *  @param $format string
     */
    function _getInfo($id, $format)
    {
        $ia = $this->gb->analyzeFile($id, $this->sessid);

        if ($format=='array') {
            return array(
                    'Format.Extent'             => $ia['playtime_string'],
                    'Format.Medium.Bitrate'     => $ia['audio']['bitrate'],
                    'Format.Medium.Channels'    => $ia['audio']['channelmode'],
                    'Format.Medium.Samplerate'  => $ia['audio']['sample_rate'],
                    'Format.Medium.Encoder'     => $ia['audio']['codec'] ? $ia['audio']['codec'] : $ia['audio']['encoder'],
                   );
        } elseif ($format=='text') {
            return "fileformat: {$ia['fileformat']}<br>
                    channels: {$ia['audio']['channels']}<br>
                    sample_rate: {$ia['audio']['sample_rate']}<br>
                    bits_per_sample: {$ia['audio']['bits_per_sample']}<br>
                    channelmode: {$ia['audio']['channelmode']}<br>
                    title: {$ia['id3v1']['title']}<br>
                    artist: {$ia['id3v1']['artist']}<br>
                    comment: {$ia['id3v1']['comment']}";
        } elseif ($format=='xml') {
            return
                  '<?xml version="1.0" encoding="utf-8"?>
                  <audioClip>
                  <metadata
                    xmlns="http://www.streamonthefly.org/"
                    xmlns:dc="http://purl.org/dc/elements/1.1/"
                    xmlns:dcterms="http://purl.org/dc/terms/"
                    xmlns:xbmf="http://www.streamonthefly.org/xbmf"
                    xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
                   >
                  <dc:title>'.$this->_getFileTitle($id).'</dc:title>
                  <dcterms:extent>'.$ia['playtime_string'].'</dcterms:extent>
                  </metadata>
                  </audioClip>';

        }
    }


    function _twoDigits($num)
    {
        if ($num < 10)
            return ("0$num");
        else
            return $num;
    }


    function _getDArr($format)
    {
        #$arr['']  = '00';
        switch($format) {
        case 'h':
            for($n=0; $n<=23; $n++) {
                $arr[$this->_twoDigits($n)] = $this->_twoDigits($n);
            }
            break;

        case 'm':
        case 's':
            for($n=0; $n<=59; $n++) {
                $arr[$this->_twoDigits($n)] = $this->_twoDigits($n);
            }
            break;
        }

        return $arr;
    }


    function _toHex($gunid)
    {
        $res = $this->dbc->query("SELECT to_hex($gunid)");
        $row = $res->fetchRow();

        return $row['to_hex'];
    }


    function _toInt8($gunid)
    {
        $res = $this->dbc->query("SELECT x'$gunid'::bigint");
        $row = $res->fetchRow();

        return $row['int8'];
    }


    function getSP()
    {
        $spData = $this->gb->loadPref($this->sessid, UI_SCRATCHPAD_KEY);
        if (!PEAR::isError($spData) && trim($spData) != '') {
            $arr = explode(' ', $spData);
            /*
            ## Akos old format #####################################
            foreach($arr as $val) {
                if (preg_match(UI_SCRATCHPAD_REGEX, $val)) {
                    list ($gunid, $date) = explode(':', $val);
                    if ($this->gb->_idFromGunid($gunid) != FALSE) {
                        $res[] = array_merge($this->_getMetaInfo($this->gb->_idFromGunid($gunid)), array('added' => $date));
                    }
                }
            }
            */

            ## new format ##########################################
            foreach($arr as $gunid) {
                if (preg_match('/[0-9]{1,20}/', $gunid)) {
                    if ($this->gb->_idFromGunid($this->_toHex($gunid)) != FALSE) {
                        $res[] = $this->_getMetaInfo($this->gb->_idFromGunid($this->_toHex($gunid)));
                    }
                }
            }


            return ($res);
        } else {
            return FALSE;
        }
    }

    function _saveSP($data)
    {
        if (is_array($data)) {
            foreach($data as $val) {
                #$str .= $val['gunid'].':'.$val['added'].' ';   ## new format ###
                $str .= $this->_toInt8($val['gunid']).' ';      ## Akos´ old format ###
            }
        }

        $this->gb->savePref($this->sessid, UI_SCRATCHPAD_KEY, $str);
    }

    function add2SP($id)
    {
        $info = $this->_getMetaInfo($id);
        $this->redirUrl = UI_BROWSER.'?popup[]=_reload_parent&popup[]=_close';

        if ($sp = $this->getSP()) {
            foreach ($sp as $key => $val) {
                if ($val['gunid'] == $info['gunid']) {
                    unset($sp[$key]);
                    $this->_retMsg('Entry $1 was already on $2.\nMoved to Top.', $info['title'], $val['added']);
                } else {
                    #$this->incAccessCounter($id);
                }
            }
        }


        $sp = array_merge(array(array('gunid'   => $info['gunid'],
                                      'added'   => date('Y-m-d')
                               ),
                          ),
                          is_array($sp) ? $sp : NULL);

        $this->_saveSP($sp);
        #$this->_retmsg('Entry $1 added', $info['title']);
        return TRUE;
    }


    function remFromSP($id)
    {
        $info = $this->_getMetaInfo($id);
        $this->redirUrl = UI_BROWSER.'?popup[]=_reload_parent&popup[]=_close';

        if ($sp = $this->getSP()) {
            foreach ($sp as $val) {
                if ($val['gunid'] != $info['gunid']) {
                    $new[] = $val;
                }
            }

            $this->_saveSP($new);
            $this->_retmsg('Entry $1 deleted', $info['title']);
            return TRUE;
        }
    }


    function _retMsg($msg, $p1=NULL, $p2=NULL, $p3=NULL, $p4=NULL, $p5=NULL, $p6=NULL, $p7=NULL, $p8=NULL, $p9=NULL)
    {
        $_SESSION['alertMsg'] .= $this->tra($msg, $p1, $p2, $p3, $p4, $p5, $p6, $p7, $p8, $p9).'\n';
    }


    function _getMetaInfo($id)
    {
        $data = array('id'          => $id,
                      'gunid'       => $this->gb->_gunidFromId($id),
                      'title'       => $this->_getMDataValue($id, 'title'),
                      'artist'      => $this->_getMDataValue($id, 'artist'),
                      'duration'    => substr($this->_getMDataValue($id, 'format.extent'), 0 ,8),
                      'type'        => $this->_getType($id),
                );

        return ($data);
    }


    function _getMDataValue($id, $key)
    {
        $value = array_pop($this->gb->getMDataValue($id, $key, $this->sessid));
        return $value['value'];
    }


    function _getFileTitle($id)
    {
        $file = array_pop($this->gb->getPath($id));
        return $file['name'];
    }

    function _getType($id)
    {
        if ($this->gb->existsPlaylist($this->sessid, $this->gb->_gunidFromId($id))) return 'playlist';
        return 'file';
        #if ($this->gb->existsAudioClip($this->sessid, $this->gb->_gunidFromId($id))) return 'audioclip';
        #if ($this->gb->existsFile($this->sessid, $this->gb->_gunidFromId($id))) return 'File';

        return FALSE;
    }
}
?>
