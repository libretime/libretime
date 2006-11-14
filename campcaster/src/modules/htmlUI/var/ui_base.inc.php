<?php
function jscom_wrapper($object, $parent, $method) {
    $args = func_get_args();
    unset($args[0]);
    unset($args[1]);
    unset($args[2]);
    return call_user_func_array(array(&$GLOBALS[$object]->$parent, "$method"), $args);
}


function errCallBack($err)
{
    echo "<pre>gm:\n".$err->getMessage()."\ndi:\n".$err->getDebugInfo()."\nui:\n".$err->getUserInfo()."\n";
    echo "<hr>BackTrace:\n";
    print_r($err->backtrace);
    echo "</pre>\n";
    exit;
}

// --- basic funtionality ---

function _getLanguages()
{
    $languages =& $_SESSION[UI_LOCALIZER_SESSNAME]['languages'];

    if (!is_array($languages)) {
        include_once dirname(__FILE__).'/localizer/loader.inc.php';
        foreach (getLanguages() as $k => $lang) {
            $languages[$lang->m_languageId] = $lang->m_nativeName;
        }

    }
    return $languages;
}


/**
 * Translate the given string using localization files.
 *
 * @param string $input
 * 		string to translate
 * @return string
 * 		translated string
 */
function tra($input)
{
    global $uiBase;

    if (defined(UI_LOCALIZER_SESSNAME)) {
        $GS =& $_SESSION[UI_LOCALIZER_SESSNAME]['GS'];
    } else {
        static $GS;
    }

    if ($uiBase->langid && !is_array($GS)) {
        #echo "load translation";
        include_once dirname(__FILE__).'/localizer/loader.inc.php';
        #echo $uiBase->langid;
        $GS = loadTranslations($uiBase->langid);
        #print_r($GS);
    }
    ## end init ####################################

    if (isset($GS[$input]) && !empty($GS[$input])) {
        $input = $GS[$input];
    }
    $nr = func_num_args();
    if ($nr > 1) {
        for ($i = 1; $i < $nr; $i++){
            $name  = '$'.$i;
            $val   = func_get_arg($i);
            $input = str_replace($name, $val, $input);
        }
    }
    return $input;
} // fn tra


function _getDArr($format)
{
    #$arr['']  = '00';
    switch($format) {
    case 'h':
        for($n=0; $n<=23; $n++) {
            $arr[sprintf('%02d', $n)] = sprintf('%02d', $n);
        }
        break;

    case 'm':
    case 's':
        for($n=0; $n<=59; $n++) {
            $arr[sprintf('%02d', $n)] = sprintf('%02d', $n);
        }
        break;
    }

    return $arr;
} // fn _getDArr


function _getNumArr($start, $end, $step=1)
{
    for($n=$start; $n<=$end; $n+=$step) {
        $arr[$n] = $n;
    }
    return $arr;
} // fn _getNumArr


/**
 * HTML User Interface module
 * @package Campcaster
 * @subpackage htmlUI
 * @version $Revision$
 */
class uiBase
{
    public $gb; // GreenBox
    public $STATIONPREFS;
    public $SCRATCHPAD;
    public $SEARCH;
    public $BROWSE;
    // Note: loading HUBBROWSE on every page load slows things down
    // a lot.  we only load it on demand.
    //public $HUBBROWSE;
    public $HUBSEARCH;
    public $PLAYLIST;
    public $SCHEDULER;
    public $SUBJECTS;
    public $EXCHANGE;
    public $TRANSFERS;
    public $redirUrl;
    public $dbc;
    public $config;
    public $sessid;
    public $userid;
    public $login;
    public $id;
    public $langid;
    public $pid;
    public $type;
    public $fid;
    public $homeid;
    public $alertMsg;

