# pylint: disable=invalid-name

from django.db import migrations

from ._migrations import legacy_migration_factory

UP = """
ALTER TABLE "cc_files" ADD COLUMN "track_type_id" INTEGER;
ALTER TABLE "cc_files" ADD CONSTRAINT "cc_files_track_type_fkey"
    FOREIGN KEY ("track_type_id")
    REFERENCES "cc_track_types" ("id");

UPDATE "cc_files" previous SET "track_type_id" = (
    SELECT "id" FROM "cc_track_types"
    WHERE "code" = previous."track_type"
)
WHERE "track_type" IS NOT NULL;

UPDATE "cc_pref" previous SET "valstr" = (
    SELECT "id" FROM "cc_track_types"
    WHERE "code" = previous."valstr"
)
WHERE "keystr" = 'tracktype_default'
AND "valstr" <> '';

ALTER TABLE "cc_files" DROP COLUMN IF EXISTS "track_type";
"""

DOWN = """
ALTER TABLE "cc_files" DROP CONSTRAINT "cc_files_track_type_fkey";
ALTER TABLE "cc_files" DROP COLUMN IF EXISTS "track_type_id";

ALTER TABLE "cc_files" ADD COLUMN "track_type" VARCHAR(16);

UPDATE "cc_pref" previous SET "valstr" = (
    SELECT "code" FROM "cc_track_types"
    WHERE "id" = previous."valstr"::int
)
WHERE "keystr" = 'tracktype_default'
AND "valstr" <> '';
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
