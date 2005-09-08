<?php
class Data {

    var  $serializeoptions = array(
                           "indent"           => "\t",        // indent with tabs
                           "rootName"         => "language",   // root tag
                           "defaultTagName"   => "item",      // tag for values with numeric keys
                           #"typeHints"        => true,
                           "keyAttribute"     => "position",
                           "addDecl"          => true,
                           "encoding"         => _ENCODING_,
                           "indentAttributes" => true,
                           "mode"             => 'simplexml'
            );

    var $unserializeoptions = array();

    var $_gs;

    function &getInstance()
        {
        static $instance;

        if (!$instance) {
            $instance = new Data();
        }

        return $instance;
    }

    function _realTransPath($file, $Id, $type='xml')
    {
        $s = '/';
        #return $_SERVER[DOCUMENT_ROOT].$s.dirname($_SERVER[SCRIPT_NAME]).$s.Data::langPath($file, $Id, $type);
        return dirname(__FILE__).$s.Data::langPath($file, $Id, $type);
    }

    function langPath($file, $Id, $type)
    {
        // $file[path]  = path
        // $file[base]  = name
        // $Id          = language-Id, e.g. en-gb.english
        // $type        = [xml | php]
        $d = '.';
        $s = '/';

        return $file[dir].$s.$file[base].$d.$Id.$d.$type;
    }

    function readTransXML2Arr($file, $Id)                       // read language-xml-file, return array
    {
        $realpath = Data::_realTransPath($file, $Id, 'xml');

        if ($arr = Data::readXML2Arr($realpath)) {       // read file & convert it
            if (!is_array($arr[item][0]) && $arr[item]) {
                // was there an item?
                $arr[item][0][key]   = $arr[item][key];
                $arr[item][0][value] = $arr[item][value];
                unset($arr[item][key]);
                unset($arr[item][value]);
            }
            return $arr;
        } else {
            // seems missing|empty file -> create array with Id only
            $arr = Data::convTransArr2XML(array(), $Id);
            return Data::_convTransXML2Arr($arr);
        }
    }

    function readXML2Arr($path)                                 // read some xml-file
    {
        if (file_exists($path)) {
            $xml = File::readAll($path);
            File::rewind($path, FILE_MODE_READ);                // needet if same file should be read second time

            return Data::convXML2Arr($xml);
        }

        Error (getGS('cannot read $1', $path));
        return false;
    }

    function writeTransXML2File($file, $xml)                    // write language-xml-file
    {
        $arr = Data::_convTransXML2Arr($xml);            // get the langId from XML-data for file-naming
        $Id = $arr['Id'];

        $realpath = Data::_realTransPath($file, $Id, 'xml');
        Data::writeXML2File($realpath, $xml);           // write data to file
    }

    function writeXML2File($path, $xml)                        // write some xml-data to file
    {
        File::write($path, $xml, FILE_MODE_WRITE);
    }

    function readGSFile2Arr($file, $Id)
    {
         unset ($this->_gs);

        $realpath = Data::_realTransPath($file, $Id, 'php');
        $data = File::readAll($realpath);

        $data = preg_replace('/regGS/', '$this->_regGS', $data);

        eval ('?>'.$data);

        return $this->_gs;
    }

    function _regGS($key, $value)
    {
        $l = count($this->_gs);

        if (substr($value,strlen($value)-3)==":en"){
            $value=substr($value,0,strlen($value)-3);
        }

        $this->_gs[$l][key] = $key;
        $this->_gs[$l][value] = $value;
    }

    function convTransArr2XML($arr, $Id)
    {
        $target['Id'] = $Id;                      // add the Id
        if ($arr[status] == 'on') {
            $target[status] =  'checked';
        }

        if (count($arr)>1) {
            foreach($arr as $key=>$val) {
                if (!is_int($key)) {            // clean array:
                    unset($arr[$key]);          // delete all non-numeric keys (maybee transmitted from form)
                }
            }
        }

        $target = array_merge($target, $arr);

        return Data::convArr2XML($target);
    }

    function convArr2XML($arr)                  // serialize some array to xml
    {
        $handle = new XML_Serializer($this->serializeoptions);
        $handle->serialize($arr);

        return $handle->getSerializedData();
    }

