ALTER TABLE cc_files DROP COLUMN description;

DROP TABLE IF EXISTS "podcast" CASCADE;

DROP TABLE IF EXISTS "imported_podcast" CASCADE;

DROP TABLE IF EXISTS "station_podcast" CASCADE;

DROP TABLE IF EXISTS "podcast_episodes" CASCADE;
