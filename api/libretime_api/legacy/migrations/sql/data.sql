-- Can't be set up in Propel's XML schema
ALTER TABLE "cc_pref" ALTER COLUMN "subjid" SET DEFAULT NULL;
CREATE UNIQUE INDEX "cc_pref_key_idx" ON "cc_pref" ("keystr") WHERE "subjid" IS NULL;
ANALYZE "cc_pref";

INSERT INTO cc_live_log ("state", "start_time") VALUES ('S', now() at time zone 'UTC');

INSERT INTO cc_pref ("keystr", "valstr") VALUES ('import_timestamp', '0');
INSERT INTO cc_pref ("keystr", "valstr") VALUES ('timezone', 'UTC');
INSERT INTO cc_pref ("keystr", "valstr") VALUES ('off_air_meta', 'LibreTime - offline');
INSERT INTO cc_pref ("keystr", "valstr") VALUES ('enable_replay_gain', 1);
INSERT INTO cc_pref ("keystr", "valstr") VALUES ('locale', 'en_US');
INSERT INTO cc_pref ("keystr", "valstr") VALUES ('plan_level', 'disabled');
INSERT INTO cc_pref ("keystr", "valstr") VALUES ('podcast_album_override', 1);
INSERT INTO cc_pref ("keystr", "valstr") VALUES ('podcast_auto_smartblock', 0);
INSERT INTO cc_pref ("keystr", "valstr") VALUES ('scheduled_play_switch', 'on');
INSERT INTO cc_pref ("keystr", "valstr") VALUES ('whats_new_dialog_viewed', 1);

INSERT INTO cc_subjs ("login", "type", "pass") VALUES ('admin', 'A', md5('admin'));
INSERT INTO cc_pref ("subjid", "keystr", "valstr") VALUES (1, 'user_locale', 'en_US');