    function convXML2Arr($xml)
    {
        $handle = new XML_Unserializer($this->unserializeoptions);
        $handle->unserialize($xml);
        $arr = $handle->getUnserializedData();

        return $arr;
    }

    function _convTransXML2Arr($xml)
    {
        $arr = Data::convXML2Arr($xml);

        if (is_array($arr[item][0])) {        // more than one items => done
            return $arr;
        } else {                              //else convert structure of array
            $conv['Id'] = $arr['Id'];
            if (is_array($arr[item])) {       // was there one item => change structure
                $conv[item][0][key]   = $arr[item][key];
                $conv[item][0][value] = $arr[item][value];
            }

        return $conv;
        }
    }

    function convArr2GS($arr)
    {
        if (is_array($arr[item])) {
            foreach ($arr[item] as $key=>$val) {
                $gs[$val['key']] = $val['value'];
            }
        return $gs;
        }
    return false;
    }

    function checkKeys($file, $data)
    {
        // check if new key doesn´t already exists in local and global file

        $testGS     = array_flip(Data::convCharsArr($data, 1, 0, _DENY_HTML_));

        $localdata  = Data::readTransXML2Arr(array('base'=>_PREFIX_, 'dir'=>$file[dir]), _DEFAULT_LANG_);
        $localGS    = Data::convArr2GS($localdata);

        $globaldata = Data::readTransXML2Arr(array('base'=>_PREFIX_GLOBAL_, 'dir'=>'..'), _DEFAULT_LANG_);
        $globalGS   = Data::convArr2GS($globaldata);

        foreach($testGS as $key=>$val) {
            if (isset($globalGS[$key])) {
                $msg[err][$key] .= getGS('key "$1" already exists in $2-file', $key, 'global');
            }
        }
        reset($testGS);

        foreach($testGS as $key=>$val) {
            if (isset($localGS[$key])) {
                $msg[err][$key] .= getGS('key "$1" already exists in $2-file', $key, 'local');
            }
        }

        return $msg;
    }

    function convStr ($str, $rmShlash, $chQuot, $html)
    {
        if ($rmShlash) {
            $str = stripslashes($str);
        }

        if ($chQuot) {
          $str =  str_replace('"', '&#34;', $str);
        }

        if ($html) {
            $str = htmlspecialchars($str);
        }

        return $str;
    }


    function convCharsArr ($input, $rmShlash, $chQuot, $html)
    {
        if (is_array ($input)) {
            foreach ($input as $key=>$val) {
                if (is_array ($val)) {
                    $arr[$key] = Data::convCharsArr ($val, $rmShlash, $chQuot, $html);
                } else {
                    $arr[$key] = Data::convStr ($val, $rmShlash, $chQuot, $html);
                }
            }
            return ($arr);
        }

        return Data::convStr ($input, $rmShlash, $chQuot, $html);
    }

    function saveTrans2XML($file, $data)
    {
        $data = Data::convCharsArr($data, 1, 0, _DENY_HTML_);             // do some cleanup

        $xml = Data::convTransArr2XML($data, $file['Id']);
        Data::writeTransXML2File($file, $xml);
    }

