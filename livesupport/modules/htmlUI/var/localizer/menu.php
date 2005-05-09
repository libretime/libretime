<?php
require_once('require.inc.php');
include_once dirname(__FILE__).'/auth.inc.php';

include_once('header.html');

loadLanguageFiles('', 'locals');
loadLanguageFiles('..', 'globals');

switch ($_REQUEST[action]) {
    case 'createLangFilesRec':
        Data::createLangFilesRec($_REQUEST[Id]);
    break;
}

echo Display::parseFolder(_START_DIR_);
echo Display::createLangMenu($_REQUEST[Id]);

#echo Display::createTOLLangMenu($_COOKIE[TOL_Language]);
#echo Display::manageLangButton();
?>
</BODY>
</HTML>