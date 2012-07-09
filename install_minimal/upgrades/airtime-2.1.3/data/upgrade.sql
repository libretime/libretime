DELETE FROM cc_pref WHERE keystr = 'system_version';
INSERT INTO cc_pref (keystr, valstr) VALUES ('system_version', '2.1.3');

UPDATE cc_show_instances SET time_filled='00:00:00' WHERE time_filled IS NULL;
ALTER TABLE cc_subjs ADD COLUMN cell_phone VARCHAR(255);
