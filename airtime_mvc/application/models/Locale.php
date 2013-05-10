<?php

class Application_Model_Locale
{
    public static function getLocales()
    {
        $con = Propel::getConnection();
        $sql = "SELECT * FROM cc_locale";
        $res =  Application_Common_Database::prepareAndExecute($sql);
        $out = array();
        foreach ($res as $r) {
            $out[$r["locale_code"]] = $r["locale_lang"];
        }

        return $out;
    }

    public static function configureLocalization($locale = null)
    {
        $codeset = 'UTF-8';
        if (is_null($locale)) {
            $lang = Application_Model_Preference::GetLocale().'.'.$codeset;
        } else {
            $lang = $locale.'.'.$codeset;
        }
        putenv("LC_ALL=$lang");
        putenv("LANG=$lang");
        $res = setlocale(LC_MESSAGES, $lang);

        $domain = 'airtime';
        bindtextdomain($domain, '../locale');
        textdomain($domain);
        bind_textdomain_codeset($domain, $codeset);
    }
}