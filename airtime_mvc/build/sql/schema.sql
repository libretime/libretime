
-----------------------------------------------------------------------
-- cc_music_dirs
-----------------------------------------------------------------------

DROP TABLE IF EXISTS "cc_music_dirs" CASCADE;

CREATE TABLE "cc_music_dirs"
(
    "id" serial NOT NULL,
    "directory" TEXT,
    "type" VARCHAR(255),
    "exists" BOOLEAN DEFAULT 't',
    "watched" BOOLEAN DEFAULT 't',
    PRIMARY KEY ("id"),
    CONSTRAINT "cc_music_dir_unique" UNIQUE ("directory")
);

-----------------------------------------------------------------------
-- cc_files
-----------------------------------------------------------------------

DROP TABLE IF EXISTS "cc_files" CASCADE;

CREATE TABLE "cc_files"
(
    "id" serial NOT NULL,
    "name" VARCHAR(255) DEFAULT '' NOT NULL,
    "mime" VARCHAR(255) DEFAULT '' NOT NULL,
    "ftype" VARCHAR(128) DEFAULT '' NOT NULL,
    "directory" INTEGER,
    "filepath" TEXT DEFAULT '',
    "import_status" INTEGER DEFAULT 1 NOT NULL,
    "currentlyaccessing" INTEGER DEFAULT 0 NOT NULL,
    "editedby" INTEGER,
    "mtime" TIMESTAMP(6),
    "utime" TIMESTAMP(6),
    "lptime" TIMESTAMP(6),
    "md5" CHAR(32),
    "track_title" VARCHAR(512),
    "artist_name" VARCHAR(512),
    "bit_rate" INTEGER,
    "sample_rate" INTEGER,
    "format" VARCHAR(128),
    "length" interval DEFAULT '00:00:00',
    "album_title" VARCHAR(512),
    "genre" VARCHAR(64),
    "comments" TEXT,
    "year" VARCHAR(16),
    "track_number" INTEGER,
    "channels" INTEGER,
    "url" VARCHAR(1024),
    "bpm" INTEGER,
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
    "file_exists" BOOLEAN DEFAULT 't',
    "soundcloud_id" INTEGER,
    "soundcloud_error_code" INTEGER,
    "soundcloud_error_msg" VARCHAR(512),
    "soundcloud_link_to_file" VARCHAR(4096),
    "soundcloud_upload_time" TIMESTAMP(6),
    "replay_gain" NUMERIC,
    "owner_id" INTEGER,
    "cuein" interval DEFAULT '00:00:00',
    "cueout" interval DEFAULT '00:00:00',
    "silan_check" BOOLEAN DEFAULT 'f',
    "hidden" BOOLEAN DEFAULT 'f',
    "is_scheduled" BOOLEAN DEFAULT 'f',
    "is_playlist" BOOLEAN DEFAULT 'f',
    "filesize" INTEGER DEFAULT 0 NOT NULL,
    "description" VARCHAR(512),
    "artwork" VARCHAR(512),
    "track_type" VARCHAR(16),
    PRIMARY KEY ("id")
);

CREATE INDEX "cc_files_md5_idx" ON "cc_files" ("md5");

CREATE INDEX "cc_files_name_idx" ON "cc_files" ("name");

-----------------------------------------------------------------------
-- cc_track_types
-----------------------------------------------------------------------

DROP TABLE IF EXISTS "cc_track_types" CASCADE;

CREATE TABLE "cc_track_types"
(
    "id" serial NOT NULL,
    "code" VARCHAR(16) DEFAULT '' NOT NULL,
    "type_name" VARCHAR(64) DEFAULT '' NOT NULL,
    "description" VARCHAR(255) DEFAULT '' NOT NULL,
    "visibility" boolean DEFAULT true NOT NULL,
    PRIMARY KEY ("id"),
    CONSTRAINT "cc_track_types_id_idx" UNIQUE ("id"),
    CONSTRAINT "cc_track_types_code_idx" UNIQUE ("code")
);

-----------------------------------------------------------------------
-- cloud_file
-----------------------------------------------------------------------

DROP TABLE IF EXISTS "cloud_file" CASCADE;

CREATE TABLE "cloud_file"
(
    "id" serial NOT NULL,
    "storage_backend" VARCHAR(512) NOT NULL,
    "resource_id" TEXT NOT NULL,
    "cc_file_id" INTEGER,
    PRIMARY KEY ("id")
);

-----------------------------------------------------------------------
-- cc_perms
-----------------------------------------------------------------------

DROP TABLE IF EXISTS "cc_perms" CASCADE;

