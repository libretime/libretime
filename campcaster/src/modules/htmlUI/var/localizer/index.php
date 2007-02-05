<?php
header("Content-type: text/html; charset=utf-8");
include('auth.inc.php');

require_once(dirname(__FILE__)."/includes/camp_html.php");
require_once(dirname(__FILE__)."/includes/Input.php");
require_once('Localizer.php');

global $g_translationStrings;
global $g_localizerConfig;

$action = Input::Get('action', 'string', 'translate', true);
$prefix = Input::Get('prefix', 'string', '', true);
$save   = Input::Get('save', 'boolean', false);

$langCode = null;
if (isset($_REQUEST['TOL_Language'])){
    $langCode = $_REQUEST['TOL_Language'];
}
//echo "<pre>";
//print_r($g_translationStrings);
//print_r($_REQUEST);
//echo "</pre>";
#$crumbs = array();
#$crumbs[] = array("Localizer", "");
#echo camp_html_breadcrumbs($crumbs);

//echo "Action: $action<br>";

if ($g_localizerConfig['MAINTENANCE']) {
    include 'maintenance.php';
}


switch ($action) {
	case 'translate':
	    require_once("translate.php");
	    translationForm($_REQUEST);
		break;

	case 'save_translation':
	    $targetLanguageId = Input::Get('localizer_target_language');
	    $data = Input::Get('data', 'array');
	    Localizer::ModifyStrings($prefix, $targetLanguageId, $data);
	    // Localizer strings are changed -> reload files
	    Localizer::LoadLanguageFiles('globals', $langCode);
	    Localizer::LoadLanguageFiles('localizer', $langCode);
	    require_once("translate.php");
	    translationForm($_REQUEST);
		break;

	case 'remove_string':
	    $deleteMe = Input::Get('string', 'string');
	    Localizer::RemoveString($prefix, $deleteMe);
	    require_once("translate.php");
	    translationForm($_REQUEST);
		break;

	case 'move_string':
		$pos1 = Input::Get('pos1', 'int');
		$pos2 = Input::Get('pos2', 'int');
	    Localizer::MoveString($prefix, $pos1, $pos2);
	    require_once("translate.php");
	    translationForm($_REQUEST);
		break;

	case 'add_missing_translation_strings':
		$missingStrings = Localizer::FindMissingStrings($prefix);
	    if (count($missingStrings) > 0) {
	        Localizer::AddStringAtPosition($prefix, 0, $missingStrings);
	    }
	    require_once("translate.php");
	    translationForm($_REQUEST);
		break;

	case 'delete_unused_translation_strings':
		$unusedStrings = Localizer::FindUnusedStrings($prefix);
	    if (count($unusedStrings) > 0) {
	       	Localizer::RemoveString($prefix, $unusedStrings);
	    }
	    require_once("translate.php");
	    translationForm($_REQUEST);
		break;

	//case 'add_string':
	//	$pos = Input::Get('pos');
	//	if ($pos == 'begin') {
	//		$pos = 0;
	//	}
	//	elseif ($pos == 'end') {
	//		$pos = null;
	//	}
	//
	//    $msg = Localizer::CompareKeys($directory, $_REQUEST['newKey']);
	//    if (count($msg) > 0) {
	//        foreach ($msg as $val => $err) {
	//            while ($key = array_search($val, $_REQUEST['newKey'])) {
	//                unset($_REQUEST['newKey'][$key]);
	//            }
	//        }
	//    }
	//	// skip if all was unset above
	//    if (count($_REQUEST['newKey'])) {
	//        Localizer::AddStringAtPosition($base, $directory, $pos, $_REQUEST['newKey']);
	//    }
	//
	//    require_once("translate.php");
	//    translationForm($_REQUEST);
	//	break;

} // switch

?>
</body>
</html>