DELETE FROM cc_pref WHERE keystr = 'system_version';
INSERT INTO cc_pref (keystr, valstr) VALUES ('system_version', '2.5.3');

ALTER TABLE cc_files DROP COLUMN state;
ALTER TABLE cc_files ADD import_status integer default 1; -- Default is "pending"
UPDATE cc_files SET import_status=0; -- Existing files are already "imported"