    /**
     * @param array $config
     * 		configurartion data
     */
    public function __construct(&$config)
    {
        $this->dbc = DB::connect($config['dsn'], TRUE);
        if (DB::isError($this->dbc)) {
            die($this->dbc->getMessage());
        }
        $this->dbc->setFetchMode(DB_FETCHMODE_ASSOC);
        $this->gb = new GreenBox($this->dbc, $config);
        $this->config =& $config;
        $this->config['accessRawAudioUrl'] = $config['storageUrlPath'].'/xmlrpc/simpleGet.php';
        $this->sessid = isset($_REQUEST[$config['authCookieName']]) ?
                            $_REQUEST[$config['authCookieName']] : null;
        $this->userid = $this->gb->getSessUserId($this->sessid);
        $this->login = $this->gb->getSessLogin($this->sessid);
        if (PEAR::isError($this->login)) {
            $this->login = null;
        }
        $this->langid =& $_SESSION['langid'];

        if (!is_null($this->login)) {
            if (isset($_REQUEST['id'])) {
                $this->id = $_REQUEST['id'];
            } else {
                $this->id = $this->gb->getObjId($this->login, $this->gb->storId);
            }
            $this->pid = $this->gb->getparent($this->id) != 1 ?
                                $this->gb->getparent($this->id) : FALSE;
            $this->type = $this->gb->getFileType($this->id);
            $this->fid = ($this->type == 'Folder') ? $this->id : $this->pid;
            $this->homeid = $this->gb->getObjId($this->login, $this->gb->storId);
        }

    }


    public function init()
    {
        $this->STATIONPREFS =& $_SESSION[UI_STATIONINFO_SESSNAME];
        $this->SCRATCHPAD = new uiScratchPad($this);
        $this->SEARCH = new uiSearch($this);
        $this->BROWSE = new uiBrowse($this);
        //$this->HUBBROWSE = new uiHubBrowse($this);
        $this->HUBSEARCH = new uiHubSearch($this);
        $this->PLAYLIST = new uiPlaylist($this);
        $this->SCHEDULER = new uiScheduler($this);
        $this->SUBJECTS = new uiSubjects($this);
        $this->EXCHANGE = new uiExchange($this);
        $this->TRANSFERS = new uiTransfers($this);
    }


    /**
     * Load system preferences.
     *
     * @param array $mask
     * @param boolean $reload
     */
    public function loadStationPrefs(&$mask, $reload=FALSE)
    {
        if (!is_array($this->STATIONPREFS) || ($reload === TRUE) ) {
        	$this->STATIONPREFS = array();
            foreach ($mask as $key => $val) {
                if (isset($val['isPref']) && $val['isPref']) {
                    if (is_string($setting = $this->gb->loadGroupPref(NULL, 'StationPrefs', $val['element']))) {
                        $this->STATIONPREFS[$val['element']] = $setting;
                    } elseif ($val['required']){
                        // set default values on first login
                        $this->gb->saveGroupPref($this->sessid, 'StationPrefs', $val['element'], $val['default']);
                        $this->STATIONPREFS[$val['element']] = $val['default'];
                    }
                }
            }
        }
    } // fn loadStationPrefs


