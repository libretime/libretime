-- Can't be set up in Propel's XML schema
ALTER TABLE "cc_pref" ALTER COLUMN "subjid" SET DEFAULT NULL;
CREATE UNIQUE INDEX "cc_pref_key_idx" ON "cc_pref" ("keystr") WHERE "subjid" IS NULL;
ANALYZE "cc_pref";

INSERT INTO cc_live_log ("state", "start_time") VALUES ('S', now() at time zone 'UTC');

INSERT INTO cc_pref ("keystr", "valstr") VALUES ('import_timestamp', '0');
INSERT INTO cc_pref ("keystr", "valstr") VALUES ('timezone', 'UTC');
INSERT INTO cc_pref ("keystr", "valstr") VALUES ('default_stream_mount_point', 'main');
INSERT INTO cc_pref ("keystr", "valstr") VALUES ('off_air_meta', 'LibreTime - offline');
INSERT INTO cc_pref ("keystr", "valstr") VALUES ('enable_replay_gain', 1);
INSERT INTO cc_pref ("keystr", "valstr") VALUES ('locale', 'en_US');
INSERT INTO cc_pref ("keystr", "valstr") VALUES ('max_bitrate', '320');
INSERT INTO cc_pref ("keystr", "valstr") VALUES ('num_of_streams', '3');
INSERT INTO cc_pref ("keystr", "valstr") VALUES ('plan_level', 'disabled');
INSERT INTO cc_pref ("keystr", "valstr") VALUES ('podcast_album_override', 1);
INSERT INTO cc_pref ("keystr", "valstr") VALUES ('podcast_auto_smartblock', 0);
INSERT INTO cc_pref ("keystr", "valstr") VALUES ('scheduled_play_switch', 'on');
INSERT INTO cc_pref ("keystr", "valstr") VALUES ('stream_bitrate', '24, 32, 48, 64, 96, 128, 160, 192, 224, 256, 320');
INSERT INTO cc_pref ("keystr", "valstr") VALUES ('stream_type', 'ogg, mp3, opus, aac');
INSERT INTO cc_pref ("keystr", "valstr") VALUES ('whats_new_dialog_viewed', 1);

INSERT INTO cc_subjs ("login", "type", "pass") VALUES ('admin', 'A', md5('admin'));
INSERT INTO cc_pref ("subjid", "keystr", "valstr") VALUES (1, 'user_locale', 'en_US');

INSERT INTO cc_stream_setting ("keyname", "value", "type") VALUES ('icecast_vorbis_metadata', 'false', 'boolean');
INSERT INTO cc_stream_setting ("keyname", "value", "type") VALUES ('output_sound_device_type', 'ALSA', 'string');
INSERT INTO cc_stream_setting ("keyname", "value", "type") VALUES ('output_sound_device', 'false', 'boolean');

INSERT INTO cc_stream_setting ("keyname", "value", "type") VALUES ('s1_enable', 'true', 'boolean');
INSERT INTO cc_stream_setting ("keyname", "value", "type") VALUES ('s1_output', 'icecast', 'string');
INSERT INTO cc_stream_setting ("keyname", "value", "type") VALUES ('s1_host', '127.0.0.1', 'string');
INSERT INTO cc_stream_setting ("keyname", "value", "type") VALUES ('s1_port', '8000', 'integer');
INSERT INTO cc_stream_setting ("keyname", "value", "type") VALUES ('s1_mount', 'main', 'string');
INSERT INTO cc_stream_setting ("keyname", "value", "type") VALUES ('s1_type', 'ogg', 'string');
INSERT INTO cc_stream_setting ("keyname", "value", "type") VALUES ('s1_bitrate', '128', 'integer');
INSERT INTO cc_stream_setting ("keyname", "value", "type") VALUES ('s1_channels', 'stereo', 'string');
INSERT INTO cc_stream_setting ("keyname", "value", "type") VALUES ('s1_admin_pass', '', 'string');
INSERT INTO cc_stream_setting ("keyname", "value", "type") VALUES ('s1_admin_user', 'admin', 'string');
INSERT INTO cc_stream_setting ("keyname", "value", "type") VALUES ('s1_user', '', 'string');
INSERT INTO cc_stream_setting ("keyname", "value", "type") VALUES ('s1_pass', 'hackme', 'string');
INSERT INTO cc_stream_setting ("keyname", "value", "type") VALUES ('s1_name', 'LibreTime!', 'string');
INSERT INTO cc_stream_setting ("keyname", "value", "type") VALUES ('s1_description', 'LibreTime Radio! Stream #1', 'string');
INSERT INTO cc_stream_setting ("keyname", "value", "type") VALUES ('s1_genre', 'genre', 'string');
INSERT INTO cc_stream_setting ("keyname", "value", "type") VALUES ('s1_url', 'https://libretime.org', 'string');

