DELETE FROM cc_pref WHERE keystr = 'system_version';
INSERT INTO cc_pref (keystr, valstr) VALUES ('system_version', '2.2.1');

ALTER TABLE cc_block
	DROP CONSTRAINT cc_block_createdby_fkey;

ALTER TABLE cc_block
	ADD CONSTRAINT cc_block_createdby_fkey FOREIGN KEY (creator_id) REFERENCES cc_subjs(id) ON DELETE CASCADE;