CREATE TABLE "cc_perms"
(
    "permid" INTEGER NOT NULL,
    "subj" INTEGER,
    "action" VARCHAR(20),
    "obj" INTEGER,
    "type" CHAR(1),
    PRIMARY KEY ("permid"),
    CONSTRAINT "cc_perms_all_idx" UNIQUE ("subj","action","obj"),
    CONSTRAINT "cc_perms_permid_idx" UNIQUE ("permid")
);

CREATE INDEX "cc_perms_subj_obj_idx" ON "cc_perms" ("subj","obj");

-----------------------------------------------------------------------
-- cc_show
-----------------------------------------------------------------------

DROP TABLE IF EXISTS "cc_show" CASCADE;

CREATE TABLE "cc_show"
(
    "id" serial NOT NULL,
    "name" VARCHAR(255) DEFAULT '' NOT NULL,
    "url" VARCHAR(255) DEFAULT '',
    "genre" VARCHAR(255) DEFAULT '',
    "description" VARCHAR(8192),
    "color" VARCHAR(6),
    "background_color" VARCHAR(6),
    "live_stream_using_airtime_auth" BOOLEAN DEFAULT 'f',
    "live_stream_using_custom_auth" BOOLEAN DEFAULT 'f',
    "live_stream_user" VARCHAR(255),
    "live_stream_pass" VARCHAR(255),
    "linked" BOOLEAN DEFAULT 'f' NOT NULL,
    "is_linkable" BOOLEAN DEFAULT 't' NOT NULL,
    "image_path" VARCHAR(255) DEFAULT '',
    "has_autoplaylist" BOOLEAN DEFAULT 'f' NOT NULL,
    "autoplaylist_id" INTEGER,
    "autoplaylist_repeat" BOOLEAN DEFAULT 'f' NOT NULL,
    PRIMARY KEY ("id")
);

-----------------------------------------------------------------------
-- cc_show_instances
-----------------------------------------------------------------------

DROP TABLE IF EXISTS "cc_show_instances" CASCADE;

CREATE TABLE "cc_show_instances"
(
    "id" serial NOT NULL,
    "description" VARCHAR(8192) DEFAULT '',
    "starts" TIMESTAMP NOT NULL,
    "ends" TIMESTAMP NOT NULL,
    "show_id" INTEGER NOT NULL,
    "record" INT2 DEFAULT 0,
    "rebroadcast" INT2 DEFAULT 0,
    "instance_id" INTEGER,
    "file_id" INTEGER,
    "time_filled" interval DEFAULT '00:00:00',
    "created" TIMESTAMP NOT NULL,
    "last_scheduled" TIMESTAMP,
    "modified_instance" BOOLEAN DEFAULT 'f' NOT NULL,
    "autoplaylist_built" BOOLEAN DEFAULT 'f' NOT NULL,
    PRIMARY KEY ("id")
);

-----------------------------------------------------------------------
-- cc_show_days
-----------------------------------------------------------------------

DROP TABLE IF EXISTS "cc_show_days" CASCADE;

CREATE TABLE "cc_show_days"
(
    "id" serial NOT NULL,
    "first_show" DATE NOT NULL,
    "last_show" DATE,
    "start_time" TIME NOT NULL,
    "timezone" VARCHAR NOT NULL,
    "duration" VARCHAR NOT NULL,
    "day" INT2,
    "repeat_type" INT2 NOT NULL,
    "next_pop_date" DATE,
    "show_id" INTEGER NOT NULL,
    "record" INT2 DEFAULT 0,
    PRIMARY KEY ("id")
);

-----------------------------------------------------------------------
-- cc_show_rebroadcast
-----------------------------------------------------------------------

DROP TABLE IF EXISTS "cc_show_rebroadcast" CASCADE;

CREATE TABLE "cc_show_rebroadcast"
(
    "id" serial NOT NULL,
    "day_offset" VARCHAR NOT NULL,
    "start_time" TIME NOT NULL,
    "show_id" INTEGER NOT NULL,
    PRIMARY KEY ("id")
);

-----------------------------------------------------------------------
-- cc_show_hosts
-----------------------------------------------------------------------

DROP TABLE IF EXISTS "cc_show_hosts" CASCADE;

CREATE TABLE "cc_show_hosts"
(
    "id" serial NOT NULL,
    "show_id" INTEGER NOT NULL,
    "subjs_id" INTEGER NOT NULL,
    PRIMARY KEY ("id")
);

-----------------------------------------------------------------------
-- cc_playlist
-----------------------------------------------------------------------

