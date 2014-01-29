DELETE FROM cc_pref WHERE keystr = 'system_version';
INSERT INTO cc_pref (keystr, valstr) VALUES ('system_version', '2.5.2');

INSERT INTO cc_locale (locale_code, locale_lang) VALUES ('hr_HR', 'Hrvatski');
INSERT INTO cc_locale (locale_code, locale_lang) VALUES ('sr_RS', 'Српски (Ћирилица)');
INSERT INTO cc_locale (locale_code, locale_lang) VALUES ('sr_RS@latin', 'Srpski (Latinica)');