    function addEntry2XML($file, $pos, $newKey)
    {
        // go throught all files matching $file[base] in $file[dir] and add entry(s)

        $newKey = Data::convCharsArr($newKey, 1, 0,_DENY_HTML_);     // do some cleanup
        $Ids = Data::_findLangFilesIds($file);

        if (!is_array($Ids)) {                 // no file there
            $Ids[] = _DEFAULT_LANG_;
        }

        foreach($Ids as $lost=>$Id) {
            $source = Data::readTransXML2Arr($file, $Id);
            unset($target);
            unset($before);
            unset($after);

            // split source in 2 parts
            if ($pos == 'begin') {
                $before = array();
                $after = $source[item];
            } elseif ($pos == 'end') {
                $before = $source[item];
                $after = array();
            } elseif ($pos == 'new') {
                $before = array();
                $after = array();
            } else {
                $before = array_splice($source[item], 0, $pos+1);
                $after = $source[item];
            }

            // build the new array
            $target[status] = false;
            $target['Id']   = $source['Id'];
            $n = 0;

            foreach($before as $nr=>$key) {                  // add first entrys
                $target[$n]['key']   = $before[$nr]['key'];
                $target[$n]['value'] = $before[$nr]['value'];
                $n++;
            }

            foreach($newKey as $nr=>$key) {                  // add new entrys
                if ($key<>'') {
                    $target[$n]['key']   = $key;

                    /*
                    if ($Id == _DEFAULT_LANG_) {
                        // value=key only in default language
                        $target[$n]['value'] = $key;
                    } else {
                    */
                        $target[$n]['value'] = '';
                    /*
                    }
                    */
                    $n++;
                }
            }

            if (is_array($after)) {
              foreach($after as $nr=>$key) {                   // add first entrys
                $target[$n][key]   = $after[$nr][key];
                $target[$n][value] = $after[$nr][value];
                $n++;
              }
            }

            $xml = Data::convArr2XML($target);
            $realpath = Data::_realTransPath($file, $Id, 'xml');
            Data::writeXML2File($realpath, $xml);
        }
        echo '<script language="javascript">parent.'._MENU_FRAME_.'.location.href = "'._MENU_SCRIPT_.'";</script>';
    }

    function _findLangFilesIds($file)
    {
        $files = File_Find::mapTreeMultiple($file[dir], 1);
        foreach($files as $key=>$filename) {
            if (preg_match("/$file[base]\.[a-z]{2}_[^.]*\.xml/", $filename)) {
                list($lost, $code, $name, $lost) = explode('.', $filename);
                $langIds[] = $code;
            }
        }

        return $langIds;
    }

    function removeEntryFromXML($file, $pos)
    {
        // go throught all files matching $file[base] in $file[dir] and remove selected entry

        $Ids = Data::_findLangFilesIds($file);
        foreach($Ids as $lost=>$Id) {
           $target = Data::readTransXML2Arr($file, $Id);
           unset($target[item][$pos]);                      // remove selected entry

           $xml = Data::convArr2XML($target);
           $realpath = Data::_realTransPath($file, $Id, 'xml');
           Data::writeXML2File($realpath, $xml);
        }
    }

    function swapEntrysOnXML($file, $pos1, $pos2)
    {
        // go throught all files matching $file[base] in $file[dir] and swap selected entrys

        $Ids = Data::_findLangFilesIds($file);
        foreach($Ids as $lost=>$Id) {
           $target = Data::readTransXML2Arr($file, $Id);

           $swap[item][$pos1]   = $target[item][$pos2];     // swap 2 entrys
           $swap[item][$pos2]   = $target[item][$pos1];

           $target[item][$pos1] = $swap[item][$pos1];       // build target array
           $target[item][$pos2] = $swap[item][$pos2];

           $xml = Data::convArr2XML($target);
           $realpath = Data::_realTransPath($file, $Id, 'xml');
           Data::writeXML2File($realpath, $xml);
        }
    }

    function searchFilesRec($startdir, $pattern, $sep)
    {
        $structure = File_Find::mapTreeMultiple($startdir);

        foreach($structure as $dir=>$file) {
            if (is_array($file)) {                                              // it´s a directory
                $filelist .= Data::searchFilesRec($startdir.'/'.$dir, $pattern, $sep);
            } else {                                                            // it´s a file
                if (preg_match($pattern, $file)) {
                    $filelist .= $sep.$startdir.'/'.$file;
                }
            }
        }

        return $filelist;

    }

    function createLangFilesRec($Id)
    {
        // go through subdirectorys and create language files for given Id

        $search      = '/('._PREFIX_.'|'._PREFIX_GLOBAL_.').'._DEFAULT_LANG_.'.xml/';     ## grad geändert
        $sep         = '|';

        $replace     = '/'._DEFAULT_LANG_.'.xml/';
        $replacement = $Id.'.xml';

        $files = Data::searchFilesRec(_START_DIR_, $search, $sep);
        $files = explode($sep, $files);

        foreach ($files as $pathname) {
            if ($pathname) {
                $base = explode('.', basename($pathname));
                $file = array('base' => $base[0],
                              'dir'  => dirname($pathname));
                $arr = Data::readTransXML2Arr($file, _DEFAULT_LANG_, 0);        // read the default file
                $arr['Id'] = $Id;
                if (is_array($arr['item'])) {
                    foreach($arr[item] as $key=>$val) {
                        unset($arr[item][$key][value]);
                    }
                }
                $handle =& Data::getInstance();
                $xml = $handle->convArr2XML($arr);
                $newpathname = preg_replace($replace, $replacement, $pathname);

                if (!file_exists($newpathname)) {
                    $handle->writeXML2File($newpathname, $xml);                     // if file already exists -> skip
                }
            }
        }
    }

