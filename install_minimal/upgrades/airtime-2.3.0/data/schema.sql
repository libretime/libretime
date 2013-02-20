
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
	ADD COLUMN silan_check boolean DEFAULT false,
	ADD COLUMN hidden boolean DEFAULT false;

ALTER TABLE cc_schedule
	ALTER COLUMN cue_in DROP DEFAULT,
	ALTER COLUMN cue_in SET NOT NULL,
	ALTER COLUMN cue_out DROP DEFAULT,
	ALTER COLUMN cue_out SET NOT NULL;

ALTER SEQUENCE cc_listener_count_id_seq
	OWNED BY cc_listener_count.id;

ALTER SEQUENCE cc_locale_id_seq
	OWNED BY cc_locale.id;

ALTER SEQUENCE cc_mount_name_id_seq
	OWNED BY cc_mount_name.id;

ALTER SEQUENCE cc_timestamp_id_seq
	OWNED BY cc_timestamp.id;

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