DROP TABLE IF EXISTS "cc_playlist" CASCADE;

CREATE TABLE "cc_playlist"
(
    "id" serial NOT NULL,
    "name" VARCHAR(255) DEFAULT '' NOT NULL,
    "mtime" TIMESTAMP(6),
    "utime" TIMESTAMP(6),
    "creator_id" INTEGER,
    "description" VARCHAR(512),
    "length" interval DEFAULT '00:00:00',
    PRIMARY KEY ("id")
);

-----------------------------------------------------------------------
-- cc_playlistcontents
-----------------------------------------------------------------------

DROP TABLE IF EXISTS "cc_playlistcontents" CASCADE;

CREATE TABLE "cc_playlistcontents"
(
    "id" serial NOT NULL,
    "playlist_id" INTEGER,
    "file_id" INTEGER,
    "block_id" INTEGER,
    "stream_id" INTEGER,
    "type" INT2 DEFAULT 0 NOT NULL,
    "position" INTEGER,
    "trackoffset" FLOAT DEFAULT 0 NOT NULL,
    "cliplength" interval DEFAULT '00:00:00',
    "cuein" interval DEFAULT '00:00:00',
    "cueout" interval DEFAULT '00:00:00',
    "fadein" TIME DEFAULT '00:00:00',
    "fadeout" TIME DEFAULT '00:00:00',
    PRIMARY KEY ("id")
);

-----------------------------------------------------------------------
-- cc_block
-----------------------------------------------------------------------

DROP TABLE IF EXISTS "cc_block" CASCADE;

CREATE TABLE "cc_block"
(
    "id" serial NOT NULL,
    "name" VARCHAR(255) DEFAULT '' NOT NULL,
    "mtime" TIMESTAMP(6),
    "utime" TIMESTAMP(6),
    "creator_id" INTEGER,
    "description" VARCHAR(512),
    "length" interval DEFAULT '00:00:00',
    "type" VARCHAR(7) DEFAULT 'dynamic',
    PRIMARY KEY ("id")
);

-----------------------------------------------------------------------
-- cc_blockcontents
-----------------------------------------------------------------------

DROP TABLE IF EXISTS "cc_blockcontents" CASCADE;

CREATE TABLE "cc_blockcontents"
(
    "id" serial NOT NULL,
    "block_id" INTEGER,
    "file_id" INTEGER,
    "position" INTEGER,
    "trackoffset" FLOAT DEFAULT 0 NOT NULL,
    "cliplength" interval DEFAULT '00:00:00',
    "cuein" interval DEFAULT '00:00:00',
    "cueout" interval DEFAULT '00:00:00',
    "fadein" TIME DEFAULT '00:00:00',
    "fadeout" TIME DEFAULT '00:00:00',
    PRIMARY KEY ("id")
);

-----------------------------------------------------------------------
-- cc_blockcriteria
-----------------------------------------------------------------------

DROP TABLE IF EXISTS "cc_blockcriteria" CASCADE;

CREATE TABLE "cc_blockcriteria"
(
    "id" serial NOT NULL,
    "criteria" VARCHAR(32) NOT NULL,
    "modifier" VARCHAR(16) NOT NULL,
    "value" VARCHAR(512) NOT NULL,
    "extra" VARCHAR(512),
    "criteriagroup" INTEGER,
    "block_id" INTEGER NOT NULL,
    PRIMARY KEY ("id")
);

-----------------------------------------------------------------------
-- cc_pref
-----------------------------------------------------------------------

DROP TABLE IF EXISTS "cc_pref" CASCADE;

CREATE TABLE "cc_pref"
(
    "id" serial NOT NULL,
    "subjid" INTEGER,
    "keystr" VARCHAR(255),
    "valstr" TEXT,
    PRIMARY KEY ("id"),
    CONSTRAINT "cc_pref_id_idx" UNIQUE ("id"),
    CONSTRAINT "cc_pref_subj_key_idx" UNIQUE ("subjid","keystr")
);

CREATE INDEX "cc_pref_subjid_idx" ON "cc_pref" ("subjid");

-----------------------------------------------------------------------
-- cc_schedule
-----------------------------------------------------------------------

DROP TABLE IF EXISTS "cc_schedule" CASCADE;

