DELETE FROM cc_pref WHERE keystr = 'system_version';
INSERT INTO cc_pref (keystr, valstr) VALUES ('system_version', '2.4.0');

DELETE FROM cc_pref WHERE keystr = 'stream_type';
INSERT INTO cc_pref (keystr, valstr) VALUES ('stream_type', 'ogg, mp3, opus, aac');
