<?php
$Smarty->register_object('UIBROWSER', $uiBrowser);
$Smarty->register_object('BROWSE', $uiBrowser->BROWSE);
$Smarty->register_object('HUBBROWSE', $uiBrowser->HUBBROWSE);
$Smarty->register_object('SEARCH', $uiBrowser->SEARCH);
$Smarty->register_object('HUBSEARCH', $uiBrowser->HUBSEARCH);
$Smarty->register_object('TRANSFERS', $uiBrowser->TRANSFERS);
$Smarty->assign_by_ref('PL', $uiBrowser->PLAYLIST);
$Smarty->assign_by_ref('SCHEDULER', $uiBrowser->SCHEDULER);
$Smarty->assign_by_ref('SCRATCHPAD', $uiBrowser->SCRATCHPAD);
$Smarty->assign_by_ref('SUBJECTS', $uiBrowser->SUBJECTS);
$Smarty->assign_by_ref('EXCHANGE', $uiBrowser->EXCHANGE);
$Smarty->assign_by_ref('JSCOM', $jscom);

$Smarty->register_function('str_repeat', 'S_str_repeat');
$Smarty->register_function('tra', 'S_tra');
$Smarty->register_function('getHour', 'S_getHour');
$Smarty->register_function('getMinute', 'S_getMinute');
$Smarty->register_function('getSecond', 'S_getSecond');
$Smarty->register_function('niceTime', 'S_niceTime');

// --- Smarty Extensions ---
/**
 * Repeat given string.
 *
 * @param array $param
 * 		must have the key values "str" and "count"
 * @return string
 * 		repeated string
 */
function S_str_repeat($param)
{
    extract($param);
    return str_repeat($str, intval($count));
} // fn S_str_repeat


/**
 * Translate given string.
 *
 * @param array $in
 * 		array of strings to be outputted translated
 * @return string
 */
function S_tra($in)
{
	echo call_user_func_array('tra', $in);
} // fn S_tra


/**
 * @param array $param
 *      An array with key values named "time" and "pause".
 * @return string
 */
function S_getHour($param)
{
    // input format is HH:MM:SS.dddddd
    extract($param);
    if (empty($time) || !is_string($time)) {
        return 0;
    }
    list($h, $m, $s) = explode(':', $time);
    $h = intval($h);
    $m = intval($m);
    $s = intval($s);
    $curr = mktime($h, $m ,$s);
    if (isset($pause) && $pause) {
        $curr = strtotime(UI_SCHEDULER_PAUSE_PL2PL, $curr);
    }
    return strftime("%H", $curr);
} // fn S_getHour


/**
 * @param array $param
 *      An array with key values named "time" and "pause".
 * @return string
 */
function S_getMinute($param)
{
    // input format is HH:MM:SS.dddddd
    extract($param);
    if (empty($time) || !is_string($time)) {
        return 0;
    }
    list ($h, $m, $s) = explode(':', $time);
    $h = intval($h);
    $m = intval($m);
    $s = intval($s);
    $curr = mktime($h, $m ,$s);
    if (isset($pause) && $pause) {
        $curr = strtotime(UI_SCHEDULER_PAUSE_PL2PL, $curr);
    }
    return strftime("%M", $curr);
} // fn S_getMinute


/**
 * @param array $param
 *      An array with key values named "time" and "pause".
 * @return string
 */
function S_getSecond($param)
{
    // input format is HH:MM:SS.dddddd
    extract($param);
    if (empty($time) || !is_string($time)) {
        return 0;
    }
    list ($h, $m, $s) = explode (':', $time);
    $h = intval($h);
    $m = intval($m);
    $s = intval($s);
    $curr = mktime($h, $m ,$s);
    if (isset($pause) && $pause) {
        $curr = strtotime(UI_SCHEDULER_PAUSE_PL2PL, $curr);
    }
    return strftime("%S", $curr);
} // fn S_getSecond


/**
 * @param array $param
 *      Array with a key value "in".
 * @return string
 */
function S_niceTime($param)
{
    require_once("../../../storageServer/var/Playlist.php");
    
    $sec = round(Playlist::playlistTimeToSeconds($param['in']));
    $formatted = isset($param['all']) && $sec >= 3600 ? strftime('%H:%M:%S', $sec) : strftime('&nbsp;&nbsp;&nbsp;%M:%S', $sec);
    
    return $formatted;
} // fn S_niceTime
?>