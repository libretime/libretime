
-----------------------------------------------------------------------------
-- cc_access
-----------------------------------------------------------------------------

DROP TABLE "cc_access" CASCADE;


CREATE TABLE "cc_access"
(
	"id" serial  NOT NULL,
	"gunid" CHAR(32),
	"token" INT8,
	"chsum" CHAR(32) default '' NOT NULL,
	"ext" VARCHAR(128) default '' NOT NULL,
	"type" VARCHAR(20) default '' NOT NULL,
	"parent" INT8,
	"owner" INTEGER,
	"ts" TIMESTAMP,
	PRIMARY KEY ("id")
);

COMMENT ON TABLE "cc_access" IS '';


SET search_path TO public;
CREATE INDEX "cc_access_gunid_idx" ON "cc_access" ("gunid");

CREATE INDEX "cc_access_parent_idx" ON "cc_access" ("parent");

CREATE INDEX "cc_access_token_idx" ON "cc_access" ("token");

-----------------------------------------------------------------------------
-- cc_backup
-----------------------------------------------------------------------------

DROP TABLE "cc_backup" CASCADE;


CREATE TABLE "cc_backup"
(
	"token" VARCHAR(64)  NOT NULL,
	"sessionid" VARCHAR(64)  NOT NULL,
	"status" VARCHAR(32)  NOT NULL,
	"fromtime" TIMESTAMP  NOT NULL,
	"totime" TIMESTAMP  NOT NULL,
	PRIMARY KEY ("token")
);

COMMENT ON TABLE "cc_backup" IS '';


SET search_path TO public;
-----------------------------------------------------------------------------
-- cc_files
-----------------------------------------------------------------------------

DROP TABLE "cc_files" CASCADE;


CREATE TABLE "cc_files"
(
	"id" serial  NOT NULL,
	"gunid" CHAR(32)  NOT NULL,
	"name" VARCHAR(255) default '' NOT NULL,
	"mime" VARCHAR(255) default '' NOT NULL,
	"ftype" VARCHAR(128) default '' NOT NULL,
	"filepath" TEXT default '',
	"state" VARCHAR(128) default 'empty' NOT NULL,
	"currentlyaccessing" INTEGER default 0 NOT NULL,
	"editedby" INTEGER,
	"mtime" TIMESTAMP(6),
	"md5" CHAR(32),
	"track_title" VARCHAR(512),
	"artist_name" VARCHAR(512),
	"bit_rate" VARCHAR(32),
	"sample_rate" VARCHAR(32),
	"format" VARCHAR(128),
	"length" TIME,
	"album_title" VARCHAR(512),
	"genre" VARCHAR(64),
	"comments" TEXT,
	"year" VARCHAR(16),
	"track_number" INTEGER,
	"channels" INTEGER,
	"url" VARCHAR(1024),
	"bpm" VARCHAR(8),
	"rating" VARCHAR(8),
	"encoded_by" VARCHAR(255),
	"disc_number" VARCHAR(8),
	"mood" VARCHAR(64),
	"label" VARCHAR(512),
	"composer" VARCHAR(512),
	"encoder" VARCHAR(64),
	"checksum" VARCHAR(256),
	"lyrics" TEXT,
	"orchestra" VARCHAR(512),
	"conductor" VARCHAR(512),
	"lyricist" VARCHAR(512),
	"original_lyricist" VARCHAR(512),
	"radio_station_name" VARCHAR(512),
	"info_url" VARCHAR(512),
	"artist_url" VARCHAR(512),
	"audio_source_url" VARCHAR(512),
	"radio_station_url" VARCHAR(512),
	"buy_this_url" VARCHAR(512),
	"isrc_number" VARCHAR(512),
	"catalog_number" VARCHAR(512),
	"original_artist" VARCHAR(512),
	"copyright" VARCHAR(512),
	"report_datetime" VARCHAR(32),
	"report_location" VARCHAR(512),
	"report_organization" VARCHAR(512),
	"subject" VARCHAR(512),
	"contributor" VARCHAR(512),
	"language" VARCHAR(512),
	PRIMARY KEY ("id"),
	CONSTRAINT "cc_files_gunid_idx" UNIQUE ("gunid")
);

COMMENT ON TABLE "cc_files" IS '';


SET search_path TO public;
CREATE INDEX "cc_files_md5_idx" ON "cc_files" ("md5");

CREATE INDEX "cc_files_name_idx" ON "cc_files" ("name");

-----------------------------------------------------------------------------
-- cc_perms
-----------------------------------------------------------------------------

DROP TABLE "cc_perms" CASCADE;


