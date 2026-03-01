# pylint: disable=invalid-name

from django.db import migrations

from ._migrations import legacy_migration_factory

UP = """
ALTER TABLE "cc_files" DROP CONSTRAINT "cc_music_dirs_folder_fkey";
ALTER TABLE "cc_files" DROP COLUMN "directory";
DROP TABLE "cc_music_dirs";
"""

DOWN = """
CREATE TABLE "cc_music_dirs"
(
    "id" serial NOT NULL,
    "directory" TEXT,
    "type" VARCHAR(255),
    "exists" BOOLEAN DEFAULT 't',
    "watched" BOOLEAN DEFAULT 't',
    PRIMARY KEY ("id"),
    CONSTRAINT "cc_music_dir_unique" UNIQUE ("directory")
);

ALTER TABLE "cc_files" ADD COLUMN "directory" INTEGER;
ALTER TABLE "cc_files" ADD CONSTRAINT "cc_music_dirs_folder_fkey"
    FOREIGN KEY ("directory")
    REFERENCES "cc_music_dirs" ("id");
"""


class Migration(migrations.Migration):
    dependencies = [
        ("legacy", "0028_3_0_0_alpha_13_2"),
    ]
    operations = [
        migrations.RunPython(
            code=legacy_migration_factory(
                target="3.0.0-alpha.13.3",
                sql=UP,
            )
        )
    ]
