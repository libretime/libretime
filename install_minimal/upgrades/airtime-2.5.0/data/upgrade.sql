DELETE FROM cc_pref WHERE keystr = 'system_version';
INSERT INTO cc_pref (keystr, valstr) VALUES ('system_version', '2.5.0');

INSERT INTO cc_playout_history (file_id, starts, ends, instance_id)
SELECT file_id, starts, ends, instance_id
FROM cc_schedule
WHERE file_id IS NOT NULL AND media_item_played = true;


CREATE VIEW ws_history AS

SELECT 
wm.start_time as starts, 
ws.name as creator,
wm.liquidsoap_data as title,
sched.instance_id as instance_id,
show.name as showname

FROM cc_webstream_metadata AS wm
LEFT JOIN cc_schedule AS sched
ON sched.id = wm.instance_id
LEFT JOIN cc_webstream AS ws
ON sched.stream_id = ws.id
LEFT JOIN cc_show AS show
ON sched.instance_id = show.id;


CREATE OR REPLACE FUNCTION migrateWebstreamHistory() RETURNS int4 AS $$

DECLARE r RECORD;
DECLARE hisid integer;

BEGIN
    FOR r IN SELECT * from ws_history LOOP
 
            insert into cc_playout_history (starts, instance_id)
            values (r.starts, r.instance_id) 
            returning id into hisid;

            insert into cc_playout_history_metadata (history_id, key, value)
            values (hisid, 'track_title', r.title);

            insert into cc_playout_history_metadata (history_id, key, value)
            values (hisid, 'artist_name', r.creator);

            insert into cc_playout_history_metadata (history_id, key, value)
            values (hisid, 'showname', r.showname);
            
    END LOOP;
return 1;
END;
$$ 
LANGUAGE plpgsql;

SELECT migrateWebstreamHistory() as output;

DROP FUNCTION migrateWebstreamHistory();
DROP VIEW ws_history;