CREATE TABLE "cc_perms"
(
	"permid" INTEGER  NOT NULL,
	"subj" INTEGER,
	"action" VARCHAR(20),
	"obj" INTEGER,
	"type" CHAR(1),
	PRIMARY KEY ("permid"),
	CONSTRAINT "cc_perms_all_idx" UNIQUE ("subj","action","obj"),
	CONSTRAINT "cc_perms_permid_idx" UNIQUE ("permid")
);

COMMENT ON TABLE "cc_perms" IS '';


SET search_path TO public;
CREATE INDEX "cc_perms_subj_obj_idx" ON "cc_perms" ("subj","obj");

-----------------------------------------------------------------------------
-- cc_show
-----------------------------------------------------------------------------

DROP TABLE "cc_show" CASCADE;


CREATE TABLE "cc_show"
(
	"id" serial  NOT NULL,
	"name" VARCHAR(255) default '' NOT NULL,
	"description" VARCHAR(512),
	"color" VARCHAR(6),
	"background_color" VARCHAR(6),
	PRIMARY KEY ("id")
);

COMMENT ON TABLE "cc_show" IS '';


SET search_path TO public;
-----------------------------------------------------------------------------
-- cc_show_instances
-----------------------------------------------------------------------------

DROP TABLE "cc_show_instances" CASCADE;


CREATE TABLE "cc_show_instances"
(
	"id" serial  NOT NULL,
	"starts" TIMESTAMP  NOT NULL,
	"ends" TIMESTAMP  NOT NULL,
	"show_id" INTEGER  NOT NULL,
	"record" INT2 default 0,
	"rebroadcast" INT2 default 0,
	"instance_id" INTEGER,
	"file_id" INTEGER,
	PRIMARY KEY ("id")
);

COMMENT ON TABLE "cc_show_instances" IS '';


SET search_path TO public;
-----------------------------------------------------------------------------
-- cc_show_days
-----------------------------------------------------------------------------

DROP TABLE "cc_show_days" CASCADE;


CREATE TABLE "cc_show_days"
(
	"id" serial  NOT NULL,
	"first_show" DATE  NOT NULL,
	"last_show" DATE,
	"start_time" TIME  NOT NULL,
	"duration" VARCHAR(255)  NOT NULL,
	"day" INT2,
	"repeat_type" INT2  NOT NULL,
	"next_pop_date" DATE,
	"show_id" INTEGER  NOT NULL,
	"record" INT2,
	PRIMARY KEY ("id")
);

COMMENT ON TABLE "cc_show_days" IS '';


SET search_path TO public;
-----------------------------------------------------------------------------
-- cc_show_rebroadcast
-----------------------------------------------------------------------------

DROP TABLE "cc_show_rebroadcast" CASCADE;


CREATE TABLE "cc_show_rebroadcast"
(
	"id" serial  NOT NULL,
	"day_offset" VARCHAR(255)  NOT NULL,
	"start_time" TIME  NOT NULL,
	"show_id" INTEGER  NOT NULL,
	PRIMARY KEY ("id")
);

COMMENT ON TABLE "cc_show_rebroadcast" IS '';


SET search_path TO public;
-----------------------------------------------------------------------------
-- cc_show_hosts
-----------------------------------------------------------------------------

DROP TABLE "cc_show_hosts" CASCADE;


CREATE TABLE "cc_show_hosts"
(
	"id" serial  NOT NULL,
	"show_id" INTEGER  NOT NULL,
	"subjs_id" INTEGER  NOT NULL,
	PRIMARY KEY ("id")
);

COMMENT ON TABLE "cc_show_hosts" IS '';


SET search_path TO public;
-----------------------------------------------------------------------------
-- cc_playlist
-----------------------------------------------------------------------------

DROP TABLE "cc_playlist" CASCADE;


CREATE TABLE "cc_playlist"
(
	"id" serial  NOT NULL,
	"name" VARCHAR(255) default '' NOT NULL,
	"state" VARCHAR(128) default 'empty' NOT NULL,
	"currentlyaccessing" INTEGER default 0 NOT NULL,
	"editedby" INTEGER,
	"mtime" TIMESTAMP(6),
	"creator" VARCHAR(32),
	"description" VARCHAR(512),
	PRIMARY KEY ("id")
);

COMMENT ON TABLE "cc_playlist" IS '';


SET search_path TO public;
-----------------------------------------------------------------------------
-- cc_playlistcontents
-----------------------------------------------------------------------------

DROP TABLE "cc_playlistcontents" CASCADE;


CREATE TABLE "cc_playlistcontents"
(
	"id" serial  NOT NULL,
	"playlist_id" INTEGER,
	"file_id" INTEGER,
	"position" INTEGER,
	"cliplength" TIME default '00:00:00',
	"cuein" TIME default '00:00:00',
	"cueout" TIME default '00:00:00',
	"fadein" TIME default '00:00:00',
	"fadeout" TIME default '00:00:00',
	PRIMARY KEY ("id")
);

