<?php
$Smarty->register_object('UIBROWSER',  $uiBrowser);
$Smarty->register_object('BROWSE',     $uiBrowser->BROWSE);
$Smarty->register_object('SEARCH',     $uiBrowser->SEARCH);
$Smarty->assign_by_ref  ('PL',         $uiBrowser->PLAYLIST);
$Smarty->assign_by_ref  ('SCHEDULER',  $uiBrowser->SCHEDULER);
$Smarty->assign_by_ref  ('SCRATCHPAD', $uiBrowser->SCRATCHPAD);
$Smarty->assign_by_ref  ('SUBJECTS',   $uiBrowser->SUBJECTS);
$Smarty->assign_by_ref  ('JSCOM',      $jscom);


$Smarty->register_function('str_repeat',        'S_str_repeat');
$Smarty->register_function('tra',               'S_tra');
$Smarty->register_function('getHour',           'S_getHour');
$Smarty->register_function('getMinute',         'S_getMinute');
$Smarty->register_function('getSecond',         'S_getSecond');
$Smarty->register_function('niceTime',          'S_niceTime');

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
 *  tra
 *
 *  Translate given string.
 *
 *  @param void array, array of strings to be outputted translated
 */
function S_tra($in)
{
    global $uiBrowser;
    foreach($in as $val) $param[] = $val;

    echo tra($param[0], $param[1], $param[2], $param[3], $param[4], $param[5], $param[6], $param[7], $param[8], $param[9]);
}

function S_getHour($param)
{
    ## input format is HH:MM:SS.dddddd
    extract ($param);
    list ($h, $m, $s) = explode (':', $time);
    $curr = mktime($h, $m ,$s);
    if ($pause) $curr = strtotime(UI_SCHEDULER_PAUSE_PL2PL, $curr);

    return strftime("%H", $curr);
}

function S_getMinute($param)
{
    ## input format is HH:MM:SS.dddddd
    extract ($param);
    list ($h, $m, $s) = explode (':', $time);
    $curr = mktime($h, $m ,$s);
    if ($pause) $curr = strtotime(UI_SCHEDULER_PAUSE_PL2PL, $curr);

    return strftime("%M", $curr);
}

function S_getSecond($param)
{
    ## input format is HH:MM:SS.dddddd
    extract ($param);
    list ($h, $m, $s) = explode (':', $time);
    $curr = mktime($h, $m ,$s);
    if ($pause) $curr = strtotime(UI_SCHEDULER_PAUSE_PL2PL, $curr);

    return strftime("%S", $curr);
}

function S_niceTime($param)
{
    extract($param);

    if (strpos($in, '.')) list ($in, $lost) = explode('.', $in);
    $in = str_replace('&nbsp;', '', $in);

    if (preg_match('/^[0-9]{1,2}:[0-9]{1,2}:[0-9]{1,2}$/', $in))    list($h, $i, $s) = explode(':', $in);
    elseif (preg_match('/^[0-9]{1,2}:[0-9]{1,2}$/', $in))           list($i, $s)     = explode(':', $in);
    else                                                            $s = $in;

    if ($all || $h > 0) $H = sprintf('%02d', $h).':';
    else        $H = '&nbsp;&nbsp;&nbsp;';
    $I = sprintf('%02d', $i).':';
    $S = sprintf('%02d', $s);

    return $H.$I.$S;
}
?>