CREATE TABLE "cc_schedule"
(
    "id" serial NOT NULL,
    "starts" TIMESTAMP NOT NULL,
    "ends" TIMESTAMP NOT NULL,
    "file_id" INTEGER,
    "stream_id" INTEGER,
    "clip_length" interval DEFAULT '00:00:00',
    "fade_in" TIME DEFAULT '00:00:00',
    "fade_out" TIME DEFAULT '00:00:00',
    "cue_in" interval NOT NULL,
    "cue_out" interval NOT NULL,
    "media_item_played" BOOLEAN DEFAULT 'f',
    "instance_id" INTEGER NOT NULL,
    "playout_status" INT2 DEFAULT 1 NOT NULL,
    "broadcasted" INT2 DEFAULT 0 NOT NULL,
    "position" INTEGER DEFAULT 0 NOT NULL,
    PRIMARY KEY ("id")
);

CREATE INDEX "cc_schedule_instance_id_idx" ON "cc_schedule" ("instance_id");

-----------------------------------------------------------------------
-- cc_sess
-----------------------------------------------------------------------

DROP TABLE IF EXISTS "cc_sess" CASCADE;

CREATE TABLE "cc_sess"
(
    "sessid" CHAR(32) NOT NULL,
    "userid" INTEGER,
    "login" VARCHAR(255),
    "ts" TIMESTAMP,
    PRIMARY KEY ("sessid")
);

CREATE INDEX "cc_sess_login_idx" ON "cc_sess" ("login");

CREATE INDEX "cc_sess_userid_idx" ON "cc_sess" ("userid");

-----------------------------------------------------------------------
-- cc_subjs
-----------------------------------------------------------------------

DROP TABLE IF EXISTS "cc_subjs" CASCADE;

CREATE TABLE "cc_subjs"
(
    "id" serial NOT NULL,
    "login" VARCHAR(255) DEFAULT '' NOT NULL,
    "pass" VARCHAR(255) DEFAULT '' NOT NULL,
    "type" CHAR(1) DEFAULT 'U' NOT NULL,
    "first_name" VARCHAR(255) DEFAULT '' NOT NULL,
    "last_name" VARCHAR(255) DEFAULT '' NOT NULL,
    "lastlogin" TIMESTAMP,
    "lastfail" TIMESTAMP,
    "skype_contact" VARCHAR,
    "jabber_contact" VARCHAR,
    "email" VARCHAR,
    "cell_phone" VARCHAR,
    "login_attempts" INTEGER DEFAULT 0,
    PRIMARY KEY ("id"),
    CONSTRAINT "cc_subjs_id_idx" UNIQUE ("id"),
    CONSTRAINT "cc_subjs_login_idx" UNIQUE ("login")
);

-----------------------------------------------------------------------
-- cc_subjs_token
-----------------------------------------------------------------------

DROP TABLE IF EXISTS "cc_subjs_token" CASCADE;

CREATE TABLE "cc_subjs_token"
(
    "id" serial NOT NULL,
    "user_id" INTEGER NOT NULL,
    "action" VARCHAR(255) NOT NULL,
    "token" VARCHAR(40) NOT NULL,
    "created" TIMESTAMP NOT NULL,
    PRIMARY KEY ("id"),
    CONSTRAINT "cc_subjs_token_idx" UNIQUE ("token")
);

-----------------------------------------------------------------------
-- cc_country
-----------------------------------------------------------------------

DROP TABLE IF EXISTS "cc_country" CASCADE;

CREATE TABLE "cc_country"
(
    "isocode" CHAR(3) NOT NULL,
    "name" VARCHAR(255) NOT NULL,
    PRIMARY KEY ("isocode")
);

-----------------------------------------------------------------------
-- cc_stream_setting
-----------------------------------------------------------------------

DROP TABLE IF EXISTS "cc_stream_setting" CASCADE;

CREATE TABLE "cc_stream_setting"
(
    "keyname" VARCHAR(64) NOT NULL,
    "value" VARCHAR(255),
    "type" VARCHAR(16) NOT NULL,
    PRIMARY KEY ("keyname")
);

-----------------------------------------------------------------------
-- cc_login_attempts
-----------------------------------------------------------------------

DROP TABLE IF EXISTS "cc_login_attempts" CASCADE;

CREATE TABLE "cc_login_attempts"
(
    "ip" VARCHAR(32) NOT NULL,
    "attempts" INTEGER DEFAULT 0,
    PRIMARY KEY ("ip")
);

-----------------------------------------------------------------------
-- cc_service_register
-----------------------------------------------------------------------

DROP TABLE IF EXISTS "cc_service_register" CASCADE;

CREATE TABLE "cc_service_register"
(
    "name" VARCHAR(32) NOT NULL,
    "ip" VARCHAR(45) NOT NULL,
    PRIMARY KEY ("name")
);

-----------------------------------------------------------------------
-- cc_live_log
-----------------------------------------------------------------------