COMMENT ON TABLE "cc_playlistcontents" IS '';


SET search_path TO public;
-----------------------------------------------------------------------------
-- cc_pref
-----------------------------------------------------------------------------

DROP TABLE "cc_pref" CASCADE;


CREATE TABLE "cc_pref"
(
	"id" serial  NOT NULL,
	"subjid" INTEGER,
	"keystr" VARCHAR(255),
	"valstr" TEXT,
	PRIMARY KEY ("id"),
	CONSTRAINT "cc_pref_id_idx" UNIQUE ("id"),
	CONSTRAINT "cc_pref_subj_key_idx" UNIQUE ("subjid","keystr")
);

COMMENT ON TABLE "cc_pref" IS '';


SET search_path TO public;
CREATE INDEX "cc_pref_subjid_idx" ON "cc_pref" ("subjid");

-----------------------------------------------------------------------------
-- cc_schedule
-----------------------------------------------------------------------------

DROP TABLE "cc_schedule" CASCADE;


CREATE TABLE "cc_schedule"
(
	"id" serial  NOT NULL,
	"playlist_id" INTEGER  NOT NULL,
	"starts" TIMESTAMP  NOT NULL,
	"ends" TIMESTAMP  NOT NULL,
	"group_id" INTEGER,
	"file_id" INTEGER,
	"clip_length" TIME default '00:00:00',
	"fade_in" TIME default '00:00:00',
	"fade_out" TIME default '00:00:00',
	"cue_in" TIME default '00:00:00',
	"cue_out" TIME default '00:00:00',
	"schedule_group_played" BOOLEAN default 'f',
	"media_item_played" BOOLEAN default 'f',
	"instance_id" INTEGER  NOT NULL,
	PRIMARY KEY ("id")
);

COMMENT ON TABLE "cc_schedule" IS '';


SET search_path TO public;
-----------------------------------------------------------------------------
-- cc_sess
-----------------------------------------------------------------------------

DROP TABLE "cc_sess" CASCADE;


CREATE TABLE "cc_sess"
(
	"sessid" CHAR(32)  NOT NULL,
	"userid" INTEGER,
	"login" VARCHAR(255),
	"ts" TIMESTAMP,
	PRIMARY KEY ("sessid")
);

COMMENT ON TABLE "cc_sess" IS '';


SET search_path TO public;
CREATE INDEX "cc_sess_login_idx" ON "cc_sess" ("login");

CREATE INDEX "cc_sess_userid_idx" ON "cc_sess" ("userid");

-----------------------------------------------------------------------------
-- cc_smemb
-----------------------------------------------------------------------------

DROP TABLE "cc_smemb" CASCADE;


CREATE TABLE "cc_smemb"
(
	"id" INTEGER  NOT NULL,
	"uid" INTEGER default 0 NOT NULL,
	"gid" INTEGER default 0 NOT NULL,
	"level" INTEGER default 0 NOT NULL,
	"mid" INTEGER,
	PRIMARY KEY ("id"),
	CONSTRAINT "cc_smemb_id_idx" UNIQUE ("id")
);

COMMENT ON TABLE "cc_smemb" IS '';


SET search_path TO public;
-----------------------------------------------------------------------------
-- cc_subjs
-----------------------------------------------------------------------------

DROP TABLE "cc_subjs" CASCADE;


CREATE TABLE "cc_subjs"
(
	"id" serial  NOT NULL,
	"login" VARCHAR(255) default '' NOT NULL,
	"pass" VARCHAR(255) default '' NOT NULL,
	"type" CHAR(1) default 'U' NOT NULL,
	"first_name" VARCHAR(255) default '' NOT NULL,
	"last_name" VARCHAR(255) default '' NOT NULL,
	"lastlogin" TIMESTAMP,
	"lastfail" TIMESTAMP,
	"skype_contact" VARCHAR(255),
	"jabber_contact" VARCHAR(255),
	"email" VARCHAR(255),
	PRIMARY KEY ("id"),
	CONSTRAINT "cc_subjs_id_idx" UNIQUE ("id"),
	CONSTRAINT "cc_subjs_login_idx" UNIQUE ("login")
);

COMMENT ON TABLE "cc_subjs" IS '';


SET search_path TO public;
-----------------------------------------------------------------------------
-- cc_trans
-----------------------------------------------------------------------------

DROP TABLE "cc_trans" CASCADE;


