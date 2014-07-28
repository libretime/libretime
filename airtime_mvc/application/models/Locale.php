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
        //putenv("LC_ALL=$lang");
        //putenv("LANG=$lang");
        //Setting the LANGUAGE env var supposedly lets gettext search inside our locale dir even if the system 
        //doesn't have the particular locale that we want installed. This doesn't actually seem to work though. -- Albert
        putenv("LANGUAGE=$locale"); 
        if (setlocale(LC_MESSAGES, $lang) === false)
        {
            Logging::warn("Your system does not have the " . $lang . " locale installed. Run: sudo locale-gen " . $lang);
        }
        
        $domain = 'airtime';
        bindtextdomain($domain, '../locale');
        textdomain($domain);
        bind_textdomain_codeset($domain, $codeset);
    }
}