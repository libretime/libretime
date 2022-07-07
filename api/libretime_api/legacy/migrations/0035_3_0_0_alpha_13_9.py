# pylint: disable=invalid-name

from django.db import migrations

from ._migrations import legacy_migration_factory

UP = """
ALTER TABLE "cc_files" ADD COLUMN "track_type_id" INTEGER;
ALTER TABLE "cc_files" ADD CONSTRAINT "cc_files_track_type_fkey"
    FOREIGN KEY ("track_type_id")
    REFERENCES "cc_track_types" ("id");

UPDATE "cc_files" SET "track_type" = NULL
WHERE "track_type" = '';

UPDATE "cc_files" file SET "track_type_id" = (
    SELECT "id" FROM "cc_track_types"
    WHERE "code" = file."track_type"
)
WHERE "track_type" IS NOT NULL;

UPDATE "cc_pref" file SET "valstr" = (
    SELECT "id" FROM "cc_track_types"
    WHERE "code" = file."valstr"
)
WHERE "keystr" = 'tracktype_default'
AND "valstr" <> '';

ALTER TABLE "cc_files" DROP COLUMN IF EXISTS "track_type";
"""

DOWN = """
ALTER TABLE "cc_files" ADD COLUMN "track_type" VARCHAR(16);

UPDATE "cc_files" file SET "track_type" = (
    SELECT "code" FROM "cc_track_types"
    WHERE "id" = file."track_type_id"
)
WHERE "track_type_id" IS NOT NULL;

UPDATE "cc_pref" pref SET "valstr" = (
    SELECT "code" FROM "cc_track_types"
    WHERE "id" = pref."valstr"::int
)
WHERE "keystr" = 'tracktype_default'
AND "valstr" <> '';

ALTER TABLE "cc_files" DROP CONSTRAINT "cc_files_track_type_fkey";
ALTER TABLE "cc_files" DROP COLUMN IF EXISTS "track_type_id";
"""


class Migration(migrations.Migration):
    dependencies = [
        ("legacy", "0034_3_0_0_alpha_13_8"),
    ]
    operations = [
        migrations.RunPython(
            code=legacy_migration_factory(
                target="3.0.0-alpha.13.9",
                sql=UP,
            )
        )
    ]
