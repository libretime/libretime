# pylint: disable=invalid-name

from django.db import migrations

from ._migrations import legacy_migration_factory

UP = """
DROP TABLE IF EXISTS "cc_smemb" CASCADE;
"""

DOWN = """
CREATE TABLE "cc_smemb"
(
    "id" INTEGER NOT NULL,
    "uid" INTEGER DEFAULT 0 NOT NULL,
    "gid" INTEGER DEFAULT 0 NOT NULL,
    "level" INTEGER DEFAULT 0 NOT NULL,
    "mid" INTEGER,
    PRIMARY KEY ("id"),
    CONSTRAINT "cc_smemb_id_idx" UNIQUE ("id")
);
"""


class Migration(migrations.Migration):
    dependencies = [
        ("legacy", "0024_3_0_0_alpha_9_2"),
    ]
    operations = [
        migrations.RunPython(
            code=legacy_migration_factory(
                target="3.0.0-alpha.9.3",
                sql=UP,
            )
        )
    ]
