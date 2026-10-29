# pylint: disable=invalid-name

from django.db import migrations

from ._migrations import legacy_migration_factory

UP = """
DROP TABLE IF EXISTS "celery_tasks";
DROP TABLE IF EXISTS "third_party_track_references";
"""

DOWN = """
CREATE TABLE "third_party_track_references"
(
    "id" serial NOT NULL,
    "service" VARCHAR(256) NOT NULL,
    "foreign_id" VARCHAR(256),
    "file_id" INTEGER DEFAULT 0,
    "upload_time" TIMESTAMP,
    "status" VARCHAR(256),
    PRIMARY KEY ("id"),
    CONSTRAINT "foreign_id_unique" UNIQUE ("foreign_id")
);

ALTER TABLE "third_party_track_references" ADD CONSTRAINT "track_reference_fkey"
    FOREIGN KEY ("file_id")
    REFERENCES "cc_files" ("id")
    ON DELETE CASCADE;

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

ALTER TABLE "celery_tasks" ADD CONSTRAINT "celery_service_fkey"
    FOREIGN KEY ("track_reference")
    REFERENCES "third_party_track_references" ("id")
    ON DELETE CASCADE;
"""


class Migration(migrations.Migration):
    dependencies = [
        ("legacy", "0046_add_override_intro_outro_playlists"),
    ]
    operations = [
        migrations.RunPython(
            code=legacy_migration_factory(
                target="47",
                sql=UP,
            )
        )
    ]
