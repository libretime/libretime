ALTER TABLE cc_files DROP COLUMN description;

DELETE FROM cc_pref WHERE keystr = 'station_podcast_id';

DROP TABLE IF EXISTS "podcast" CASCADE;

DROP TABLE IF EXISTS "imported_podcast" CASCADE;

DROP TABLE IF EXISTS "station_podcast" CASCADE;

DROP TABLE IF EXISTS "podcast_episodes" CASCADE;