    /**
     *  Add elements/rules/groups to an given HTML_QuickForm object
     *
     *  @param HTML_Quickform $form
     * 		reference to HTML_QuickForm object
     *  @param array $mask
     * 		reference to array defining the form elements
     *  @param string $side
     * 		can be 'client' or 'server' - this is where the form validation occurs.
     */
    public static function parseArrayToForm(&$form, &$mask, $side='client')
    {
        foreach ($mask as $v) {
            $attrs = isset($v['attributes']) ? $v['attributes'] : null;
            $type = isset($v['type']) ? $v['type'] : null;
            $label = isset($v['label']) ? tra($v['label']) : '';
            $required = (isset($v['required']) && $v['required']);
            $groupit = (isset($v['groupit']) && $v['groupit']);

            ## add elements ########################
            if ($type == 'radio') {
                foreach ($v['options'] as $textLabel => $radioValue) {
                    $radio[] =& $form->createElement($type, NULL, NULL, $textLabel, $radioValue, $attrs);
                }
                $form->addGroup($radio, $v['element'], $label);
                unset($radio);
            } elseif ($type == 'select') {
                $elem[$v['element']] =& $form->createElement($type, $v['element'], $label, $v['options'], $attrs);

                $multiple = isset($v['multiple']) && $v['multiple'];
                $elem[$v['element']]->setMultiple($multiple);

                if (isset($v['selected'])) {
                    $elem[$v['element']]->setSelected($v['selected']);
                }
                if (!$groupit) {
                    $form->addElement($elem[$v['element']]);
                }
            } elseif ($type == 'date') {
                $elem[$v['element']] =& $form->createElement($type, $v['element'], $label, $v['options'], $attrs);
                if (!$groupit) {
                    $form->addElement($elem[$v['element']]);
                }
            } elseif ( ($type == 'checkbox') || ($type == 'static') ) {
                $elem[$v['element']] =& $form->createElement($type, $v['element'], $label, tra($v['text']), $attrs);
                if (!$groupit) {
                    $form->addElement($elem[$v['element']]);
                }
            } elseif (!is_null($type)) {
                if (is_null($attrs)) {
                    $attrs = array();
                }
                if (in_array($type, array('text','file','password'))) {
                    $addAttrs = array('size' => UI_INPUT_STANDARD_SIZE,
                                      'maxlength'=>UI_INPUT_STANDARD_MAXLENGTH);
                    $attrs = array_merge($addAttrs, $attrs);
                } elseif ($type=='textarea') {
                    $addAttrs = array('rows'=>UI_TEXTAREA_STANDART_ROWS,
                                      'cols'=>UI_TEXTAREA_STANDART_COLS);
                    $attrs = array_merge($addAttrs, $attrs);
                } elseif (in_array($type, array('button', 'submit', 'reset'))) {
                    $addAttrs = array('class'=>UI_BUTTON_STYLE);
                    $attrs = array_merge($addAttrs, $attrs);
                }

                $elem[$v['element']] =& $form->createElement($type,
                    $v['element'],
                    $label,
                    $attrs);
                if (!$groupit) {
                    $form->addElement($elem[$v['element']]);
                }
            }
            ## add required rule ###################
            if ($required) {
                $form->addRule($v['element'], isset($v['requiredmsg']) ? tra($v['requiredmsg']) : tra('Missing value for $1', $label), 'required', NULL, $side);
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
            if (isset($v['rule']) && $v['rule']) {
                $form->addRule($v['element'], isset($v['rulemsg']) ? tra($v['rulemsg']) : tra('$1 must be $2', tra($v['element']), tra($v['rule'])), $v['rule'] ,$v['format'], $side);
            }
            ## add group ###########################
            if (isset($v['group']) && is_array($v['group'])) {
                foreach ($v['group'] as $val) {
                    $groupthose[] =& $elem[$val];
                }
                $groupName = isset($v['name']) ? $v['name'] : null;
                $seperator = isset($v['seperator']) ? $v['seperator'] : null;
                $appendName = isset($v['appendName']) ? $v['appendName'] : null;
                $form->addGroup($groupthose, $groupName, $label, $seperator, $appendName);
                if (isset($v['rule']) && $v['rule']) {
                    $form->addRule($v['name'], isset($v['rulemsg']) ? tra($v['rulemsg']) : tra('$1 must be $2', tra($v['name'])), $v['rule'], $v['format'], $side);
                }
                if (isset($v['grouprule']) && $v['grouprule']) {
                    $form->addGroupRule($v['name'], $v['arg1'], $v['grouprule'], $v['format'], $v['howmany'], $side, $v['reset']);
                }
                unset($groupthose);
            }
            ## check error on type file ##########
            if ($type == 'file') {
                if (isset($_POST[$v['element']]['error'])) {
                    $form->setElementError($v['element'], isset($v['requiredmsg']) ? tra($v['requiredmsg']) : tra('Missing value for $1', $label));
                }
            }
        }

        reset($mask);
        $form->validate();
    } // fn parseArrayToForm


    /**
     * Converts date-array from form into string
     *
     * @param array $input
     * 		array of form-elements
     */
//    function _dateArr2Str(&$input)
//    {
//        foreach ($input as $k => $v){
//            if (is_array($v)) {
//                if ( ( isset($v['d']) ) && ( isset($v['M']) || isset($v['m']) ) && ( isset($v['Y']) || isset($v['y']) ) ) {
//                    $input[$k] = $v['Y'].$v['y'].'-'.sprintf('%02d', $v['M'].$v['m']).'-'.sprintf('%02d', $v['d']);
//                }
//                if ( ( isset($v['H']) ) || isset($v['h'] ) && ( isset($v['i']) ) && ( isset($v['s']) ) ) {
//                    $input[$k] = sprintf('%02d', $v['H'].$v['h']).':'.sprintf('%02d', $v['i']).':'.sprintf('%02d', $v['s']);
//                }
//            }
//        }
//
//        return $input;
//    } // fn _dateArr2Str


    /**
     * Call getid3 library to analyze media file and show some results
     *
     * @param int $id
     * 		local ID of file
     * @param string $format
     */
    public function analyzeFile($id, $format)
    {
        $ia = $this->gb->analyzeFile($id, $this->sessid);
        $s  = $ia['playtime_seconds'];
        $extent = date('H:i:s', floor($s)-date('Z')).substr(number_format($s, 6), strpos(number_format($s, 6), '.'));

        if ($format=='text') {
            return "<div align='left'><pre>".var_export($ia, TRUE)."</pre></div>";
        } elseif ($format=='xml') {
            return '!!!XML IS DEPRICATED!!!
                    <?xml version="1.0" encoding="utf-8"?>
                    <audioClip>
                    <metadata
                      xmlns:dc="http://purl.org/dc/elements/1.1/"
                      xmlns:dcterms="http://purl.org/dc/terms/"
                      xmlns:xml="http://www.w3.org/XML/1998/namespace"
                      xmlns:ls="http://mdlf.org/campcaster/elements/1.0/"
                     >
                   <dc:title>'.$this->_getFileTitle($id).'</dc:title>
                   <dcterms:extent>'.$extent.'</dcterms:extent>
                   </metadata>
                   </audioClip>';
        }
        return FALSE;
    } // fn analyzeFile


    public function toHex($gunid)
    {
        $res = $this->dbc->query("SELECT to_hex($gunid)");
        $row = $res->fetchRow();
        return $row['to_hex'];
    } // fn toHex


    public function toInt8($gunid)
    {
        $res = $this->dbc->query("SELECT x'$gunid'::bigint");
        $row = $res->fetchRow();
        return $row['int8'];
    } // fn toInt8


    /**
     * Add an alert message to the session var.
     * @todo Fix this to use call_user_func()
     *
     * @param string $msg
     * @param string $p1
     * @param string $p2
     * @param string $p3
     * @param string $p4
     * @param string $p5
     * @param string $p6
     * @param string $p7
     * @param string $p8
     * @param string $p9
     */
    public function _retMsg($msg, $p1=NULL, $p2=NULL, $p3=NULL, $p4=NULL, $p5=NULL, $p6=NULL, $p7=NULL, $p8=NULL, $p9=NULL)
    {
        if (!isset($_SESSION['alertMsg'])) {
            $_SESSION['alertMsg'] = '';
        }
        $_SESSION['alertMsg'] .= tra($msg, $p1, $p2, $p3, $p4, $p5, $p6, $p7, $p8, $p9).'\n';
    } // fn _retMsg


    public function getMetaInfo($id)
    {
        $type = strtolower($this->gb->getFileType($id));
        $data = array('id'          => $id,
                      'gunid'       => $this->gb->_gunidFromId($id),
                      'title'       => $this->getMetadataValue($id, UI_MDATA_KEY_TITLE),
                      'creator'     => $this->getMetadataValue($id, UI_MDATA_KEY_CREATOR),
                      'duration'    => $this->getMetadataValue($id, UI_MDATA_KEY_DURATION),
                      'type'        => $type,
                      #'isAvailable' => $type == 'playlist' ? $this->gb->playlistIsAvailable($id, $this->sessid) : NULL,
                );
         return ($data);
    } // fn getMetaInfo


    public function getMetadataValue($id, $key, $langid=NULL, $deflangid=UI_DEFAULT_LANGID)
    {
        if (!$langid) {
            $langid = $_SESSION['langid'];
        }

        if (is_array($arr = $this->gb->getMDataValue($id, $key, $this->sessid, $langid, $deflangid))) {
            $value = current($arr);
            return $value['value'];
        }
        return FALSE;
    } // fn getMetadataValue


    public function setMetadataValue($id, $key, $value, $langid=NULL)
    {
        if (!$langid) {
            $langid = UI_DEFAULT_LANGID;
        }
        if (ini_get('magic_quotes_gpc')) {
            $value = str_replace("\'", "'", $value);
        }

        if ($this->gb->setMDataValue($id, $key, $this->sessid, $value, $langid)) {
            return TRUE;
        }
        return FALSE;
    } // fn setMetadataValue


    /**
     * @param unknown_type $id
     * @return string/FALSE
     */
    private function _getFileTitle($id)
    {
        if (is_array($arr = $this->gb->getPath($id))) {
            $file = array_pop($arr);
            return $file['name'];
        }
        return FALSE;
    } // fn _getFileTitle


//    function _isFolder($id)
//    {
//        if (strtolower($this->gb->getFileType($id)) != 'folder') {
//            return FALSE;
//        }
//        return TRUE;
//    } // fn _isFolder


    public static function formElementEncode($str)
    {
        $str = str_replace(':', '__', $str);
        #$str = str_replace('.', '_', $str);
        return $str;
    } // fn formElementEncode


    public static function formElementDecode($str)
    {
        $str = str_replace('__', ':', $str);
        #$str = str_replace('_', '.', $str);
        return $str;
    } // fn formElementDecode

} // class uiBase
?>