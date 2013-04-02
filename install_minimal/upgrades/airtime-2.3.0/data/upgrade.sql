DELETE FROM cc_pref WHERE keystr = 'system_version';
INSERT INTO cc_pref (keystr, valstr) VALUES ('system_version', '2.3.0');

INSERT INTO cc_stream_setting ("keyname", "value", "type") VALUES ('off_air_meta', 'Airtime - offline', 'string');
INSERT INTO cc_pref("keystr", "valstr") VALUES('enable_replay_gain', 1);

INSERT INTO cc_stream_setting ("keyname", "value", "type") VALUES ('s1_admin_user', '', 'string');
INSERT INTO cc_stream_setting ("keyname", "value", "type") VALUES ('s1_admin_pass', '', 'string');
INSERT INTO cc_stream_setting ("keyname", "value", "type") VALUES ('s2_admin_user', '', 'string');
INSERT INTO cc_stream_setting ("keyname", "value", "type") VALUES ('s2_admin_pass', '', 'string');
INSERT INTO cc_stream_setting ("keyname", "value", "type") VALUES ('s3_admin_user', '', 'string');
INSERT INTO cc_stream_setting ("keyname", "value", "type") VALUES ('s3_admin_pass', '', 'string');

--Make sure that cc_music_dir has a trailing '/' and cc_files does not have a leading '/'
UPDATE cc_music_dirs SET directory = directory || '/' where id in (select id from cc_music_dirs where substr(directory, length(directory)) != '/');
UPDATE cc_files SET filepath = substring(filepath from 2) where id in (select id from cc_files where substring(filepath from 1 for 1) = '/');

UPDATE cc_files SET cueout = length where cueout = '00:00:00';

UPDATE cc_schedule SET cue_out = clip_length WHERE cue_out = '00:00:00';

UPDATE cc_schedule SET fade_out = '00:00:59.9' WHERE fade_out > '00:00:59.9';
UPDATE cc_schedule SET fade_in = '00:00:59.9' WHERE fade_in > '00:00:59.9';
UPDATE cc_playlistcontents SET fadeout = '00:00:59.9' WHERE fadeout > '00:00:59.9';
UPDATE cc_playlistcontents SET fadein = '00:00:59.9' WHERE fadein > '00:00:59.9';
UPDATE cc_blockcontents SET fadeout = '00:00:59.9' WHERE fadeout > '00:00:59.9';
UPDATE cc_blockcontents SET fadein = '00:00:59.9' WHERE fadein > '00:00:59.9';

INSERT INTO cc_pref("keystr", "valstr") VALUES('locale', 'en_CA');

INSERT INTO cc_pref("subjid", "keystr", "valstr") VALUES(1, 'user_locale', 'en_CA');

INSERT INTO cc_locale (locale_code, locale_lang) VALUES ('en_CA', 'English (Canada)');
INSERT INTO cc_locale (locale_code, locale_lang) VALUES ('en_GB', 'English (Britain)');
INSERT INTO cc_locale (locale_code, locale_lang) VALUES ('en_US', 'English (USA)');
INSERT INTO cc_locale (locale_code, locale_lang) VALUES ('cs_CZ', 'Český');
INSERT INTO cc_locale (locale_code, locale_lang) VALUES ('de_DE', 'Deutsch');
INSERT INTO cc_locale (locale_code, locale_lang) VALUES ('de_AT', 'Österreichisches Deutsch');
INSERT INTO cc_locale (locale_code, locale_lang) VALUES ('es_ES', 'Español');
INSERT INTO cc_locale (locale_code, locale_lang) VALUES ('fr_FR', 'Français');
INSERT INTO cc_locale (locale_code, locale_lang) VALUES ('it_IT', 'Italiano');
INSERT INTO cc_locale (locale_code, locale_lang) VALUES ('ko_KR', '한국어');
INSERT INTO cc_locale (locale_code, locale_lang) VALUES ('pl_PL', 'Polski');
INSERT INTO cc_locale (locale_code, locale_lang) VALUES ('pt_BR', 'Português Brasileiro');
INSERT INTO cc_locale (locale_code, locale_lang) VALUES ('ru_RU', 'Русский');
INSERT INTO cc_locale (locale_code, locale_lang) VALUES ('zh_CN', '简体中文');
INSERT INTO cc_locale (locale_code, locale_lang) VALUES ('el_GR', 'Ελληνικά');
