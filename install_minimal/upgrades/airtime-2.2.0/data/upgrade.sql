DELETE FROM cc_pref WHERE keystr = 'system_version';
INSERT INTO cc_pref (keystr, valstr) VALUES ('system_version', '2.2.0');


INSERT INTO cc_stream_setting (keyname, value, type) VALUES ('s1_name', 'Airtime!', 'string');
INSERT INTO cc_stream_setting (keyname, value, type) VALUES ('s2_name', '', 'string');
INSERT INTO cc_stream_setting (keyname, value, type) VALUES ('s3_name', '', 'string');

INSERT INTO cc_stream_setting (keyname, value, type) VALUES ('s1_channels', 'stereo', 'string');
INSERT INTO cc_stream_setting (keyname, value, type) VALUES ('s2_channels', 'stereo', 'string');
INSERT INTO cc_stream_setting (keyname, value, type) VALUES ('s3_channels', 'stereo', 'string');

ALTER TABLE cc_files
	DROP CONSTRAINT cc_files_gunid_idx;

DROP TABLE cc_access;

DROP SEQUENCE cc_access_id_seq;

CREATE FUNCTION airtime_to_int(chartoconvert character varying) RETURNS integer
    AS 
    'SELECT CASE WHEN trim($1) SIMILAR TO ''[0-9]+'' THEN CAST(trim($1) AS integer) ELSE NULL END;'
    LANGUAGE SQL
    IMMUTABLE
    RETURNS NULL ON NULL INPUT;

CREATE SEQUENCE cc_block_id_seq
	START WITH 1
	INCREMENT BY 1
	NO MAXVALUE
	NO MINVALUE
	CACHE 1;

CREATE SEQUENCE cc_blockcontents_id_seq
	START WITH 1
	INCREMENT BY 1
	NO MAXVALUE
	NO MINVALUE
	CACHE 1;

CREATE SEQUENCE cc_blockcriteria_id_seq
	START WITH 1
	INCREMENT BY 1
	NO MAXVALUE
	NO MINVALUE
	CACHE 1;

CREATE SEQUENCE cc_webstream_id_seq
	START WITH 1
	INCREMENT BY 1
	NO MAXVALUE
	NO MINVALUE
	CACHE 1;

CREATE SEQUENCE cc_webstream_metadata_id_seq
	START WITH 1
	INCREMENT BY 1
	NO MAXVALUE
	NO MINVALUE
	CACHE 1;

CREATE TABLE cc_block (
	id integer DEFAULT nextval('cc_block_id_seq'::regclass) NOT NULL,
	name character varying(255) DEFAULT ''::character varying NOT NULL,
	mtime timestamp(6) without time zone,
	utime timestamp(6) without time zone,
	creator_id integer,
	description character varying(512),
	length interval DEFAULT '00:00:00'::interval,
	type character varying(7) DEFAULT 'static'::character varying
);

CREATE TABLE cc_blockcontents (
	id integer DEFAULT nextval('cc_blockcontents_id_seq'::regclass) NOT NULL,
	block_id integer,
	file_id integer,
	"position" integer,
	cliplength interval DEFAULT '00:00:00'::interval,
	cuein interval DEFAULT '00:00:00'::interval,
	cueout interval DEFAULT '00:00:00'::interval,
	fadein time without time zone DEFAULT '00:00:00'::time without time zone,
	fadeout time without time zone DEFAULT '00:00:00'::time without time zone
);

CREATE TABLE cc_blockcriteria (
	id integer DEFAULT nextval('cc_blockcriteria_id_seq'::regclass) NOT NULL,
	criteria character varying(32) NOT NULL,
	modifier character varying(16) NOT NULL,
	"value" character varying(512) NOT NULL,
	extra character varying(512),
	block_id integer NOT NULL
);

CREATE TABLE cc_webstream (
	id integer DEFAULT nextval('cc_webstream_id_seq'::regclass) NOT NULL,
	name character varying(255) NOT NULL,
	description character varying(255) NOT NULL,
	url character varying(512) NOT NULL,
	length interval DEFAULT '00:00:00'::interval NOT NULL,
	creator_id integer NOT NULL,
	mtime timestamp(6) without time zone NOT NULL,
	utime timestamp(6) without time zone NOT NULL,
	mime character varying(255)
);

CREATE TABLE cc_webstream_metadata (
	id integer DEFAULT nextval('cc_webstream_metadata_id_seq'::regclass) NOT NULL,
	instance_id integer NOT NULL,
	start_time timestamp without time zone NOT NULL,
	liquidsoap_data character varying(1024) NOT NULL
);

ALTER TABLE cc_files
	DROP COLUMN gunid,
	ADD COLUMN replay_gain character varying(16),
    ADD COLUMN owner_id integer;
	ALTER COLUMN bpm TYPE integer using airtime_to_int(bpm) /* TYPE change - table: cc_files original: character varying(8) new: integer */;

ALTER TABLE cc_playlistcontents
	ADD COLUMN block_id integer,
	ADD COLUMN stream_id integer,
	ADD COLUMN type smallint DEFAULT 0 NOT NULL;

ALTER TABLE cc_schedule
	ADD COLUMN stream_id integer;

ALTER TABLE cc_subjs
	ADD COLUMN cell_phone character varying(255);

ALTER TABLE cc_block
	ADD CONSTRAINT cc_block_pkey PRIMARY KEY (id);

ALTER TABLE cc_blockcontents
	ADD CONSTRAINT cc_blockcontents_pkey PRIMARY KEY (id);

ALTER TABLE cc_blockcriteria
	ADD CONSTRAINT cc_blockcriteria_pkey PRIMARY KEY (id);

ALTER TABLE cc_webstream
	ADD CONSTRAINT cc_webstream_pkey PRIMARY KEY (id);

ALTER TABLE cc_webstream_metadata
	ADD CONSTRAINT cc_webstream_metadata_pkey PRIMARY KEY (id);

ALTER TABLE cc_block
	ADD CONSTRAINT cc_block_createdby_fkey FOREIGN KEY (creator_id) REFERENCES cc_subjs(id);

ALTER TABLE cc_blockcontents
	ADD CONSTRAINT cc_blockcontents_block_id_fkey FOREIGN KEY (block_id) REFERENCES cc_block(id) ON DELETE CASCADE;

ALTER TABLE cc_blockcontents
	ADD CONSTRAINT cc_blockcontents_file_id_fkey FOREIGN KEY (file_id) REFERENCES cc_files(id) ON DELETE CASCADE;

ALTER TABLE cc_blockcriteria
	ADD CONSTRAINT cc_blockcontents_block_id_fkey FOREIGN KEY (block_id) REFERENCES cc_block(id) ON DELETE CASCADE;

ALTER TABLE cc_playlistcontents
	ADD CONSTRAINT cc_playlistcontents_block_id_fkey FOREIGN KEY (block_id) REFERENCES cc_block(id) ON DELETE CASCADE;

ALTER TABLE cc_schedule
	ADD CONSTRAINT cc_show_stream_fkey FOREIGN KEY (stream_id) REFERENCES cc_webstream(id) ON DELETE CASCADE;

ALTER TABLE cc_webstream_metadata
	ADD CONSTRAINT cc_schedule_inst_fkey FOREIGN KEY (instance_id) REFERENCES cc_schedule(id) ON DELETE CASCADE;

DROP FUNCTION airtime_to_int(chartoconvert character varying);

UPDATE cc_files
SET owner_id=(SELECT id FROM cc_subjs WHERE type='A' LIMIT 1)
WHERE owner_id is NULL


