<?php

function XMLtoHTML($input)
{
    return "<p>".nl2br(str_replace( "\t", '&nbsp;&nbsp;&nbsp;&nbsp;',  htmlentities($input)))."</p>";
}


function ArrayToHTML($input)
{
    return "<p>".nl2br(str_replace(" ", "&nbsp;", print_r($input, true)))."</p>";
}


function pl($input)
{
    echo "<p>$input</p>";
}

if (!function_exists('cropstr')) {
    function cropStr ($input, $length, $char='')
    {
        if (is_numeric($length)) {
            if ($char) {
                if (strpos ($input, $char)) {
                    $len =  strrpos(substr($input, 0, $length), $char);
                }
            } else {
                $len = $length;
            }
            $output = substr ($input, 0, $len);
            if (strlen ($input)>$len) {
                $output .= "...";
            }
        } else {
            return $input;
        }

        return $output;
    }
}

function isInt ($in, $noZero = true)
{
    if ($noZero && !($in>0)) {
        return false;
    }
    if (preg_match('/^[0-9]*$/', $in)) {
        return true;
    }

    return false;
}

function Error($msg)
{
    $GLOBALS[error][msg] .= "<div class='error'>$msg</div>";
}

if (!function_exists('putGS')) {
    function putGS($s)
    {
        global $gs, $TOL_Language;
        $nr=func_num_args();
        if (!isset($gs[$s]) || ($gs[$s]==''))
            $my="$s (not translated)";
        else
            $my= $gs[$s];
        if ($nr>1)
            for ($i=1;$i<$nr;$i++){
                $name='$'.$i;
                $val=func_get_arg($i);
                $my=str_replace($name,$val,$my);
            }
        echo $my;
    }
}

if (!function_exists('getGS')) {
    function getGS($s)
    {
        global $gs, $TOL_Language;
        $nr=func_num_args();
        if (!isset($gs[$s]) || ($gs[$s]=='') )
            $my="$s (not translated)";
        else
            $my= $gs[$s];
        if ($nr>1)
            for ($i=1;$i<$nr;$i++){
                $name='$'.$i;
                $val=func_get_arg($i);
                $my=str_replace($name,$val,$my);
            }
        return  $my;
    }
}

function loadLanguageFiles($path, $base)
{
    global $gs;

    if (!isset($_COOKIE['TOL_Language'])){
        $_COOKIE['TOL_Language'] = Data::langName2Id(_DEFAULT_LANG_);
    }

    $languages = Data::getLanguages();

    foreach($languages as $lang) {
        if($lang['Code'] == $_COOKIE['TOL_Language']) {
            $Id = $lang['Id'];
            break;
        }
    }


    $langfile[dir]  = $path;
    $langfile[base] = $base;
    $defG = Data::readTransXML2Arr($langfile, _DEFAULT_LANG_);
    $defG = Data::convArr2GS($defG);
    $usrG = Data::readTransXML2Arr($langfile, $Id);
    $usrG = Data::convArr2GS($usrG);
    $gs = array_merge($gs, $defG, $usrG);

}


function &loadTranslations($langId)
{
    ## use this to load the translations for livesupport ########################

    $langfile=array('dir'   => '..',
                    'base'  => 'locals');

    $defG = Data::readTransXML2Arr($langfile, $langId);
    $defG = Data::convArr2GS($defG);
    $usrG = Data::readTransXML2Arr($langfile, $langId);
    $usrG = Data::convArr2GS($usrG);
    $gs   = array_merge($gs, $defG, $usrG);

    return $gs;
}
?>