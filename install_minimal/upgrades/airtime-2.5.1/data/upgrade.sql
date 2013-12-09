DELETE FROM cc_pref WHERE keystr = 'system_version';
INSERT INTO cc_pref (keystr, valstr) VALUES ('system_version', '2.5.1');
