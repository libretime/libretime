<?php
$Smarty->register_object('UIBROWSER',  $uiBrowser);
$Smarty->register_object('BROWSE',     $uiBrowser->BROWSE);
$Smarty->register_object('SEARCH',     $uiBrowser->SEARCH);
$Smarty->assign_by_ref  ('PL',         $uiBrowser->PLAYLIST);
$Smarty->assign_by_ref  ('SCHEDULER',  $uiBrowser->SCHEDULER);
$Smarty->assign_by_ref  ('SCRATCHPAD', $uiBrowser->SCRATCHPAD);

$Smarty->register_function('str_repeat',        'S_str_repeat');
$Smarty->register_function('urlencode',         'S_urlencode');
$Smarty->register_function('htmlspecialchars',  'S_htmlspecialchars');
$Smarty->register_function('system',            'S_system');
$Smarty->register_function('tra',               'S_tra');
$Smarty->register_function('getHour',           'S_getHour');
$Smarty->register_function('getMinute',         'S_getMinute');
$Smarty->register_function('getSecond',         'S_getSecond');

// --- Smarty Extensions ---
/**
 *  str_repeat
 *
 *  Repeate given string.
 *
 *  @param str string, string to repeate
 *  @param count numeric, how often to repeate (converted to type integer)
 *  @return string, repeated string
 */
function S_str_repeat($param)
{
    extract($param);
    return str_repeat($str, intval($count));

}


 /**
 *  urlencode
 *
 *  Encode given string to use in URL.
 *
 *  @param str string, string to encode
 *  @return string, encoded string
 */
function S_urlencode($param)
{
    extract($param);
    return urlencode($str);
}


/**
 *  htmlspecialchars
 *
 *  convert special chars in given string to html-entitys.
 *
 *  @param str string, string to convert
 *  @return string, converted string
 */
function S_htmlspecialchars($param)
{
    extract($param);
    return htmlspecialchars($str);
}


/**
 *  tra
 *
 *  Translate given string.
 *
 *  @param void array, array of strings to be outputted translated
 */
function S_tra($param)
{
    global $uiBrowser;

    echo tra($param[0], $param[1], $param[2], $param[3], $param[4], $param[5], $param[6], $param[7], $param[8], $param[9]);
}


function S_getHour($param)
{
    ## input format is HH:MM:SS.dddddd
    extract ($param);
    list ($h, $m, $s) = explode (':', $time);
    return $h;
}

function S_getMinute($param)
{
    ## input format is HH:MM:SS.dddddd
    extract ($param);
    list ($h, $m, $s) = explode (':', $time);
    return $m;
}

function S_getSecond($param)
{
    ## input format is HH:MM:SS.dddddd
    extract ($param);
    list ($h, $m, $s) = explode (':', $time);
    return $s;
}
?>