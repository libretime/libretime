# pylint: disable=invalid-name

from django.db import migrations

from ._migrations import legacy_migration_factory

UP = """
ALTER TABLE "podcast_episodes"
    ADD COLUMN "created_at" TIMESTAMP;

UPDATE "podcast_episodes"
    SET "created_at" = LEAST("publication_date", now() AT TIME ZONE 'UTC');

ALTER TABLE "podcast_episodes"
    ALTER COLUMN "created_at" SET DEFAULT (now() AT TIME ZONE 'UTC'),
    ALTER COLUMN "created_at" SET NOT NULL;
"""

DOWN = """
ALTER TABLE "podcast_episodes"
    DROP COLUMN IF EXISTS "created_at";
"""


class Migration(migrations.Migration):
    dependencies = [
        ("legacy", "0047_drop_third_party_track_references"),
    ]
    operations = [
        migrations.RunPython(
            code=legacy_migration_factory(
                target="48",
                sql=UP,
            )
        )
    ]
