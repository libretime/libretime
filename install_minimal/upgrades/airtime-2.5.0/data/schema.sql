
CREATE SEQUENCE cc_playout_history_id_seq
	START WITH 1
	INCREMENT BY 1
	NO MAXVALUE
	NO MINVALUE
	CACHE 1;

CREATE SEQUENCE cc_playout_history_metadata_id_seq
	START WITH 1
	INCREMENT BY 1
	NO MAXVALUE
	NO MINVALUE
	CACHE 1;

CREATE SEQUENCE cc_playout_history_template_field_id_seq
	START WITH 1
	INCREMENT BY 1
	NO MAXVALUE
	NO MINVALUE
	CACHE 1;

CREATE SEQUENCE cc_playout_history_template_id_seq
	START WITH 1
	INCREMENT BY 1
	NO MAXVALUE
	NO MINVALUE
	CACHE 1;

CREATE TABLE cc_playout_history (
	id integer DEFAULT nextval('cc_playout_history_id_seq'::regclass) NOT NULL,
	file_id integer,
	starts timestamp without time zone NOT NULL,
	ends timestamp without time zone,
	instance_id integer
);

CREATE TABLE cc_playout_history_metadata (
	id integer DEFAULT nextval('cc_playout_history_metadata_id_seq'::regclass) NOT NULL,
	history_id integer NOT NULL,
	"key" character varying(128) NOT NULL,
	"value" character varying(128) NOT NULL
);

CREATE TABLE cc_playout_history_template (
	id integer DEFAULT nextval('cc_playout_history_template_id_seq'::regclass) NOT NULL,
	name character varying(128) NOT NULL,
	type character varying(35) NOT NULL
);

CREATE TABLE cc_playout_history_template_field (
	id integer DEFAULT nextval('cc_playout_history_template_field_id_seq'::regclass) NOT NULL,
	template_id integer NOT NULL,
	name character varying(128) NOT NULL,
	label character varying(128) NOT NULL,
	type character varying(128) NOT NULL,
	is_file_md boolean DEFAULT false NOT NULL,
	"position" integer NOT NULL
);

ALTER TABLE cc_playout_history
	ADD CONSTRAINT cc_playout_history_pkey PRIMARY KEY (id);

ALTER TABLE cc_playout_history_metadata
	ADD CONSTRAINT cc_playout_history_metadata_pkey PRIMARY KEY (id);

ALTER TABLE cc_playout_history_template
	ADD CONSTRAINT cc_playout_history_template_pkey PRIMARY KEY (id);

ALTER TABLE cc_playout_history_template_field
	ADD CONSTRAINT cc_playout_history_template_field_pkey PRIMARY KEY (id);

ALTER TABLE cc_playout_history
	ADD CONSTRAINT cc_his_item_inst_fkey FOREIGN KEY (instance_id) REFERENCES cc_show_instances(id) ON DELETE SET NULL;

ALTER TABLE cc_playout_history
	ADD CONSTRAINT cc_playout_history_file_tag_fkey FOREIGN KEY (file_id) REFERENCES cc_files(id) ON DELETE CASCADE;

ALTER TABLE cc_playout_history_metadata
	ADD CONSTRAINT cc_playout_history_metadata_entry_fkey FOREIGN KEY (history_id) REFERENCES cc_playout_history(id) ON DELETE CASCADE;

ALTER TABLE cc_playout_history_template_field
	ADD CONSTRAINT cc_playout_history_template_template_fkey FOREIGN KEY (template_id) REFERENCES cc_playout_history_template(id) ON DELETE CASCADE;
