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
        $dbc = DB::connect($config['dsn'], TRUE);
        if (DB::isError($dbc)) {
            die($dbc->getMessage());
        }
        $dbc->setFetchMode(DB_FETCHMODE_ASSOC);
        $this->gb =& new GreenBox(&$dbc, $config);
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
                                            ($v['type']=='textarea'                  ? array_merge($v['attributes'], array('rows'=>UI_TEXTAREA_STANDART_ROWS, 'cols'=>UI_TEXTAREA_STANDART_COLS)) : $v['attributes'])
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
     *  getInfo
     *
     *  Call getid3 library to analyze media file and show some results
     *
     *  @param $id int local ID of file
     *  @param $format string
     */
    function getInfo($id, $format)
    {
        $ia = $this->gb->analyzeFile($id, $this->sessid);

        if ($format=='array') {
            return array(
                    'Format.Extent'             => $ia['playtime_seconds'],
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
                  <dc:title  >taken from test xml</dc:title>
                  <dcterms:extent  >00:30:00.000000</dcterms:extent>
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


    function getSP()
    {
        $spData = $this->gb->loadPref($this->sessid, 'scratchPadContents');
        if (!PEAR::isError($spData)) {
            $arr = explode(' ', $spData);
            foreach($arr as $val) {
                $pieces = explode(':', $val);
                $filedata = $this->getFileInfo($pieces[0]);
                $res[] = array('id'         => $pieces[0],
                               #'added'      => $pieces[1],
                               'name'       => $filedata['name'],
                               'duration'   => $filedata['playtime_string']
                         );
            }

            return ($res);
        } else {
            return FALSE;
        }
    }

    function saveSP($data)
    {
        foreach($data as $val) {
            $str .= $val['id'].':'.$val['added'].' ';
        }

        $this->gb->savePref($this->sessid, 'scratchPadContents', trim($str));
    }

    function add2SP($id)
    {
        if ($sp = $this->getSP()) {
            foreach ($sp as $val) {
                if ($val['id'] == $id) $exists = TRUE;
            }
        }

        if(!$exists) {
            $sp[] = array('id'      => $id,
                          'added'   => date('Y-m-d')
                    );
            $this->saveSP($sp);
            $this->_retmsg('Entry $1 added', $id);
        } else {
            $this->_retmsg('Entry $1 already exists', $id);
        }

        $this->redirUrl = UI_BROWSER.'?popup[]=_reload_parent&popup[]=_close';
    }

    function _retmsg($msg, $p1=NULL, $p2=NULL, $p3=NULL, $p4=NULL, $p5=NULL, $p6=NULL, $p7=NULL, $p8=NULL, $p9=NULL)
    {
        $_SESSION['alertMsg'] = $this->tra($msg, $p1, $p2, $p3, $p4, $p5, $p6, $p7, $p8, $p9);
    }


    function getFileName($id)
    {
        $file = array_pop($this->gb->getPath($id));
        return $file['name'];
    }

    function getFileInfo($id)
    {
        $f = $this->gb->analyzeFile($id, $this->sessid);
        return array(
                    'name'              => $this->getFileName($id),
                    'type'              => 0,
                    'filename'          => $f['filename'],
                    'playtime_seconds'  => $f['playtime_seconds'],
                    'playtime_string'   => $f['playtime_string']
                );
    }
}
?>