DROP TABLE IF EXISTS "cc_live_log" CASCADE;

CREATE TABLE "cc_live_log"
(
    "id" serial NOT NULL,
    "state" VARCHAR(32) NOT NULL,
    "start_time" TIMESTAMP NOT NULL,
    "end_time" TIMESTAMP,
    PRIMARY KEY ("id")
);

-----------------------------------------------------------------------
-- cc_webstream
-----------------------------------------------------------------------

DROP TABLE IF EXISTS "cc_webstream" CASCADE;

CREATE TABLE "cc_webstream"
(
    "id" serial NOT NULL,
    "name" VARCHAR(255) NOT NULL,
    "description" VARCHAR(255) NOT NULL,
    "url" VARCHAR(512) NOT NULL,
    "length" interval DEFAULT '00:00:00' NOT NULL,
    "creator_id" INTEGER NOT NULL,
    "mtime" TIMESTAMP(6) NOT NULL,
    "utime" TIMESTAMP(6) NOT NULL,
    "lptime" TIMESTAMP(6),
    "mime" VARCHAR,
    PRIMARY KEY ("id")
);

-----------------------------------------------------------------------
-- cc_webstream_metadata
-----------------------------------------------------------------------

DROP TABLE IF EXISTS "cc_webstream_metadata" CASCADE;

CREATE TABLE "cc_webstream_metadata"
(
    "id" serial NOT NULL,
    "instance_id" INTEGER NOT NULL,
    "start_time" TIMESTAMP NOT NULL,
    "liquidsoap_data" VARCHAR(1024) NOT NULL,
    PRIMARY KEY ("id")
);

-----------------------------------------------------------------------
-- cc_mount_name
-----------------------------------------------------------------------

DROP TABLE IF EXISTS "cc_mount_name" CASCADE;

CREATE TABLE "cc_mount_name"
(
    "id" serial NOT NULL,
    "mount_name" VARCHAR NOT NULL,
    PRIMARY KEY ("id")
);

-----------------------------------------------------------------------
-- cc_timestamp
-----------------------------------------------------------------------

DROP TABLE IF EXISTS "cc_timestamp" CASCADE;

CREATE TABLE "cc_timestamp"
(
    "id" serial NOT NULL,
    "timestamp" TIMESTAMP NOT NULL,
    PRIMARY KEY ("id")
);

-----------------------------------------------------------------------
-- cc_listener_count
-----------------------------------------------------------------------

DROP TABLE IF EXISTS "cc_listener_count" CASCADE;

CREATE TABLE "cc_listener_count"
(
    "id" serial NOT NULL,
    "timestamp_id" INTEGER NOT NULL,
    "mount_name_id" INTEGER NOT NULL,
    "listener_count" INTEGER NOT NULL,
    PRIMARY KEY ("id")
);

-----------------------------------------------------------------------
-- cc_playout_history
-----------------------------------------------------------------------

DROP TABLE IF EXISTS "cc_playout_history" CASCADE;

CREATE TABLE "cc_playout_history"
(
    "id" serial NOT NULL,
    "file_id" INTEGER,
    "starts" TIMESTAMP NOT NULL,
    "ends" TIMESTAMP,
    "instance_id" INTEGER,
    PRIMARY KEY ("id")
);

-----------------------------------------------------------------------
-- cc_playout_history_metadata
-----------------------------------------------------------------------

DROP TABLE IF EXISTS "cc_playout_history_metadata" CASCADE;

CREATE TABLE "cc_playout_history_metadata"
(
    "id" serial NOT NULL,
    "history_id" INTEGER NOT NULL,
    "key" VARCHAR(128) NOT NULL,
    "value" VARCHAR(128) NOT NULL,
    PRIMARY KEY ("id")
);

-----------------------------------------------------------------------
-- cc_playout_history_template
-----------------------------------------------------------------------

DROP TABLE IF EXISTS "cc_playout_history_template" CASCADE;

CREATE TABLE "cc_playout_history_template"
(
    "id" serial NOT NULL,
    "name" VARCHAR(128) NOT NULL,
    "type" VARCHAR(35) NOT NULL,
    PRIMARY KEY ("id")
);

-----------------------------------------------------------------------
-- cc_playout_history_template_field
-----------------------------------------------------------------------

DROP TABLE IF EXISTS "cc_playout_history_template_field" CASCADE;

