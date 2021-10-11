-- Replacing system_version with schema_version
DELETE FROM cc_pref WHERE keystr = 'system_version';
INSERT INTO cc_pref (keystr, valstr) VALUES ('schema_version', '2.5.2');

ALTER TABLE cc_show ADD COLUMN image_path varchar(255) DEFAULT '';
ALTER TABLE cc_show_instances ADD COLUMN description varchar(255) DEFAULT '';