    function langId2Name($id, $type='xml')
    {

        if ($arr = Data::readXML2Arr('./languages.xml')) {
            $languages = $arr[language];
            unset($arr);
        }else {
            return 'cannot open languages.xml<br>';
        }

        foreach($languages as $nr=>$lang) {
            if ($lang['Id'] == $id) {
                return $lang[Name];
            }
        }
    }

    function langName2Id($name, $type='xml')
    {
        if ($arr = Data::readXML2Arr('./languages.xml')) {
            $languages = $arr[language];
            unset($arr);
        }else {
            return 'cannot open languages.xml<br>';
        }

        foreach($languages as $nr=>$lang) {
            if ($lang[Name] == $name) {
                return $lang['Id'];
            }
        }
    }

    function getLanguages()
    {
        switch (_LANG_BASE_) {
        case 'xml':
            if ($arr = Data::readXML2Arr('./languages.xml')) {
                $languages = $arr[language];
                unset($arr);
            }else {
                return getGS('cannot read $1', 'languages.xml').'<br>';
            }
            break;

        case 'campsite':
            if ($languages = DB_Handle::readCSLang2Arr()) {
              // do nothing
            } else {
            return getGS('cannot read $1', 'campsite.Languages').'<br>';
            }
            break;
        }

    return $languages;
    }


    function collectExprTPL($file)
    {
        $n = 0;

        $filePattern       = '/(.*).tpl$/';
        $functPattern1     = '/##([^#]+)##/iU';

        $sep = '|';
        $filelist  =  explode($sep, Data::searchFilesRec($file[dir], $filePattern, $sep));
        #print_r($filelist);

        if (count($filelist)==0)
            return FALSE;

        foreach ($filelist as $name) {                                                  // read in all the files
            $data = array_merge($data, file($name));
        }
        #print_r($data);

        if (count($data)==0)
            return FALSE;

        foreach ($data as $line) {
            if (preg_match_all($functPattern1, $line, $m)) {                            // collact all matches
                foreach ($m[1] as $match) {
                    $n++;
                    $matches[$match] = $n;
                 }
            }
        }

        if (is_array($matches)==FALSE)
            return FALSE;

        $matches = array_flip($matches);
        asort($matches);

        return $matches;

    }


    function collectExprPHP($file)
    {
        $n = 0;

        $filePattern         = '/(.*).php/';                                             // all .php files
        $functPattern[]      = '/tra( )*\(( )*\'([^\']*)\'/iU';                          // like tra('edit "$1"', ...);  '
        $functPattern[]      = '/tra( )*\(( )*"([^"]*)"/iU';                             // like tra("edit '$1'", ...);  "
        $functPattern[]      = '/_retMsg( )*\(( )*\'([^\']*)\'/iU';                      // '
        $functPattern[]      = '/_retMsg( )*\(( )*"([^"]*)"/iU';                         // "

        $files = File_Find::mapTreeMultiple($file['dir'], 1);

        foreach ($files as $name) {
            if (preg_match($filePattern, $name)) {
                $filelist[] = $name;                                                    // list of .php-scripts in this folder
            }
        }

        if (count($filelist)==0)
            return FALSE;

        foreach ($filelist as $name) {                                                  // read in all the files
            $data = array_merge($data, file($file[dir].'/'.$name));
        }

        if (count($data)==0)
            return FALSE;

        foreach ($data as $line) {
            foreach ($functPattern as $pattern) {
                if (preg_match_all($pattern, $line, $m)) {                           // collact all matches
                    foreach ($m[3] as $match) {
                        $n++;
                        $matches[$match] = $n;
                    }
                }
             }
        }

        if (is_array($matches)==FALSE)
            return FALSE;

        $matches = array_flip($matches);
        asort($matches);

        return $matches;

    }
}
?>