CREATE TABLE "cc_playout_history_template_field"
(
    "id" serial NOT NULL,
    "template_id" INTEGER NOT NULL,
    "name" VARCHAR(128) NOT NULL,
    "label" VARCHAR(128) NOT NULL,
    "type" VARCHAR(128) NOT NULL,
    "is_file_md" BOOLEAN DEFAULT 'f' NOT NULL,
    "position" INTEGER NOT NULL,
    PRIMARY KEY ("id")
);

-----------------------------------------------------------------------
-- third_party_track_references
-----------------------------------------------------------------------

DROP TABLE IF EXISTS "third_party_track_references" CASCADE;

CREATE TABLE "third_party_track_references"
(
    "id" serial NOT NULL,
    "service" VARCHAR(256) NOT NULL,
    "foreign_id" VARCHAR(256),
    "file_id" INTEGER DEFAULT 0 NOT NULL,
    "upload_time" TIMESTAMP,
    "status" VARCHAR(256),
    PRIMARY KEY ("id"),
    CONSTRAINT "foreign_id_unique" UNIQUE ("foreign_id")
);

-----------------------------------------------------------------------
-- celery_tasks
-----------------------------------------------------------------------

DROP TABLE IF EXISTS "celery_tasks" CASCADE;

CREATE TABLE "celery_tasks"
(
    "id" serial NOT NULL,
    "task_id" VARCHAR(256) NOT NULL,
    "track_reference" INTEGER NOT NULL,
    "name" VARCHAR(256),
    "dispatch_time" TIMESTAMP,
    "status" VARCHAR(256) NOT NULL,
    PRIMARY KEY ("id"),
    CONSTRAINT "id_unique" UNIQUE ("id")
);

-----------------------------------------------------------------------
-- podcast
-----------------------------------------------------------------------

DROP TABLE IF EXISTS "podcast" CASCADE;

CREATE TABLE "podcast"
(
    "id" serial NOT NULL,
    "url" VARCHAR(4096) NOT NULL,
    "title" VARCHAR(4096) NOT NULL,
    "creator" VARCHAR(4096),
    "description" VARCHAR(4096),
    "language" VARCHAR(4096),
    "copyright" VARCHAR(4096),
    "link" VARCHAR(4096),
    "itunes_author" VARCHAR(4096),
    "itunes_keywords" VARCHAR(4096),
    "itunes_summary" VARCHAR(4096),
    "itunes_subtitle" VARCHAR(4096),
    "itunes_category" VARCHAR(4096),
    "itunes_explicit" VARCHAR(4096),
    "owner" INTEGER,
    PRIMARY KEY ("id")
);

-----------------------------------------------------------------------
-- station_podcast
-----------------------------------------------------------------------

DROP TABLE IF EXISTS "station_podcast" CASCADE;

CREATE TABLE "station_podcast"
(
    "id" serial NOT NULL,
    "podcast_id" INTEGER NOT NULL,
    PRIMARY KEY ("id")
);

-----------------------------------------------------------------------
-- imported_podcast
-----------------------------------------------------------------------

DROP TABLE IF EXISTS "imported_podcast" CASCADE;

CREATE TABLE "imported_podcast"
(
    "id" serial NOT NULL,
    "auto_ingest" BOOLEAN DEFAULT 'f' NOT NULL,
    "auto_ingest_timestamp" TIMESTAMP,
    "album_override" BOOLEAN DEFAULT 'f' NOT NULL,
    "podcast_id" INTEGER NOT NULL,
    PRIMARY KEY ("id")
);

-----------------------------------------------------------------------
-- podcast_episodes
-----------------------------------------------------------------------

DROP TABLE IF EXISTS "podcast_episodes" CASCADE;

CREATE TABLE "podcast_episodes"
(
    "id" serial NOT NULL,
    "file_id" INTEGER,
    "podcast_id" INTEGER NOT NULL,
    "publication_date" TIMESTAMP NOT NULL,
    "download_url" VARCHAR(4096) NOT NULL,
    "episode_guid" VARCHAR(4096) NOT NULL,
    "episode_title" VARCHAR(4096) NOT NULL,
    "episode_description" TEXT NOT NULL,
    PRIMARY KEY ("id")
);

ALTER TABLE "cc_files" ADD CONSTRAINT "cc_files_owner_fkey"
    FOREIGN KEY ("owner_id")
    REFERENCES "cc_subjs" ("id");

ALTER TABLE "cc_files" ADD CONSTRAINT "cc_files_editedby_fkey"
    FOREIGN KEY ("editedby")
    REFERENCES "cc_subjs" ("id");

ALTER TABLE "cc_files" ADD CONSTRAINT "cc_music_dirs_folder_fkey"
    FOREIGN KEY ("directory")
    REFERENCES "cc_music_dirs" ("id");

