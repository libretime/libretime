# pylint: disable=invalid-name

from django.db import migrations

from ._migrations import legacy_migration_factory

UP = """
CREATE TABLE "sessions"
(
    "id" CHAR(32) NOT NULL,
    "modified" INTEGER,
    "lifetime" INTEGER,
    "data" TEXT,
    PRIMARY KEY ("id")
);
"""

DOWN = """
DROP TABLE IF EXISTS "sessions" CASCADE;
"""


class Migration(migrations.Migration):
    dependencies = [
        ("legacy", "0044_add_track_types_analyzer_options"),
    ]
    operations = [
        migrations.RunPython(
            code=legacy_migration_factory(
                target="45",
                sql=UP,
            )
        )
    ]
