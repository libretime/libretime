DELETE FROM cc_pref WHERE id IN (
SELECT cc_pref.id	 
FROM cc_pref
LEFT OUTER JOIN (
   SELECT MAX(id) as id, keystr, subjid 
   FROM cc_pref 
   GROUP BY keystr, subjid
) as KeepRows ON
   cc_pref.id = KeepRows.id
WHERE
   KeepRows.id IS NULL
);

DELETE FROM cc_pref WHERE keystr = 'system_version';
INSERT INTO cc_pref (keystr, valstr) VALUES ('system_version', '2.4.0');

DELETE FROM cc_pref WHERE keystr = 'stream_type';
INSERT INTO cc_pref (keystr, valstr) VALUES ('stream_type', 'ogg, mp3, opus, aac');

UPDATE cc_files
SET is_scheduled = true
WHERE id IN (SELECT DISTINCT(file_id) FROM cc_schedule WHERE playout_status != -1);

UPDATE cc_files
SET is_playlist = true
WHERE id IN (SELECT DISTINCT(file_id) FROM cc_playlistcontents);

UPDATE cc_files
SET is_playlist = true
WHERE id IN (SELECT DISTINCT(file_id) FROM cc_blockcontents);

INSERT INTO cc_locale (locale_code, locale_lang) VALUES ('hu_HU', 'Magyar');