ALTER TABLE "cloud_file" ADD CONSTRAINT "cloud_file_FK_1"
    FOREIGN KEY ("cc_file_id")
    REFERENCES "cc_files" ("id")
    ON DELETE CASCADE;

ALTER TABLE "cc_perms" ADD CONSTRAINT "cc_perms_subj_fkey"
    FOREIGN KEY ("subj")
    REFERENCES "cc_subjs" ("id")
    ON DELETE CASCADE;

ALTER TABLE "cc_show" ADD CONSTRAINT "cc_playlist_autoplaylist_fkey"
    FOREIGN KEY ("autoplaylist_id")
    REFERENCES "cc_playlist" ("id")
    ON DELETE SET NULL;

ALTER TABLE "cc_show_instances" ADD CONSTRAINT "cc_show_fkey"
    FOREIGN KEY ("show_id")
    REFERENCES "cc_show" ("id")
    ON DELETE CASCADE;

ALTER TABLE "cc_show_instances" ADD CONSTRAINT "cc_original_show_instance_fkey"
    FOREIGN KEY ("instance_id")
    REFERENCES "cc_show_instances" ("id")
    ON DELETE CASCADE;

ALTER TABLE "cc_show_instances" ADD CONSTRAINT "cc_recorded_file_fkey"
    FOREIGN KEY ("file_id")
    REFERENCES "cc_files" ("id")
    ON DELETE CASCADE;

ALTER TABLE "cc_show_days" ADD CONSTRAINT "cc_show_fkey"
    FOREIGN KEY ("show_id")
    REFERENCES "cc_show" ("id")
    ON DELETE CASCADE;

ALTER TABLE "cc_show_rebroadcast" ADD CONSTRAINT "cc_show_fkey"
    FOREIGN KEY ("show_id")
    REFERENCES "cc_show" ("id")
    ON DELETE CASCADE;

ALTER TABLE "cc_show_hosts" ADD CONSTRAINT "cc_perm_show_fkey"
    FOREIGN KEY ("show_id")
    REFERENCES "cc_show" ("id")
    ON DELETE CASCADE;

ALTER TABLE "cc_show_hosts" ADD CONSTRAINT "cc_perm_host_fkey"
    FOREIGN KEY ("subjs_id")
    REFERENCES "cc_subjs" ("id")
    ON DELETE CASCADE;

ALTER TABLE "cc_playlist" ADD CONSTRAINT "cc_playlist_createdby_fkey"
    FOREIGN KEY ("creator_id")
    REFERENCES "cc_subjs" ("id")
    ON DELETE CASCADE;

ALTER TABLE "cc_playlistcontents" ADD CONSTRAINT "cc_playlistcontents_file_id_fkey"
    FOREIGN KEY ("file_id")
    REFERENCES "cc_files" ("id")
    ON DELETE CASCADE;

ALTER TABLE "cc_playlistcontents" ADD CONSTRAINT "cc_playlistcontents_block_id_fkey"
    FOREIGN KEY ("block_id")
    REFERENCES "cc_block" ("id")
    ON DELETE CASCADE;

ALTER TABLE "cc_playlistcontents" ADD CONSTRAINT "cc_playlistcontents_playlist_id_fkey"
    FOREIGN KEY ("playlist_id")
    REFERENCES "cc_playlist" ("id")
    ON DELETE CASCADE;

ALTER TABLE "cc_block" ADD CONSTRAINT "cc_block_createdby_fkey"
    FOREIGN KEY ("creator_id")
    REFERENCES "cc_subjs" ("id")
    ON DELETE CASCADE;

ALTER TABLE "cc_blockcontents" ADD CONSTRAINT "cc_blockcontents_file_id_fkey"
    FOREIGN KEY ("file_id")
    REFERENCES "cc_files" ("id")
    ON DELETE CASCADE;

ALTER TABLE "cc_blockcontents" ADD CONSTRAINT "cc_blockcontents_block_id_fkey"
    FOREIGN KEY ("block_id")
    REFERENCES "cc_block" ("id")
    ON DELETE CASCADE;

ALTER TABLE "cc_blockcriteria" ADD CONSTRAINT "cc_blockcontents_block_id_fkey"
    FOREIGN KEY ("block_id")
    REFERENCES "cc_block" ("id")
    ON DELETE CASCADE;

ALTER TABLE "cc_pref" ADD CONSTRAINT "cc_pref_subjid_fkey"
    FOREIGN KEY ("subjid")
    REFERENCES "cc_subjs" ("id")
    ON DELETE CASCADE;

