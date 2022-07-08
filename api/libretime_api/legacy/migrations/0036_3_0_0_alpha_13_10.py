# pylint: disable=invalid-name

from django.db import migrations

from ._migrations import legacy_migration_factory

UP = """
DROP TABLE IF EXISTS "cc_country" CASCADE;
"""

DOWN = """
CREATE TABLE "cc_country"
(
    "isocode" CHAR(3) NOT NULL,
    "name" VARCHAR(255) NOT NULL,
    PRIMARY KEY ("isocode")
);
"""


class Migration(migrations.Migration):
    dependencies = [
        ("legacy", "0035_3_0_0_alpha_13_9"),
    ]
    operations = [
        migrations.RunPython(
            code=legacy_migration_factory(
                target="3.0.0-alpha.13.10",
                sql=UP,
            )
        )
    ]
