<?php
class Display {

    function transForm ($source, $target, $file, $onlyUntranslated=false)
    {
        // creates an form for translation
        // from $source to $target language
        $html .= '<form name="transForm" action="'._PANEL_SCRIPT_.'" target="'._PANEL_FRAME_.'" method="post">
                  <table border="0">

                  <tr><td colspan="2">
                    <b>'.getGS('source:').'</b> '.Data::langPath($file, $source[Id], 'xml').'<br>
                    <b>'.getGS('target:').'</b> '.Data::langPath($file, $target[Id], 'xml').'<br>

                    <input type="hidden" name="action" value="save_translation">
                    <INPUT TYPE="hidden" name="dir" value="'.$file[dir].'">
                    <INPUT TYPE="hidden" name="base" value="'.$file[base].'">
                    <INPUT TYPE="hidden" name="Id" value="'.$target[Id].'">
                  </td></tr>
                  ';


        if ($file[Id] != _DEFAULT_LANG_) {

        /*
            if ($onlyUntranslated=='on') {
                $checker = ' checked';
            }
        */
        if (!$file['sourceId']) {
            $file['sourceId'] = _DEFAULT_LANG_;
        }

        $html .= '<tr><td colspan="2"><hr></td></tr>
                  <tr>
                   <td colspan="2">'.
                    Display::sourceLangMenu($file[sourceId], $file).'
                    <input type="submit" Value="'.getGS('ok').'">
                   </td>
                  </tr>
                  <tr>
                   <td colspan="2">  <br>
                    <input type="submit" name="onlyUntranslated" Value="'.getGS('show only untranslated').'">
                   </td>
                  </tr>';
        }

        $s = Data::convArr2GS($source);
        $t = Data::convArr2GS($target);
        $nr = 0;

        if (is_array($s)) {
          while (list($key, $value) = each($s)) {

            if (!$value) {      // item in default-lang-file was not translated
                $value = $key;
            }

            if (isset($t[$key]) && (trim($t[$key])!='')) {
                $insval = Data::convStr($t[$key], 0, 1, 0);
                $pre  = '';
                $post = '';
            } else {
                $insval = '';
                $pre    = '<FONT COLOR="red">';
                $post   = '</FONT>';
            }

            $displKey = Data::convStr($key, 0, 0, !_DENY_HTML_);

            if ($onlyUntranslated && !empty($t[$key])) {
                $html .= "<input name='data[$nr][key]' type='hidden' value=\"$displKey\">
                          <input name='data[$nr][value]' type='hidden' value=\"$insval\">";
            } else {
                $html .= '<tr><td colspan="2"><hr></td></tr>';
                $html .= '<tr><td>';

                if ($target[Id] == _DEFAULT_LANG_) {
                    $html .= "<b>".getGS('key:')."</b> $pre$displKey$post<BR>\n";
                } else {
                    if ($source[item][$nr][from]) {
                        $html .= "<b>$source[Id]:</b> $pre".$source[item][$nr][from]."$post<BR>\n";
                    } else {
                        $html .= "<b>"._DEFAULT_LANG_.":</b> $pre$value$post<BR>\n";
                    }
                }

                $html .= "<input name='data[$nr][key]' type='hidden' value=\"$displKey\">";
                $html .= "<input name='data[$nr][value]' type='text' size='50' value=\"$insval\">";
                $html .= "</td><td>\n";

                if ($target[Id] == _DEFAULT_LANG_) {     // default language => can change keys
                    $fileparms = "Id=$source[Id]&base=$file[base]&dir=$file[dir]";

                    if ($nr==0) {                        // swap last and first entry
                        $prev = count($s)-1;
                        $next = $nr+1;
                    } elseif ($nr == count($s)-1) {     // swap last and first entry
                        $prev = $nr-1;
                        $next = 0;
                    } else {                             // swap entrys linear
                    $prev = $nr-1;
                    $next = $nr+1;
                    }


                    $rem_href      = _PANEL_SCRIPT_."?action=removeEntryFromXML&pos=$nr&$fileparms";
                    $mv_up_href    = _PANEL_SCRIPT_."?action=swapEntrysOnXML&pos1=$nr&pos2=$prev&$fileparms";
                    $mv_down_href  = _PANEL_SCRIPT_."?action=swapEntrysOnXML&pos1=$nr&pos2=$next&$fileparms";

                    $html .= '<a href="'.$mv_up_href.'" target="'._PANEL_FRAME_.'"><img src="'._ICONS_DIR_.'/button_arrow_up.gif" border="0"></a><br>'.
                             "<a href='$rem_href' onClick=\"return confirm('".getGS('really delete this entry?')."')\" target='"._PANEL_FRAME_."'><img src='"._ICONS_DIR_."/button_delete.gif' border='0' vspace='4'></a><br>".
                             '<a href="'.$mv_down_href.'" target="'._PANEL_FRAME_.'"><img src="'._ICONS_DIR_.'/button_arrow_down.gif" border="0"></a>';
                }

                $html .= '</td></tr>';
            }
            $nr++;
          }
        }

        $html .= '<tr><td colspan="2"><hr></td></tr>
                  <tr>
                    <td>
                      <input type="checkbox" name="data[status]" '.$target[status].'>'.getGS('fully translated').'
                      &nbsp;&nbsp;&nbsp;&nbsp;
                      <input type="submit" Value="'.getGS('ok').'">
                    </td>
                  </tr>
                </form>
                </table>';

        return $html;
    }

