# pylint: disable=invalid-name

from django.db import migrations

from ._migrations import legacy_migration_factory

UP = """
ALTER TABLE cc_files ADD COLUMN description VARCHAR(512);

-----------------------------------------------------------------------
-- podcast
-----------------------------------------------------------------------

CREATE TABLE IF NOT EXISTS "podcast"
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

CREATE TABLE IF NOT EXISTS "station_podcast"
(
    "id" serial NOT NULL,
    "podcast_id" INTEGER NOT NULL,
    PRIMARY KEY ("id")
);

-----------------------------------------------------------------------
-- imported_podcast
-----------------------------------------------------------------------

CREATE TABLE IF NOT EXISTS "imported_podcast"
(
    "id" serial NOT NULL,
    "auto_ingest" BOOLEAN DEFAULT 'f' NOT NULL,
    "auto_ingest_timestamp" TIMESTAMP,
    "podcast_id" INTEGER NOT NULL,
    PRIMARY KEY ("id")
);

-----------------------------------------------------------------------
-- podcast_episodes
-----------------------------------------------------------------------

CREATE TABLE IF NOT EXISTS "podcast_episodes"
(
    "id" serial NOT NULL,
    "file_id" INTEGER,
    "podcast_id" INTEGER NOT NULL,
    "publication_date" TIMESTAMP NOT NULL,
    "download_url" VARCHAR(4096) NOT NULL,
    "episode_guid" VARCHAR(4096) NOT NULL,
    PRIMARY KEY ("id")
);


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

"""

DOWN = """
ALTER TABLE cc_files DROP COLUMN description;

DELETE FROM cc_pref WHERE keystr = 'station_podcast_id';

DROP TABLE IF EXISTS "podcast" CASCADE;

DROP TABLE IF EXISTS "imported_podcast" CASCADE;

DROP TABLE IF EXISTS "station_podcast" CASCADE;

DROP TABLE IF EXISTS "podcast_episodes" CASCADE;
"""


class Migration(migrations.Migration):
    dependencies = [
        ("legacy", "0013_2_5_15"),
    ]
    operations = [
        migrations.RunPython(
            code=legacy_migration_factory(
                target="2.5.16",
                sql=UP,
            )
        )
    ]
