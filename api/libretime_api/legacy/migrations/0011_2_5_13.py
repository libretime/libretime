# pylint: disable=invalid-name

from django.db import migrations

from ._migrations import legacy_migration_factory

UP = """
-----------------------------------------------------------------------
-- third_party_track_references
-----------------------------------------------------------------------

CREATE TABLE IF NOT EXISTS "third_party_track_references"
(
    "id" serial NOT NULL,
    "service" VARCHAR(256) NOT NULL,
    "foreign_id" VARCHAR(256),
    "file_id" INTEGER NOT NULL,
    "upload_time" TIMESTAMP,
    "status" VARCHAR(256),
    PRIMARY KEY ("id"),
    CONSTRAINT "foreign_id_unique" UNIQUE ("foreign_id")
);

-----------------------------------------------------------------------
-- celery_tasks
-----------------------------------------------------------------------

CREATE TABLE IF NOT EXISTS "celery_tasks"
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


ALTER TABLE "third_party_track_references" ADD CONSTRAINT "track_reference_fkey"
    FOREIGN KEY ("file_id")
    REFERENCES "cc_files" ("id")
    ON DELETE CASCADE;

ALTER TABLE "celery_tasks" ADD CONSTRAINT "celery_service_fkey"
    FOREIGN KEY ("track_reference")
    REFERENCES "third_party_track_references" ("id")
    ON DELETE CASCADE;
"""

DOWN = """
-----------------------------------------------------------------------
-- third_party_track_references
-----------------------------------------------------------------------
DROP TABLE IF EXISTS "third_party_track_references" CASCADE;

-----------------------------------------------------------------------
-- celery_tasks
-----------------------------------------------------------------------
DROP TABLE IF EXISTS "celery_tasks" CASCADE;
"""


class Migration(migrations.Migration):
    dependencies = [
        ("legacy", "0010_2_5_12"),
    ]
    operations = [
        migrations.RunPython(
            code=legacy_migration_factory(
                target="2.5.13",
                sql=UP,
            ),
            reverse_code=legacy_migration_factory(
                target="2.5.12",
                sql=DOWN,
                reverse=True,
            ),
        )
    ]
