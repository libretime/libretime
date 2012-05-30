DROP TRIGGER calculate_position ON cc_playlistcontents;

DROP FUNCTION calculate_position();

CREATE FUNCTION airtime_to_int(chartoconvert character varying) RETURNS integer
    AS 
    'SELECT CASE WHEN trim($1) SIMILAR TO ''[0-9]+'' THEN CAST(trim($1) AS integer) ELSE NULL END;'
    LANGUAGE SQL
    IMMUTABLE
    RETURNS NULL ON NULL INPUT;

ALTER TABLE cc_files
	DROP CONSTRAINT cc_music_dirs_folder_fkey;

ALTER TABLE cc_playlist
	DROP CONSTRAINT cc_playlist_editedby_fkey;

CREATE SEQUENCE cc_live_log_id_seq
	START WITH 1
	INCREMENT BY 1
	NO MAXVALUE
	NO MINVALUE
	CACHE 1;

CREATE SEQUENCE cc_subjs_token_id_seq
	START WITH 1
	INCREMENT BY 1
	NO MAXVALUE
	NO MINVALUE
	CACHE 1;

CREATE TABLE cc_live_log (
	id integer DEFAULT nextval('cc_live_log_id_seq'::regclass) NOT NULL,
	"state" character varying(32) NOT NULL,
	start_time timestamp without time zone NOT NULL,
	end_time timestamp without time zone
);

CREATE TABLE cc_subjs_token (
	id integer DEFAULT nextval('cc_subjs_token_id_seq'::regclass) NOT NULL,
	user_id integer NOT NULL,
	"action" character varying(255) NOT NULL,
	token character varying(40) NOT NULL,
	created timestamp without time zone NOT NULL
);

ALTER TABLE cc_files
	ADD COLUMN utime timestamp(6) without time zone,
	ADD COLUMN lptime timestamp(6) without time zone,
	ADD COLUMN file_exists boolean DEFAULT true,
	ADD COLUMN soundcloud_upload_time timestamp(6) without time zone,
	ALTER COLUMN bit_rate TYPE integer USING airtime_to_int(bit_rate) /* TYPE change - table: cc_files original: character varying(32) new: integer */,
	ALTER COLUMN sample_rate TYPE integer USING airtime_to_int(bit_rate) /* TYPE change - table: cc_files original: character varying(32) new: integer */,
	ALTER COLUMN length TYPE interval /* TYPE change - table: cc_files original: time without time zone new: interval */,
	ALTER COLUMN length SET DEFAULT '00:00:00'::interval;
    
UPDATE cc_files SET utime = now()::timestamp(0);
UPDATE cc_files SET length = '00:00:00' WHERE length is NULL;

ALTER TABLE cc_music_dirs
	ADD COLUMN "exists" boolean DEFAULT true,
	ADD COLUMN watched boolean DEFAULT true;

ALTER TABLE cc_playlist
	DROP COLUMN "state",
	DROP COLUMN currentlyaccessing,
	DROP COLUMN editedby,
	DROP COLUMN creator,
	ADD COLUMN utime timestamp(6) without time zone,
	ADD COLUMN creator_id integer,
	ADD COLUMN length interval DEFAULT '00:00:00'::interval;
    
UPDATE cc_playlist SET utime = mtime;
UPDATE cc_playlist AS pl SET length = (SELECT pt.length FROM cc_playlisttimes AS pt WHERE pt.id = pl.id);
DROP VIEW cc_playlisttimes;

ALTER TABLE cc_playlistcontents
	ALTER COLUMN cliplength TYPE interval /* TYPE change - table: cc_playlistcontents original: time without time zone new: interval */,
	ALTER COLUMN cliplength SET DEFAULT '00:00:00'::interval,
	ALTER COLUMN cuein TYPE interval /* TYPE change - table: cc_playlistcontents original: time without time zone new: interval */,
	ALTER COLUMN cuein SET DEFAULT '00:00:00'::interval,
	ALTER COLUMN cueout TYPE interval /* TYPE change - table: cc_playlistcontents original: time without time zone new: interval */,
	ALTER COLUMN cueout SET DEFAULT '00:00:00'::interval;
    
UPDATE cc_playlistcontents SET cliplength = '00:00:00' WHERE cliplength is NULL;
UPDATE cc_playlistcontents SET cuein = '00:00:00' WHERE cuein is NULL;
UPDATE cc_playlistcontents SET cueout = '00:00:00' WHERE cueout is NULL;

