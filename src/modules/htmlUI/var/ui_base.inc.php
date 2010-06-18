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

function __autoload($p_className)
{
    foreach (uiBase::$m_classMapping as $item) {
        if (strtolower($p_className) == $item['class']) {
            $class_filename = $item['file'];
            require_once(dirname(__FILE__).'/'.$class_filename);
            break;
        }  
    }
}


/**
 * HTML User Interface module
 * @package Campcaster
 * @subpackage htmlUI
 * @version $Revision$
 */
class uiBase
{
    /**
     * @var GreenBox
     */
    public $gb;

    public $STATIONPREFS;

    /**
     * @var uiScratchPad
     */
    private $SCRATCHPAD;

    /**
     * @var uiSearch
     */
    private $SEARCH;

    /**
     * @var uiBrowse
     */
    private $BROWSE;

    /**
     * @todo loading HUBBROWSE on every page load slows things down
     * a lot.  We should only load it on demand.
     *
     * @var uiHubBrowse
     */
    private $HUBBROWSE;

    /**
     * @var uiHubSearch
     */
    private $HUBSEARCH;

    /**
     * @var uiPlaylist
     */
    private $PLAYLIST;

    /**
     * @var uiScheduler
     */
    private $SCHEDULER;

    /**
     * @var uiSubjects
     */
    private $SUBJECTS;

    /**
     * @var uiExchange
     */
    private $EXCHANGE;

    /**
     * @var uiTransfers
     */
    private $TRANSFERS;

    /**
     * @var string
     */
    public $redirUrl;

    /**
     * @var DB
     */
    //public $dbc;

    /**
     * @var string
     */
    public $sessid;

    /**
     * @var int
     */
    public $userid;

    public $login;
    public $id;
    public $langid;
    public $pid;
    public $type;
    public $fid;
    public $homeid;

    /**
     * @var string
     */
    public $alertMsg;
    
    /**
     * This mapping keeps relation between uiBase::properties,
     * class names and filenames and is used in
     * __autoload() and uiBase::__get() functions.
     *
     * @var array
     */
    public static $m_classMapping = array(
        'SCRATCHPAD'   => array('class' => 'uiscratchpad', 'file' => 'ui_scratchpad.class.php'),
        'SEARCH'       => array('class' => 'uisearch', 'file' => 'ui_search.class.php'),
        'BROWSE'       => array('class' => 'uibrowse', 'file' => 'ui_browse.class.php'),
        'HUBBROWSE'    => array('class' => 'uihubbrowse', 'file' => 'ui_hubBrowse.class.php'),
        'HUBSEARCH'    => array('class' => 'uihubsearch', 'file' => 'ui_hubSearch.class.php'),
        'PLAYLIST'     => array('class' => 'uiplaylist', 'file' => 'ui_playlist.class.php'),
        'SCHEDULER'    => array('class' => 'uischeduler', 'file' => 'ui_scheduler.class.php'),
        'SUBJECTS'     => array('class' => 'uisubjects', 'file' => 'ui_subjects.class.php'),
        'EXCHANGE'     => array('class' => 'uiexchange', 'file' => 'ui_exchange.class.php'),
        'TRANSFERS'    => array('class' => 'uitransfers', 'file' => 'ui_transfers.class.php'),
        'CALENDAR'     => array('class' => 'uicalendar', 'file' => 'ui_calendar.class.php'),
        array('class' => 'jscom', 'file' => 'ui_jscom.php'),
        'TWITTER'      => array('class' => 'uitwitter', 'file' => 'ui_twitter.class.php'),
        array('class' => 'twitter', 'file' => 'lib/twitter.class.php') 
    );


    /**
     * @param array $config
     * 		configuration data
     */
    public function __construct()
    {
        global $CC_DBC, $CC_CONFIG;
        $this->gb = new GreenBox();
        $CC_CONFIG['accessRawAudioUrl'] = $CC_CONFIG['storageUrlPath'].'/xmlrpc/simpleGet.php';
        $this->sessid = isset($_REQUEST[$CC_CONFIG['authCookieName']]) ?
                            $_REQUEST[$CC_CONFIG['authCookieName']] : null;
        $this->userid = GreenBox::GetSessUserId($this->sessid);
        $this->login = Alib::GetSessLogin($this->sessid);
        if (PEAR::isError($this->login)) {
            $this->login = null;
        }
        $this->langid =& $_SESSION['langid'];

        if (!is_null($this->login)) {
            if (isset($_REQUEST['id']) && is_numeric($_REQUEST['id'])) {
                $this->id = $_REQUEST['id'];
            } else {
                $this->id = M2tree::GetObjId($this->login, $this->gb->storId);
            }
            if (!is_null($this->id)) {
                $parentId = M2tree::GetParent($this->id);
                $this->pid = ($parentId != 1) ? $parentId : FALSE;
                $this->type = Greenbox::getFileType($this->id);
                $this->fid = ($this->type == 'Folder') ? $this->id : $this->pid;
                $this->homeid = M2tree::GetObjId($this->login, $this->gb->storId);
            }
        }

    }


