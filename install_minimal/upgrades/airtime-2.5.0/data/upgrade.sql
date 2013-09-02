DELETE FROM cc_pref WHERE keystr = 'system_version';
INSERT INTO cc_pref (keystr, valstr) VALUES ('system_version', '2.5.0');

INSERT INTO cc_playout_history (file_id, starts, ends, instance_id)
SELECT file_id, starts, ends, instance_id
FROM cc_schedule
WHERE media_item_played = true;