    function sourceLangMenu ($currId, $file)
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

            } else {
            return getGS('cannot read $1', 'campsite.Languages').'<br>';
            }
            break;
        }

        foreach ($languages as $key=>$val) {
            if (!(file_exists("$file[dir]/$file[base].$val[Id].xml") && ($file[Id] != $val[Id]))) {
                 unset($languages[$key]);
            }
        }

        $menu .=
            getGS('translate from:').'
            &nbsp;
            <SELECT NAME="sourceId">';

        $menu .= Display::_langMenuOptions($languages, $currId);

        $menu .= '
            </select>';

        return $menu;
    }

    function _langMenuOptions ($languages, $currId)
    {
        foreach($languages as $key=>$val) {

            if ($currId == $val[Id]) {
                $curr = 'selected';
            } else {
                unset($curr);
            }

            $options .= "<option value='$val[Id]' $curr>$val[NativeName]</option>\n";
        }

        return $options;
    }

    function createLangMenu ($currId)
    {
        $languages = Data::getLanguages();

        $menu .= '
          <form name="selLang" action="'._MENU_SCRIPT_.'" target="'._MENU_FRAME_.'" method="post">
            <input type="hidden" name="action" value="createLangFilesRec">
            <SELECT NAME="Id">';

        $menu .= Display::_langMenuOptions($languages, $currId);

        $menu .= '
            </select>
            <br>
            <input type="submit" value="'.getGS('create language files').'">
          </form>';

        return $menu;
    }

    function createTOLLangMenu ($currId)
    {
        $languages = Data::getLanguages();

        $menu .= '
          <form name="selTOLLang" action="'._FRAME_SCRIPT_.'" target="'._PARENT_FRAME_.'" method="post">
            <SELECT NAME="TOL_Language">';

        $menu .= Display::_langMenuOptions($languages, $currId);

        $menu .= '
            </select>
            <br>
            <input type="submit" value="'.getGS('choose language').'">
          </form>';

        return $menu;
    }

    function manageLangButton()
    {
        $html = '
            <form action="'._PANEL_SCRIPT_.'" target="'._PANEL_FRAME_.'" method="post">
              <input type="hidden" name="action" value="manageLanguages">
              <input type="submit" value="'.getGS('manage languages').'">
            </form>';

        return $html;
    }

    function manageLangForm()
    {
        $languages = Data::getLanguages();

        $html .= '
            <table border="1">
              <tr>
                <th>'.getGS('name').'</th>
                <th>'.getGS('native name').'</th>
                <th>'.getGS('code').'</th>
                <th>'.getGS('edit').'</th>
                <th>'.getGS('delete').'</th>
              </tr>
            ';

            foreach($languages as $nr=>$l) {
                $editLink = '<a href="'._PANEL_SCRIPT_.'?action=editLanguage&Id='.$l[Code].'.'.$l[Name].'">'.getGS('edit').'</a>';
                $delLink  = '<a href="'._PANEL_SCRIPT_.'?action=delLanguage&Id='.$l[Id].'">'.getGS('delete').'</a>';
                $html .= "<tr><td>$l[Name]</td><td>$l[NativeName]</td><td>$l[Code]</td><th>$editLink</th><th>$delLink</th></tr>";
            }


        return $html;
    }

    function parseFolder($dirname, $depth=0)
    {
        $space = 2;

        $structure = File_Find::mapTreeMultiple($dirname);
        ksort($structure, SORT_STRING);
        #print_r($structure);

        if ($depth == 0) {
            $html .= str_repeat(' ',$depth * $space).'<b><a href="'._PANEL_SCRIPT_.'?action=newLangFilePref&dir='.$dirname.'/'.$dir.'" target="'._PANEL_FRAME_.'">'.strtoupper(' / ')."</a></b>\n";
        }

        foreach($structure as $dir=>$file) {
            if (is_array($file)) {              // it´s a directory
                unset($base);
                unset($baseadd);

                if (!(substr($dir, 0, strlen(_PREFIX_HIDE_)) == _PREFIX_HIDE_)) {   // hide special dirs
                    $html .= str_repeat(' ', ($depth+1) * $space).'<b><a href="'._PANEL_SCRIPT_.'?action=newLangFilePref&dir='.$dirname.'/'.$dir.'" target="'._PANEL_FRAME_.'">'.strtoupper($dir)."</a></b>\n";
                    $html .= Display::parseFolder($dirname.'/'.$dir, $depth+1);
                }
            } else {                            // it´s a file
                if (((strpos(' '.$file, _PREFIX_) == 1) || (strpos(' '.$file, _PREFIX_GLOBAL_) == 1))
                     &&
                   (substr($file, strlen($file) - 4) == '.xml')) {

                    if (!_MAINTAINANCE_ && preg_match("/[^.]*\."._DEFAULT_LANG_."\.xml/", $file)) {
                        // skip default language if not maintainance mode
                    } else {
                    $Id = explode('.', $file);
                    $html .= str_repeat(' ', ($depth+1) * $space).'<a href="'._PANEL_SCRIPT_.'?action=translate&Id='.$Id[1].'.'.$Id[2].'&base='.$Id[0].'&dir='.$dirname.'" target="'._PANEL_FRAME_.'">'.$file."</a>\n";
                    }
                }
            }

        }

        if ($depth == 0) {
            return "<pre>$html</pre>";
        } else {
            return $html;
        }
    }

    function addEntrySelection($arr, $file)
        {
        $html = '
            <hr>
            <form action="'._PANEL_SCRIPT_.'" target="'._PANEL_FRAME_.'" method="post">
              <input type="hidden" name="action" value="addEntryForm">
              <input type="hidden" name="Id" value="'.$arr[Id].'">
              <input type="hidden" name="base" value="'.$file[base].'">
              <input type="hidden" name="dir" value="'.$file[dir].'">
              '.getGS('add new fields:').' <input name="amount" value="1" size="1">
              <select name="pos">
                <option value="begin">'.getGS('at begin').'</option>
                <option value="end">'.getGS('at end').'</option>';

        if (is_array($arr[item])) {
            foreach($arr[item] as $nr=>$val) {
                $html .= "<option value='".$nr."'>".getGS('after').' '.cropstr($arr[item][$nr][value], 15)."</option>";
            }
        }

        $html .= '
              </select>

              <input type="submit" value="'.getGS('ok').'">
            </form>';

        if (_MAINTAINANCE_)
            $html .= '
            <a href="'._PANEL_SCRIPT_.'?action=collectExpr&dir='.$file[dir].'&Id='.$arr[Id].'&base='.$file[base].'" target="'._PANEL_FRAME_.'"><input type="button" value="'.getGs('collect expressions').'"></a>
            ';

        return $html;
        }

    function addEntry2XML($file, $pos, $amount)
    {
        // check input
        if (!isInt($amount)) {
            return getGS('go').' <a href="JavaScript:history.back()">'.getGS('back').'</a> '.getGS('and enter a positive integer value');
        }

        $html .= '
            <form action="'._PANEL_SCRIPT_.'" target="'._PANEL_FRAME_.'" method="post">
             <table border="0">
              <input type="hidden" name="action" value="addEntry2XML">
              <input type="hidden" name="Id" value="'.$file[Id].'">
              <input type="hidden" name="base" value="'.$file[base].'">
              <input type="hidden" name="dir" value="'.$file[dir].'">
              <input type="hidden" name="pos" value="'.$pos.'">';

        for($n=1; $n<=$amount; $n++) {
            $html .= "<tr><td><input name='newKey[$n]' size='50'></td></tr>";
        }

        $html .=
            '<tr><td><input type="submit" value="'.getGS('save to file').'"></td></tr>
             </table>
            </form>';
    return $html;
    }

    function newLangFilePref($dir)
    {
        // at first check if default files already exists
        $handle = opendir ($dir);
        while (false !== ($file = readdir ($handle))) {
            $exists[$file] = true;
        }
        closedir($handle);

        $html .= '
            '.getGS('create new language file in').' '.strtoupper($dir).'
            <form action="'._PANEL_SCRIPT_.'" target="'._PANEL_FRAME_.'" method="post">
              <input type="hidden" name="action" value="newLangFileForm">
              <input type="hidden" name="dir" value="'.$dir.'">';

        if ($dir == _START_DIR_.'/') {
            if ($exists[_PREFIX_.'.'._DEFAULT_LANG_.'.xml'] && $exists[_PREFIX_GLOBAL_.'.'._DEFAULT_LANG_.'.xml']) {
                return getGS('$1 and $2 files already exist in $3', _PREFIX_, _PREFIX_GLOBAL_, strtoupper($dir));
            } else {
                if ($exists[_PREFIX_GLOBAL_.'.'._DEFAULT_LANG_.'.xml']) {
                    $globals .= ' disabled';
                    $locals  .= ' checked';
                }
                if ($exists[_PREFIX_.'.'._DEFAULT_LANG_.'.xml']) {
                    $locals  .= ' disabled';
                    $globals .= ' checked';
                }

            $html .= '
              Type:<br>
              <input type="radio" name="base" value="'._PREFIX_.'"'.$locals.'>'._PREFIX_.'
              <input type="radio" name="base" value="'._PREFIX_GLOBAL_.'"'.$globals.'>'._PREFIX_GLOBAL_;
            }
        } else {
            if ($exists[_PREFIX_.'.'._DEFAULT_LANG_.'.xml']) {
                return getGS('$1 file already exist in $2', _PREFIX_, strtoupper($dir));
            } else {
                $html .= '<input type="hidden" name="base" value="'._PREFIX_.'">';
            }
        }

        $html .= '
              <br>
              '.getGS('entrys:').'<br>
              <input name="amount" value="1" size="2">

              <input type="submit" value="'.getGS('ok').'">
            </form>';

    return $html;
    }

    function newLangFileForm($amount, $base, $dir)
    {
        // check input
        if (!$base) {
            return getGS('go').' <a href="JavaScript:history.back()">'.getGS('back').'</a> '.getGS('and select file type');
        }
        if (!isInt($amount)) {
            return getGS('go').' <a href="JavaScript:history.back()">'.getGS('back').'</a> '.getGS('and enter a positive integer value');
        }

        $html .= '
            '.getGS('create new language file $1', strtoupper($dir).'/'.$base.'.'._DEFAULT_LANG_.'.xml').'
            <form action="'._PANEL_SCRIPT_.'" target="'._PANEL_FRAME_.'" method="post">
             <table border="0">
              <input type="hidden" name="action" value="storeNewLangFile">
              <input type="hidden" name="base" value="'.$base.'">
              <input type="hidden" name="dir" value="'.$dir.'">';

        for($n=1; $n<=$amount; $n++) {
            $html .= "<tr><td><input name='newKey[$n]' size='50'></td></tr>";
        }

        $html .=
            '<tr><td><input type="submit" value="'.getGS('save to file').'"></td></tr>
             </table>
            </form>';
    return $html;
    }
}
?>