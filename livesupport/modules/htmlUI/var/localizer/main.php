<?php
require_once('require.inc.php');
include_once('header.html');

$data =& Data::getInstance();
loadLanguageFiles('', 'locals');
loadLanguageFiles('..', 'globals');

switch ($_REQUEST[action]) {


    case 'read2gs':                                         // read XML to array (similar to include(GS) )

        $arr = $data->readTransXML2Arr($file, _DEFAULT_LANG_);
        $gs  = $data->convArr2GS($arr);
        print_r($gs);

        break;

    case 'translate':                                       // translate an xml-file
    case 'save_translation':

        $file = array('dir'         => $_REQUEST['dir'],
                      'base'        => $_REQUEST['base'],
                      'Id'          => $_REQUEST['Id'],
                      'sourceId'    => $_REQUEST[sourceId]);

        if ($_REQUEST[action] == "save_translation") {
            $data->saveTrans2XML($file, $_REQUEST[data]);
            loadLanguageFiles('..', 'globals');             // maybee localizers expressions are changed->reload
            loadLanguageFiles('', 'locals');
        }
                  
        $source = $data->readTransXML2Arr($file, _DEFAULT_LANG_);
        $target = $data->readTransXML2Arr($file, $_REQUEST['Id']);

        if ($_REQUEST[sourceId]) {                          // translate from
             $from = $data->readTransXML2Arr($file, $_REQUEST[sourceId]);
             $source['Id'] = $from['Id'];
             foreach($from[item] as $nr=>$item) {
                 if($item[value]) {
                     $source[item][$nr][from] = $item[value];
                 }
             }
        }

        $output .= Display::transForm($source, $target, $file, $_REQUEST[onlyUntranslated]);

        if ($_REQUEST['Id'] == _DEFAULT_LANG_) {
            $output .= Display::addEntrySelection($source, $file);
           }

        break;


    case 'addEntryForm':
        $file = array('dir'         => $_REQUEST['dir'],
                      'base'        => $_REQUEST['base'],
                      'Id'          => $_REQUEST['Id']);

        $output .= Display::addEntry2XML($file, $_REQUEST['pos'], $_REQUEST['amount']);

        break;


    case 'addEntry2XML':
        $file = array('dir'         => $_REQUEST['dir'],
                      'base'        => $_REQUEST['base'],
                      'Id'          => _DEFAULT_LANG_);

        $msg = Data::checkKeys($file, $_REQUEST[newKey]);
        if ($msg[err]) {
            foreach ($msg[err] as $val=>$err) {
                Error($err);
                while ($key = array_search($val, $_REQUEST[newKey])) {
                    unset($_REQUEST[newKey][$key]);
                }
            }
        }

        if (count($_REQUEST[newKey])) {  // skip if all was unset above
            $data->addEntry2XML($file, $_REQUEST[pos], $_REQUEST[newKey]);
        }

        $source = $data->readTransXML2Arr($file, _DEFAULT_LANG_);
        $target = $data->readTransXML2Arr($file, $_REQUEST['Id']);

        $output .= Display::transForm($source, $target, $file);
        $output .= Display::addEntrySelection($source, $file);

        break;

    case 'removeEntryFromXML':
        $file = array('dir'         => $_REQUEST['dir'],
                      'base'        => $_REQUEST['base'],
                      'Id'          => _DEFAULT_LANG_);

        $data->removeEntryFromXML($file, $_REQUEST[pos]);

        $source = $data->readTransXML2Arr($file, _DEFAULT_LANG_);
        $target = $data->readTransXML2Arr($file, $_REQUEST['Id']);

        $output .= Display::transForm($source, $target, $file);
        $output .= Display::addEntrySelection($source, $file);

        break;

    case 'swapEntrysOnXML':
        $file = array('dir'         => $_REQUEST['dir'],
                      'base'        => $_REQUEST['base'],
                      'Id'          => _DEFAULT_LANG_);

        $data->swapEntrysOnXML($file, $_REQUEST[pos1], $_REQUEST[pos2]);

        $source = $data->readTransXML2Arr($file, _DEFAULT_LANG_);
        $target = $data->readTransXML2Arr($file, $_REQUEST['Id']);

        $output .= Display::transForm($source, $target, $file);
        $output .= Display::addEntrySelection($source, $file);

        break;

    case 'newLangFilePref':
        $output .= Display::newLangFilePref($_REQUEST['dir'], $_REQUEST[denied]);

        break;

    case 'newLangFileForm':
        $output .= Display::newLangFileForm($_REQUEST[amount], $_REQUEST['base'], $_REQUEST['dir']);

        break;

    case 'storeNewLangFile':
        $file = array('dir'         => $_REQUEST['dir'],
                      'base'        => $_REQUEST['base'],
                      'Id'          => _DEFAULT_LANG_);

        $data->addEntry2XML($file, 'new', $_REQUEST[newKey]);

        break;

    case 'manageLanguages':
        $output .= Display::manageLangForm();

        break;

    case 'collectExpr':
        $file = array('dir'         => $_REQUEST['dir'],
                      'base'        => $_REQUEST['base'],
                      'Id'          => _DEFAULT_LANG_);

        $newKeys = Data::collectExprPHP($file);
        $newKeys = array_merge($newKeys, Data::collectExprTPL($file));

        $msg = Data::checkKeys($file, $newKeys);

        if ($msg[err]) {
            foreach ($msg[err] as $val=>$err) {
                #Error($err);
                while ($key = array_search($val, $newKeys)) {
                    unset($newKeys[$key]);
                }
            }
        }

        if (count($newKeys)) {      // skip if all was unset above
            $data->addEntry2XML($file, 'begin', $newKeys);
        }

        $source = $data->readTransXML2Arr($file, _DEFAULT_LANG_);
        $output .= Display::transForm($source, $source, $file);
        $output .= Display::addEntrySelection($source, $file);
        break;
}

echo '<h3>'.getGS('localizer').'</h3>'.$error[msg].$output;
?>
</BODY>
</HTML>