INSERT INTO cc_stream_setting ("keyname", "value", "type") VALUES ('s2_enable', 'false', 'boolean');
INSERT INTO cc_stream_setting ("keyname", "value", "type") VALUES ('s2_output', 'icecast', 'string');
INSERT INTO cc_stream_setting ("keyname", "value", "type") VALUES ('s2_host', '', 'string');
INSERT INTO cc_stream_setting ("keyname", "value", "type") VALUES ('s2_port', '', 'integer');
INSERT INTO cc_stream_setting ("keyname", "value", "type") VALUES ('s2_mount', '', 'string');
INSERT INTO cc_stream_setting ("keyname", "value", "type") VALUES ('s2_type', '', 'string');
INSERT INTO cc_stream_setting ("keyname", "value", "type") VALUES ('s2_bitrate', '', 'integer');
INSERT INTO cc_stream_setting ("keyname", "value", "type") VALUES ('s2_channels', 'stereo', 'string');
INSERT INTO cc_stream_setting ("keyname", "value", "type") VALUES ('s2_admin_pass', '', 'string');
INSERT INTO cc_stream_setting ("keyname", "value", "type") VALUES ('s2_admin_user', '', 'string');
INSERT INTO cc_stream_setting ("keyname", "value", "type") VALUES ('s2_user', '', 'string');
INSERT INTO cc_stream_setting ("keyname", "value", "type") VALUES ('s2_pass', '', 'string');
INSERT INTO cc_stream_setting ("keyname", "value", "type") VALUES ('s2_name', '', 'string');
INSERT INTO cc_stream_setting ("keyname", "value", "type") VALUES ('s2_description', '', 'string');
INSERT INTO cc_stream_setting ("keyname", "value", "type") VALUES ('s2_genre', '', 'string');
INSERT INTO cc_stream_setting ("keyname", "value", "type") VALUES ('s2_url', '', 'string');

INSERT INTO cc_stream_setting ("keyname", "value", "type") VALUES ('s3_enable', 'false', 'boolean');
INSERT INTO cc_stream_setting ("keyname", "value", "type") VALUES ('s3_output', 'icecast', 'string');
INSERT INTO cc_stream_setting ("keyname", "value", "type") VALUES ('s3_host', '', 'string');
INSERT INTO cc_stream_setting ("keyname", "value", "type") VALUES ('s3_port', '', 'integer');
INSERT INTO cc_stream_setting ("keyname", "value", "type") VALUES ('s3_mount', '', 'string');
INSERT INTO cc_stream_setting ("keyname", "value", "type") VALUES ('s3_type', '', 'string');
INSERT INTO cc_stream_setting ("keyname", "value", "type") VALUES ('s3_bitrate', '', 'integer');
INSERT INTO cc_stream_setting ("keyname", "value", "type") VALUES ('s3_channels', 'stereo', 'string');
INSERT INTO cc_stream_setting ("keyname", "value", "type") VALUES ('s3_admin_pass', '', 'string');
INSERT INTO cc_stream_setting ("keyname", "value", "type") VALUES ('s3_admin_user', '', 'string');
INSERT INTO cc_stream_setting ("keyname", "value", "type") VALUES ('s3_user', '', 'string');
INSERT INTO cc_stream_setting ("keyname", "value", "type") VALUES ('s3_pass', '', 'string');
INSERT INTO cc_stream_setting ("keyname", "value", "type") VALUES ('s3_name', '', 'string');
INSERT INTO cc_stream_setting ("keyname", "value", "type") VALUES ('s3_description', '', 'string');
INSERT INTO cc_stream_setting ("keyname", "value", "type") VALUES ('s3_genre', '', 'string');
INSERT INTO cc_stream_setting ("keyname", "value", "type") VALUES ('s3_url', '', 'string');

INSERT INTO cc_stream_setting ("keyname", "value", "type") VALUES ('s4_enable', 'false', 'boolean');
INSERT INTO cc_stream_setting ("keyname", "value", "type") VALUES ('s4_output', 'icecast', 'string');
INSERT INTO cc_stream_setting ("keyname", "value", "type") VALUES ('s4_host', '', 'string');
INSERT INTO cc_stream_setting ("keyname", "value", "type") VALUES ('s4_port', '', 'integer');
INSERT INTO cc_stream_setting ("keyname", "value", "type") VALUES ('s4_mount', '', 'string');
INSERT INTO cc_stream_setting ("keyname", "value", "type") VALUES ('s4_type', '', 'string');
INSERT INTO cc_stream_setting ("keyname", "value", "type") VALUES ('s4_bitrate', '', 'integer');
INSERT INTO cc_stream_setting ("keyname", "value", "type") VALUES ('s4_channels', 'stereo', 'string');
INSERT INTO cc_stream_setting ("keyname", "value", "type") VALUES ('s4_admin_pass', '', 'string');
INSERT INTO cc_stream_setting ("keyname", "value", "type") VALUES ('s4_admin_user', '', 'string');
INSERT INTO cc_stream_setting ("keyname", "value", "type") VALUES ('s4_user', '', 'string');
INSERT INTO cc_stream_setting ("keyname", "value", "type") VALUES ('s4_pass', '', 'string');
INSERT INTO cc_stream_setting ("keyname", "value", "type") VALUES ('s4_name', '', 'string');
INSERT INTO cc_stream_setting ("keyname", "value", "type") VALUES ('s4_description', '', 'string');
INSERT INTO cc_stream_setting ("keyname", "value", "type") VALUES ('s4_genre', '', 'string');
INSERT INTO cc_stream_setting ("keyname", "value", "type") VALUES ('s4_url', '', 'string');
