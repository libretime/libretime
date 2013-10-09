DELETE FROM cc_pref WHERE keystr = 'system_version';
INSERT INTO cc_pref (keystr, valstr) VALUES ('system_version', '2.5.0');

INSERT INTO cc_playout_history (file_id, starts, ends, instance_id)
SELECT file_id, starts, ends, instance_id
FROM cc_schedule
WHERE media_item_played = true;

drop view ws_history;
create view ws_history as

select 
wm.start_time as starts, 
ws.name as creator,
wm.liquidsoap_data as title,
sched.instance_id as instance_id

from cc_webstream_metadata as wm
left join cc_schedule as sched
on sched.id = wm.instance_id
left join cc_webstream as ws
on sched.stream_id = ws.id;


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

            
    END LOOP;
return 1;
END;
$$ 
LANGUAGE plpgsql;

SELECT migrateWebstreamHistory() as output;