ALTER TABLE "cc_schedule" ADD CONSTRAINT "cc_show_inst_fkey"
    FOREIGN KEY ("instance_id")
    REFERENCES "cc_show_instances" ("id")
    ON DELETE CASCADE;

ALTER TABLE "cc_schedule" ADD CONSTRAINT "cc_show_file_fkey"
    FOREIGN KEY ("file_id")
    REFERENCES "cc_files" ("id")
    ON DELETE CASCADE;

ALTER TABLE "cc_schedule" ADD CONSTRAINT "cc_show_stream_fkey"
    FOREIGN KEY ("stream_id")
    REFERENCES "cc_webstream" ("id")
    ON DELETE CASCADE;

ALTER TABLE "cc_sess" ADD CONSTRAINT "cc_sess_userid_fkey"
    FOREIGN KEY ("userid")
    REFERENCES "cc_subjs" ("id")
    ON DELETE CASCADE;

ALTER TABLE "cc_subjs_token" ADD CONSTRAINT "cc_subjs_token_userid_fkey"
    FOREIGN KEY ("user_id")
    REFERENCES "cc_subjs" ("id")
    ON DELETE CASCADE;

ALTER TABLE "cc_webstream_metadata" ADD CONSTRAINT "cc_schedule_inst_fkey"
    FOREIGN KEY ("instance_id")
    REFERENCES "cc_schedule" ("id")
    ON DELETE CASCADE;

ALTER TABLE "cc_listener_count" ADD CONSTRAINT "cc_timestamp_inst_fkey"
    FOREIGN KEY ("timestamp_id")
    REFERENCES "cc_timestamp" ("id")
    ON DELETE CASCADE;

ALTER TABLE "cc_listener_count" ADD CONSTRAINT "cc_mount_name_inst_fkey"
    FOREIGN KEY ("mount_name_id")
    REFERENCES "cc_mount_name" ("id")
    ON DELETE CASCADE;

ALTER TABLE "cc_playout_history" ADD CONSTRAINT "cc_playout_history_file_tag_fkey"
    FOREIGN KEY ("file_id")
    REFERENCES "cc_files" ("id")
    ON DELETE CASCADE;

ALTER TABLE "cc_playout_history" ADD CONSTRAINT "cc_his_item_inst_fkey"
    FOREIGN KEY ("instance_id")
    REFERENCES "cc_show_instances" ("id")
    ON DELETE SET NULL;

ALTER TABLE "cc_playout_history_metadata" ADD CONSTRAINT "cc_playout_history_metadata_entry_fkey"
    FOREIGN KEY ("history_id")
    REFERENCES "cc_playout_history" ("id")
    ON DELETE CASCADE;

ALTER TABLE "cc_playout_history_template_field" ADD CONSTRAINT "cc_playout_history_template_template_fkey"
    FOREIGN KEY ("template_id")
    REFERENCES "cc_playout_history_template" ("id")
    ON DELETE CASCADE;

ALTER TABLE "third_party_track_references" ADD CONSTRAINT "track_reference_fkey"
    FOREIGN KEY ("file_id")
    REFERENCES "cc_files" ("id")
    ON DELETE CASCADE;

ALTER TABLE "celery_tasks" ADD CONSTRAINT "celery_service_fkey"
    FOREIGN KEY ("track_reference")
    REFERENCES "third_party_track_references" ("id")
    ON DELETE CASCADE;

ALTER TABLE "podcast" ADD CONSTRAINT "podcast_owner_fkey"
    FOREIGN KEY ("owner")
    REFERENCES "cc_subjs" ("id")
    ON DELETE CASCADE;

ALTER TABLE "station_podcast" ADD CONSTRAINT "podcast_id_fkey"
    FOREIGN KEY ("podcast_id")
    REFERENCES "podcast" ("id")
    ON DELETE CASCADE;

ALTER TABLE "imported_podcast" ADD CONSTRAINT "podcast_id_fkey"
    FOREIGN KEY ("podcast_id")
    REFERENCES "podcast" ("id")
    ON DELETE CASCADE;

ALTER TABLE "podcast_episodes" ADD CONSTRAINT "podcast_episodes_cc_files_fkey"
    FOREIGN KEY ("file_id")
    REFERENCES "cc_files" ("id")
    ON DELETE CASCADE;

ALTER TABLE "podcast_episodes" ADD CONSTRAINT "podcast_episodes_podcast_id_fkey"
    FOREIGN KEY ("podcast_id")
    REFERENCES "podcast" ("id")
    ON DELETE CASCADE;
