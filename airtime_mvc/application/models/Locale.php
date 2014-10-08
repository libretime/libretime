<?php

class Application_Model_Locale
{
    public static $locales = array(
    	    "en_CA" => "English (Canada)",
            "en_GB" => "English (Britain)",
            "en_US" => "English (USA)",
            "cs_CZ" => "Český",
            "de_DE" => "Deutsch",
            "de_AT" => "Deutsch (Österreich)",
            "el_GR" => "Ελληνικά",
            "es_ES" => "Español",
            "fr_FR" => "Français",
            "hr_HR" => "Hrvatski",
            "hu_HU" => "Magyar",
            "it_IT" => "Italiano",
            "ja_JP"    => "日本語",
            "ko_KR" => "한국어",
            "pl_PL" => "Polski",
            "pt_BR" => "Português (Brasil)",
            "ru_RU" => "Русский",
            "sr_RS" => "Српски (Ћирилица)",
            "sr_RS@latin" => "Srpski (Latinica)",
            "zh_CN" => "简体中文"
        );
    
    public static function getLocales()
    {
        return self::$locales;
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

