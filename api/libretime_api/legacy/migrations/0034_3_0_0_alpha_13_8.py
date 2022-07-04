# pylint: disable=invalid-name

from django.db import migrations

from ._migrations import legacy_migration_factory

UP = """
DROP TABLE IF EXISTS "cloud_file" CASCADE;
"""

DOWN = """
CREATE TABLE "cloud_file"
(
    "id" serial NOT NULL,
    "storage_backend" VARCHAR(512) NOT NULL,
    "resource_id" TEXT NOT NULL,
    "cc_file_id" INTEGER,
    PRIMARY KEY ("id")
);

ALTER TABLE "cloud_file" ADD CONSTRAINT "cloud_file_FK_1"
    FOREIGN KEY ("cc_file_id")
    REFERENCES "cc_files" ("id")
    ON DELETE CASCADE;
"""


class Migration(migrations.Migration):
    dependencies = [
        ("legacy", "0033_3_0_0_alpha_13_7"),
    ]
    operations = [
        migrations.RunPython(
            code=legacy_migration_factory(
                target="3.0.0-alpha.13.8",
                sql=UP,
            )
        )
    ]