ALTER TABLE cc_schedule
	DROP COLUMN playlist_id,
	DROP COLUMN group_id,
	DROP COLUMN schedule_group_played,
	ADD COLUMN playout_status smallint DEFAULT 1 NOT NULL,
	ADD COLUMN broadcasted smallint DEFAULT 0 NOT NULL,
	ALTER COLUMN clip_length TYPE interval /* TYPE change - table: cc_schedule original: time without time zone new: interval */,
	ALTER COLUMN clip_length SET DEFAULT '00:00:00'::interval,
	ALTER COLUMN cue_in TYPE interval /* TYPE change - table: cc_schedule original: time without time zone new: interval */,
	ALTER COLUMN cue_in SET DEFAULT '00:00:00'::interval,
	ALTER COLUMN cue_out TYPE interval /* TYPE change - table: cc_schedule original: time without time zone new: interval */,
	ALTER COLUMN cue_out SET DEFAULT '00:00:00'::interval;
    
UPDATE cc_schedule SET clip_length = '00:00:00' WHERE clip_length is NULL;
UPDATE cc_schedule SET cue_in = '00:00:00' WHERE cue_in is NULL;
UPDATE cc_schedule SET cue_out = '00:00:00' WHERE cue_out is NULL;

ALTER TABLE cc_show
	ADD COLUMN live_stream_using_airtime_auth boolean DEFAULT false,
	ADD COLUMN live_stream_using_custom_auth boolean DEFAULT false,
	ADD COLUMN live_stream_user character varying(255),
	ADD COLUMN live_stream_pass character varying(255);
   
ALTER TABLE cc_show_instances
	ADD COLUMN created timestamp without time zone,
	ADD COLUMN last_scheduled timestamp without time zone,
	ALTER COLUMN time_filled TYPE interval /* TYPE change - table: cc_show_instances original: time without time zone new: interval */,
	ALTER COLUMN time_filled SET DEFAULT '00:00:00'::interval;
    
UPDATE cc_show_instances SET time_filled = '00:00:00' WHERE time_filled is NULL;
UPDATE cc_show_instances SET created = now();
UPDATE cc_show_instances SET last_scheduled = now();

ALTER TABLE cc_show_instances
	ALTER COLUMN created SET NOT NULL;

ALTER TABLE cc_live_log
	ADD CONSTRAINT cc_live_log_pkey PRIMARY KEY (id);

ALTER TABLE cc_subjs_token
	ADD CONSTRAINT cc_subjs_token_pkey PRIMARY KEY (id);

ALTER TABLE cc_files
	ADD CONSTRAINT cc_music_dirs_folder_fkey FOREIGN KEY (directory) REFERENCES cc_music_dirs(id);

ALTER TABLE cc_playlist
	ADD CONSTRAINT cc_playlist_createdby_fkey FOREIGN KEY (creator_id) REFERENCES cc_subjs(id);

ALTER TABLE cc_subjs_token
	ADD CONSTRAINT cc_subjs_token_idx UNIQUE (token);

ALTER TABLE cc_subjs_token
	ADD CONSTRAINT cc_subjs_token_userid_fkey FOREIGN KEY (user_id) REFERENCES cc_subjs(id) ON DELETE CASCADE;

CREATE INDEX cc_files_file_exists_idx ON cc_files USING btree (file_exists);

DROP FUNCTION airtime_to_int(chartoconvert character varying);


UPDATE cc_playlist SET creator_id = (SELECT id FROM cc_subjs WHERE type = 'A' LIMIT 1);

DELETE FROM cc_pref WHERE keystr = 'scheduled_play_switch';
INSERT INTO cc_pref(keystr, valstr) VALUES('scheduled_play_switch', 'on');

INSERT INTO cc_live_log(state, start_time) VALUES('S', now() at time zone 'UTC');

DELETE FROM cc_pref WHERE keystr = 'system_version';
INSERT INTO cc_pref (keystr, valstr) VALUES ('system_version', '2.1.0');
 
--UPDATE
UPDATE cc_schedule SET playout_status = 1 WHERE id in (SELECT DISTINCT s.id FROM cc_schedule as s LEFT JOIN cc_show_instances as si ON si.id = s.instance_id WHERE s.ends <= si.ends AND s.playout_status >= 0); 

UPDATE cc_schedule SET playout_status = 2 WHERE id in (SELECT DISTINCT s.id FROM cc_schedule as s LEFT JOIN cc_show_instances as si ON si.id = s.instance_id WHERE s.starts < si.ends AND s.ends > si.ends AND s.playout_status >= 0);

UPDATE cc_schedule SET playout_status = 0 WHERE id in (SELECT DISTINCT s.id FROM cc_schedule as s LEFT JOIN cc_show_instances as si ON si.id = s.instance_id WHERE s.starts > si.ends AND s.playout_status >= 0);


