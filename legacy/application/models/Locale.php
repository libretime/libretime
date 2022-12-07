<?php

declare(strict_types=1);

final class Application_Model_Locale
{
    private static $domains = ['libretime'];

    public static $locales = [
        'en_US' => 'English (USA)',
        'cs_CZ' => 'Český',
        'de_AT' => 'Deutsch (Österreich)',
        'de_DE' => 'Deutsch',
        'el_GR' => 'Ελληνικά',
        'en_CA' => 'English (Canada)',
        'en_GB' => 'English (Britain)',
        'es_ES' => 'Español',
        'fr_FR' => 'Français',
        'hr_HR' => 'Hrvatski',
        'hu_HU' => 'Magyar',
        'it_IT' => 'Italiano',
        'ja_JP' => '日本語',
        'ko_KR' => '한국어',
        // 'nl_NL' => '',
        'pl_PL' => 'Polski',
        'pt_BR' => 'Português (Brasil)',
        'ru_RU' => 'Русский',
        'sr_RS' => 'Српски (Ћирилица)',
        'sr_RS@latin' => 'Srpski (Latinica)',
        // 'tr_TR' => '',
        'uk_UA' => 'украї́нська мо́ва',
        'zh_CN' => '简体中文',
    ];

    public static function getLocales()
    {
        return self::$locales;
    }

    public static function configureLocalization($locale = null)
    {
        $codeset = 'UTF-8';
        if (is_null($locale)) {
            $lang = Application_Model_Preference::GetLocale() . '.' . $codeset;
        } else {
            $lang = $locale . '.' . $codeset;
        }
        // putenv("LC_ALL=$lang");
        // putenv("LANG=$lang");
        // Setting the LANGUAGE env var supposedly lets gettext search inside our locale dir even if the system
        // doesn't have the particular locale that we want installed. This doesn't actually seem to work though. -- Albert
        putenv("LANGUAGE={$locale}");
        if (setlocale(LC_MESSAGES, $lang) === false) {
            Logging::warn('Your system does not have the ' . $lang . ' locale installed. Run: sudo locale-gen ' . $lang);
        }

        // We need to run bindtextdomain and bind_textdomain_codeset for each domain we're using.
        foreach (self::$domains as $domain) {
            bindtextdomain($domain, '../locale');
            bind_textdomain_codeset($domain, $codeset);
        }

        textdomain('libretime');
    }

    /**
     * We need this function for the case where a user has logged out, but
     * has an airtime_locale cookie containing their locale setting.
     *
     * If the user does not have an airtime_locale cookie set, we default
     * to the station locale.
     *
     * When the user logs in, the value set in the login form will be passed
     * into the airtime_locale cookie. This cookie is also updated when
     * a user updates their user settings.
     */
    public static function getUserLocale()
    {
        $request = Zend_Controller_Front::getInstance()->getRequest();

        return $request->getCookie('airtime_locale', Application_Model_Preference::GetLocale());
    }
}
