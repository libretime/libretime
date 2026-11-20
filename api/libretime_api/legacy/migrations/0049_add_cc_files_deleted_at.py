# pylint: disable=invalid-name

from django.db import migrations

from ._migrations import legacy_migration_factory

UP = """
ALTER TABLE "cc_files"
    ADD COLUMN "deleted_at" timestamp without time zone DEFAULT NULL,
    ADD COLUMN "deleted_by_id" integer DEFAULT NULL;

ALTER TABLE "cc_files"
    ADD CONSTRAINT cc_files_deleted_by_id_fkey
        FOREIGN KEY (deleted_by_id)
        REFERENCES public.cc_subjs (id)
        ON DELETE SET NULL;
"""

DOWN = """
ALTER TABLE "cc_files"
    DROP CONSTRAINT IF EXISTS cc_files_deleted_by_id_fkey;

ALTER TABLE "cc_files"
    DROP COLUMN IF EXISTS "deleted_by_id",
    DROP COLUMN IF EXISTS "deleted_at";
"""


class Migration(migrations.Migration):
    dependencies = [
        ("legacy", "0048_add_podcast_episode_create_at"),
    ]
    operations = [
        migrations.RunPython(
            code=legacy_migration_factory(
                target="49",
                sql=UP,
            )
        )
    ]
