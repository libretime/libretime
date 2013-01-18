DELETE FROM cc_pref WHERE keystr = 'system_version';
INSERT INTO cc_pref (keystr, valstr) VALUES ('system_version', '2.3.0');

INSERT INTO cc_stream_setting ("keyname", "value", "type") VALUES ('off_air_meta', 'Airtime - offline', 'string');
INSERT INTO cc_pref("keystr", "valstr") VALUES('enable_replay_gain', 1);

--Make sure that cc_music_dir has a trailing '/' and cc_files does not have a leading '/'
UPDATE cc_music_dir SET directory = directory || '/' where id in (select id from cc_music_dirs where substr(directory, length(directory)) != '/');
UPDATE cc_files SET filepath = substring(filepath from 2) where id in (select id from cc_files where substring(filepath from 1 for 1) = '/')

CREATE SEQUENCE cc_listener_count_id_seq
	START WITH 1
	INCREMENT BY 1
	NO MAXVALUE
	NO MINVALUE
	CACHE 1;

CREATE SEQUENCE cc_locale_id_seq
	START WITH 1
	INCREMENT BY 1
	NO MAXVALUE
	NO MINVALUE
	CACHE 1;

CREATE SEQUENCE cc_mount_name_id_seq
	START WITH 1
	INCREMENT BY 1
	NO MAXVALUE
	NO MINVALUE
	CACHE 1;

CREATE SEQUENCE cc_timestamp_id_seq
	START WITH 1
	INCREMENT BY 1
	NO MAXVALUE
	NO MINVALUE
	CACHE 1;

CREATE TABLE cc_listener_count (
	id integer DEFAULT nextval('cc_listener_count_id_seq'::regclass) NOT NULL,
	timestamp_id integer NOT NULL,
	mount_name_id integer NOT NULL,
	listener_count integer NOT NULL
);

CREATE TABLE cc_locale (
	id integer DEFAULT nextval('cc_locale_id_seq'::regclass) NOT NULL,
	locale_code character varying(16) NOT NULL,
	locale_lang character varying(128) NOT NULL
);

CREATE TABLE cc_mount_name (
	id integer DEFAULT nextval('cc_mount_name_id_seq'::regclass) NOT NULL,
	mount_name character varying(255) NOT NULL
);

CREATE TABLE cc_timestamp (
	id integer DEFAULT nextval('cc_timestamp_id_seq'::regclass) NOT NULL,
	"timestamp" timestamp without time zone NOT NULL
);

ALTER TABLE cc_files
	ADD COLUMN cuein interval DEFAULT '00:00:00'::interval,
	ADD COLUMN cueout interval DEFAULT '00:00:00'::interval,
	ADD COLUMN hidden boolean DEFAULT false;

ALTER TABLE cc_listener_count
	ADD CONSTRAINT cc_listener_count_pkey PRIMARY KEY (id);

ALTER TABLE cc_locale
	ADD CONSTRAINT cc_locale_pkey PRIMARY KEY (id);

ALTER TABLE cc_mount_name
	ADD CONSTRAINT cc_mount_name_pkey PRIMARY KEY (id);

ALTER TABLE cc_timestamp
	ADD CONSTRAINT cc_timestamp_pkey PRIMARY KEY (id);

ALTER TABLE cc_listener_count
	ADD CONSTRAINT cc_mount_name_inst_fkey FOREIGN KEY (mount_name_id) REFERENCES cc_mount_name(id) ON DELETE CASCADE;

ALTER TABLE cc_listener_count
	ADD CONSTRAINT cc_timestamp_inst_fkey FOREIGN KEY (timestamp_id) REFERENCES cc_timestamp(id) ON DELETE CASCADE;

INSERT INTO cc_pref("keystr", "valstr") VALUES('locale', 'en_CA');

INSERT INTO cc_pref("subjid", "keystr", "valstr") VALUES(1, 'user_locale', 'en_CA');

INSERT INTO cc_locale (locale_code, locale_lang) VALUES ('en_CA', 'English');
INSERT INTO cc_locale (locale_code, locale_lang) VALUES ('fr_FR', 'Français');
INSERT INTO cc_locale (locale_code, locale_lang) VALUES ('de_DE', 'Deutsch');
INSERT INTO cc_locale (locale_code, locale_lang) VALUES ('it_IT', 'Italiano');
INSERT INTO cc_locale (locale_code, locale_lang) VALUES ('ko_KR', '한국어');
INSERT INTO cc_locale (locale_code, locale_lang) VALUES ('ru_RU', 'Русский');
INSERT INTO cc_locale (locale_code, locale_lang) VALUES ('es_ES', 'Español');
INSERT INTO cc_locale (locale_code, locale_lang) VALUES ('zh_CN', '简体中文');
