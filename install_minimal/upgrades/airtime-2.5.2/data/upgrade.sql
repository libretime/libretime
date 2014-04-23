INSERT INTO cc_locale (locale_code, locale_lang) VALUES ('hr_HR', 'Hrvatski');
INSERT INTO cc_locale (locale_code, locale_lang) VALUES ('sr_RS', 'Српски (Ћирилица)');
INSERT INTO cc_locale (locale_code, locale_lang) VALUES ('sr_RS@latin', 'Srpski (Latinica)');

UPDATE cc_locale SET locale_lang='Deutsch (Österreich)' WHERE locale_code='de_AT';
UPDATE cc_locale SET locale_lang='Português Brasileiro' WHERE locale_code='pt_BR';

-- NOTE BECAUSE OF CACHING NOW ANY UPGRADES TO cc_pref MUST NOT BE DONE HERE.
