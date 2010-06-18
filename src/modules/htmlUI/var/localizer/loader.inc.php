<?php
require_once dirname(__FILE__).'/Localizer.php';

function loadTranslations($langid)
{
    Localizer::LoadLanguageFiles('masks', $langid, true);
    Localizer::LoadLanguageFiles('application', $langid, true);
    return Localizer::LoadLanguageFiles('templates', $langid, true);
}

function getLanguages()
{
    return LOCALIZER::getAllLanguages(NULL, FALSE, TRUE);    
}
?>