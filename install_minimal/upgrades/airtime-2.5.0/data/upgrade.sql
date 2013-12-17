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
LEFT JOIN cc_show_instances as showinstances
ON sched.instance_id = showinstances.id
LEFT JOIN cc_show AS show
ON showinstances.show_id = show.id;

CREATE OR REPLACE FUNCTION migrateWebstreamHistory() RETURNS int4 AS $$

DECLARE r RECORD;
DECLARE hisid integer;

BEGIN
    FOR r IN SELECT * from ws_history LOOP
 
            insert into cc_playout_history (starts, instance_id)
            values (r.starts, r.instance_id) 
            returning id into hisid;

            insert into cc_playout_history_metadata (history_id, key, value)
            values (hisid, 'track_title', substring(r.title from 1 for 128));

            insert into cc_playout_history_metadata (history_id, key, value)
            values (hisid, 'artist_name', substring(r.creator from 1 for 128));

            insert into cc_playout_history_metadata (history_id, key, value)
            values (hisid, 'showname', substring(r.showname from 1 for 128));
            
    END LOOP;
return 1;
END;
$$ 
LANGUAGE plpgsql;

SELECT migrateWebstreamHistory() as output;

DROP FUNCTION migrateWebstreamHistory();
DROP VIEW ws_history;

DELETE from cc_show_instances AS ins 
WHERE (ins.starts,ins.ends,ins.show_id) 
IN (SELECT starts,ends,show_id FROM cc_show_instances GROUP BY starts,ends,show_id HAVING count(*) >1 ) 
AND ins.id NOT IN (SELECT min(id) FROM cc_show_instances GROUP BY starts,ends,show_id HAVING count(*) >1 );


DELETE FROM cc_schedule 
WHERE id 
IN (SELECT sc.id FROM cc_schedule AS sc LEFT JOIN cc_show_instances AS i ON sc.instance_id=i.id LEFT JOIN cc_show AS s ON i.show_id=s.id WHERE sc.starts<i.starts ORDER BY sc.starts);