    public function init()
    {
        $this->STATIONPREFS =& $_SESSION[UI_STATIONINFO_SESSNAME];
    }
    
    /**
     * Dynamically initialize uiBase properties,
     * which keep objects (uiBase->SEARCH, $uiBase->BROWSE etc)
     *
     * @param unknown_type $p_class
     * @return unknown
     */
    public function __get($p_class)
    {
        if (strtoupper($p_class !== $p_class)) {
            return;    
        }
        
        if (!is_object($this->$p_class)) {
            if ($class_name = uiBase::$m_classMapping[$p_class]['class']) {
                $this->$p_class = new $class_name($this);
            }  
        }
        return $this->$p_class;
    }


    /**
     * Load the system preferences into the session.
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
                	$setting = $this->gb->loadGroupPref(NULL, 'StationPrefs', $val['element']);
                    if (is_string($setting)) {
                        $this->STATIONPREFS[$val['element']] = $setting;
                    } elseif ($val['required']) {
                        // set default values on first login
                        $default = isset($val['default'])?$val['default']:null;
                        $this->gb->saveGroupPref($this->sessid, 'StationPrefs', $val['element'], $default);
                        $this->STATIONPREFS[$val['element']] = $default;
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
                $v['options']['maxYear'] = isset($v['options']['maxYear']) ? $v['options']['maxYear'] : date('Y') + 10;
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
        global $CC_DBC;
        $res = $CC_DBC->query("SELECT to_hex($gunid)");
        $row = $res->fetchRow();
        return $row['to_hex'];
    } // fn toHex


    public function toInt8($gunid)
    {
        global $CC_DBC;
        $res = $CC_DBC->query("SELECT x'$gunid'::bigint");
        $row = $res->fetchRow();
        return $row['int8'];
    } // fn toInt8


    /**
     * Add an alert message to the session var.
     *
     * @param string $msg
     */
    public function _retMsg($msg)
    {
        if (!isset($_SESSION['alertMsg'])) {
            $_SESSION['alertMsg'] = '';
        }
       	$args = func_get_args();
        $_SESSION['alertMsg'] .= call_user_func_array('tra', $args).'\n';
    } // fn _retMsg


    public function getMetaInfo($id)
    {
        $type = strtolower(GreenBox::getFileType($id));
        $data = array('id' => $id,
                      'gunid' => BasicStor::GunidFromId($id),
                      'title' => $this->getMetadataValue($id, UI_MDATA_KEY_TITLE),
                      'creator' => $this->getMetadataValue($id, UI_MDATA_KEY_CREATOR),
                      'duration' => $this->getMetadataValue($id, UI_MDATA_KEY_DURATION),
                      'type' => $type,
                      #'isAvailable' => $type == 'playlist' ? $this->gb->playlistIsAvailable($id, $this->sessid) : NULL,
                );
         return ($data);
    } // fn getMetaInfo


    public function getMetadataValue($id, $key, $langid=NULL, $deflangid=UI_DEFAULT_LANGID)
    {
        if (!is_numeric($id)) {
            return null;
        }
        if (!$langid) {
            $langid = $_SESSION['langid'];
        }

        return $this->gb->getMetadataValue($id, $key, $this->sessid, $langid, $deflangid);
    } // fn getMetadataValue


    public function setMetadataValue($id, $key, $value, $langid=NULL)
    {
        if (!$langid) {
            $langid = UI_DEFAULT_LANGID;
        }
//        if (ini_get('magic_quotes_gpc')) {
//            $value = str_replace("\'", "'", $value);
//        }

        if ($this->gb->setMetadataValue($id, $key, $this->sessid, $value, $langid)) {
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
        if (is_array($arr = GreenBox::GetPath($id))) {
            $file = array_pop($arr);
            return $file['name'];
        }
        return FALSE;
    } // fn _getFileTitle


//    function _isFolder($id)
//    {
//        if (strtolower(GreenBox::getFileType($id)) != 'folder') {
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