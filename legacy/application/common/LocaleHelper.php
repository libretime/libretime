<?php

// Global functions for translating domain-specific strings

class Application_Common_LocaleHelper
{
    /**
     * Return an array of all ISO 639-1 language codes and their corresponding translated language names.
     *
     * @return array the array of language codes to names
     */
    public static function getISO6391LanguageCodes()
    {
        /*
         * From: https://www.binarytides.com/php-array-of-iso-639-1-language-codes-and-names/
         *
         * ISO 639-1 Language Codes
         * https://en.wikipedia.org/wiki/List_of_ISO_639-1_codes
         */
        return [
            'en' => _('English'),
            'aa' => _('Afar'),
            'ab' => _('Abkhazian'),
            'af' => _('Afrikaans'),
            'am' => _('Amharic'),
            'ar' => _('Arabic'),
            'as' => _('Assamese'),
            'ay' => _('Aymara'),
            'az' => _('Azerbaijani'),
            'ba' => _('Bashkir'),
            'be' => _('Belarusian'),
            'bg' => _('Bulgarian'),
            'bh' => _('Bihari'),
            'bi' => _('Bislama'),
            'bn' => _('Bengali/Bangla'),
            'bo' => _('Tibetan'),
            'br' => _('Breton'),
            'ca' => _('Catalan'),
            'co' => _('Corsican'),
            'cs' => _('Czech'),
            'cy' => _('Welsh'),
            'da' => _('Danish'),
            'de' => _('German'),
            'dz' => _('Bhutani'),
            'el' => _('Greek'),
            'eo' => _('Esperanto'),
            'es' => _('Spanish'),
            'et' => _('Estonian'),
            'eu' => _('Basque'),
            'fa' => _('Persian'),
            'fi' => _('Finnish'),
            'fj' => _('Fiji'),
            'fo' => _('Faeroese'),
            'fr' => _('French'),
            'fy' => _('Frisian'),
            'ga' => _('Irish'),
            'gd' => _('Scots/Gaelic'),
            'gl' => _('Galician'),
            'gn' => _('Guarani'),
            'gu' => _('Gujarati'),
            'ha' => _('Hausa'),
            'hi' => _('Hindi'),
            'hr' => _('Croatian'),
            'hu' => _('Hungarian'),
            'hy' => _('Armenian'),
            'ia' => _('Interlingua'),
            'ie' => _('Interlingue'),
            'ik' => _('Inupiak'),
            'in' => _('Indonesian'),
            'is' => _('Icelandic'),
            'it' => _('Italian'),
            'iw' => _('Hebrew'),
            'ja' => _('Japanese'),
            'ji' => _('Yiddish'),
            'jw' => _('Javanese'),
            'ka' => _('Georgian'),
            'kk' => _('Kazakh'),
            'kl' => _('Greenlandic'),
            'km' => _('Cambodian'),
            'kn' => _('Kannada'),
            'ko' => _('Korean'),
            'ks' => _('Kashmiri'),
            'ku' => _('Kurdish'),
            'ky' => _('Kirghiz'),
            'la' => _('Latin'),
            'ln' => _('Lingala'),
            'lo' => _('Laothian'),
            'lt' => _('Lithuanian'),
            'lv' => _('Latvian/Lettish'),
            'mg' => _('Malagasy'),
            'mi' => _('Maori'),
            'mk' => _('Macedonian'),
            'ml' => _('Malayalam'),
            'mn' => _('Mongolian'),
            'mo' => _('Moldavian'),
            'mr' => _('Marathi'),
            'ms' => _('Malay'),
            'mt' => _('Maltese'),
            'my' => _('Burmese'),
            'na' => _('Nauru'),
            'nb' => _('Norwegian Bokmål'),
            'ne' => _('Nepali'),
            'nl' => _('Dutch'),
            'no' => _('Norwegian'),
            'oc' => _('Occitan'),
            'om' => _('(Afan)/Oromoor/Oriya'),
            'pa' => _('Punjabi'),
            'pl' => _('Polish'),
            'ps' => _('Pashto/Pushto'),
            'pt' => _('Portuguese'),
            'qu' => _('Quechua'),
            'rm' => _('Rhaeto-Romance'),
            'rn' => _('Kirundi'),
            'ro' => _('Romanian'),
            'ru' => _('Russian'),
            'rw' => _('Kinyarwanda'),
            'sa' => _('Sanskrit'),
            'sd' => _('Sindhi'),
            'sg' => _('Sangro'),
            'sh' => _('Serbo-Croatian'),
            'si' => _('Singhalese'),
            'sk' => _('Slovak'),
            'sl' => _('Slovenian'),
            'sm' => _('Samoan'),
            'sn' => _('Shona'),
            'so' => _('Somali'),
            'sq' => _('Albanian'),
            'sr' => _('Serbian'),
            'ss' => _('Siswati'),
            'st' => _('Sesotho'),
            'su' => _('Sundanese'),
            'sv' => _('Swedish'),
            'sw' => _('Swahili'),
            'ta' => _('Tamil'),
            'te' => _('Tegulu'),
            'tg' => _('Tajik'),
            'th' => _('Thai'),
            'ti' => _('Tigrinya'),
            'tk' => _('Turkmen'),
            'tl' => _('Tagalog'),
            'tn' => _('Setswana'),
            'to' => _('Tonga'),
            'tr' => _('Turkish'),
            'ts' => _('Tsonga'),
            'tt' => _('Tatar'),
            'tw' => _('Twi'),
            'uk' => _('Ukrainian'),
            'ur' => _('Urdu'),
            'uz' => _('Uzbek'),
            'vi' => _('Vietnamese'),
            'vo' => _('Volapuk'),
            'wo' => _('Wolof'),
            'xh' => _('Xhosa'),
            'yo' => _('Yoruba'),
            'zh' => _('Chinese'),
            'zu' => _('Zulu'),
        ];
    }
}