CREATE TABLE "cc_trans"
(
	"id" serial  NOT NULL,
	"trtok" CHAR(16)  NOT NULL,
	"direction" VARCHAR(128)  NOT NULL,
	"state" VARCHAR(128)  NOT NULL,
	"trtype" VARCHAR(128)  NOT NULL,
	"lock" CHAR(1) default 'N' NOT NULL,
	"target" VARCHAR(255),
	"rtrtok" CHAR(16),
	"mdtrtok" CHAR(16),
	"gunid" CHAR(32),
	"pdtoken" INT8,
	"url" VARCHAR(255),
	"localfile" VARCHAR(255),
	"fname" VARCHAR(255),
	"title" VARCHAR(255),
	"expectedsum" CHAR(32),
	"realsum" CHAR(32),
	"expectedsize" INTEGER,
	"realsize" INTEGER,
	"uid" INTEGER,
	"errmsg" VARCHAR(255),
	"jobpid" INTEGER,
	"start" TIMESTAMP,
	"ts" TIMESTAMP,
	PRIMARY KEY ("id"),
	CONSTRAINT "cc_trans_id_idx" UNIQUE ("id"),
	CONSTRAINT "cc_trans_token_idx" UNIQUE ("pdtoken"),
	CONSTRAINT "cc_trans_trtok_idx" UNIQUE ("trtok")
);

COMMENT ON TABLE "cc_trans" IS '';


SET search_path TO public;
CREATE INDEX "cc_trans_gunid_idx" ON "cc_trans" ("gunid");

CREATE INDEX "cc_trans_state_idx" ON "cc_trans" ("state");

ALTER TABLE "cc_access" ADD CONSTRAINT "cc_access_owner_fkey" FOREIGN KEY ("owner") REFERENCES "cc_subjs" ("id");

ALTER TABLE "cc_files" ADD CONSTRAINT "cc_files_editedby_fkey" FOREIGN KEY ("editedby") REFERENCES "cc_subjs" ("id");

ALTER TABLE "cc_perms" ADD CONSTRAINT "cc_perms_subj_fkey" FOREIGN KEY ("subj") REFERENCES "cc_subjs" ("id") ON DELETE CASCADE;

ALTER TABLE "cc_show_instances" ADD CONSTRAINT "cc_show_fkey" FOREIGN KEY ("show_id") REFERENCES "cc_show" ("id") ON DELETE CASCADE;

ALTER TABLE "cc_show_instances" ADD CONSTRAINT "cc_original_show_instance_fkey" FOREIGN KEY ("instance_id") REFERENCES "cc_show_instances" ("id") ON DELETE CASCADE;

ALTER TABLE "cc_show_instances" ADD CONSTRAINT "cc_recorded_file_fkey" FOREIGN KEY ("file_id") REFERENCES "cc_files" ("id") ON DELETE CASCADE;

ALTER TABLE "cc_show_days" ADD CONSTRAINT "cc_show_fkey" FOREIGN KEY ("show_id") REFERENCES "cc_show" ("id") ON DELETE CASCADE;

ALTER TABLE "cc_show_rebroadcast" ADD CONSTRAINT "cc_show_fkey" FOREIGN KEY ("show_id") REFERENCES "cc_show" ("id") ON DELETE CASCADE;

ALTER TABLE "cc_show_hosts" ADD CONSTRAINT "cc_perm_show_fkey" FOREIGN KEY ("show_id") REFERENCES "cc_show" ("id") ON DELETE CASCADE;

ALTER TABLE "cc_show_hosts" ADD CONSTRAINT "cc_perm_host_fkey" FOREIGN KEY ("subjs_id") REFERENCES "cc_subjs" ("id") ON DELETE CASCADE;

ALTER TABLE "cc_playlist" ADD CONSTRAINT "cc_playlist_editedby_fkey" FOREIGN KEY ("editedby") REFERENCES "cc_subjs" ("id");

ALTER TABLE "cc_playlistcontents" ADD CONSTRAINT "cc_playlistcontents_file_id_fkey" FOREIGN KEY ("file_id") REFERENCES "cc_files" ("id") ON DELETE CASCADE;

ALTER TABLE "cc_playlistcontents" ADD CONSTRAINT "cc_playlistcontents_playlist_id_fkey" FOREIGN KEY ("playlist_id") REFERENCES "cc_playlist" ("id") ON DELETE CASCADE;

ALTER TABLE "cc_pref" ADD CONSTRAINT "cc_pref_subjid_fkey" FOREIGN KEY ("subjid") REFERENCES "cc_subjs" ("id") ON DELETE CASCADE;

ALTER TABLE "cc_schedule" ADD CONSTRAINT "cc_show_inst_fkey" FOREIGN KEY ("instance_id") REFERENCES "cc_show_instances" ("id") ON DELETE CASCADE;

ALTER TABLE "cc_sess" ADD CONSTRAINT "cc_sess_userid_fkey" FOREIGN KEY ("userid") REFERENCES "cc_subjs" ("id") ON DELETE CASCADE;
